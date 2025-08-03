{* Mostrar tasa BCV solo si timezone es America/Caracas *}
    {if $timezone|default:'' == "America/Caracas" && $bcv_rate|default:false}
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info text-center" style="font-size:18px; font-weight:bold;">
                    💱 Tasa BCV del día: {$bcv_rate} Bs/USD
                </div>
            </div>
        </div>
    {/if}