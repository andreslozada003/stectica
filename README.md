# Yeral Stecti

Sistema de gestión para clínica estética, construido con Laravel 12 y preparado para MySQL.

## Módulos incluidos en la base

- Panel operativo rosado con agenda, alertas de inventario e indicadores.
- Roles de usuario: `admin` y `worker`.
- Pacientes e historial clínico, alergias y consentimientos con firma almacenada.
- Agenda por especialista, box, estado de cita y tratamiento.
- Sesiones clínicas con notas y mapa corporal/facial JSON.
- Inventario de insumos médicos y productos cosmetológicos.
- Pagos con diferentes métodos y estados.

## Inicio local

1. Cree una base de datos MySQL llamada `yeral_stecti`.
2. Revise las credenciales en `.env`.
3. Ejecute `php artisan migrate`.
4. Inicie con `php artisan serve` y abra la dirección indicada.

> Para producción, las firmas, fotos antes/después y documentos clínicos deben almacenarse en un disco privado (no público), con control de acceso y copias de seguridad cifradas.
