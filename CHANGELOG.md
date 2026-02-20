![NetBillX](install/img/logo.png)

# CHANGELOG

## 2024.10.23
- Recarga personalizada de saldo para administrador — solicitada por Javi Tech
- Solo el administrador puede editar clientes — solicitado por Fiberwan
- Solo el administrador puede mostrar la contraseña — solicitado por Fiberwan

## 2024.10.18
- El administrador puede configurar sesión única desde Ajustes
- Las transacciones no pagadas expiran automáticamente
- Tipo de registro
- Posibilidad de iniciar sesión como usuario desde la vista de cliente
- Permite seleccionar si el registro del cliente requiere OTP o no
- Se agrega Meta.php para información adicional

## 2024.10.15
- Seguridad CSRF
- El administrador solo puede tener una sesión activa
- Mover la configuración miscelánea a una nueva página
- Corregir estado de cliente en línea
- Contar usuarios compartidos en línea para Radius REST
- Corregir impresión de factura

## 2024.10.7
- Mostrar si el cliente está en línea o no
- Cambiar el tema de la factura para impresión
- Reorganizar la vista del cliente

## 2024.9.23
- Precio con descuento
- Preajuste de ráfaga (Burst Preset)

## 2024.9.20
- Olvidé mi contraseña
- Olvidé mi nombre de usuario
- Plantilla de encabezado público

## 2024.9.13
- Agregar venta de VPN MikroTik — por @agstrxyz
- Rediseño del tema — por @Focuslinkstech
- Corrección de varios errores y ajustes generales

## 2024.8.28
- Agregar estado del router (Offline/Online) — por @Focuslinkstech
- Mostrar router offline en el panel principal
- Corregir traducciones — por @ahmadhusein17
- Agregar página de información de pago para mostrar al cliente antes de comprar
- Plantilla de voucher
- Cambiar Niceedit por Summernote
- El cliente puede cambiar su idioma — por @Focuslinkstech
- Corregir sensibilidad a mayúsculas/minúsculas en vouchers
- Administrador de plugins con 3 pestañas

## 2024.8.19
- Nueva página “Información de Pago” para informar al cliente qué pasarela de pago es recomendable
- Mover la interfaz de cliente a la carpeta user-ui
- Plantilla de voucher
- Cambiar editor a Summernote
- El cliente puede cambiar idioma

## 2024.8.6
- Corregir escáner de código QR
- Simplificar verificación de contraseña CHAP
- Cuotas basadas en FreeRadius REST
- Corregir auditoría de pasarela de pago

## 2024.8.6
- Corregir nombre de usuario PPPoE del cliente

## 2024.8.5
- Agregar bandeja de entrada de correo para clientes
- Agregar cliente PPPoE e IP PPPoE para crear usuario e IP estáticos
- Agregar botón Sync
- Permitir dirección MAC como nombre de usuario
- Mapas de router

## 2024.8.1
- Mostrar plan de ancho de banda en el panel del cliente
- Agregar auditoría de pasarela de pago
- Corregir Plugin Manager

## 2024.7.23
- Agregar fecha de uso del voucher
- Página de reportes unificada en una sola
- Corregir fecha de inicio en el dashboard
- Corregir parámetros de instalación

## 2024.7.23
- Agregar información adicional de factura al cliente
- Agregar inicio de sesión solo con voucher, sin nombre de usuario
- Agregar información adicional de factura en comentarios de MikroTik
- Agregar URL dinámica de la aplicación para instalación
- Corregir clientes activos para vouchers

## 2024.7.15
- API Radius REST
- Documentación de inicio rápido
- Mostrar nueva actualización solo una vez

## 2024.6.21
- Agregar filtro de resultados en vouchers y planes de internet
- Agregar scripts on-login y on-logout
- Agregar IP local para PPPoE

## 2024.6.19
- Nuevo sistema de dispositivos compatible con equipos que no sean MikroTik mediante archivos personalizados
- Agregar IP local en el pool
- Corrección personalizada de fecha de expiración para postpago
- Clientes expirados pueden cambiar a otro plan de internet
- Instalador de plugins
- Actualizar caché del gestor de plugins
- Archivo Docker — por George Njeri (@Swagfin)

## 2024.5.21
- Agregar modo mantenimiento — por @freeispradius
- Agregar sistema de impuestos — por @freeispradius
- Exportar lista de clientes a CSV con filtros
- Corregir variables de Radius — por @freeispradius
- Agregar rollback de actualización

