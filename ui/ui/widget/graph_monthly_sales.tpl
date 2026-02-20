<script type="text/javascript">
    {if $_c['hide_tmc'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {

                var monthlySales = JSON.parse('{/literal}{$monthlySales|json_encode}{literal}');

                var monthNames = [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ];

                var labels = [];
                var data = [];

                for (var i = 1; i <= 12; i++) {
                    var month = findMonthData(monthlySales, i);
                    labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
                    data.push(month ? month.totalSales : 0);
                }

                var ctx = document.getElementById('salesChart').getContext('2d');

                // Gradiente futurista
                var gradient = ctx.createLinearGradient(0, 0, 0, 250);
                gradient.addColorStop(0, 'rgba(59,130,246,0.9)');
                gradient.addColorStop(1, 'rgba(30,58,138,0.9)');

                const futuristicBarPlugin = {
                    id: 'futuristicBar',

                    beforeDraw(chart) {
                        const {ctx, chartArea} = chart;
                        ctx.save();
                        ctx.fillStyle = "rgba(0,0,0,0.04)";
                        ctx.fillRect(chartArea.left, chartArea.bottom + 5, chartArea.width, 8);
                        ctx.restore();
                    }
                };

                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ventas mensuales',
                            data: data,
                            backgroundColor: gradient,
                            borderRadius: 8,
                            borderWidth: 0,
                            hoverBackgroundColor: 'rgba(37,99,235,1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: {
                            duration: 1800,
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
                                        return 'Ventas: ' + context.raw.toLocaleString();
                                    }
                                }
                            }
                        }
                    },
                    plugins: [futuristicBarPlugin]
                });
            });

            function findMonthData(monthlySales, month) {
                for (var i = 0; i < monthlySales.length; i++) {
                    if (monthlySales[i].month === month) {
                        return monthlySales[i];
                    }
                }
                return null;
            }
        {/literal}
    {/if}
</script>