{include file="sections/header.tpl"}

{* Mostrar tasa BCV solo si timezone es America/Caracas y hay tasa disponible *}
{if $timezone|default:'' == "America/Caracas" && $bcv_rate|default:false}
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info text-center" style="font-size:18px; font-weight:bold;">
            💱 Tasa BCV del día:
            <a href="#" data-bs-toggle="modal" data-bs-target="#bcvCalculatorModal" style="text-decoration: underline; cursor: pointer;">
                <span id="bcvRate">{$bcv_rate}</span> Bs/USD
            </a>
        </div>
    </div>
</div>

{* Modal Bootstrap para la calculadora *}
<div class="modal fade" id="bcvCalculatorModal" tabindex="-1" aria-labelledby="bcvCalculatorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bcvCalculatorModalLabel">Calculadora BCV</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="usdAmount" class="form-label">Monto en USD</label>
          <input id="usdAmount" type="number" min="0" step="any" class="form-control" placeholder="Ingrese monto en USD" />
        </div>
        <div class="mb-3">
          <label for="bsAmount" class="form-label">Equivalente en Bs</label>
          <input id="bsAmount" type="text" class="form-control" readonly placeholder="Resultado en Bs" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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

    // Opcional: Limpiar campos al abrir modal
    const modal = document.getElementById('bcvCalculatorModal');
    modal.addEventListener('show.bs.modal', function () {
        usdInput.value = '';
        bsOutput.value = '';
        usdInput.focus();
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
