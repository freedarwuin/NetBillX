<style>
.dashboard-row {
    margin-bottom: 20px;
    border-bottom: 1px solid #e0e0e0; /* separador entre filas */
    padding-bottom: 20px;
}
.small-box {
    border-radius: 12px;
    position: relative;
    display: block;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    color: #fff;
}
.small-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.small-box .inner {
    text-align: center;
    padding: 20px 10px;
}
.small-box h4 {
    font-size: 1.7rem;
    font-weight: 700;
    margin: 0;
}
.small-box p {
    font-size: 1rem;
    margin: 5px 0 0 0;
    font-weight: 500;
}
.small-box .icon {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 3rem;
    opacity: 0.2;
}
.small-box-footer {
    display: block;
    text-align: center;
    padding: 8px 0;
    background: rgba(255,255,255,0.1);
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    border-radius: 0 0 12px 12px;
}
.small-box-footer:hover {
    background: rgba(255,255,255,0.2);
}
</style>

<div class="row dashboard-row">
    {if in_array($_admin['user_type'],['SuperAdmin','Admin', 'Report'])}
    <div class="col-sm-6 col-md-3 mb-3">
        <div class="small-box" style="background: linear-gradient(135deg,#1E90FF,#63B8FF);">
            <div class="inner">
                <h4 class="count" data-count="{number_format($iday,0,$_c['dec_point'],$_c['thousands_sep'])}"><sup style="font-size:0.7rem;">{$_c['currency_code']}</sup>0</h4>
                <p>{Lang::T('Income Today')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-cash"></i>
            </div>
            <a href="{Text::url('reports/by-date')}" class="small-box-footer">
                {Lang::T('More info')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-md-3 mb-3">
        <div class="small-box" style="background: linear-gradient(135deg,#28a745,#71d88b);">
            <div class="inner">
                <h4 class="count" data-count="{number_format($imonth,0,$_c['dec_point'],$_c['thousands_sep'])}"><sup style="font-size:0.7rem;">{$_c['currency_code']}</sup>0</h4>
                <p>{Lang::T('Income This Month')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{Text::url('reports/by-period')}" class="small-box-footer">
                {Lang::T('More info')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    {/if}

    <div class="col-sm-6 col-md-3 mb-3">
        <div class="small-box" style="background: linear-gradient(135deg,#FFC107,#FFD54F); color:#333;">
            <div class="inner">
                <h4 class="count" data-count="{$u_act}/{$u_all-$u_act}">0</h4>
                <p>{Lang::T('Active')}/{Lang::T('Expired')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-checkmark-circled"></i>
            </div>
            <a href="{Text::url('plan/list')}" class="small-box-footer" style="color:#333;">
                {Lang::T('More info')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-sm-6 col-md-3 mb-3">
        <div class="small-box" style="background: linear-gradient(135deg,#dc3545,#f28b8b);">
            <div class="inner">
                <h4 class="count" data-count="{$c_all}">0</h4>
                <p>{Lang::T('Customers')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-ios-people"></i>
            </div>
            <a href="{Text::url('customers/list')}" class="small-box-footer">
                {Lang::T('More info')} <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
// Animación de conteo de números
document.addEventListener("DOMContentLoaded", function() {
    const counters = document.querySelectorAll('.count');
    counters.forEach(counter => {
        const target = counter.getAttribute('data-count').replace(/,/g,'');
        let count = 0;
        const duration = 1000;
        const increment = target / (duration / 20);

        const update = () => {
            count += increment;
            if(count >= target) count = target;
            counter.innerHTML = counter.innerHTML.includes('/')
                ? counter.getAttribute('data-count')
                : Math.floor(count).toLocaleString();
            if(count < target) requestAnimationFrame(update);
        }
        update();
    });
});
</script>