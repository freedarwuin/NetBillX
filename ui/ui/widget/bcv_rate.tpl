<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">💱 Tasa BCV</div>
    <div class="panel-body">
        {* Mostrar tasa BCV solo si timezone es America/Caracas *}
        {if $timezone|default:'' == "America/Caracas" && $bcv_rate|default:false}
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info text-center" style="font-size:18px; font-weight:bold;">
                        💱 Tasa BCV del día: {$bcv_rate} Bs/USD
                    </div>
                    {if $bcv_message|default:false}
                        <div class="text-center small text-muted">
                            {$bcv_message}
                        </div>
                    {/if}
                </div>
            </div>
        {else}
            <div class="text-center text-muted small">
                La tasa BCV no está disponible para tu zona horaria.
            </div>
        {/if}
    </div>
</div>