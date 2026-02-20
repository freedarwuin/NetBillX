<style>
.panel-info {
    background: #0f172a !important;
    border-radius: 18px;
    box-shadow: 0 0 40px rgba(0, 255, 255, 0.08);
}

.panel-heading {
    color: #38bdf8 !important;
    font-weight: 600;
    letter-spacing: 1px;
}

canvas {
    filter: drop-shadow(0 0 20px rgba(0,255,255,0.15));
}
</style>

<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">{Lang::T('All Users Insights')}</div>
    <div class="panel-body">
        <canvas id="userRechargesChart"></canvas>
    </div>
</div>

<script>
{literal}
document.addEventListener("DOMContentLoaded", function() {

    var u_act = parseInt('{/literal}{$u_act}{literal}');
    var c_all = parseInt('{/literal}{$c_all}{literal}');
    var u_all = parseInt('{/literal}{$u_all}{literal}');

    var expired = u_all - u_act;
    var inactive = c_all - u_all;
    if (inactive < 0) inactive = 0;

    var ctx = document.getElementById('userRechargesChart').getContext('2d');

    const neonColors = [
        '#00ff9f',   // Verde neon
        '#ff006e',   // Rosa neon
        '#00d4ff'    // Azul neon
    ];

    const darkNeon = [
        '#007f5f',
        '#8b003a',
        '#004c6d'
    ];

    const futuristicPlugin = {
        id: 'futuristicGlow',

        beforeDraw(chart) {
            const ctx = chart.ctx;
            const width = chart.width;
            const height = chart.height;

            // Sombra base flotante
            ctx.save();
            ctx.fillStyle = "rgba(0,255,255,0.08)";
            ctx.beginPath();
            ctx.ellipse(width/2, height/2 + 30, 160, 40, 0, 0, 2 * Math.PI);
            ctx.fill();
            ctx.restore();
        },

        beforeDatasetDraw(chart) {
            const {ctx} = chart;
            const meta = chart.getDatasetMeta(0);

            meta.data.forEach((element, index) => {
                ctx.save();

                // Glow neon
                ctx.shadowColor = neonColors[index];
                ctx.shadowBlur = 25;

                // Espesor 3D
                ctx.fillStyle = darkNeon[index];
                for(let i = 0; i < 12; i++){
                    ctx.translate(0, 1);
                    element.draw(ctx);
                }

                ctx.restore();
            });
        }
    };

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Usuarios Activos', 'Usuarios Expirados', 'Usuarios Inactivos'],
            datasets: [{
                data: [u_act, expired, inactive],
                backgroundColor: neonColors,
                borderColor: '#0f172a',
                borderWidth: 3,
                hoverOffset: 30
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 1.3,
            rotation: -90,
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#e2e8f0',
                        padding: 20,
                        boxWidth: 15,
                        font: {
                            size: 14,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#020617',
                    titleColor: '#00f5ff',
                    bodyColor: '#ffffff',
                    padding: 14,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            let total = context.dataset.data.reduce((a,b)=>a+b,0);
                            let value = context.raw;
                            let percentage = ((value/total)*100).toFixed(1);
                            return context.label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        },
        plugins: [futuristicPlugin]
    });

});
{/literal}
</script>