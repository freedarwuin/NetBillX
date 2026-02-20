<style>
.card-modern {
    background: #ffffff;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 45px rgba(0,0,0,0.12);
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
}
</style>

<div class="card-modern">
    <div class="card-title">{Lang::T('All Users Insights')}</div>
    <canvas id="userRechargesChart"></canvas>
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

    // 🎨 Gradientes radiales suaves
    function createGradient(color1, color2) {
        var gradient = ctx.createRadialGradient(200, 200, 50, 200, 200, 300);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    var gradientActive = createGradient('#4ade80', '#15803d');
    var gradientExpired = createGradient('#fb7185', '#9f1239');
    var gradientInactive = createGradient('#60a5fa', '#1e3a8a');

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
                borderColor: '#ffffff',
                borderWidth: 4,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            animation: {
                duration: 1200,
                easing: 'easeOutExpo'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        boxWidth: 15,
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
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
        }
    });

});
{/literal}
</script>