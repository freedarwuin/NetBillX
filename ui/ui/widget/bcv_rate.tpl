<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">
        💱 Tasa BCV - Depuración
    </div>
    <div class="panel-body">

        {if isset($bcv_rate)}
            <p><strong>bcv_rate:</strong> {$bcv_rate}</p>
        {else}
            <p style="color:red;">bcv_rate NO está definido</p>
        {/if}

        {if isset($bcv_history) && $bcv_history|@count > 0}
            <p><strong>bcv_history ({$bcv_history|@count} registros):</strong></p>
            <ul>
                {foreach $bcv_history as $day}
                    <li>
                        Fecha: {$day.rate_date} — Tasa: {$day.rate} — Cambio: {$day.change}
                    </li>
                {/foreach}
            </ul>
        {else}
            <p style="color:red;">bcv_history NO tiene registros</p>
        {/if}

    </div>
</div>