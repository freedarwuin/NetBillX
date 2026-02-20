<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">{Lang::T('All Users Insights')}</div>
    <div class="panel-body">
        <canvas id="userRechargesChart"></canvas>
    </div>
</div>

<script type="text/javascript">
{literal}
document.addEventListener("DOMContentLoaded", function() {

    var u_act = parseInt('{/literal}{$u_act}{literal}');
    var c_all = parseInt('{/literal}{$c_all}{literal}');
    var u_all = parseInt('{/literal}{$u_all}{literal}');

    var expired = u_all - u_act;
    var inactive = c_all - u_all;
    if (inactive < 0) inactive = 0;

    var ctx = document.getElementById('userRechargesChart').getContext('2d');

    const topColors = ['#22c55e', '#ef4444', '#3b82f6'];
    const bottomColors = ['#14532d', '#7f1d1d', '#1e3a8a'];

    const ultra3DPlugin = {
        id: 'ultra3D',

        beforeDraw(chart) {
            const ctx = chart.ctx;
            const width = chart.width;
            const height = chart.height;

            // sombra base elegante
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

                // ligera inclinación 3D
                ctx.transform(1, 0, 0, 0.78, 0, 28);

                // grosor refinado
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

            // reflejo superior sutil
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

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Usuarios Activos', 'Usuarios Expirados', 'Usuarios Inactivos'],
            datasets: [{
                data: [u_act, expired, inactive],
                backgroundColor: topColors,
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 25
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 1.3,
            rotation: -90,
            animation: {
                duration: 1800,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        boxWidth: 15,
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
                            let total = context.dataset.data.reduce((a,b)=>a+b,0);
                            let value = context.raw;
                            let percentage = ((value/total)*100).toFixed(1);
                            return context.label + ': ' + value + ' (' + percentage + '%)';
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