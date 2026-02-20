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

    // 🎨 Colores superiores
    var colorsTop = [
        '#22c55e',  // Verde moderno
        '#ef4444',  // Rojo elegante
        '#3b82f6'   // Azul corporativo
    ];

    // 🎨 Colores inferiores (más oscuros para efecto 3D)
    var colorsBottom = [
        '#14532d',
        '#7f1d1d',
        '#1e3a8a'
    ];

    // Plugin para crear profundidad 3D
    const threeDPlugin = {
        id: 'threeD',
        beforeDatasetDraw(chart, args) {
            const {ctx} = chart;
            const meta = chart.getDatasetMeta(0);

            meta.data.forEach((element, index) => {
                ctx.save();
                ctx.fillStyle = colorsBottom[index];
                ctx.translate(0, 12); // grosor hacia abajo
                element.draw(ctx);
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
                backgroundColor: colorsTop,
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 1.2,
            animation: {
                duration: 1400,
                easing: 'easeOutCubic'
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
                    cornerRadius: 8,
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
        plugins: [threeDPlugin]
    });

});
{/literal}
</script>