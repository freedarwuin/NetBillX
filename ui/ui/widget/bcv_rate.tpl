<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">💱 Tasa BCV</div>
    <div class="panel-body">

        {if $bcv_rate}

            <div class="alert alert-info text-center" style="font-size:18px; font-weight:bold;">
                💱 Tasa BCV del día: {$bcv_rate} Bs/USD
            </div>

            {if $bcv_history|@count > 0}
                <div class="row">
                    {foreach $bcv_history as $day name=loop}

                        <div class="col-md-4 mb-3">

                            <div style="
                                border:1px solid #e6e6e6;
                                border-radius:8px;
                                box-shadow:0 2px 6px rgba(0,0,0,0.05);
                                padding:12px;
                                background:#fff;
                            ">

                                <div style="
                                    font-weight:bold;
                                    font-size:13px;
                                    color:#777;
                                    margin-bottom:8px;
                                ">
                                    {$day.rate_date|date_format:"%d/%m/%Y"}
                                </div>

                                <div style="margin-bottom:6px;">

                                    <span style="
                                        font-size:18px;
                                        {if $day.change == 'up'}
                                            color:#007bff;
                                            font-weight:bold;
                                        {elseif $day.change == 'down'}
                                            color:#d9534f;
                                            font-weight:bold;
                                        {/if}
                                    ">
                                        {$day.rate} Bs/USD
                                    </span>

                                </div>

                                <div>
                                    {if $day.change == 'up'}
                                        <span class="label label-primary">⬆ Subió</span>
                                    {elseif $day.change == 'down'}
                                        <span class="label label-danger">⬇ Bajó</span>
                                    {elseif $day.change == 'same'}
                                        <span class="label label-default">— Igual</span>
                                    {else}
                                        <span class="label label-default">—</span>
                                    {/if}
                                </div>

                            </div>

                        </div>

                        {if ($smarty.foreach.loop.iteration % 3) == 0}
                            <div class="col-md-12">
                                <hr style="margin:18px 0; border-top:1px solid #eee;">
                            </div>
                        {/if}

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