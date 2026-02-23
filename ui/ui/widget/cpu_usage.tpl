<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">
        💻 Uso de CPU Actual: {$cpu_usage}%
    </div>

    <div class="panel-body">

        {if $cpu_history|@count > 0}

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Uso CPU (%)</th>
                        <th>Variación</th>
                    </tr>
                </thead>
                <tbody>

                    {foreach $cpu_history as $row}
                        <tr>
                            <td>{$row.time}</td>

                            <td>
                                {if $row.cpu >= 85}
                                    <span style="color:#ef4444;font-weight:bold;">
                                        {$row.cpu}%
                                    </span>
                                {elseif $row.cpu >= 60}
                                    <span style="color:#f59e0b;font-weight:bold;">
                                        {$row.cpu}%
                                    </span>
                                {else}
                                    <span style="color:#22c55e;font-weight:bold;">
                                        {$row.cpu}%
                                    </span>
                                {/if}
                            </td>

                            <td>
                                {if $row.change == 'up'}
                                    <span style="color:#ef4444;">▲ Subió</span>
                                {elseif $row.change == 'down'}
                                    <span style="color:#22c55e;">▼ Bajó</span>
                                {elseif $row.change == 'same'}
                                    <span style="color:#6b7280;">● Igual</span>
                                {else}
                                    -
                                {/if}
                            </td>
                        </tr>
                    {/foreach}

                </tbody>
            </table>

        {else}
            <div class="alert alert-warning">
                No hay datos de CPU disponibles.
            </div>
        {/if}

    </div>
</div>