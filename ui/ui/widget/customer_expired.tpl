<div class="panel panel-warning mb-20 panel-hovered project-stats table-responsive">
    <div class="panel-heading">{Lang::T('Accounts Expired Today')}</div>

    <div class="table-responsive">
        <table class="table table-sm table-hover table-bordered">
            <thead>
                <tr>
                    <th>
                        <select class="form-control form-control-sm" onchange="changeExpiredDefault(this)">
                            <option value="username" {if $cookie.expdef == 'username'}selected{/if}>{Lang::T('Username')}</option>
                            <option value="fullname" {if $cookie.expdef == 'fullname'}selected{/if}>{Lang::T('Full Name')}</option>
                            <option value="phone" {if $cookie.expdef == 'phone'}selected{/if}>{Lang::T('Phone')}</option>
                            <option value="email" {if $cookie.expdef == 'email'}selected{/if}>{Lang::T('Email')}</option>
                        </select>
                    </th>
                    <th>{Lang::T('Activated / Expired')}</th>
                    <th>{Lang::T('Internet Package')}</th>
                    <th>{Lang::T('Server')}</th>
                    <th>{Lang::T('Status')}</th>
                    <th>{Lang::T('Expiry Progress')}</th>
                </tr>
            </thead>

            <tbody>
                {foreach $expire as $expired}

                    {assign var="exp_time" value=strtotime($expired.expiration|cat:" "|cat:$expired.time)}
                    {assign var="start_time" value=strtotime($expired.recharged_on|cat:" "|cat:$expired.recharged_time)}
                    {assign var="now_time" value=time()}

                    {assign var="duration" value=$exp_time-$start_time}
                    {assign var="elapsed" value=$now_time-$start_time}

                    {if $duration <= 0}
                        {assign var="progress" value=100}
                    {else}
                        {assign var="progress" value=round(($elapsed/$duration)*100)}
                        {if $progress < 0}{assign var="progress" value=0}{/if}
                        {if $progress > 100}{assign var="progress" value=100}{/if}
                    {/if}

                    {if $exp_time < $now_time}
                        {assign var="row_class" value="table-danger"}
                    {elseif $progress >= 80}
                        {assign var="row_class" value="table-warning"}
                    {elseif $progress >= 50}
                        {assign var="row_class" value="table-info"}
                    {else}
                        {assign var="row_class" value=""}
                    {/if}

                    <tr class="{$row_class}">

                        <td>
                            <a href="{Text::url('customers/view/',$expired.id)}">
                                {if $cookie.expdef == 'fullname'}
                                    {$expired.fullname}
                                {elseif $cookie.expdef == 'phone'}
                                    {$expired.phonenumber}
                                {elseif $cookie.expdef == 'email'}
                                    {$expired.email}
                                {else}
                                    {$expired.username}
                                {/if}
                            </a>
                        </td>

                        <td>
                            <small class="live-start"
                                   data-start="{$start_time}"
                                   title="{Lang::dateAndTimeFormat($expired.recharged_on,$expired.recharged_time)}">
                                   {Lang::timeElapsed($expired.recharged_on|cat:" "|cat:$expired.recharged_time)}
                            </small>
                            /
                            <span class="live-exp"
                                  data-exp="{$exp_time}"
                                  title="{Lang::dateAndTimeFormat($expired.expiration,$expired.time)}">
                                  {Lang::timeElapsed($expired.expiration|cat:" "|cat:$expired.time)}
                            </span>
                        </td>

                        <td>{$expired.namebp}</td>
                        <td>{$expired.routers}</td>

                        <td class="status-cell" data-exp="{$exp_time}">
                            {if $exp_time < $now_time}
                                ❌ {Lang::T('Expired')}
                            {elseif $progress >= 80}
                                ⚠️ {Lang::T('Expiring Soon')}
                            {else}
                                ✅ {Lang::T('Active')}
                            {/if}
                        </td>

                        <td>
                            <div class="progress" style="height:18px;">
                                <div class="progress-bar live-progress"
                                     data-start="{$start_time}"
                                     data-exp="{$exp_time}"
                                     style="width: {$progress}%">
                                     {$progress}%
                                </div>
                            </div>
                        </td>

                    </tr>

                {/foreach}
            </tbody>
        </table>
    </div>

    &nbsp; {include file="pagination.tpl"}
</div>

<style>
.live-progress {
    transition: width 0.5s linear, background-color 0.5s linear;
}
</style>

<script>
// Traducciones del sistema (Smarty las reemplaza)
var langAgo = "{Lang::T('ago')}";
var langIn  = "{Lang::T('in')}";

function formatTime(seconds) {

    var d = Math.floor(seconds / 86400);
    var h = Math.floor((seconds % 86400) / 3600);
    var m = Math.floor((seconds % 3600) / 60);
    var s = seconds % 60;

    var result = "";
    if (d > 0) result += d + "d ";
    if (h > 0) result += h + "h ";
    if (m > 0) result += m + "m ";
    if (d === 0 && h === 0) result += s + "s";

    return result.trim();
}

function updateDashboard() {

    var now = Math.floor(Date.now() / 1000);

    // Actualizar barras
    document.querySelectorAll('.live-progress').forEach(function(bar){

        var start = parseInt(bar.dataset.start);
        var exp   = parseInt(bar.dataset.exp);

        var duration = exp - start;
        var elapsed  = now - start;

        var percent = duration > 0 ? Math.round((elapsed / duration) * 100) : 100;

        if (percent < 0) percent = 0;
        if (percent > 100) percent = 100;

        bar.style.width = percent + "%";
        bar.textContent = percent + "%";

        bar.classList.remove('bg-success','bg-info','bg-warning','bg-danger');

        if (percent < 50) bar.classList.add('bg-success');
        else if (percent < 80) bar.classList.add('bg-info');
        else if (percent < 100) bar.classList.add('bg-warning');
        else bar.classList.add('bg-danger');
    });

    // Actualizar estado
    document.querySelectorAll('.status-cell').forEach(function(cell){
        var exp = parseInt(cell.dataset.exp);
        if (now >= exp) {
            cell.innerHTML = "❌ " + "{Lang::T('Expired')}";
        }
    });

    // Activated (count up)
    document.querySelectorAll('.live-start').forEach(function(el){
        var start = parseInt(el.dataset.start);
        var diff = now - start;
        if (diff >= 0) {
            el.textContent = formatTime(diff) + " " + langAgo;
        }
    });

    // Expired (count down / up)
    document.querySelectorAll('.live-exp').forEach(function(el){
        var exp = parseInt(el.dataset.exp);
        var diff = exp - now;

        if (diff > 0) {
            el.textContent = langIn + " " + formatTime(diff);
        } else {
            el.textContent = formatTime(Math.abs(diff)) + " " + langAgo;
        }
    });
}

// 🔁 Actualiza cada segundo
setInterval(updateDashboard, 1000);
updateDashboard();

function changeExpiredDefault(fl) {
    setCookie('expdef', fl.value, 365);
    setTimeout(function(){ location.reload(); }, 500);
}
</script>