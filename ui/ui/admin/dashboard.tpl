{include file="sections/header.tpl"}

{* =========================================================
   Función para mostrar widgets según posición
   ========================================================= *}
{function showWidget pos=0}
    {foreach $widgets as $w}
        {if $w['position'] == $pos}
            {$w['content']}
        {/if}
    {/foreach}
{/function}

{* =========================================================
   Mostrar tasa BCV solo para Venezuela (America/Caracas)
   ========================================================= *}
{if $timezone|default:'' == "America/Caracas" && $bcv_rate|default:false}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info text-center" style="font-size:18px;font-weight:bold;">
                💱 Tasa BCV del día: {$bcv_rate} Bs/USD
            </div>
        </div>
    </div>
{/if}

{* =========================================================
   Render dinámico del dashboard
   ========================================================= *}
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

{* =========================================================
   Notificación de nueva versión
   ========================================================= *}
{if $_c['new_version_notify'] != 'disable'}
<script>
document.addEventListener('DOMContentLoaded', function () {

    function formatChannel(channel) {
        return (channel || 'official').toUpperCase();
    }

    function versionLabel(version, channel) {
        return 'Versión: ' + version + ' (' + formatChannel(channel) + ')';
    }

    function setVersionText(text) {
        $('#version').text(text);
    }

    function checkForUpdates(localData) {

        if (!localData.version) return;

        var localVersion = localData.version;
        var localChannel = localData.channel || 'official';

        setVersionText(versionLabel(localVersion, localChannel));

        $.getJSON(
            'https://raw.githubusercontent.com/freedarwuin/NetBillX/master/version.json?' + Math.random()
        ).done(function (remoteData) {

            if (!remoteData.version) return;

            var latestVersion = remoteData.version;
            var latestChannel = remoteData.channel || 'official';

            if (localVersion === latestVersion) return;

            setVersionText(
                'Actual disponible: ' +
                versionLabel(latestVersion, latestChannel)
            );

            var cookieName = 'nbx_version_' + latestVersion.replace(/\./g, '_');

            if (getCookie(cookieName) === 'done') return;

            Swal.fire({
                icon: 'info',
                title: 'Nueva versión disponible',
                html:
                    '<b>Instalada:</b> ' + localVersion + ' (' + formatChannel(localChannel) + ')<br>' +
                    '<b>Disponible:</b> ' + latestVersion + ' (' + formatChannel(latestChannel) + ')',
                toast: true,
                position: 'bottom-right',
                showConfirmButton: true,
                showCloseButton: true,
                timer: 30000,
                timerProgressBar: true,
                confirmButtonText: 'Actualizar ahora',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('./update.php', '_blank');
                }
            });

            setCookie(cookieName, 'done', 7);
        });
    }

    $.getJSON('./version.json?' + Math.random())
        .done(checkForUpdates);

});
</script>
{/if}

{include file="sections/footer.tpl"}
