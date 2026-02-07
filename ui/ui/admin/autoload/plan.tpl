<option value="">{Lang::T('Select Plans')}</option>
{foreach $d as $ds}
<option value="{$ds['id']}">
    {if $ds['enabled'] neq 1}PLAN DEACTIVATED &bull; {/if}
    {$ds['name_plan']} &bull;
    {Lang::moneyFormat($ds['price'])}
    {if $ds['prepaid'] neq 'yes'} &bull; POSTPAID  {/if}
</option>
{/foreach}