## 2024.5.17
- Estado del cliente: Activo / Bloqueado / Deshabilitado
- Agregar búsqueda con orden en la lista de clientes

## 2024.5.16
- Confirmar cambio de uso

## 2024.5.14
- Mostrar plan y ubicación en la lista de expirados
- Pago personalizable para recargas

## 2024.5.8
- Corregir errores de burst — por @Gerandonk
- Corregir sincronización de burst — por @Gerandonk

## 2024.5.7
- Corregir tiempo para períodos en días
- Corregir atributos FreeRadius — por @agstrxyz
- Agregar código de voucher numérico — por @pro-cms

## 2024.4.30
- ACTUALIZACIÓN CRÍTICA: la lógica de recarga no verificaba el estado activo/inactivo, provocando que clientes expirados permanecieran en el pool expirado
- Prevenir doble envío en recarga de saldo

## 2024.4.29
- Paginación en mapas
- Búsqueda en mapas
- Corregir lógica de extensión
- Corregir lógica de recarga para no eliminar datos cuando el cliente no cambia de plan

## 2024.4.23
- Corregir paginación de vouchers
- Corregir traducción de idioma
- Corregir confirmación al solicitar extensión
- Enviar notificación por Telegram cuando el cliente solicita extensión
- Exportar lista de usuarios prepago — por @freeispradius
- Corregir visualización de vouchers — por @agstrxyz

## 2024.4.21
- Restaurar cron antiguo

## 2024.4.15
- Clientes postpago pueden solicitar extensión de expiración si está habilitado
- Correcciones de código — por @ahmadhusein17 y @agstrxyz

## 2024.4.4
- DataTables para lista de clientes — por @Focuslinkstech
- Agregar facturas a recordatorios
- Prevenir doble envío en recarga y renovación

## 2024.4.3
- Exportar logs a CSV — por @agstrxyz
- Usar nombre de usuario si el código de país está vacío

## 2024.4.2
- Corregir API REST
- Corregir registro de IP con Cloudflare — por @Gerandonk
- Mostrar tipo de cliente Personal o Empresa en el dashboard

## 2024.3.26
- Cambiar paginador para facilitar personalización usando pagination.tpl

## 2024.3.25
- Corregir mapas en HTTP
- Corregir cancelación de pagos

## 2024.3.23
- Mapas a altura completa
- Mostrar “Obtener direcciones” en lugar de coordenadas
- Etiquetas de mapas siempre visibles

## 2024.3.22
- Corregir mensajes broadcast — por @Focuslinkstech
- Agregar selector de ubicación

## 2024.3.20
- Corrección de varios errores

## 2024.3.19
- Agregar tipo de cliente: Personal o Empresa — por @pro-cms
- Corregir mensajes broadcast — por @Focuslinkstech
- Agregar geolocalización del cliente — por @Focuslinkstech
- Cambiar menú de cliente

## 2024.3.18
- Agregar envío masivo de SMS — por @Focuslinkstech
- Corregir notificaciones con facturas

## 2024.3.16
- Corregir cobro en cero
- Desconectar cliente desde Radius sin bucle — por @Gerandonk

## 2024.3.15
- Corregir vista del cliente para listar plan activo
- Facturación adicional usando atributos del cliente

## 2024.3.14
- Agregar notas a facturas
- Agregar facturación adicional
- Ver facturas desde el panel del cliente

## 2024.3.13
- Sistema postpago
- Costos adicionales

## 2024.3.12
- Verificar período de validez para que el cálculo de precios no afecte otros períodos
- Agregar firewall con .htaccess (solo Apache)
- Múltiples pasarelas de pago — por @Focuslinkstech
- Corregir lógica de múltiples pasarelas de pago
- Corregir eliminación de atributos
- Permitir eliminar pasarelas de pago
- Permitir eliminar plugins

## 2024.3.6
- Cambiar vista de atributos

## 2024.3.4
- Agregar [[username]] a recordatorios
- Corregir visualización de agente al editar
- Corregir contraseña del administrador al enviar notificaciones
- Verificar existencia de archivos para páginas

## 2024.3.3
- Cambiar botón de carga — por @Focuslinkstech
- Agregar anuncios para clientes — por @Gerandonk
- Agregar validez por período PPPoE — por @Gerandonk

## 2024.2.29
- Corregir funcionalidad de hooks
- Cambiar menú de cliente

