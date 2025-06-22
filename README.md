# Laravel SaaS Multitenancy CRUD Generator

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Acerca del Proyecto

Este es un proyecto de boilerplate para aplicaciones SaaS multitenant utilizando Laravel 12. Incluye un generador de CRUD personalizados que respetan la arquitectura multitenant, separando claramente los recursos de administración central (landlord) y los recursos específicos de cada inquilino (tenant).

## Características

- **Stack Tecnológico**: Laravel 12, React 19, Inertia 2, TypeScript, Tailwind 4, shadcn/ui
- **Multitenancy**: Implementado con spatie/laravel-multitenancy v4
- **Arquitectura**: Service-Repository pattern con Form Requests y Controllers delgados
- **Autenticación**: Sistemas separados para admin (landlord) y usuarios (tenants)
- **Sistema de Permisos**: Implementado con spatie/laravel-permission
- **Generador de CRUD**: Comando `php artisan make:saas:resource` para scaffolding completo

## Requisitos

- PHP 8.2+
- PostgreSQL 15+ (configurado en puerto 5434)
- Node.js 20+ y npm 10+
- Composer 2.5+

## Instalación

```bash
# Clonar el repositorio
git clone https://github.com/MarcoVegaR/Boilerplate.git
cd Boilerplate

# Instalar dependencias
composer install
npm install

# Configuración del entorno
cp .env.example .env
php artisan key:generate

# Configurar la base de datos en .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5434
# DB_DATABASE=saas_landlord
# DB_USERNAME=postgres
# DB_PASSWORD=tu_contraseña

# Ejecutar migraciones y seeders
php artisan migrate
php artisan db:seed

# Compilar assets
npm run dev
```

## Uso del Generador de CRUD SaaS

```bash
# Generar un recurso completo para el contexto de administración
php artisan make:saas:resource Product --all

# Generar un recurso completo para el contexto de tenant
php artisan make:saas:resource Order --tenant --all

# Generar solo componentes específicos
php artisan make:saas:resource Category --model --controller
```

## Estructura del Proyecto

- **Models**: `app/Models/`
- **Migrations**: `database/migrations/landlord/` y `database/migrations/tenant/`
- **Controllers**: `app/Http/Controllers/Admin/` y `app/Http/Controllers/Tenant/`
- **Repositories**: `app/Repositories/Admin/` y `app/Repositories/Tenant/`
- **Services**: `app/Services/Admin/` y `app/Services/Tenant/`
- **Requests**: `app/Http/Requests/Admin/` y `app/Http/Requests/Tenant/`
- **Policies**: `app/Policies/`
- **React Components**: `resources/js/Pages/Admin/` y `resources/js/Pages/Tenant/`

## Pruebas

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar pruebas específicas
php artisan test --filter=MakeSaasResourceCommandTest
```

## Licencia

Este proyecto está licenciado bajo la [Licencia MIT](https://opensource.org/licenses/MIT).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
