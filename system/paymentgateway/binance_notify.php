<?php
/**
 * Binance Pay Webhook (Notify)
 */

global $config;

$headers = getallheaders();
$body = file_get_contents('php://input');

$timestamp = $headers['BinancePay-Timestamp'] ?? '';
$nonce = $headers['BinancePay-Nonce'] ?? '';
$signature = $headers['BinancePay-Signature'] ?? '';

$payload = $timestamp . "\n" . $nonce . "\n" . $body . "\n";

$expected = strtoupper(
    hash_hmac('SHA512', $payload, $config['binance_secret_key'])
);

if ($expected !== $signature) {
    http_response_code(401);
    echo 'INVALID SIGNATURE';
    exit;
}

$data = json_decode($body, true);

if (!isset($data['status']) || $data['status'] !== 'SUCCESS') {
    echo 'IGNORED';
    exit;
}

$order = $data['data'];
$merchantTradeNo = $order['merchantTradeNo'];
$tradeStatus = $order['tradeStatus'];

if ($tradeStatus !== 'PAID') {
    echo 'NOT PAID';
    exit;
}

/* =========================
   MARCAR FACTURA PAGADA
========================= */

$trx = ORM::for_table('tbl_payment_gateway')
    ->where('gateway_trx_id', $order['prepayId'])
    ->find_one();

if (!$trx || $trx->status == 2) {
    echo 'ALREADY PROCESSED';
    exit;
}

// Marcar como pagado
$trx->status = 2;
$trx->paid_date = date('Y-m-d H:i:s');
$trx->save();

// Activar servicio
Package::activateInvoice($trx->id);

echo 'SUCCESS';
