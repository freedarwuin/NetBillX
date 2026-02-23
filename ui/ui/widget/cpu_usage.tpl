<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">
        💻 Uso de CPU en Tiempo Real
    </div>

    <div class="panel-body text-center">
        <canvas id="cpuChart" height="220"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
{literal}
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document.getElementById('cpuChart').getContext('2d');

    let cpuValue = 0;

    function getColor(value){
        if(value < 60) return '#22c55e';      // verde
        if(value < 85) return '#f59e0b';      // amarillo
        return '#ef4444';                     // rojo
    }

    const centerTextPlugin = {
        id: 'centerText',
        afterDraw(chart) {
            const {ctx, width, height} = chart;
            ctx.save();
            ctx.font = "bold 28px sans-serif";
            ctx.fillStyle = "#111827";
            ctx.textAlign = "center";
            ctx.fillText(cpuValue + "%", width/2, height/2 + 8);
            ctx.restore();
        }
    };

    const cpuChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['CPU Used', 'Available'],
            datasets: [{
                data: [0, 100],
                backgroundColor: ['#22c55e', '#e5e7eb'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            rotation: -90,
            circumference: 360,
            animation: {
                animateRotate: true,
                duration: 800
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context){
                            return context.raw + '%';
                        }
                    }
                }
            }
        },
        plugins: [centerTextPlugin]
    });

    async function updateCPU(){
        try{
            const response = await fetch('cpu_usage_ajax.php');
            const data = await response.text();
            cpuValue = parseFloat(data);

            if(isNaN(cpuValue)) cpuValue = 0;

            cpuChart.data.datasets[0].data = [cpuValue, 100 - cpuValue];
            cpuChart.data.datasets[0].backgroundColor = [
                getColor(cpuValue),
                '#e5e7eb'
            ];

            cpuChart.update();
        }catch(e){
            console.log("Error CPU:", e);
        }
    }

    updateCPU();
    setInterval(updateCPU, 2000); // actualiza cada 2 segundos

});
{/literal}
</script>