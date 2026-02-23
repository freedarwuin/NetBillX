<?php
class CpuWidget
{
    // Retorna el HTML/JS del widget
    public static function getWidget()
    {
        ob_start();
        ?>
        <div class="panel panel-info panel-hovered mb20 activities">
            <div class="panel-heading">💻 Uso de CPU</div>
            <div class="panel-body">
                <canvas id="cpuUsageChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script type="text/javascript">
        {literal}
        document.addEventListener("DOMContentLoaded", function() {

            var ctx = document.getElementById('cpuUsageChart').getContext('2d');

            const topColors = ['#3b82f6'];
            const bottomColors = ['#1e3a8a'];

            const ultra3DPlugin = {
                id: 'ultra3D',

                beforeDraw(chart) {
                    const ctx = chart.ctx;
                    const width = chart.width;
                    const height = chart.height;
                    ctx.save();
                    ctx.fillStyle = "rgba(0,0,0,0.08)";
                    ctx.beginPath();
                    ctx.ellipse(width/2, height/2 + 32, 160, 42, 0, 0, 2 * Math.PI);
                    ctx.fill();
                    ctx.restore();
                },

                beforeDatasetDraw(chart) {
                    const {ctx} = chart;
                    const meta = chart.getDatasetMeta(0);
                    meta.data.forEach((element, index) => {
                        ctx.save();
                        ctx.fillStyle = bottomColors[index];
                        ctx.transform(1, 0, 0, 0.78, 0, 28);
                        for(let i = 0; i < 8; i++){
                            ctx.translate(0, 1);
                            element.draw(ctx);
                        }
                        ctx.restore();
                    });
                },

                afterDatasetDraw(chart) {
                    const {ctx} = chart;
                    const width = chart.width;
                    const height = chart.height;
                    const gradient = ctx.createLinearGradient(0, height/2 - 100, 0, height/2);
                    gradient.addColorStop(0, "rgba(255,255,255,0.12)");
                    gradient.addColorStop(1, "rgba(255,255,255,0)");
                    ctx.save();
                    ctx.beginPath();
                    ctx.ellipse(width/2, height/2 - 18, 105, 32, 0, Math.PI, 2 * Math.PI);
                    ctx.fillStyle = gradient;
                    ctx.fill();
                    ctx.restore();
                }
            };

            // Datos iniciales
            var cpuData = [0];
            var cpuChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Ahora'],
                    datasets: [{
                        label: 'Uso de CPU (%)',
                        data: cpuData,
                        borderColor: topColors,
                        backgroundColor: 'rgba(59,130,246,0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    animation: { duration: 1000 },
                    scales: {
                        y: { min: 0, max: 100 }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: { boxWidth: 15, font: { size: 14, weight: '600' } }
                        },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + ' %';
                                }
                            }
                        }
                    }
                },
                plugins: [ultra3DPlugin]
            });

            // Actualizar CPU cada 1 segundo
            setInterval(async () => {
                const response = await fetch('cpu_usage.php');
                const usage = parseFloat(await response.text());

                if(cpuChart.data.labels.length > 20){
                    cpuChart.data.labels.shift();
                    cpuChart.data.datasets[0].data.shift();
                }
                cpuChart.data.labels.push('');
                cpuChart.data.datasets[0].data.push(usage);
                cpuChart.update();
            }, 1000);

        });
        {/literal}
        </script>
        <?php
        return ob_get_clean();
    }
}