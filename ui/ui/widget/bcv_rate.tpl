<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">💱 Tasa BCV</div>
    <div class="panel-body">

        {* Mostrar tasa actual solo si timezone es America/Caracas *}
        {if $timezone|default:'' == "America/Caracas" && $bcv_rate|default:false}
            <div class="alert alert-info text-center" style="font-size:18px; font-weight:bold;">
                💱 Tasa BCV del día: {$bcv_rate} Bs/USD
            </div>

            {if $bcv_message|default:false}
                <div class="text-center small text-muted mb-3">
                    {$bcv_message}
                </div>
            {/if}

            {* Historial de los últimos 7 días en tarjetas *}
            <div class="row">
                {foreach $bcv_history as $day}
                    <div class="col-md-4 mb-2">
                        <div class="card border-info h-100 text-center">
                            <div class="card-header">
                                {$day['rate_date']}
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{$day['rate']} Bs/USD</h5>
                                {* Comparación con la tasa actual *}
                                {if $bcv_rate && $day['rate'] > $bcv_rate}
                                    <span class="badge bg-danger">⬆ Subió</span>
                                {elseif $bcv_rate && $day['rate'] < $bcv_rate}
                                    <span class="badge bg-success">⬇ Bajó</span>
                                {else}
                                    <span class="badge bg-secondary">— Igual</span>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        {else}
            <div class="text-center text-muted small">
                La tasa BCV no está disponible para tu zona horaria.
            </div>
        {/if}

    </div>
</div>