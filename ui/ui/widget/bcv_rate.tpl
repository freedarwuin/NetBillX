<div class="panel panel-info panel-hovered mb20 activities" style="border-radius:14px; overflow:hidden;">

    {* ==========================
       HEADER
    =========================== *}
    <div class="panel-heading" style="background:#ffffff; padding:20px; border-bottom:1px solid #eee;">
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
            </div>

            <div>
                <div style="font-size:13px; color:#777;">
                    💱 Tasa Oficial USDT
                    {if $usdt_date}
                        <span style="color:#aaa;">— {$usdt_date|date_format:"%d/%m/%Y"}</span>
                    {/if}
                </div>

                <div style="font-size:34px; font-weight:700; margin-top:6px; color:#2c3e50;">
                    {if $usdt_rate}{$usdt_rate|number_format:4:",":"."}{else}-{/if} <span style="font-size:14px; font-weight:500; color:#777;">Bs/USDT</span>
                </div>
            </div>

        </div>
    </div>

    {* ==========================
       BODY
    =========================== *}
    <div class="panel-body" style="background:#f9fafc; padding:25px;">

        {* ==========================
           GRÁFICOS
        =========================== *}
        <div style="background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); margin-bottom:25px;">
            <canvas id="bcvChart" height="90"></canvas>
            <canvas id="usdtChart" height="90" style="margin-top:25px;"></canvas>
        </div>

        {* ==========================
           HISTORIAL BCV
        =========================== *}
        {if $bcv_history|@count > 0}
            <h5>Historial BCV últimos días</h5>
            <div class="row">
                {foreach $bcv_history as $day}
                    <div class="col-md-4 mb-3">
                        <div style="border-radius:10px; padding:15px; background:#ffffff; box-shadow:0 3px 8px rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-size:13px; color:#888; margin-bottom:6px;">{$day.rate_date|date_format:"%d/%m/%Y"}</div>
                                <div style="font-size:18px; font-weight:bold; {if $day.change == 'up'}color:#007bff;{elseif $day.change == 'down'}color:#d9534f;{/if}">
                                    {$day.rate|number_format:4:",":"."} Bs/USD
                                </div>
                                <div style="margin-top:6px;">
                                    {if $day.change == 'up'}<span class="label label-primary">⬆ Subió</span>{elseif $day.change == 'down'}<span class="label label-danger">⬇ Bajó</span>{else}<span class="label label-default">—</span>{/if}
                                </div>
                            </div>
                            <img src="system/uploads/banco-central-de-venezuela-logo-png_seeklogo-622560.png" style="max-width:45px; opacity:0.7;">
                        </div>
                    </div>
                {/foreach}
            </div>
        {/if}

        {* ==========================
           HISTORIAL USDT
        =========================== *}
        {if $usdt_history|@count > 0}
            <h5>Historial USDT últimos días</h5>
            <div class="row">
                {foreach $usdt_history as $day}
                    <div class="col-md-4 mb-3">
                        <div style="border-radius:10px; padding:15px; background:#ffffff; box-shadow:0 3px 8px rgba(0,0,0,0.05); display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-size:13px; color:#888; margin-bottom:6px;">{$day.rate_date|date_format:"%d/%m/%Y"}</div>
                                <div style="font-size:18px; font-weight:bold; {if $day.change == 'up'}color:#007bff;{elseif $day.change == 'down'}color:#d9534f;{/if}">
                                    {$day.rate|number_format:4:",":"."} Bs/USDT
                                </div>
                                <div style="margin-top:6px;">
                                    {if $day.change == 'up'}<span class="label label-primary">⬆ Subió</span>{elseif $day.change == 'down'}<span class="label label-danger">⬇ Bajó</span>{else}<span class="label label-default">—</span>{/if}
                                </div>
                            </div>
                            <img src="system/uploads/usdt-logo.png" style="max-width:45px; opacity:0.7;">
                        </div>
                    </div>
                {/foreach}
            </div>
        {/if}

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // ===== BCV
    const ctxBcv = document.getElementById('bcvChart');
    if(ctxBcv && {$chart_values nofilter}.length > 0) {
        const bcvData = {$chart_values nofilter};
        const bcvLabels = {$chart_labels nofilter};
        const bcvColor = bcvData[bcvData.length-1] >= bcvData[0] ? '#28a745' : '#d9534f';

        new Chart(ctxBcv, {
            type: 'line',
            data: { labels: bcvLabels, datasets: [{ data: bcvData, borderColor: bcvColor, backgroundColor: bcvColor+'20', borderWidth:3, tension:0.4, fill:true, pointRadius:3, pointBackgroundColor:bcvColor }]},
            options: { responsive:true, plugins:{ legend:{display:false} }, scales:{ y:{beginAtZero:false} } }
        });
    }

    // ===== USDT
    const ctxUsdt = document.getElementById('usdtChart');
    if(ctxUsdt && {$usdt_chart_values nofilter}.length > 0) {
        const usdtData = {$usdt_chart_values nofilter};
        const usdtLabels = {$usdt_chart_labels nofilter};
        const usdtColor = usdtData[usdtData.length-1] >= usdtData[0] ? '#f39c12' : '#e74c3c';

        new Chart(ctxUsdt, {
            type: 'line',
            data: { labels: usdtLabels, datasets: [{ data: usdtData, borderColor: usdtColor, backgroundColor: usdtColor+'20', borderWidth:3, tension:0.4, fill:true, pointRadius:3, pointBackgroundColor:usdtColor }]},
            options: { responsive:true, plugins:{ legend:{display:false} }, scales:{ y:{beginAtZero:false} } }
        });
    }
});
</script>