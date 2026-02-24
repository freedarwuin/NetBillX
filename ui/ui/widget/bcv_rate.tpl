<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">💱 Tasa BCV del día: {$bcv_rate} Bs/USD</div>
    <div class="panel-body">

        {if $bcv_rate}

            {* =======================
               GRÁFICO DE TENDENCIA
            ======================== *}
            {if $chart_labels|@count > 0}
                <div class="mb20">
                    <canvas id="bcvChart" height="90"></canvas>
                </div>
            {/if}

            {if $bcv_history|@count > 0}
                <div class="row">
                    {foreach $bcv_history as $day name=loop}
                        <div class="col-md-4 mb-3">
                            <div style="
                                border:1px solid #e6e6e6;
                                border-radius:8px;
                                box-shadow:0 2px 6px rgba(0,0,0,0.05);
                                padding:12px;
                                background:#fff;
                                display:flex;
                                align-items:center;
                                justify-content: space-between;
                            ">
                                <div>
                                    <div style="
                                        font-weight:bold;
                                        font-size:13px;
                                        color:#777;
                                        margin-bottom:8px;
                                    ">
                                        {$day.rate_date|date_format:"%d/%m/%Y"}
                                        {if $day.rate_date == $smarty.now|date_format:"%Y-%m-%d"}
                                            <span style="color:#28a745; font-weight:bold;">(Hoy)</span>
                                        {/if}
                                    </div>

                                    <div style="margin-bottom:6px;">
                                        <span style="
                                            font-size:18px;
                                            {if $day.change == 'up'}
                                                color:#007bff; font-weight:bold;
                                            {elseif $day.change == 'down'}
                                                color:#d9534f; font-weight:bold;
                                            {elseif $day.change == 'same'}
                                                color:#6c757d;
                                            {/if}
                                        ">
                                            {$day.rate} Bs/USD
                                        </span>
                                    </div>

                                    <div>
                                        {if $day.change == 'up'}
                                            <span class="label label-primary">⬆ Subió</span>
                                        {elseif $day.change == 'down'}
                                            <span class="label label-danger">⬇ Bajó</span>
                                        {elseif $day.change == 'same'}
                                            <span class="label label-default">— Sin cambio</span>
                                        {else}
                                            <span class="label label-default">—</span>
                                        {/if}
                                    </div>
                                </div>

                                <div style="margin-left:12px; text-align:center;">
                                    <img src="system/uploads/banco-central-de-venezuela-logo-png_seeklogo-622560.png"
                                         alt="Logo Banco Central de Venezuela"
                                         style="max-width:60px; height:auto;">
                                </div>
                            </div>
                        </div>

                        {if ($smarty.foreach.loop.iteration % 3) == 0}
                            <div class="col-md-12">
                                <hr style="margin:18px 0; border-top:1px solid #eee;">
                            </div>
                        {/if}
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


{* =======================
   SCRIPT DEL GRÁFICO
======================= *}
{if $chart_labels|@count > 0}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document.getElementById('bcvChart').getContext('2d');

    const dataValues = {$chart_values nofilter};
    const firstValue = dataValues[0];
    const lastValue = dataValues[dataValues.length - 1];

    // Color dinámico según tendencia general
    const trendColor = lastValue >= firstValue ? '#007bff' : '#d9534f';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {$chart_labels nofilter},
            datasets: [{
                label: 'Tasa BCV (Bs/USD)',
                data: dataValues,
                borderColor: trendColor,
                backgroundColor: trendColor + '20',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
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
{/if}