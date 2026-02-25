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
                        <span style="color:#aaa;"> — {$rate_date|date_format:"%d/%m/%Y"}</span>
                    {/if}
                </div>
                <div style="font-size:34px; font-weight:700; margin-top:6px; color:#2c3e50;">
                    {$bcv_rate|number_format:4:",":"."}
                    <span style="font-size:14px; font-weight:500; color:#777;">Bs/USD</span>
                </div>
            </div>

            <div style="text-align:right;">
                <div style="font-size:13px; color:#777;">Variación últimos 9 días</div>
                <div style="font-size:24px; font-weight:bold; margin-top:4px;
                    {if $variation_percent >= 0} color:#28a745; {else} color:#d9534f; {/if}">
                    {if $variation_percent >= 0}+{/if}{$variation_percent}%
                    {if $variation_percent >= 0}📈{else}📉{/if}
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
                {else} — Activa {/if}
                <a href="https://www.dolarvzla.com/settings/api" target="_blank" style="margin-left:8px; text-decoration:underline;">Actualizar</a>
            </div>
        {/if}
    </div>

    {* ==========================
       BODY
    =========================== *}
    <div class="panel-body" style="background:#f9fafc; padding:25px;">

        {if $bcv_rate || $usdt_rate || $eur_rate}

            {* ==========================
               GRÁFICOS
            =========================== *}
            <div style="background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); margin-bottom:25px;">
                <canvas id="bcvChart" height="90"></canvas>
                <canvas id="usdtChart" height="90" style="margin-top:20px;"></canvas>
                <canvas id="eurChart" height="90" style="margin-top:20px;"></canvas>
            </div>

            {* ==========================
               HISTORIAL BCV
            =========================== *}
            {if $bcv_history|@count > 0}
                <div class="row">
                    {foreach $bcv_history as $day}
                        <div class="col-md-4 mb-3">
                            <div style="border-radius:10px; padding:15px; background:#ffffff; box-shadow:0 3px 8px rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <div style="font-size:13px; color:#888; margin-bottom:6px;">{$day.rate_date|date_format:"%d/%m/%Y"}</div>
                                    <div style="font-size:18px; font-weight:bold; {if $day.change == 'up'}color:#007bff;{elseif $day.change == 'down'}color:#d9534f;{/if}">
                                        {$day.rate|number_format:4:",":"."} Bs/USD
                                    </div>
                                    <div style="margin-top:6px;">
                                        {if $day.change == 'up'}<span class="label label-primary">⬆ Subió</span>
                                        {elseif $day.change == 'down'}<span class="label label-danger">⬇ Bajó</span>
                                        {else}<span class="label label-default">—</span>{/if}
                                    </div>
                                </div>
                                <img src="system/uploads/banco-central-de-venezuela-logo-png_seeklogo-622560.png" style="max-width:45px; opacity:0.7;">
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/if}

        {else}
            <div class="text-center text-muted small">Las tasas aún no están disponibles.</div>
        {/if}

    </div>
</div>

{literal}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    function renderChart(ctxId, labels, values, color, labelText) {
        const ctx = document.getElementById(ctxId);
        if (!ctx) return;
        if (!values || values.length === 0) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: labelText,
                    data: values,
                    borderColor: color,
                    backgroundColor: color + '20',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: color
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    }

{/literal}{$chart_labels nofilter}{literal}
const labels = {$chart_labels nofilter};

const bcvValues = {$chart_values nofilter};
const usdtValues = {$usdt_chart_values nofilter};
const eurValues = {$eur_chart_values nofilter};

const bcvTrend = bcvValues[0] <= bcvValues[bcvValues.length-1] ? '#28a745' : '#d9534f';
const usdtTrend = usdtValues && usdtValues[0] <= usdtValues[usdtValues.length-1] ? '#007bff' : '#d9534f';
const eurTrend  = eurValues  && eurValues[0]  <= eurValues[eurValues.length-1] ? '#ff9900' : '#d9534f';

renderChart('bcvChart', labels, bcvValues, bcvTrend, 'BCV (Bs/USD)');
renderChart('usdtChart', labels, usdtValues, usdtTrend, 'USDT (Bs/USDT)');
renderChart('eurChart', labels, eurValues, eurTrend, 'EUR (Bs/EUR)');

});
</script>
{/literal}