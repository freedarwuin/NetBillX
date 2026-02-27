<div class="panel panel-info panel-hovered mb20 activities" style="border-radius:14px; overflow:hidden;">

    {* ================= HEADER ================= *}
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
                    {$bcv_rate|number_format:4:",":"."} <span style="font-size:14px; font-weight:500; color:#777;">Bs/USD</span>
                </div>
                {if $eur_rate}
                    <div style="font-size:14px; font-weight:500; color:#555; margin-top:4px;">
                        💶 Euro: {$eur_rate|number_format:4:",":"."} Bs/EUR
                    </div>
                {/if}
            </div>

            <div style="text-align:right;">
                <div style="font-size:13px; color:#777;">📊 Variación USD:</div>
                <div style="
                    font-size:24px;
                    font-weight:bold;
                    margin-top:4px;
                    {if $variacion_valor_usd >= 0} color:#28a745; {else} color:#d9534f; {/if}
                ">
                    {if $variacion_valor_usd >= 0}+{/if}{$variacion_texto_usd}
                    {if $variacion_valor_usd >= 0}📈{else}📉{/if}
                </div>

                {if $variacion_valor_eur !== null}
                    <div style="font-size:13px; color:#777; margin-top:6px;">📊 Variación Euro:</div>
                    <div style="
                        font-size:20px;
                        font-weight:bold;
                        {if $variacion_valor_eur >= 0} color:#ffc107; {else} color:#d39e00; {/if}
                    ">
                        {if $variacion_valor_eur >= 0}+{/if}{$variacion_texto_eur}
                        {if $variacion_valor_eur >= 0}📈{else}📉{/if}
                    </div>
                {/if}
            </div>
        </div>

        {* ================= ESTADO API ================= *}
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

    {* ================= BODY ================= *}
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

{* ================= SCRIPT CHART.JS ================= *}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('bcvChart');
    if (!ctx) return;

    // Convertir PHP arrays a JSON y desactivar filtrado de Smarty
    const labels = {$chart_labels|@json_encode nofilter};
    const bcvData = {$chart_values_usd|@json_encode nofilter};
    const euroData = {$chart_values_eur|@json_encode nofilter};
    const usdtData = {$chart_values_usdt|@json_encode nofilter};

    // Crear gráfico
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
                    label: 'Euro',
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