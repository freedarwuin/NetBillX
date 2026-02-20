# NetBillX - Plataforma de Facturación ISP para MikroTik

![NetBillX](install/img/logo.png)

## Descripción

NetBillX es un sistema de facturación y gestión diseñado para Proveedores de Servicios de Internet (ISP) que operan infraestructura MikroTik. Permite administrar clientes, planes de servicio, autenticación y cobros desde una plataforma centralizada, automatizada y escalable.

## Características

- Generador e impresión profesional de vouchers
- Integración con FreeRadius
- Auto-registro de clientes
- Gestión de saldo de usuario
- Renovación automática de planes utilizando el saldo disponible
- Soporte Multi Router MikroTik
- Administración de servicios Hotspot y PPPoE
- Instalación rápida y simplificada
- Soporte multi idioma
- Integración con pasarelas de pago
- Validación de inicio de sesión por SMS
- Notificaciones automáticas por WhatsApp al cliente
- Notificaciones administrativas por Telegram

Ver [Cómo funciona](https://github.com/freedarwuin/NetBillX/wiki/C%C3%B3mo-Funciona)

## Pasarela de pago y Plugins

- [Lista de Pasarelas de Pago](https://github.com/orgs/freedarwuin/repositories?q=payment+gateway)
- [Lista de Plugins](https://github.com/orgs/freedarwuin/repositories?q=plugin)

Las pasarelas de pago y plugins pueden descargarse e instalarse directamente desde el Administrador de Plugins del sistema.

## Requisitos del sistema

La mayoría de servidores web con PHP y MySQL pueden ejecutar NetBillX sin inconvenientes.

Requisitos mínimos:

- Linux o Windows OS
- PHP 8.2 o superior
- Soporte para PDO y MySQLi
- Librería de imágenes PHP-GD2
- PHP-CURL
- PHP-ZIP
- PHP-Mbstring
- MySQL 4.1.x o superior

Puede instalarse en dispositivos Raspberry Pi.

Nota: Para entornos ISP en producción se recomienda Linux, ya que la configuración de tareas programadas (cronjob) es más estable y sencilla que en Windows.

## Registro de cambios

[CHANGELOG.md](CHANGELOG.md)

## Instalación

[Instrucciones de instalación](https://github.com/freedarwuin/NetBillX/wiki)

## Freeradius

Soporte para [Freeradius con Base de Datos](https://github.com/freedarwuin/NetBillX/wiki/FreeRadius)

## Soporte comunitario

- [Discusión en Github](https://github.com/freedarwuin/NetBillX/discussions)
- [Grupo de WhatsApp](https://t.me/phpmixbill)

## Soporte técnico

Este software es libre y de código abierto, distribuido sin garantía.

El soporte técnico profesional es un servicio independiente y tiene un costo desde $10 USD.

Si requieres soporte técnico directo, es necesario realizar el pago correspondiente.

Puedes realizar consultas generales sin costo en la [página de discusiones](https://github.com/freedarwuin/NetBillX/discussions) o mediante [WhatsApp](https://wa.me/584224512433?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.)

## Licencia

Licencia Pública General GNU versión 2 o posterior

Ver archivo [LICENSE](LICENSE)

## Dona a freedarwuin

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://paypal.me/DPedroa)

## PATROCINADORES

- [mixradius.com](https://mixradius.com/) Servicios de facturación Radius
- [mlink.id](https://mlink.id)
- [https://github.com/sonyinside](https://github.com/sonyinside)

## Gracias

Agradecemos a todas las personas y proveedores ISP que contribuyen activamente al desarrollo y mejora continua del proyecto.

<a href="https://github.com/freedarwuin/NetBillX/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=freedarwuin/NetBillX" />
</a>