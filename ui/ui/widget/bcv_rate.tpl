<div class="panel panel-info panel-hovered mb20 activities" style="border-radius:14px; overflow:hidden;">

    <div class="panel-heading" style="background:#ffffff; padding:20px; border-bottom:1px solid #eee;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
            <div>
                <div style="font-size:13px; color:#777;">
                    💱 Tasa Oficial BCV
                    {if $rate_date}<span style="color:#aaa;">— {$rate_date|date_format:"%d/%m/%Y"}</span>{/if}
                </div>
                <div style="font-size:34px; font-weight:700; margin-top:6px; color:#2c3e50;">
                    {$bcv_rate|number_format:4:",":"."}
                    <span style="font-size:14px; font-weight:500; color:#777;">Bs/USD</span>
                </div>
            </div>

            <div style="text-align:right;">
                <div style="font-size:13px; color:#777;">Variación últimos 9 días</div>
                <div style="font-size:24px; font-weight:bold; margin-top:4px;
                    {if $variation_percent >= 0}color:#28a745;{else}color:#d9534f;{/if}">
                    {if $variation_percent >= 0}+{/if}{$variation_percent}%
                    {if $variation_percent >= 0}📈{else}📉{/if}
                </div>
            </div>
        </div>
    </div>

    <div class="panel-body" style="background:#f9fafc; padding:25px;">

        {if $bcv_rate}

            {* ====== Preparar arrays cronológicos ====== *}
            {assign var="history" value=$bcv_history|@array_reverse}
            {assign var="chart_labels" value=[]}
            {assign var="chart_bcv" value=[]}
            {assign var="chart_usdt" value=[]}

            {foreach $history as $day}
                {$chart_labels[] = $day.rate_date|date_format:"%d/%m"}
                {$chart_bcv[] = $day.rate}
                {if $day.usdt !== null}{$chart_usdt[] = $day.usdt}{else}{$chart_usdt[] = null}{/if}
            {/foreach}

            <div style="background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <canvas id="bcvChart" height="120"></canvas>
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

    const labels = {$chart_labels nofilter};
    const bcvData = {$chart_bcv nofilter};
    const usdtData = {$chart_usdt nofilter};

    if (!bcvData || bcvData.length === 0) return;

    const bcvTrendColor = bcvData[bcvData.length-1] >= bcvData[0] ? '#28a745' : '#d9534f';
    const usdtTrendColor = usdtData[usdtData.length-1] >= usdtData[0] ? '#007bff' : '#ff6600';

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'BCV',
                    data: bcvData,
                    borderColor: bcvTrendColor,
                    backgroundColor: bcvTrendColor + '20',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: bcvTrendColor
                },
                {
                    label: 'USDT',
                    data: usdtData,
                    borderColor: usdtTrendColor,
                    backgroundColor: usdtTrendColor + '20',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: usdtTrendColor
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true, position: 'top' } },
            scales: { y: { beginAtZero: false } }
        }
    });

});
</script>