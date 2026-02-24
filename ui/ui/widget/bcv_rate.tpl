<div class="panel panel-info panel-hovered mb20 activities">
    <div class="panel-heading">💱 Tasa BCV del día: {$bcv_rate} Bs/USD</div>
    <div class="panel-body">

        {if $bcv_rate}

            <div class="row">
                {foreach $bcv_history as $day}

                    <div class="col-md-4 mb-3">
                        <div style="
                            border:1px solid #e6e6e6;
                            border-radius:8px;
                            box-shadow:0 2px 6px rgba(0,0,0,0.05);
                            padding:12px;
                            background:#fff;
                            display:flex;
                            align-items:center;
                            justify-content: space-between;
                        ">

                            <div>
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
                                            color:#007bff; font-weight:bold;
                                        {elseif $day.change == 'down'}
                                            color:#d9534f; font-weight:bold;
                                        {elseif !$day.is_real}
                                            color:#999;
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
                                    {elseif !$day.is_real}
                                        <span class="label label-default">Día sin publicación</span>
                                    {else}
                                        <span class="label label-default">—</span>
                                    {/if}
                                </div>
                            </div>

                            <div style="margin-left:12px; text-align:center;">
                                <img src="system/uploads/banco-central-de-venezuela-logo-png_seeklogo-622560.png"
                                     alt="Logo BCV"
                                     style="max-width:60px; height:auto;">
                            </div>

                        </div>
                    </div>

                {/foreach}
            </div>

        {else}
            <div class="text-center text-muted small">
                La tasa BCV aún no está disponible.
            </div>
        {/if}

    </div>
</div>