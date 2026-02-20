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

            // Degradado moderno verde-azulado
            var gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(16,185,129,0.9)');
            gradient.addColorStop(1, 'rgba(5,150,105,0.9)');

            const premiumBarPlugin = {
                id: 'premiumBar',
                beforeDraw(chart) {
                    const {ctx, chartArea} = chart;
                    ctx.save();
                    ctx.fillStyle = "rgba(0,0,0,0.04)";
                    ctx.fillRect(chartArea.left, chartArea.bottom + 6, chartArea.width, 8);
                    ctx.restore();
                }
            };

            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Miembros registrados',
                        data: data,
                        backgroundColor: gradient,
                        borderRadius: 10,
                        borderSkipped: false,
                        borderWidth: 0,
                        hoverBackgroundColor: 'rgba(4,120,87,1)'
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1700,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    weight: '600'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.06)'
                            },
                            ticks: {
                                font: {
                                    weight: '600'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    return 'Registrados: ' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                },
                plugins: [premiumBarPlugin]
            });

        });
    {/literal}
</script>