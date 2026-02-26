<div class="panel panel-info panel-hovered mb20 activities" style="border-radius:14px; overflow:hidden;">

    {* ==========================
       HEADER
    =========================== *}
    <div class="panel-heading" style="background:#ffffff; padding:20px; border-bottom:1px solid #eee;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
            <div>
                <div style="font-size:13px; color:#777;">
                    💱 Tasa Oficial BCV
                    {if $rate_date}
                        <span style="color:#aaa;">— {$rate_date|date_format:"%d/%m/%Y"}</span>
                    {/if}
                </div>
                <div style="font-size:34px; font-weight:700; margin-top:6px; color:#2c3e50;">
                    {$bcv_rate|number_format:4:",":"."}
                    <span style="font-size:14px; font-weight:500; color:#777;">Bs/USD</span>
                </div>

                {* Variación EUR debajo de la fecha *}
                {if $eur_rate}
                    <div style="font-size:14px; font-weight:500; color:#555; margin-top:4px;">
                        💶 Tasa Euro: {$eur_rate|number_format:4:",":"."} Bs/EUR
                        <span style="{if $variacion_valor_euro >= 0}color:#28a745;{else}color:#d9534f;{/if} font-weight:bold;">
                            ({$variacion_texto_euro})
                        </span>
                    </div>
                {/if}
            </div>

            <div style="text-align:right;">
                <div style="font-size:13px; color:#777;">📊 Variación USD respecto al día anterior:</div>
                <div style="
                    font-size:24px;
                    font-weight:bold;
                    margin-top:4px;
                    {if $variacion_valor >= 0} color:#28a745; {else} color:#d9534f; {/if}
                ">
                    {if $variacion_valor >= 0}+{/if}{$variacion_texto}
                    {if $variacion_valor >= 0}📈{else}📉{/if}
                </div>
            </div>
        </div>

        {* ==========================
           ESTADO API
        =========================== *}
        {if $dolarvzla_api_expiration}
            <div style="
                margin-top:15px;
                font-size:12px;
                padding:6px 10px;
                border-radius:6px;
                display:inline-block;
                {if $dolarvzla_api_expired} background:#f8d7da; color:#721c24;
                {elseif $dolarvzla_api_expiring_soon} background:#fff3cd; color:#856404;
                {else} background:#e9f7ef; color:#155724; {/if}
            ">
                🔑 Expira: <strong>{$dolarvzla_api_expiration}</strong>
                {if $dolarvzla_api_expired} — VENCIDA
                {elseif $dolarvzla_api_expiring_soon} — Por vencer
                {else} — Activa{/if}
                <a href="https://www.dolarvzla.com/settings/api" target="_blank" style="margin-left:8px; text-decoration:underline;">Actualizar</a>
            </div>
        {/if}
    </div>

    {* ==========================
       BODY
    =========================== *}
    <div class="panel-body" style="background:#f9fafc; padding:25px;">
        {if $bcv_rate}
            <div style="background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); margin-bottom:25px;">
                <canvas id="bcvChart" height="90"></canvas>
            </div>
        {else}
            <div class="text-center text-muted small">La tasa BCV aún no está disponible.</div>
        {/if}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('bcvChart');
    if (!ctx) return;

    const history = {$bcv_history|@json_encode};
    const maxRecords = 20;
    const slicedHistory = history.slice(0, maxRecords).reverse(); // más recientes primero

    const labels = [];
    const bcvData = [];
    const usdtData = [];
    const euroData = [];

    let lastUsdt = 0;
    let lastEuro = 0;

    slicedHistory.forEach(item => {
        labels.push(item.rate_date);

        // BCV
        bcvData.push(item.rate ?? 0);

        // USDT
        if (item.usdt != null) {
            usdtData.push(item.usdt);
            lastUsdt = item.usdt;
        } else {
            usdtData.push(lastUsdt);
        }

        // EUR
        if ('eur' in item && item.eur != null) {
            euroData.push(item.eur);
            lastEuro = item.eur;
        } else {
            euroData.push(lastEuro);
        }
    });

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'BCV',
                    data: bcvData,
                    borderColor: '#007bff',
                    backgroundColor: '#007bff20',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#007bff'
                },
                {
                    label: 'USDT',
                    data: usdtData,
                    borderColor: '#28a745',
                    backgroundColor: '#28a74520',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#28a745'
                },
                {
                    label: 'EUR',
                    data: euroData,
                    borderColor: '#ffc107',
                    backgroundColor: '#ffc10720',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#ffc107'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top' }
            },
            scales: {
                y: { beginAtZero: false }
            }
        }
    });
});
</script>