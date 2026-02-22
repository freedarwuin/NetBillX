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

                    {* ===== CALCULAR FECHAS ===== *}
                    {assign var="exp_time" value=strtotime($expired.expiration|cat:" "|cat:$expired.time)}
                    {assign var="start_time" value=strtotime($expired.recharged_on|cat:" "|cat:$expired.recharged_time)}
                    {assign var="now_time" value=time()}

                    {* ===== CALCULAR PROGRESO SEGURO ===== *}
                    {assign var="duration" value=$exp_time-$start_time}
                    {assign var="elapsed" value=$now_time-$start_time}

                    {if $duration <= 0}
                        {assign var="progress" value=100}
                    {else}
                        {assign var="progress" value=round(($elapsed/$duration)*100)}

                        {if $progress < 0}
                            {assign var="progress" value=0}
                        {/if}

                        {if $progress > 100}
                            {assign var="progress" value=100}
                        {/if}
                    {/if}

                    {* ===== COLOR DINÁMICO ISP ===== *}
                    {if $exp_time < $now_time}
                        {assign var="bar_class" value="bg-danger"}
                        {assign var="progress" value=100}
                        {assign var="row_class" value="table-danger"}
                    {elseif $progress >= 80}
                        {assign var="bar_class" value="bg-warning"}
                        {assign var="row_class" value="table-warning"}
                    {elseif $progress >= 50}
                        {assign var="bar_class" value="bg-info"}
                        {assign var="row_class" value="table-info"}
                    {else}
                        {assign var="bar_class" value="bg-success"}
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
                            <small data-toggle="tooltip"
                                   title="{Lang::dateAndTimeFormat($expired.recharged_on,$expired.recharged_time)}">
                                {Lang::timeElapsed($expired.recharged_on|cat:" "|cat:$expired.recharged_time)}
                            </small>
                            /
                            <span data-toggle="tooltip"
                                  title="{Lang::dateAndTimeFormat($expired.expiration,$expired.time)}">
                                {Lang::timeElapsed($expired.expiration|cat:" "|cat:$expired.time)}
                            </span>
                        </td>

                        <td>
                            {if $expired.namebp == 'Basic'}
                                <i class="fa fa-wifi text-primary"></i> {$expired.namebp}
                            {elseif $expired.namebp == 'Premium'}
                                <i class="fa fa-rocket text-success"></i> {$expired.namebp}
                            {else}
                                <i class="fa fa-network-wired text-secondary"></i> {$expired.namebp}
                            {/if}
                        </td>

                        <td>{$expired.routers}</td>

                        <td>
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
                                <div class="progress-bar {$bar_class}"
                                     role="progressbar"
                                     style="width: {$progress}%"
                                     aria-valuenow="{$progress}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"
                                     data-toggle="tooltip"
                                     title="{$progress}% elapsed">
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

<script>
function changeExpiredDefault(fl) {
    setCookie('expdef', fl.value, 365);
    setTimeout(function(){ location.reload(); }, 500);
}
</script>