## 2024.2.28
- Corregir compra de plan con saldo
- Agregar fecha de expiración en recordatorios

## 2024.2.27
- Corregir rutas de notificaciones
- Redirigir al dashboard si ya está logueado

## 2024.2.26
- Limpiar JS y CSS no utilizados
- Agregar validaciones de autorización
- Ruta personalizada para carpetas
- Corregir varios errores

## 2024.2.23
- Integración con impresora NetBillX
- Corregir facturas
- Agregar ID de administrador en transacciones

## 2024.2.22
- Agregar carga al enviar formularios
- Enlace a ajustes cuando se oculta un widget

## 2024.2.21
- Corregir instalador SQL
- Eliminar espacios múltiples en idiomas
- Cambiar teléfono para requerir OTP — por @Focuslinkstech
- Cambiar formulario de burst
- Eliminar tabla responsive, congelar primera columna

## 2024.2.20
- Corregir lista de administradores
- Límite de Burst
- Mejorar carga — por @Focuslinkstech

## 2024.2.19
- Inicio del desarrollo de API
- Múltiples niveles de administrador
- Atributos de cliente — por @Focuslinkstech
- Menú Radius

## 2024.2.13
- Traducción automática de idiomas
- Cambio de estructura de idioma a JSON
- Guardar menú colapsado

## 2024.2.12
- Niveles de administrador: SuperAdmin, Admin, Reportes, Agente, Ventas
- Exportar clientes a CSV
- Sesiones usando cookies

## 2024.2.7
- Ocultar contenido del dashboard

## 2024.2.6
- Cachear gráficos para apertura más rápida

## 2024.2.5
- Actualización del dashboard de administrador:
    - Clientes registrados mensuales
    - Ventas mensuales totales
    - Usuarios activos

## 2024.2.2
- Corregir edición de plan para usuario

## 2024.1.24
- Agregar envío de prueba para SMS, WhatsApp y Telegram

## 2024.1.19
- Marketplace de plugins, temas y pasarelas de pago de pago (Codecanyon)
- Corregir lista del gestor de plugins

## 2024.1.18
- Corregir MikroTik: pool $poolId siempre vacío

## 2024.1.17
- Cambio menor: menú de plugins con notificaciones — por @Focuslinkstech

## 2024.1.16
- Agregar color amarillo a planes no permitidos para compra
- Corregir selección de pool Radius
- Agregar precio a notificaciones de recordatorio
- Soporte para impresora térmica en facturas

## 2024.1.15
- Corregir cron de planes solo para administrador — por @Focuslinkstech

## 2024.1.11
- Planes solo para administrador — por @Focuslinkstech
- Corregir Plugin Manager

## 2024.1.9
- Agregar prefijo al generar vouchers

## 2024.1.8
- Pedido de usuario expirado por fecha de expiración

## 2024.1.2
- Paginación de usuarios expirados — por @Focuslinkstech

## 2023.12.21
- AdminLTE moderno — por @sabtech254
- Actualizar user-dashboard.tpl — por @Focuslinkstech

## 2023.12.19
- Corregir búsqueda de clientes
- Deshabilitar registro: el cliente activa solo con voucher y el voucher es la contraseña
- Eliminar todos los vouchers usados

## 2023.12.18
- Dividir SMS a 160 caracteres solo para módem MikroTik

## 2023.12.14
- Envío de SMS usando MikroTik con módem instalado
- Agregar tipo de cliente: mostrar solo PPPOE, Hotspot o ambos

## 2023.11.17
- Detalles de error no visibles para el cliente

## 2023.11.15
- Paquetes multi-router para clientes
- Corregir edición de paquetes: el administrador puede cambiar cliente a otro router

## 2023.11.9
- Corregir variables en cron
- Corregir actualización de planes

## 2023.10.27
- Respaldo y restauración de base de datos
- Corregir verificación de clientes Radius

## 2023.10.25
- Corregir verificación de archivos en cron (error solo en instalaciones nuevas)

## 2023.10.24
- Corregir lógica de cronjob
- Asignar router a NAS (aún no utilizado)
- Corregir paginación
- Mover alertas fuera de hardcode

## 2023.10.20
- Ver factura
- Reenviar factura
- Voucher personalizado

## 2023.10.17
- ¡Feliz cumpleaños para mí 🎂!
- Soporte FreeRadius con MySQL
- Regreso del soporte de temas
- Visor de logs

