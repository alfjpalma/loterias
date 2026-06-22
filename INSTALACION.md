# Sistema de Control de Agencias de Loterías
## Instrucciones de Instalación en XAMPP

---

## Requisitos

- XAMPP con PHP 8.0+ y MySQL 8
- Composer (para reportes Excel/PDF avanzados)
- Apache con mod_rewrite habilitado

---

## Paso 1 — Copiar el proyecto

Copia la carpeta `loterias/` dentro de:
```
C:\xampp\htdocs\loterias\
```

---

## Paso 2 — Crear la base de datos

1. Abre XAMPP → Start **Apache** y **MySQL**
2. Abre tu navegador en: `http://localhost/phpmyadmin`
3. Clic en **Nueva** (base de datos)
4. Importa el archivo: `database/schema.sql`
   - Clic en **Importar** → Seleccionar archivo → `schema.sql` → Ejecutar

---

## Paso 3 — Configurar la conexión

Edita `config/database.php` si tus credenciales MySQL son distintas:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // tu contraseña de MySQL
define('DB_NAME', 'loterias_db');
```

---

## Paso 4 — Activar mod_rewrite en Apache

1. Abre: `C:\xampp\apache\conf\httpd.conf`
2. Busca y descomenta (quita el `#`):
   ```
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
3. Busca `AllowOverride None` (en la sección de htdocs) y cámbialo a:
   ```
   AllowOverride All
   ```
4. Reinicia Apache en XAMPP

---

## Paso 5 — Instalar dependencias (opcional pero recomendado)

Para activar reportes **Excel (.xlsx)** y **PDF** avanzados:

```bash
cd C:\xampp\htdocs\loterias
composer install
```

> Si no tienes Composer: https://getcomposer.org/download/
>
> Sin Composer, los reportes funcionan igual pero generan **CSV** y **HTML imprimible**.

---

## Paso 6 — Configurar la contraseña del admin

Abre en el navegador:
```
http://localhost/loterias/database/seed_password.php
```
Esto actualiza el hash de la contraseña en la base de datos.

**Luego elimina ese archivo** por seguridad:
```
database/seed_password.php  ← BORRAR después de usarlo
```

---

## Paso 7 — Acceder al sistema

```
http://localhost/loterias
```

### Credenciales iniciales

| Campo    | Valor       |
|----------|-------------|
| Usuario  | `admin`     |
| Contraseña | `Admin2024!` |

---

## Estructura del proyecto

```
loterias/
├── ajax/               Endpoints AJAX
├── config/             Configuración y BD
│   ├── config.php      ← CONFIGURACIÓN GENERAL
│   └── database.php    ← CREDENCIALES BD
├── controllers/        Controladores MVC
├── database/           Schema SQL
├── helpers/            Auth, CSRF, Flash
├── models/             Modelos de datos
├── public/
│   ├── css/style.css   Estilos personalizados
│   └── js/             JavaScript y AJAX
├── reports/            Generadores PDF/Excel
├── views/              Vistas HTML/PHP
│   ├── layout/         Header, Sidebar, Footer
│   ├── auth/           Login
│   ├── dashboard/      Panel principal
│   ├── agencias/       CRUD Agencias
│   ├── taquillas/      CRUD Taquillas
│   ├── sistemas/       CRUD Sistemas
│   ├── usuarios/       CRUD Usuarios
│   ├── ventas/         Registro de ventas
│   ├── cuadres/        Cuadre de caja
│   ├── conciliacion/   Comparativo automático
│   └── reportes/       Generación de reportes
├── .htaccess           Rewrite rules + seguridad
├── composer.json       Dependencias
├── index.php           Front controller
└── router.php          Router de URLs
```

---

## Roles de usuario

| Rol           | Permisos                                          |
|---------------|---------------------------------------------------|
| Administrador | Acceso total (CRUD de todo + eliminar)            |
| Operador      | Solo registrar ventas y cuadres (sin configurar)  |

---

## Funcionalidades

- **Dashboard** con estadísticas, gráficos (Chart.js) y conciliación del día
- **Registro diario de ventas** por taquilla con cálculo automático en tiempo real
- **Cuadre de caja** por agencia con 9 conceptos de ingreso
- **Conciliación automática** Ventas vs Caja (cuadrado ✅ / diferencia ⚠️)
- **Reportes PDF y Excel**: por agencia, taquilla, sistema, cuadre y comparativo
- **CRUD completo**: Agencias, Taquillas, Sistemas, Usuarios
- **Seguridad**: CSRF, sesiones seguras, contraseñas bcrypt
- **Responsive**: funciona en móvil y desktop
- **Auditoría**: registro de todas las acciones

---

## Solución de problemas comunes

### Error 500 / Página en blanco
- Verifica que mod_rewrite esté activo
- Revisa `AllowOverride All` en httpd.conf

### "Error de conexión a la base de datos"
- Verifica credenciales en `config/database.php`
- Asegúrate de que MySQL esté corriendo en XAMPP

### Reportes solo generan HTML
- Ejecuta `composer install` para activar FPDF/PhpSpreadsheet

### URL no funciona (404 en rutas internas)
- Verifica que `RewriteBase /loterias/` en `.htaccess` coincida con tu URL
- Si usas otro directorio, ajusta `BASE_URL` en `config/config.php`
