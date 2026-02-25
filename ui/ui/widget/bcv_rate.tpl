<div class="panel panel-info panel-hovered mb20 activities" style="border-radius:14px; overflow:hidden;">

    {* ==========================
       HEADER
    =========================== *}
    <div class="panel-heading" style="background:#ffffff; padding:20px; border-bottom:1px solid #eee;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
            <div>
                <div style="font-size:13px; color:#777;">
                    💱 Tasa Oficial BCV
                    {if $bcv.latest.date}
                        <span style="color:#aaa;">
                            — {$bcv.latest.date|date_format:"%d/%m/%Y"}
                        </span>
                    {/if}
                </div>

                <div style="font-size:34px; font-weight:700; margin-top:6px; color:#2c3e50;">
                    {$bcv.latest.usd|number_format:4:",":"."}
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
                    {if $variation_percent >= 0}📈{else}📉{/if}
                </div>
            </div>
        </div>
    </div>

    {* ==========================
       BODY
    =========================== *}
    <div class="panel-body" style="background:#f9fafc; padding:25px;">

        {if $bcv.latest.usd}

            {* ==========================
               GRÁFICO BCV
            =========================== *}

            {assign var="chart_values" value=[]}
            {assign var="chart_labels" value=[]}

            {foreach $bcv.bcv_history as $day}
                {$chart_values[] = $day.rate}
                {$chart_labels[] = $day.rate_date|date_format:"%d/%m"}
            {/foreach}

            <div style="background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <canvas id="bcvChart" height="90"></canvas>
            </div>

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
    const labels = {$chart_labels nofilter};

    if (!dataValues || dataValues.length === 0) return;

    const trendColor = dataValues[dataValues.length-1] >= dataValues[0] ? '#28a745' : '#d9534f';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                borderColor: trendColor,
                backgroundColor: trendColor + '20',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointBackgroundColor: trendColor
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: false } }
        }
    });
});
</script>