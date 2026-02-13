{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        <div class="panel panel-primary">
            <div class="panel-body" style="font-size:13px;">

                <!-- ENCABEZADO -->
                <div class="text-center">
                    <h3><strong>FACTURA</strong></h3>
                </div>

                <table width="100%">
                    <tr>
                        <td>
                            <strong>{$empresa.nombre}</strong><br>
                            RIF: {$empresa.rif}<br>
                            Dirección Fiscal: {$empresa.direccion}<br>
                            Teléfono: {$empresa.telefono}
                        </td>
                        <td align="right">
                            <strong>Factura N°:</strong> {$factura.numero}<br>
                            <strong>N° Control:</strong> {$factura.control}<br>
                            <strong>Fecha:</strong> {$factura.fecha}
                        </td>
                    </tr>
                </table>

                <hr>

                <!-- DATOS DEL CLIENTE -->
                <table width="100%">
                    <tr>
                        <td>
                            <strong>Cliente:</strong> {$cliente.nombre}<br>
                            <strong>RIF / C.I.:</strong> {$cliente.rif}<br>
                            <strong>Dirección:</strong> {$cliente.direccion}
                        </td>
                    </tr>
                </table>

                <br>

                <!-- DETALLE -->
                <table class="table table-bordered" width="100%" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th width="10%">Cant.</th>
                            <th width="20%">Precio Unit.</th>
                            <th width="20%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $items as $item}
                        <tr>
                            <td>{$item.descripcion}</td>
                            <td align="center">{$item.cantidad}</td>
                            <td align="right">{$item.precio|number_format:2:",":"."}</td>
                            <td align="right">{$item.total|number_format:2:",":"."}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>

                <!-- TOTALES -->
                <table width="100%">
                    <tr>
                        <td align="right">
                            Base Imponible: Bs. {$factura.base|number_format:2:",":"."}<br>
                            IVA ({$factura.iva_porcentaje}%): Bs. {$factura.iva|number_format:2:",":"."}<br>
                            <strong>Total a Pagar: Bs. {$factura.total|number_format:2:",":"."}</strong>
                        </td>
                    </tr>
                </table>

                <hr>

                <!-- NOTA LEGAL -->
                <div style="font-size:11px;">
                    Documento emitido conforme a la normativa fiscal vigente del SENIAT.
                    Conserve esta factura para efectos fiscales.
                </div>

            </div>
        </div>

        <!-- BOTONES -->
        <div class="text-center">
            <a href="{Text::url('')}plan/list" class="btn btn-default btn-sm">
                <i class="ion-reply-all"></i> Finalizar
            </a>

            <a href="{Text::url('')}plan/print/{$factura.id}" target="_blank"
               class="btn btn-info btn-sm">
                <i class="glyphicon glyphicon-print"></i> Imprimir
            </a>

            <a href="https://api.whatsapp.com/send/?text={$whatsapp}"
               target="_blank" class="btn btn-success btn-sm">
                <i class="glyphicon glyphicon-share"></i> WhatsApp
            </a>
        </div>

    </div>
</div>

{include file="sections/footer.tpl"}
