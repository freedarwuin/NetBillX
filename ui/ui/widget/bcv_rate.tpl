<canvas id="bcvChart" height="90"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded",function(){
    const ctx = document.getElementById('bcvChart');
    if(!ctx) return;

    const labels = {$chart_labels|raw};
    const bcvData = {$chart_values_usd|raw};
    const euroData = {$chart_values_eur|raw};
    const usdtData = {$chart_values_usdt|raw};

    new Chart(ctx,{
        type:'line',
        data:{
            labels:labels,
            datasets:[
                {label:'BCV', data:bcvData, borderColor:'#007bff', backgroundColor:'#007bff20', borderWidth:3, tension:0.4, fill:true, pointRadius:3, pointBackgroundColor:'#007bff'},
                {label:'USDT', data:usdtData, borderColor:'#28a745', backgroundColor:'#28a74520', borderWidth:3, tension:0.4, fill:true, pointRadius:3, pointBackgroundColor:'#28a745'},
                {label:'Euro', data:euroData, borderColor:'#ffc107', backgroundColor:'#ffc10720', borderWidth:3, tension:0.4, fill:true, pointRadius:3, pointBackgroundColor:'#ffc107'}
            ]
        },
        options:{responsive:true, plugins:{legend:{display:true, position:'top'}}, scales:{y:{beginAtZero:false}}}
    });
});
</script>