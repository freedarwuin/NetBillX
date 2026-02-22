<div class="panel panel-success panel-hovered mb20">
    <div class="panel-heading">
        💰 Binance P2P USDT/VES (BUY)
    </div>

    <div class="panel-body">

        {if $binance_rate}

            <div class="alert alert-success text-center" style="font-size:18px; font-weight:bold;">
                Promedio Actual: {$binance_rate} Bs/USDT
            </div>

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Prom</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Ofertas</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$binance_history item=row}
                    <tr>
                        <td>{$row.rate_date}</td>

                        <td>
                            {if $row.change == 'up'}
                                <span style="color:green;">▲ {$row.avg_rate}</span>
                            {elseif $row.change == 'down'}
                                <span style="color:red;">▼ {$row.avg_rate}</span>
                            {else}
                                {$row.avg_rate}
                            {/if}
                        </td>

                        <td>{$row.min_rate}</td>
                        <td>{$row.max_rate}</td>
                        <td>{$row.offers}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

            <!-- Contador en vivo -->
            <div class="text-right" style="font-size:12px; color:#666;">
                Última actualización:
                <span id="binance-live-time"
                      data-time="{$binance_history[0].rate_date|strtotime}">
                      {$binance_history[0].rate_date}
                </span>
            </div>

        {else}
            <div class="alert alert-warning text-center">
                No hay datos aún.
            </div>
        {/if}

    </div>
</div>

<style>
#binance-live-time {
    font-weight: bold;
}
</style>
<script>
function updateBinanceTimer() {

    var now = Math.floor(Date.now() / 1000);

    document.querySelectorAll('#binance-live-time').forEach(function(el){

        var serverTime = parseInt(el.dataset.time);
        if (!serverTime) return;

        var diff = now - serverTime;
        if (diff < 0) diff = 0;

        var m = Math.floor(diff / 60);
        var s = diff % 60;

        if (m > 0) {
            el.textContent = m + "m " + s + "s atrás";
        } else {
            el.textContent = s + "s atrás";
        }

    });
}

// 🔁 Igual que tu otro widget
setInterval(updateBinanceTimer, 1000);
updateBinanceTimer();
</script>