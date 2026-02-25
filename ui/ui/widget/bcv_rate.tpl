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
                        <span style="color:#aaa;">
                            — {$rate_date|date_format:"%d/%m/%Y"}
                        </span>
                    {/if}
                </div>

                <div style="font-size:34px; font-weight:700; margin-top:6px; color:#2c3e50;">
                    {$bcv_rate|number_format:4:",":"."}
                    <span style="font-size:14px; font-weight:500; color:#777;">
                        Bs/USD
                    </span>
                </div>
            </div>

            <div style="text-align:right;">
                <div style="font-size:13px; color:#777;">
                    Variación últimos 9 días
                </div>

                <div style="
                    font-size:24px;
                    font-weight:bold;
                    margin-top:4px;
                    {if $variation_percent >= 0}
                        color:#28a745;
                    {else}
                        color:#d9534f;
                    {/if}
                ">
                    {if $variation_percent >= 0}+{/if}{$variation_percent}%
                    {if $variation_percent >= 0}
                        📈
                    {else}
                        📉
                    {/if}
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
                {if $dolarvzla_api_expired}
                    background:#f8d7da;
                    color:#721c24;
                {elseif $dolarvzla_api_expiring_soon}
                    background:#fff3cd;
                    color:#856404;
                {else}
                    background:#e9f7ef;
                    color:#155724;
                {/if}
            ">
                🔑 Expira: <strong>{$dolarvzla_api_expiration}</strong>

                {if $dolarvzla_api_expired}
                    — VENCIDA
                {elseif $dolarvzla_api_expiring_soon}
                    — Por vencer
                {else}
                    — Activa
                {/if}

                <a href="https://www.dolarvzla.com/settings/api"
                   target="_blank"
                   style="margin-left:8px; text-decoration:underline;">
                    Actualizar
                </a>
            </div>
        {/if}

    </div>

    {* ==========================
       BODY
    =========================== *}

    <div class="panel-body" style="background:#f9fafc; padding:25px;">

        {if $bcv_rate}

            {* ==========================
               GRÁFICO BCV
            =========================== *}

            <div style="background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); margin-bottom:25px;">
                <canvas id="bcvChart" height="90"></canvas>
            </div>

            {* ==========================
               HISTORIAL COMBINADO BCV + USDT
            =========================== *}

            {if $history|@count > 0}

                <div class="row">
                    {foreach $history as $date => $day}

                        <div class="col-md-4 mb-3">
                            <div style="
                                border-radius:10px;
                                padding:15px;
                                background:#ffffff;
                                box-shadow:0 3px 8px rgba(0,0,0,0.05);
                                display:flex;
                                justify-content:space-between;
                                align-items:center;
                            ">

                                <div>
                                    <div style="font-size:13px; color:#888; margin-bottom:6px;">
                                        {$date|date_format:"%d/%m/%Y"}
                                    </div>

                                    {* BCV *}
                                    {if isset($day.bcv)}
                                        <div style="
                                            font-size:16px;
                                            font-weight:bold;
                                            {if $day.bcv.change == 'up'}
                                                color:#007bff;
                                            {elseif $day.bcv.change == 'down'}
                                                color:#d9534f;
                                            {/if}
                                        ">
                                            {$day.bcv.rate|number_format:4:",":"."} Bs/USD
                                        </div>
                                        <div style="margin-top:4px; font-size:12px; color:#555;">
                                            {if $day.bcv.change == 'up'}⬆ Subió
                                            {elseif $day.bcv.change == 'down'}⬇ Bajó
                                            {else}—{/if}
                                        </div>
                                    {/if}

                                    {* USDT *}
                                    {if isset($day.usdt)}
                                        <div style="
                                            font-size:16px;
                                            font-weight:bold;
                                            margin-top:8px;
                                            {if $day.usdt.change == 'up'}
                                                color:#28a745;
                                            {elseif $day.usdt.change == 'down'}
                                                color:#d9534f;
                                            {/if}
                                        ">
                                            Buy: {$day.usdt.buy|number_format:4:",":"."} | Sell: {$day.usdt.sell|number_format:4:",":"."} Bs
                                        </div>
                                        <div style="margin-top:4px; font-size:12px; color:#555;">
                                            {if $day.usdt.change == 'up'}⬆ Subió
                                            {elseif $day.usdt.change == 'down'}⬇ Bajó
                                            {else}—{/if}
                                        </div>
                                    {/if}
                                </div>

                                <img src="system/uploads/banco-central-de-venezuela-logo-png_seeklogo-622560.png"
                                     style="max-width:45px; opacity:0.7;">

                            </div>
                        </div>

                    {/foreach}
                </div>

            {/if}

        {else}
            <div class="text-center text-muted small">
                La tasa BCV aún no está disponible.
            </div>
        {/if}

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document.getElementById('bcvChart');
    if (!ctx) return;

    const dataValues = {$chart_values nofilter};
    const dataUSDT   = {$chart_usdt_values nofilter};
    if (!dataValues || dataValues.length === 0) return;

    const firstValue = dataValues[0];
    const lastValue = dataValues[dataValues.length - 1];
    const trendColor = lastValue >= firstValue ? '#28a745' : '#d9534f';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {$chart_labels nofilter},
            datasets: [
                {
                    label: 'BCV',
                    data: dataValues,
                    borderColor: trendColor,
                    backgroundColor: trendColor + '20',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: trendColor
                },
                {
                    label: 'USDT',
                    data: dataUSDT,
                    borderColor: '#007bff',
                    backgroundColor: '#007bff20',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#007bff'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });

});
</script>