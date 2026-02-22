<script type="text/javascript">
{literal}
document.addEventListener("DOMContentLoaded", function() {
    var counts = JSON.parse('{/literal}{$monthlyRegistered|json_encode}{literal}');

    var monthNames = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    var labels = [];
    var data = [];
    var colors = [];

    for (var i = 1; i <= 12; i++) {
        var month = counts.find(count => count.date === i);
        var count = month ? month.count : 0;
        labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
        data.push(count);
        // Rojo si > 50, azul de lo contrario
        colors.push(count > 50 ? 'rgba(255,0,0,0.6)' : 'rgba(0,123,255,0.6)');
    }

    var ctx = document.getElementById('chart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Miembros registrados',
                data: data,
                backgroundColor: colors,
                borderColor: colors.map(c => c.replace('0.6','0.9')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' registros';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)',
                        borderDash: [3,3]
                    }
                }
            }
        }
    });
});
{/literal}
</script>