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

    for (var i = 1; i <= 12; i++) {
        var month = counts.find(count => count.date === i);
        labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
        data.push(month ? month.count : 0);
    }

    var ctx = document.getElementById('chart').getContext('2d');

    // Crear gradiente azul
    var gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(0,123,255,0.7)');
    gradient.addColorStop(1, 'rgba(0,123,255,0.3)');

    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Miembros registrados',
                data: data,
                backgroundColor: data.map(value => value > 50 ? 'rgba(255,0,0,0.6)' : gradient),
                borderColor: data.map(value => value > 50 ? 'rgba(255,0,0,0.9)' : 'rgba(0,123,255,0.9)'),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' registros';
                        }
                    }
                },
                datalabels: {
                    display: true,
                    color: '#000',
                    anchor: 'end',
                    align: 'end',
                    font: {
                        weight: 'bold'
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
                        borderDash: [3, 3]
                    }
                }
            }
        },
        plugins: [ChartDataLabels] // Si usas chartjs-plugin-datalabels
    });
});
{/literal}
</script>