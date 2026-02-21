<div class="row">

    {if in_array($_admin['user_type'],['SuperAdmin','Admin','Report'])}
        <div class="col-lg-2-4 col-md-4 col-sm-6 col-xs-12">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h4 class="text-bold">
                        <sup>{$_c['currency_code']}</sup>
                        {number_format($iday,0,$_c['dec_point'],$_c['thousands_sep'])}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-clock"></i>
                </div>
                <a href="{Text::url('reports/by-date')}" class="small-box-footer">
                    {Lang::T('Facturación hoy')}
                </a>
            </div>
        </div>

        <div class="col-lg-2-4 col-md-4 col-sm-6 col-xs-12">
            <div class="small-box bg-green">
                <div class="inner">
                    <h4 class="text-bold">
                        <sup>{$_c['currency_code']}</sup>
                        {number_format($imonth,0,$_c['dec_point'],$_c['thousands_sep'])}
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-android-calendar"></i>
                </div>
                <a href="{Text::url('reports/by-period')}" class="small-box-footer">
                    {Lang::T('Facturación mensual')}
                </a>
            </div>
        </div>
    {/if}

    <div class="col-lg-2-4 col-md-4 col-sm-6 col-xs-12">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h4 class="text-bold">
                    {$u_act}/{$u_all-$u_act}
                </h4>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="{Text::url('plan/list')}" class="small-box-footer">
                {Lang::T('Activo')}/{Lang::T('Venció')}
            </a>
        </div>
    </div>

    <div class="col-lg-2-4 col-md-4 col-sm-6 col-xs-12">
        <div class="small-box bg-red">
            <div class="inner">
                <h4 class="text-bold">{$c_all}</h4>
            </div>
            <div class="icon">
                <i class="ion ion-android-people"></i>
            </div>
            <a href="{Text::url('customers/list')}" class="small-box-footer">
                {Lang::T('Clientes')}
            </a>
        </div>
    </div>

    {* NUEVO CUADRO BCV *}
    {if $timezone|default:'' == "America/Caracas" && $bcv_rate|default:false}
        <div class="col-lg-2-4 col-md-4 col-sm-6 col-xs-12">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h4 class="text-bold">
                        {$bcv_rate} Bs/USD
                    </h4>
                </div>
                <div class="icon">
                    <i class="ion ion-social-usd"></i>
                </div>
                <div class="small-box-footer">
                    💱 Tasa BCV del día
                </div>
            </div>
        </div>
    {/if}

</div>