## 2023.9.21
- El cliente puede extender su plan
- El cliente puede desactivar su plan activo
- Variable nux-router para seleccionar solo planes de ese router
- Mostrar hasta 30 usuarios expirados

## 2023.9.20
- Corregir encabezado de saldo del cliente
- Desactivar plan activo del cliente
- Sincronizar plan del cliente con MikroTik
- Recargar cliente desde detalles
- Agregar páginas de Política de Privacidad y Términos y Condiciones

## 2023.9.13
- Agregar saldo actual en notificaciones
- Comprar plan para un amigo
- Recargar plan de un amigo
- Corregir recarga de plan
- Mostrar plan activo en la lista de clientes
- Corregir contador de clientes en el dashboard
- Mostrar saldo del cliente en el encabezado
- Corregir Plugin Manager usando Http::Get
- Mostrar página de error cuando el sistema falla

## 2023.9.7
- Corregir eliminación de cliente PPPoE
- Eliminar cliente activo antes de borrar
- Mostrar IP y MAC aunque no sea Hotspot

## 2023.9.6
- Pool expirado: el cliente se mueve automáticamente tras expirar el plan
- Corregir eliminación de cliente
- Eliminar tabla tbl_language

## 2023.9.1.1
- Corregir eliminación de clientes por cron
- Corregir texto de recordatorio

## 2023.9.1
- Correcciones críticas: el tiempo de expiración se calculaba desde la expiración anterior
- El tiempo no se actualizaba al extender el plan
- Agregar botón Cancelar en dashboard cuando hay paquete no pagado
- Corregir nombre de usuario en dashboard

## 2023.8.30
- Subir logo desde ajustes
- Corregir valores de impresión
- Corregir tiempo al editar prepago

## 2023.8.28
- Extender expiración al comprar el mismo paquete
- Corregir calendario
- Agregar tiempo de recarga
- Corregir transferencia de saldo

## 2023.8.24
- Transferencia de saldo entre clientes
- Optimizar cronjob
- Ver información del cliente
- AJAX para selección de clientes

## 2023.8.18
- Corregir cron de renovación automática
- Agregar comentario al usuario en MikroTik

## 2023.8.16
- El administrador puede agregar saldo al cliente
- Mostrar saldo al usuario
- Usar Select2 para desplegables

## 2023.8.15
- Corregir eliminación de cliente PPPoE
- Corregir encabezado admin y cliente
- Corregir exportación PDF por período
- Agregar contraseña PPPoE editable solo por administrador
- Configuración de código de país
- Tabla Meta de clientes para atributos
- Corregir formulario de agregar/editar cliente
- Editor de mensajes de notificación
- Cron de recordatorios
- Sistema de saldo: el cliente puede depositar dinero
- Renovación automática usando saldo del cliente

## 2023.8.1
- Script de actualización con un clic
- Carpeta de UI personalizada
- Eliminar textos de depuración
- Corregir JS de proveedores

## 2023.7.28
- Corregir enlace de compra de voucher
- Agregar campo email al registro
- Cambiar diseño del formulario de registro
- Agregar ajuste para deshabilitar vouchers
- Corregir título de planes PPPoE
- Corregir caché de plugins

## 2023.6.20
- Ocultar hora en fecha de creación (compatibilidad con validez por minutos y horas)

## 2023.6.15
- El cliente puede conectarse a internet desde el dashboard
- Corregir confirmación al eliminar
- Cambiar logo de NetBillX
- Uso de Composer
- Corregir búsqueda de clientes
- Verificación de cliente, si no existe se cierra sesión
- Contraseña visible pero oculta
- Código de voucher oculto

## 2023.6.8
- Corregir registro sin OTP
- El usuario no usará teléfono como username si OTP está deshabilitado
- Corregir bug PPPoE

## 2026.02.03
- Actualización de presets Burst para planes comerciales actuales
- Mantener planes base: 10M, 40M y 80M
- Agregar planes de alta velocidad: 100M, 150M, 200M, 250M y 300M
- Eliminar presets obsoletos (menores a 10M)
- Normalizar lógica Burst:
    - MIR = 2× velocidad contratada
    - Burst Threshold = 75% del MIR
    - Limit-at = 50% del CIR
- Simplificar interfaz de selección de Burst Preset

## 2026.02.04
- Actualización de presets Burst alineados a planes comerciales
- Nuevos planes hasta 1G
- Normalización completa de Burst
- Limpieza de presets antiguos

## 2026.02.05
- Corrección de detalles de la factura