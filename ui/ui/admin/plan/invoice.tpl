{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-6 col-sm-12 col-md-offset-3">
        <div class="panel panel-hovered panel-primary panel-stacked mb30">
            <div class="panel-heading text-center">
                <strong>FACTURA</strong>
            </div>

            <div class="panel-body">

                <form class="form-horizontal" method="post" action="{Text::url('')}plan/print" target="_blank">

                    <!-- CONTENIDO FACTURA -->
                    <pre id="content" style="border:0;text-align:left;background-color:white;font-family:monospace;padding:15px;"></pre>

                    <textarea class="hidden" id="formcontent" name="content">
==============================
            FACTURA
==============================

{$in['empresa_nombre']}
RIF: {$in['empresa_rif']}
Dirección: {$in['empresa_direccion']}
Teléfono: {$in['empresa_telefono']}

--------------------------------
Factura N°: {$in['invoice']}
N° Control: {$in['control_number']}
Fecha: {$in['fecha']}
--------------------------------

Cliente: {$in['cliente_nombre']}
RIF / C.I.: {$in['cliente_rif']}
Dirección: {$in['cliente_direccion']}

================================
DETALLE
================================
{$invoice}
--------------------------------

Base Imponible: Bs. {$in['base']}
IVA ({$in['iva_porcentaje']}%): Bs. {$in['iva']}

================================
TOTAL A PAGAR:
Bs. {$in['total']}
================================

Documento emitido conforme a la
normativa fiscal vigente.
Conserve para efectos fiscales.

</textarea>

                    <input type="hidden" name="id" value="{$in['id']}">

                    <!-- BOTONES ORIGINALES -->
                    <a href="{Text::url('')}plan/list" class="btn btn-default btn-sm">
                        <i class="ion-reply-all"></i>{Lang::T('Finish')}
                    </a>

                    <a href="https://api.whatsapp.com/send/?text={$whatsapp}" target="_blank"
                        class="btn btn-primary btn-sm">
                        <i class="glyphicon glyphicon-share"></i> WhatsApp
                    </a>

                    <a href="{Text::url('')}plan/view/{$in['id']}/send"
                        class="btn btn-info text-black btn-sm">
                        <i class="glyphicon glyphicon-envelope"></i> {Lang::T("Resend")}
                    </a>

                    <hr>

                    <a href="{Text::url('')}plan/print/{$in['id']}" target="_print"
                        class="btn btn-info text-black btn-sm">
                        <i class="glyphicon glyphicon-print"></i> {Lang::T('Print')} HTML
                    </a>

                    <button type="submit" class="btn btn-info text-black btn-sm">
                        <i class="glyphicon glyphicon-print"></i> {Lang::T('Print')} Text
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.getElementById('content').innerHTML =
        document.getElementById('formcontent').innerHTML;
</script>

{include file="sections/footer.tpl"}
