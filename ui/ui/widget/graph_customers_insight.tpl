<style>
.panel-glass {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    padding: 25px;
    transition: 0.3s ease-in-out;
}

.panel-glass:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.2);
}

.panel-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
}
</style>

<div class="panel-glass">
    <div class="panel-title">{Lang::T('All Users Insights')}</div>
    <canvas id="userRechargesChart"></canvas>
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

    var totalUsers = u_act + expired + inactive;

    var ctx = document.getElementById('userRechargesChart').getContext('2d');

    // 🎨 Crear gradientes dinámicos
    var gradientActive = ctx.createLinearGradient(0, 0, 0, 400);
    gradientActive.addColorStop(0, '#22c55e');
    gradientActive.addColorStop(1, '#15803d');

    var gradientExpired = ctx.createLinearGradient(0, 0, 0, 400);
    gradientExpired.addColorStop(0, '#f43f5e');
    gradientExpired.addColorStop(1, '#9f1239');

    var gradientInactive = ctx.createLinearGradient(0, 0, 0, 400);
    gradientInactive.addColorStop(0, '#3b82f6');
    gradientInactive.addColorStop(1, '#1e3a8a');

    // Plugin para texto central dinámico
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw(chart) {
            const {width} = chart;
            const {height} = chart;
            const ctx = chart.ctx;

            ctx.restore();
            const fontSize = (height / 130).toFixed(2);
            ctx.font = fontSize + "em sans-serif";
            ctx.textBaseline = "middle";

            const text = totalUsers + " Usuarios";
            const textX = Math.round((width - ctx.measureText(text).width) / 2);
            const textY = height / 2;

            ctx.fillStyle = "#111";
            ctx.fillText(text, textX, textY);
            ctx.save();
        }
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Usuarios Activos', 'Usuarios Expirados', 'Usuarios Inactivos'],
            datasets: [{
                data: [u_act, expired, inactive],
                backgroundColor: [
                    gradientActive,
                    gradientExpired,
                    gradientInactive
                ],
                borderWidth: 0,
                hoverOffset: 18
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1400,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 25,
                        boxWidth: 18,
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
                            let value = context.raw;
                            let percentage = ((value / totalUsers) * 100).toFixed(1);
                            return context.label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        },
        plugins: [centerTextPlugin]
    });

});
{/literal}
</script>