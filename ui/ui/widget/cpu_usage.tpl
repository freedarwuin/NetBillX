<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">💻 {Lang::T('CPU Usage')}</div>
    <div class="panel-body">
        <canvas id="cpuUsageChart"></canvas>
    </div>
</div>

<script type="text/javascript">
{literal}
document.addEventListener("DOMContentLoaded", function() {

    var cpu = parseFloat('{/literal}{$cpu_usage}{literal}');
    if (isNaN(cpu)) cpu = 0;

    var available = 100 - cpu;
    if (available < 0) available = 0;

    var ctx = document.getElementById('cpuUsageChart').getContext('2d');

    // 🎨 Color dinámico según carga
    var topColor;
    var bottomColor;

    if (cpu < 60) {
        topColor = '#22c55e';      // Verde
        bottomColor = '#14532d';
    } else if (cpu < 85) {
        topColor = '#f59e0b';      // Amarillo
        bottomColor = '#78350f';
    } else {
        topColor = '#ef4444';      // Rojo
        bottomColor = '#7f1d1d';
    }

    const ultra3DPlugin = {
        id: 'ultra3D',

        beforeDraw(chart) {
            const ctx = chart.ctx;
            const width = chart.width;
            const height = chart.height;

            ctx.save();
            ctx.fillStyle = "rgba(0,0,0,0.08)";
            ctx.beginPath();
            ctx.ellipse(width/2, height/2 + 32, 140, 38, 0, 0, 2 * Math.PI);
            ctx.fill();
            ctx.restore();
        },

        beforeDatasetDraw(chart) {
            const {ctx} = chart;
            const meta = chart.getDatasetMeta(0);

            meta.data.forEach((element, index) => {
                ctx.save();
                ctx.fillStyle = index === 0 ? bottomColor : '#1e293b';
                ctx.transform(1, 0, 0, 0.78, 0, 26);

                for(let i = 0; i < 8; i++){
                    ctx.translate(0, 1);
                    element.draw(ctx);
                }

                ctx.restore();
            });
        },

        afterDraw(chart) {
            const {ctx, width, height} = chart;

            ctx.save();
            ctx.font = "bold 26px sans-serif";
            ctx.fillStyle = "#111827";
            ctx.textAlign = "center";
            ctx.fillText(cpu + "%", width/2, height/2 + 8);
            ctx.restore();
        }
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['CPU Used', 'Available'],
            datasets: [{
                data: [cpu, available],
                backgroundColor: [topColor, '#e5e7eb'],
                borderColor: '#ffffff',
                borderWidth: 2,
                cutout: '65%',
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 1.4,
            rotation: -90,
            circumference: 180,
            animation: {
                duration: 1500,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        boxWidth: 14,
                        font: {
                            size: 14,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#111827',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        },
        plugins: [ultra3DPlugin]
    });

});
{/literal}
</script>