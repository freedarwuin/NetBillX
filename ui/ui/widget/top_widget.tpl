<div class="row">

    {if in_array($_admin['user_type'],['SuperAdmin','Admin', 'Report'])}

    <!-- Income Today -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <p class="text-uppercase" style="font-size:12px;opacity:.8;margin-bottom:5px;">
                    {Lang::T('Income Today')}
                </p>
                <h3 class="text-bold" style="margin:0;">
                    <sup style="font-size:14px">{$_c['currency_code']}</sup>
                    {number_format($iday,0,$_c['dec_point'],$_c['thousands_sep'])}
                </h3>
            </div>
            <div class="icon" style="opacity:.3;">
                <i class="ion ion-cash"></i>
            </div>
            <a href="{Text::url('reports/by-date')}" class="small-box-footer">
                {Lang::T('View Details')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Income This Month -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <p class="text-uppercase" style="font-size:12px;opacity:.8;margin-bottom:5px;">
                    {Lang::T('Income This Month')}
                </p>
                <h3 class="text-bold" style="margin:0;">
                    <sup style="font-size:14px">{$_c['currency_code']}</sup>
                    {number_format($imonth,0,$_c['dec_point'],$_c['thousands_sep'])}
                </h3>
            </div>
            <div class="icon" style="opacity:.3;">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{Text::url('reports/by-period')}" class="small-box-footer">
                {Lang::T('View Report')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    {/if}

    <!-- Active / Expired -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <p class="text-uppercase" style="font-size:12px;opacity:.8;margin-bottom:5px;">
                    {Lang::T('Service Status')}
                </p>
                <h3 class="text-bold" style="margin:0;">
                    {$u_act}
                    <small style="font-size:14px;">/ {$u_all-$u_act}</small>
                </h3>
                <p style="font-size:12px;margin-top:5px;">
                    {Lang::T('Active')} / {Lang::T('Expired')}
                </p>
            </div>
            <div class="icon" style="opacity:.3;">
                <i class="ion ion-person-stalker"></i>
            </div>
            <a href="{Text::url('plan/list')}" class="small-box-footer">
                {Lang::T('Manage Plans')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Customers -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <p class="text-uppercase" style="font-size:12px;opacity:.8;margin-bottom:5px;">
                    {Lang::T('Total Customers')}
                </p>
                <h3 class="text-bold" style="margin:0;">
                    {$c_all}
                </h3>
            </div>
            <div class="icon" style="opacity:.3;">
                <i class="ion ion-android-people"></i>
            </div>
            <a href="{Text::url('customers/list')}" class="small-box-footer">
                {Lang::T('View Customers')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

</div>
