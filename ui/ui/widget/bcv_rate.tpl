<div class="panel panel-info panel-hovered mb20 activities">

    <div class="panel-heading" style="display:flex; justify-content:space-between; flex-wrap:wrap; align-items:center;">

        <div>
            <div style="font-size:13px; color:#777;">
                💱 Tasa Oficial BCV — {$rate_date|date_format:"%d/%m/%Y"}
            </div>

            <div style="font-size:28px; font-weight:700; margin-top:4px; color:#2c3e50;">
                USD: {$bcv_usd|number_format:4:",":"."} Bs
                {if $bcv_usdt} | USDT: {$bcv_usdt|number_format:4:",":"."} Bs{/if}
                {if $bcv_eur} | EUR: {$bcv_eur|number_format:4:",":"."} Bs{/if}
            </div>
        </div>

        <div style="font-size:36px; opacity:0.6;">
            🏦
        </div>

    </div>

    <div class="panel-body" style="background:#f9fafc; padding:20px;">
        <canvas id="multiCurrencyChart" height="100"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('multiCurrencyChart');
    if (!ctx) return;

    const labels = {$chart_labels nofilter}; // fechas históricas
    const usdData  = {$chart_usd nofilter};
    const usdtData = {$chart_usdt nofilter};
    const eurData  = {$chart_eur nofilter};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'USD',
                    data: usdData,
                    borderColor: '#28a745',
                    backgroundColor: '#28a74520',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'USDT',
                    data: usdtData,
                    borderColor: '#007bff',
                    backgroundColor: '#007bff20',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'EUR',
                    data: eurData,
                    borderColor: '#ff9800',
                    backgroundColor: '#ff980020',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: false } }
        }
    });
});
</script>