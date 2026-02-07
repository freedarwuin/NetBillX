<option value="">{Lang::T('Select Plans')}</option>
{foreach $d as $ds}
<option value="{$ds['id']}">
    {if $ds['enabled'] neq 1}DESACTIVADO PLAN &bull; {/if}
    {$ds['name_plan']} &bull;
    {Lang::moneyFormat($ds['price'])}
    {if $ds['prepaid'] neq 'yes'} &bull; POSTPAGO  {/if}
</option>
{/foreach}