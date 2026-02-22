<div class="panel panel-success panel-hovered mb20">
    <div class="panel-heading">
        💰 Binance P2P USDT/VES (BUY)
    </div>

    <div class="panel-body">

        {if $binance_avg}

            <div class="alert alert-success text-center" style="font-size:18px; font-weight:bold;">
                Promedio General: {$binance_avg} Bs/USDT
            </div>

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Método</th>
                        <th>Mín</th>
                        <th>Máx</th>
                        <th>Prom</th>
                        <th>Ofertas</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$binance_data key=metodo item=info}
                    <tr>
                        <td>{$metodo}</td>
                        <td>{$info.min}</td>
                        <td>{$info.max}</td>
                        <td><strong>{$info.avg}</strong></td>
                        <td>{$info.ofertas}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

            <div class="text-right" style="font-size:12px; color:#999;">
                Actualizado: {$binance_time}
            </div>

        {else}
            <div class="alert alert-warning text-center">
                No se pudo obtener información de Binance.
            </div>
        {/if}

    </div>
</div>