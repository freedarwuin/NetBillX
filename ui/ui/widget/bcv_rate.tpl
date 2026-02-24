<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('bcvChart').getContext('2d');

    const chartLabels = [
        {foreach $bcv_history as $d}"{$d.rate_date|date_format:"%d/%m"}"{if not $smarty.foreach.d.last},{/if}{/foreach}
    ].reverse();

    const chartData = [
        {foreach $bcv_history as $d}{$d.rate}{if not $smarty.foreach.d.last},{/if}{/foreach}
    ].reverse();

    const pointColors = [
        {foreach $bcv_history as $d}
            {if $d.change=='up'}'#007bff'{elseif $d.change=='down'}'#d9534f'{else}'#555'{/if}
            {if not $smarty.foreach.d.last},{/if}
        {/foreach}
    ].reverse();

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Bs/USD',
                data: chartData,
                fill: false,
                borderColor: '#007bff',
                tension: 0.2,
                pointBackgroundColor: pointColors,
                pointRadius: 5
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { x: { display: true }, y: { display: true } }
        }
    });
</script>