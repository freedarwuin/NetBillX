<div class="row">
    {if in_array($_admin['user_type'],['SuperAdmin','Admin', 'Report'])}
    <div class="col-sm-6 col-md-3 mb-3">
        <div class="small-box" style="background: linear-gradient(135deg,#1E90FF,#63B8FF); border-radius: 12px; color:#fff;">
            <div class="inner text-center">
                <h4 style="font-size:1.5rem; font-weight:700;">
                    <sup style="font-size:0.7rem;">{$_c['currency_code']}</sup>
                    {number_format($iday,0,$_c['dec_point'],$_c['thousands_sep'])}
                </h4>
                <p>{Lang::T('Income Today')}</p>
            </div>
            <div class="icon" style="top:10px; right:10px; font-size:3rem; opacity:0.2;">
                <i class="ion ion-cash"></i>
            </div>
            <a href="{Text::url('reports/by-date')}" class="small-box-footer" style="color:#fff; text-decoration:none;">
                {Lang::T('More info')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-md-3 mb-3">
        <div class="small-box" style="background: linear-gradient(135deg,#28a745,#71d88b); border-radius: 12px; color:#fff;">
            <div class="inner text-center">
                <h4 style="font-size:1.5rem; font-weight:700;">
                    <sup style="font-size:0.7rem;">{$_c['currency_code']}</sup>
                    {number_format($imonth,0,$_c['dec_point'],$_c['thousands_sep'])}
                </h4>
                <p>{Lang::T('Income This Month')}</p>
            </div>
            <div class="icon" style="top:10px; right:10px; font-size:3rem; opacity:0.2;">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{Text::url('reports/by-period')}" class="small-box-footer" style="color:#fff; text-decoration:none;">
                {Lang::T('More info')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    {/if}

    <div class="col-sm-6 col-md-3 mb-3">
        <div class="small-box" style="background: linear-gradient(135deg,#FFC107,#FFD54F); border-radius: 12px; color:#333;">
            <div class="inner text-center">
                <h4 style="font-size:1.5rem; font-weight:700;">
                    {$u_act}/{$u_all-$u_act}
                </h4>
                <p>{Lang::T('Active')}/{Lang::T('Expired')}</p>
            </div>
            <div class="icon" style="top:10px; right:10px; font-size:3rem; opacity:0.2;">
                <i class="ion ion-checkmark-circled"></i>
            </div>
            <a href="{Text::url('plan/list')}" class="small-box-footer" style="color:#333; text-decoration:none;">
                {Lang::T('More info')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-md-3 mb-3">
        <div class="small-box" style="background: linear-gradient(135deg,#dc3545,#f28b8b); border-radius: 12px; color:#fff;">
            <div class="inner text-center">
                <h4 style="font-size:1.5rem; font-weight:700;">{$c_all}</h4>
                <p>{Lang::T('Total number of registered customers')}</p>
            </div>
            <div class="icon" style="top:10px; right:10px; font-size:3rem; opacity:0.2;">
                <i class="ion ion-ios-people"></i>
            </div>
            <a href="{Text::url('customers/list')}" class="small-box-footer" style="color:#fff; text-decoration:none;">
                {Lang::T('More info')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>