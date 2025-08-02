{include file="sections/header.tpl"}

{* Mostrar tasa BCV y calculadora solo si timezone es America/Caracas y hay tasa disponible *}
{if $timezone|default:'' == "America/Caracas" && $bcv_rate|default:false}
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info text-center" style="font-size:18px; font-weight:bold;">

            💱 Tasa BCV del día: <span id="bcvRate">{$bcv_rate}</span> Bs/USD

            <br><br>

            <div class="input-group justify-content-center" style="max-width: 400px; margin: auto;">
                <input id="usdAmount" type="number" min="0" step="any" class="form-control" placeholder="Ingrese monto en USD" aria-label="USD amount" />
                <span class="input-group-text">USD × {$bcv_rate} =</span>
                <input id="bsAmount" type="text" class="form-control" readonly placeholder="Resultado en Bs" aria-label="Resultado en Bs" />
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const usdInput = document.getElementById('usdAmount');
        const bsOutput = document.getElementById('bsAmount');
        const bcvRate = parseFloat(document.getElementById('bcvRate').textContent);

        usdInput.addEventListener('input', function() {
            let usd = parseFloat(usdInput.value);
            if (isNaN(usd) || usd < 0) {
                bsOutput.value = '';
                return;
            }
            let bs = usd * bcvRate;
            bsOutput.value = bs.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    });
</script>
{/if}

{function showWidget pos=0}
    {foreach $widgets as $w}
        {if $w['position'] == $pos}
            {$w['content']}
        {/if}
    {/foreach}
{/function}

{assign dtipe value="dashboard_`$tipeUser`"}

{assign rows explode(".", $_c[$dtipe])}
{assign pos 1}
{foreach $rows as $cols}
    {if $cols == 12}
        <div class="row">
            <div class="col-md-12">
                {showWidget widgets=$widgets pos=$pos}
            </div>
        </div>
        {assign pos value=$pos+1}
    {else}
        {assign colss explode(",", $cols)}
        <div class="row">
            {foreach $colss as $c}
                <div class="col-md-{$c}">
                    {showWidget widgets=$widgets pos=$pos}
                </div>
                {assign pos value=$pos+1}
            {/foreach}
        </div>
    {/if}
{/foreach}

{if $_c['new_version_notify'] != 'disable'}
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            $.getJSON("./version.json?" + Math.random(), function(data) {
                var localVersion = data.version;
                $('#version').html('Version: ' + localVersion);
                $.getJSON(
                    "https://raw.githubusercontent.com/freedarwuin/NetBillX/master/version.json?" +
                    Math.random(),
                    function(data) {
                        var latestVersion = data.version;
                        if (localVersion !== latestVersion) {
                            $('#version').html('Latest Version: ' + latestVersion);
                            if (getCookie(latestVersion) != 'done') {
                                Swal.fire({
                                    icon: 'info',
                                    title: "New Version Available\nVersion: " + latestVersion,
                                    toast: true,
                                    position: 'bottom-right',
                                    showConfirmButton: true,
                                    showCloseButton: true,
                                    timer: 30000,
                                    confirmButtonText: '<a href="{Text::url('community')}#latestVersion" style="color: white;">Update Now</a>',
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                });
                                setCookie(latestVersion, 'done', 7);
                            }
                        }
                    });
            });
        });
    </script>
{/if}

{include file="sections/footer.tpl"}
