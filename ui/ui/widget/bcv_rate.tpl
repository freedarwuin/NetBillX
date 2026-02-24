<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">
        💱 Tasa BCV del día: {$bcv_rate} Bs/USD
    </div>

    <div class="panel-body">

        {if $bcv_rate}

            {* ==========================
               RESUMEN FINANCIERO
            =========================== *}

            <div style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                margin-bottom:20px;
                padding:15px;
                border-radius:10px;
                background:#f8f9fa;
            ">

                <div>
                    <div style="font-size:13px; color:#777;">
                        Variación últimos 9 días
                    </div>

                    <div style="
                        font-size:26px;
                        font-weight:bold;
                        {if $variation_percent >= 0}
                            color:#28a745;
                        {else}
                            color:#d9534f;
                        {/if}
                    ">
                        {if $variation_percent >= 0}+{/if}{$variation_percent}%
                    </div>
                </div>

                <div style="font-size:40px;">
                    {if $variation_percent >= 0}
                        📈
                    {else}
                        📉
                    {/if}
                </div>

            </div>

            {* ==========================
               GRÁFICO
            =========================== *}

            <div class="mb20">
                <canvas id="bcvChart" height="90"></canvas>
            </div>

            {* ==========================
               HISTORIAL EN TARJETAS
            =========================== *}

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
                                    </div>

                                    <div style="margin-bottom:6px;">
                                        <span style="
                                            font-size:18px;
                                            {if $day.change == 'up'}
                                                color:#007bff; font-weight:bold;
                                            {elseif $day.change == 'down'}
                                                color:#d9534f; font-weight:bold;
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
                                        {else}
                                            <span class="label label-default">—</span>
                                        {/if}
                                    </div>
                                </div>

                                <div style="margin-left:12px;">
                                    <img src="system/uploads/banco-central-de-venezuela-logo-png_seeklogo-622560.png"
                                         style="max-width:50px;">
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


{* ==========================
   SCRIPT GRÁFICO PREMIUM
========================== *}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document.getElementById('bcvChart').getContext('2d');
    const dataValues = {$chart_values nofilter};
    const firstValue = dataValues[0];
    const lastValue = dataValues[dataValues.length - 1];

    const trendColor = lastValue >= firstValue ? '#28a745' : '#d9534f';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {$chart_labels nofilter},
            datasets: [{
                label: 'Tasa BCV (Bs/USD)',
                data: dataValues,
                borderColor: trendColor,
                backgroundColor: trendColor + '20',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: trendColor
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
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