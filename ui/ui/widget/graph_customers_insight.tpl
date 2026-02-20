<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">{Lang::T('All Users Insights')}</div>
    <div class="panel-body">
        <canvas id="userRechargesChart"></canvas>
    </div>
</div>

<script type="text/javascript">
{literal}
document.addEventListener("DOMContentLoaded", function() {

    var u_act = '{/literal}{$u_act}{literal}';
    var c_all = '{/literal}{$c_all}{literal}';
    var u_all = '{/literal}{$u_all}{literal}';

    var expired = u_all - u_act;
    var inactive = c_all - u_all;

    if (inactive < 0) inactive = 0;

    var ctx = document.getElementById('userRechargesChart').getContext('2d');

    var data = {
        labels: ['Usuarios Activos', 'Usuarios Expirados', 'Usuarios Inactivos'],
        datasets: [{
            label: 'Estado de Usuarios',
            data: [parseInt(u_act), parseInt(expired), parseInt(inactive)],
            backgroundColor: [
                'rgba(34, 197, 94, 0.85)',   // Verde moderno
                'rgba(239, 68, 68, 0.85)',   // Rojo suave
                'rgba(59, 130, 246, 0.85)'   // Azul elegante
            ],
            borderColor: [
                'rgba(21, 128, 61, 1)',
                'rgba(185, 28, 28, 1)',
                'rgba(29, 78, 216, 1)'
            ],
            borderWidth: 2,
            hoverOffset: 12
        }]
    };

    var options = {
        responsive: true,
        aspectRatio: 1.2,
        animation: {
            animateScale: true,
            animateRotate: true
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 18,
                    padding: 20,
                    font: {
                        size: 14,
                        weight: 'bold'
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let value = context.raw;
                        let percentage = ((value / total) * 100).toFixed(1);
                        return context.label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    };

    new Chart(ctx, {
        type: 'doughnut', // Más moderno que pie
        data: data,
        options: options
    });

});
{/literal}
</script>