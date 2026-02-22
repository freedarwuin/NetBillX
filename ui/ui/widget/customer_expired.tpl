<div class="panel panel-warning mb-20 panel-hovered project-stats table-responsive">
    <div class="panel-heading">{Lang::T('Accounts Expired Today')}</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>
                        <select class="form-control form-control-sm" onchange="changeExpiredDefault(this)">
                            <option value="username" {if $cookie['expdef'] == 'username'}selected{/if}>{Lang::T('Username')}</option>
                            <option value="fullname" {if $cookie['expdef'] == 'fullname'}selected{/if}>{Lang::T('Full Name')}</option>
                            <option value="phone" {if $cookie['expdef'] == 'phone'}selected{/if}>{Lang::T('Phone')}</option>
                            <option value="email" {if $cookie['expdef'] == 'email'}selected{/if}>{Lang::T('Email')}</option>
                        </select>
                    </th>
                    <th>{Lang::T('Activated / Expired')}</th>
                    <th>{Lang::T('Internet Package')}</th>
                    <th>{Lang::T('Location / Router')}</th>
                    <th>{Lang::T('Status')}</th>
                    <th>{Lang::T('Expiry Progress')}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $expire as $expired}
                    {assign var="rem_exp" value="{$expired['expiration']} {$expired['time']}"}
                    {assign var="rem_started" value="{$expired['recharged_on']} {$expired['recharged_time']}"}
                    {assign var="exp_time" value=strtotime($expired['expiration'].' '.$expired['time'])}
                    {assign var="start_time" value=strtotime($expired['recharged_on'].' '.$expired['recharged_time'])}
                    {assign var="now_time" value=time()}
                    {assign var="progress" value=round((($now_time-$start_time)/($exp_time-$start_time))*100)}
                    <tr class="{if $exp_time < $now_time}table-danger{elseif $exp_time - $now_time <= 86400}table-info{/if}">
                        <td><a href="{Text::url('customers/view/',$expired['id'])}">
                            {if $cookie['expdef'] == 'fullname'}
                                {$expired['fullname']}
                            {elseif $cookie['expdef'] == 'phone'}
                                {$expired['phonenumber']}
                            {elseif $cookie['expdef'] == 'email'}
                                {$expired['email']}
                            {else}
                                {$expired['username']}
                            {/if}
                        </a></td>
                        <td>
                            <small data-toggle="tooltip" data-placement="top"
                                title="{Lang::dateAndTimeFormat($expired['recharged_on'],$expired['recharged_time'])}">
                                {Lang::timeElapsed($rem_started)}
                            </small>
                            /
                            <span data-toggle="tooltip" data-placement="top"
                                title="{Lang::dateAndTimeFormat($expired['expiration'],$expired['time'])}">
                                {Lang::timeElapsed($rem_exp)}
                            </span>
                        </td>
                        <td>
                            {if $expired['namebp'] == 'Basic'}
                                <i class="fa fa-wifi text-primary"></i> {$expired['namebp']}
                            {elseif $expired['namebp'] == 'Premium'}
                                <i class="fa fa-rocket text-success"></i> {$expired['namebp']}
                            {else}
                                <i class="fa fa-network-wired text-warning"></i> {$expired['namebp']}
                            {/if}
                        </td>
                        <td>{$expired['routers']}</td>
                        <td>
                            {if $exp_time < $now_time}
                                ❌ Expired
                            {elseif $exp_time - $now_time <= 86400}
                                ⚠️ Expires Soon
                            {else}
                                ✅ Active
                            {/if}
                        </td>
                        <td>
                            <div class="progress" style="height: 15px;">
                                <div class="progress-bar {if $exp_time < $now_time}bg-danger{elseif $exp_time - $now_time <= 86400}bg-info{else}bg-success{/if}"
                                     role="progressbar"
                                     style="width: {max(0,min($progress,100))}%"
                                     aria-valuenow="{max(0,min($progress,100))}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"
                                     data-toggle="tooltip"
                                     data-placement="top"
                                     title="{$progress}% elapsed">
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
        setTimeout(() => {
            location.reload();
        }, 500);
    }
</script>