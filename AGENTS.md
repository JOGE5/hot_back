# AGENTS.md - Proyecto HOT

## Contexto general
Este proyecto corresponde al sistema web del Hotel Club Campestre La Mansión.

La arquitectura será:
- Backend: Laravel API + Filament Admin Panel
- Frontend huésped: Vue separado
- Base de datos: MySQL, nombre `hot_bd`

## Reglas principales
Antes de modificar código, analizar primero la estructura del proyecto.

No modificar archivos innecesarios.

No romper migraciones existentes.

No mezclar el panel administrativo con el frontend de huéspedes.

Filament se usará dentro del backend Laravel, no como proyecto separado.

## Roles del sistema
- SUPER ADMIN
- ADMIN
- RECEPCIONISTA
- CHEF
- HUESPED

## Permisos generales
HUÉSPED:
Solo puede ver su panel, realizar reservas, ver sus reservas y gestionar su perfil.

RECEPCIONISTA:
Solo puede ver huéspedes, reservaciones y habitaciones. Puede validar códigos de check-in.

CHEF:
Solo puede gestionar inventario de materia prima y menú diario.

ADMIN:
Puede ver todo, eliminar lógicamente chefs, recepcionistas y huéspedes, restaurar eliminados y descargar reportes.

SUPER ADMIN:
Puede ver todo, eliminar lógicamente admins, chefs, recepcionistas y huéspedes, restaurar eliminados y descargar reportes.

## Reglas de negocio
Toda eliminación debe ser lógica usando SoftDeletes.

Debe existir papelera administrativa para recuperar registros eliminados.

El registro público solo debe crear usuarios con rol HUESPED.

El huésped realiza una reservación con método de pago.

Al confirmarse el pago, el sistema debe enviar recibo o factura al correo del huésped.

El correo debe incluir un código de verificación para check-in.

El recepcionista ingresa ese código en el sistema y el backend valida si coincide.

El sistema debe soportar idioma español e inglés.

La autenticación debe permitir Google Auth.

La verificación de dos pasos debe ser obligatoria.

El panel administrativo debe incluir autenticación facial obligatoria.

El menú de cocina debe actualizarse día a día según lo que registre el chef.

## Módulos iniciales
1. Roles
2. Usuarios
3. Huéspedes
4. Habitaciones
5. Reservaciones
6. Pagos
7. Check-in
8. Cocina
9. Inventario
10. Menú diario
11. Reportes
12. Papelera

## Convenciones
Usar nombres de tablas en español plural:
- roles
- users
- huespedes
- habitaciones
- reservaciones
- pagos
- checkin_codigos
- ingredientes
- platos
- menu_diarios

En modelos con nombres en español, definir explícitamente:
protected $table = 'nombre_tabla';

Ejemplo:
protected $table = 'habitaciones';

No asumir pluralización automática de Laravel para palabras en español.

## Forma de trabajo
Trabajar por fases.
Explicar qué archivos se van a tocar antes de editarlos.
No implementar módulos futuros si no fueron solicitados.
Priorizar código limpio, escalable y fácil de entender.