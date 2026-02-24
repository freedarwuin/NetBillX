<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">
        💱 Tasa BCV del día: {$bcv_rate|default:'N/D'} Bs/USD
    </div>
    <div class="panel-body">

        {if $bcv_rate}

            {if $bcv_history|@count > 0}
                <!-- Gráfico de línea -->
                <div style="margin-bottom:20px;">
                    <canvas id="bcvChart" height="150"></canvas>
                </div>

                <div class="row">
                    {foreach $bcv_history as $day name=loop}
                        <div class="col-md-4 mb-3">
                            <div style="
                                border:1px solid #e6e6e6;
                                border-radius:8px;
                                box-shadow:0 2px 6px rgba(0,0,0,0.05);
                                padding:12px;
                                background:#fff;
                                display:flex;
                                align-items:center;
                                justify-content: space-between;
                            ">

                                <div>
                                    <div style="font-weight:bold;font-size:13px;color:#777;margin-bottom:8px;">
                                        {$day.rate_date|date_format:"%d/%m/%Y"}
                                    </div>
                                    <div style="margin-bottom:6px;">
                                        <span style="font-size:18px;
                                            {if $day.change=='up'}color:#007bff;font-weight:bold;
                                            {elseif $day.change=='down'}color:#d9534f;font-weight:bold;
                                            {else}color:#555;{/if}">
                                            {$day.rate} Bs/USD
                                        </span>
                                    </div>
                                    <div>
                                        {if $day.change=='up'}
                                            <span class="label label-primary">⬆ Subió</span>
                                        {elseif $day.change=='down'}
                                            <span class="label label-danger">⬇ Bajó</span>
                                        {else}
                                            <span class="label label-default">— Sin cambio</span>
                                        {/if}
                                    </div>
                                </div>

                                <div style="margin-left:12px; text-align:center;">
                                    <img src="system/uploads/banco-central-de-venezuela-logo-png_seeklogo-622560.png"
                                         alt="Logo BCV"
                                         style="max-width:60px; height:auto;">
                                </div>

                            </div>
                        </div>

                        {if ($smarty.foreach.loop.iteration % 3) == 0}
                            <div class="col-md-12"><hr style="margin:18px 0; border-top:1px solid #eee;"></div>
                        {/if}

                    {/foreach}
                </div>

                <!-- Chart.js -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    const ctx = document.getElementById('bcvChart').getContext('2d');
                    const labels = {$bcv_history|@json_encode|replace:'"',''|replace:'\\',''};
                    const rates = {$bcv_history|@json_encode|replace:'"',''|replace:'\\',''};

                    const chartLabels = [ {foreach $bcv_history as $d}"{$d.rate_date|date_format:"%d/%m"}",{foreachelse}{/foreach} ].reverse();
                    const chartData = [ {foreach $bcv_history as $d}{$d.rate},{foreachelse}{/foreach} ].reverse();
                    const pointColors = [ {foreach $bcv_history as $d}
                        {if $d.change=='up'}'#007bff'{elseif $d.change=='down'}'#d9534f'{else}'#555'{/if},
                    {foreachelse}{/foreach} ].reverse();

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                label: 'Bs/USD',
                                data: chartData,
                                fill: false,
                                borderColor: '#007bff',
                                tension: 0.2,
                                pointBackgroundColor: pointColors,
                                pointRadius: 5
                            }]
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales: { x: { display: true }, y: { display: true } }
                        }
                    });
                </script>

            {else}
                <div class="text-center text-muted small">
                    No hay datos históricos disponibles.
                </div>
            {/if}

        {else}
            <div class="text-center text-muted small">
                La tasa BCV aún no está disponible.
            </div>
        {/if}

    </div>
</div>