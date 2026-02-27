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
                    {if $variacion_valor >= 0} color:#28a745; {else} color:#d9534f; {/if}
                ">
                    {if $variacion_valor >= 0}+{/if}{$variacion_texto}
                    {if $variacion_valor >= 0}📈{else}📉{/if}
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

{if $bcv_rate}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = {$chart_labels|escape:"json"};
        const bcvData = {$chart_values|escape:"json"};
        const euroData = {$chart_euro_values|escape:"json"};
        const usdtData = {$chart_usdt_values|escape:"json"};

        const ctx = document.getElementById('bcvChart').getContext('2d');

        // Encuentra los valores máximo y mínimo de cada conjunto de datos
        const allData = [...bcvData, ...euroData, ...usdtData];
        const maxValue = Math.max(...allData);
        const minValue = Math.min(...allData);

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Tasa BCV (Bs/USD)',
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        data: bcvData,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Tasa Euro (Bs/EUR)',
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        data: euroData,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Tasa USDT',
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        data: usdtData,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,  // No forzar a que comience desde cero
                        suggestedMin: minValue - 5,  // Ajuste mínimo dinámico
                        suggestedMax: maxValue + 5,  // Ajuste máximo dinámico
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(2);  // Mostrar dos decimales
                            }
                        }
                    }
                }
            }
        });
    </script>
{/if}