<div class="panel panel-success panel-hovered mb20" id="binance-widget">
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

        {else}
            <div class="alert alert-warning text-center">
                No hay datos aún.
            </div>
        {/if}

    </div>
</div>

<script>
function refrescarBinance() {
    fetch("index.php?_route=widget/update_binance")
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const nuevoWidget = doc.querySelector("#binance-widget");

            if (nuevoWidget) {
                document.querySelector("#binance-widget").innerHTML = nuevoWidget.innerHTML;
            }
        })
        .catch(err => console.log("Error:", err));
}

// Refresca cada 1 segundo
setInterval(refrescarBinance, 1000);
</script>