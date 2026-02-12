<div class="invoice">

    <div class="header">
        <h1 style="font-size:16px;">FACTURA DE VENTA</h1>
        <div style="font-size:12px; font-weight:bold;">
            {$_c['CompanyName']}<br>
            RIF: {$_c['company_rif']}<br>
            {$_c['address']}<br>
            Tel: {$_c['phone']}
        </div>
        <hr>
    </div>

    <div style="font-size:12px;">
        <strong>Nº Factura:</strong> {$in['invoice']}<br>
        <strong>Fecha:</strong> {$date}<br>
        <strong>Vendedor:</strong> {$_admin['fullname']}
    </div>

    <hr>

    <div style="font-size:12px;">
        <strong>DATOS DEL CLIENTE</strong><br>
        Nombre: {$in['fullname']}<br>
        Documento: {$in['customer_doc']}<br>
        Usuario: {$in['username']}
    </div>

    <hr>

    <table style="width:100%; font-size:12px;">
        <tr>
            <td><strong>Descripción</strong></td>
            <td align="right"><strong>Total</strong></td>
        </tr>
        <tr>
            <td>
                Servicio {$in['type']}<br>
                {$in['plan_name']}
            </td>
            <td align="right">
                {Lang::moneyFormat($in['price'])}
            </td>
        </tr>
    </table>

    <hr>

    {assign var="iva_percent" value=16}
    {assign var="base" value=$in['price']/(1+$iva_percent/100)}
    {assign var="iva" value=$in['price']-$base}

    <div style="font-size:12px;">
        Base Imponible: {Lang::moneyFormat($base)}<br>
        IVA {$iva_percent}%: {Lang::moneyFormat($iva)}<br>
        <strong>Total a Pagar: {Lang::moneyFormat($in['price'])}</strong>
    </div>

    <hr>

    {if $in['type'] != 'Balance'}
        <div style="font-size:11px;">
            Activado: {Lang::dateAndTimeFormat($in['recharged_on'], $in['recharged_time'])}<br>
            Vence: {Lang::dateAndTimeFormat($in['expiration'], $in['time'])}
        </div>
        <hr>
    {/if}

    <div style="font-size:11px; text-align:center;">
        Método de Pago: {$in['method']}<br>
        Gracias por su preferencia
    </div>

</div>
