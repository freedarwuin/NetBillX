<div class="panel panel-info panel-hovered mb20 activities" style="border-radius:14px; overflow:hidden;">
    <div class="panel-heading" style="background:#fff; padding:20px; border-bottom:1px solid #eee;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
            <div>
                <div style="font-size:13px; color:#777;">
                    💱 Tasa Oficial BCV
                    {if $rate_date}
                        <span style="color:#aaa;">— {$rate_date|date_format:"%d/%m/%Y"}</span>
                    {/if}
                </div>
                <div style="font-size:34px; font-weight:700; margin-top:6px; color:#2c3e50;">
                    {$bcv_rate|number_format:4:",":"."} <span style="font-size:14px; font-weight:500; color:#777;">Bs/USD</span>
                </div>
                {if $eur_rate}
                    <div style="font-size:14px; font-weight:500; color:#555; margin-top:4px;">
                        💶 Euro: {$eur_rate|number_format:4:",":"."} Bs/EUR
                    </div>
                {/if}
            </div>
        </div>
    </div>

    <div class="panel-body" style="background:#f9fafc; padding:25px;">
        {if $bcv_rate}
            <div style="background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); margin-bottom:25px;">
                <canvas id="bcvChart" height="90"></canvas>
            </div>
        {else}
            <div class="text-center text-muted small">La tasa BCV aún no está disponible.</div>
        {/if}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('bcvChart');
    if (!ctx) return;

    const labels = {$chart_labels|raw};
    const bcvData = {$chart_values_usd|raw};
    const euroData = {$chart_values_eur|raw};
    const usdtData = {$chart_values_usdt|raw};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                { label: 'BCV', data: bcvData, borderColor: '#007bff', backgroundColor:'#007bff20', borderWidth:3, tension:0.4, fill:true, pointRadius:3, pointBackgroundColor:'#007bff' },
                { label: 'USDT', data: usdtData, borderColor: '#28a745', backgroundColor:'#28a74520', borderWidth:3, tension:0.4, fill:true, pointRadius:3, pointBackgroundColor:'#28a745' },
                { label: 'Euro', data: euroData, borderColor: '#ffc107', backgroundColor:'#ffc10720', borderWidth:3, tension:0.4, fill:true, pointRadius:3, pointBackgroundColor:'#ffc107' }
            ]
        },
        options: {
            responsive:true,
            plugins:{ legend:{ display:true, position:'top' } },
            scales:{ y:{ beginAtZero:false } }
        }
    });
});
</script>