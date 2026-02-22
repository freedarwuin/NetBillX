<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">💱 Tasa BCV</div>
    <div class="panel-body">

        {if $bcv_rate}

            <div class="alert alert-info text-center" style="font-size:18px; font-weight:bold;">
                💱 Tasa BCV del día: {$bcv_rate} Bs/USD
            </div>

            {if $bcv_history|@count > 0}
                <div class="row">
                    {foreach $bcv_history as $day}
                        <div class="col-md-4 mb-2">
                            <div class="card border-info h-100 text-center">
                                <div class="card-header">
                                    {$day.rate_date|date_format:"%d/%m/%Y"}
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        {$day.rate} Bs/USD
                                    </h5>

                                    {if $day.change == 'up'}
                                        <span class="badge bg-primary">⬆ Subió</span>
                                    {elseif $day.change == 'down'}
                                        <span class="badge bg-danger">⬇ Bajó</span>
                                    {elseif $day.change == 'same'}
                                        <span class="badge bg-secondary">— Igual</span>
                                    {else}
                                        <span class="badge bg-secondary">—</span>
                                    {/if}

                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {/if}

        {else}
            <div class="text-center text-muted small">
                La tasa BCV aún no está disponible.
            </div>
        {/if}

    </div>
</div>