# Backend — Guía rápida

Bienvenido al backend del proyecto (Laravel 12). Este README explica cómo levantar el proyecto localmente, cómo usar los contenedores Docker (dev/prod) y ofrece una breve explicación del Dockerfile y de los ambientes de docker-compose.

**Requisitos:** PHP 8.3+, Composer 2, Node 18+, Docker (opcional para producción/desarrollo con contenedores).

**Archivo:** [backend/README.md](backend/README.md#L1)

**Resumen rápido:**
- **Local (sin Docker):** clonar, copiar `.env`, instalar dependencias con Composer y npm, generar clave, migrar y ejecutar.
- **Con Docker (dev):** levantar servicios con `docker-compose -f docker-compose-dev.yml up -d` (Postgres, Redis, pgAdmin).
- **Con Docker (prod):** construir imagen con Dockerfile y desplegar usando `docker-compose -f docker-compose-prod.yml up -d`.

**Comandos útiles (resumen):**
- **Instalar dependencias:** `composer install --no-interaction && npm install`
- **Variables de entorno:** `cp .env.example .env` y editar según sea necesario
- **Clave de aplicación:** `php artisan key:generate`
- **Migrar y seed:** `php artisan migrate --seed`
- **Enlazar storage:** `php artisan storage:link`
- **Iniciar servidor local:** `php artisan serve --host=0.0.0.0 --port=8000`

## Instalación local (paso a paso)

1. Clona el repositorio y entra en la carpeta del backend:

   git clone <repo-url>
   cd backend

2. Copia el archivo de entorno y actualiza variables:

   cp .env.example .env
   # Edita .env con tus credenciales de DB, Redis y otras claves

3. Instala dependencias PHP y JS:

   composer install --no-interaction
   npm install

4. Genera la clave de la aplicación, ejecuta migraciones y crea el enlace de storage:

   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link

5. Compila activos (opcional en desarrollo):

   npm run dev

6. Levanta el servidor:

   php artisan serve --host=0.0.0.0 --port=8000

## Uso con Docker (dev)

- Archivo: `docker-compose-dev.yml` (servicios locales útiles para desarrollo)
- Servicios incluidos: `laravel-db` (Postgres 17), `laravel-redis` (Redis), `laravel-pgadmin` (pgAdmin)
- Propósito: facilitar desarrollo local con persistencia en volúmenes Docker. Exponer puertos 5432 (Postgres), 6379 (Redis) y 4000 (pgAdmin) para acceso desde el host.

Comando típico:

   docker-compose -f docker-compose-dev.yml up -d

Luego configura `.env` para apuntar a las credenciales del contenedor (host: `127.0.0.1` o `localhost` según tu Docker), o usa los valores por defecto que están en `docker-compose-dev.yml`.

## Uso con Docker (prod)

- Archivo: `docker-compose-prod.yml`
- Flujo esperado: construir una imagen `laravel-app:latest` (a partir del `Dockerfile`), luego desplegar servicios: `server-web`, `horizon`, `scheduler`, `redis`, `pgsql-prod`.
- En entorno de producción se recomienda mantener las variables sensibles en `.env.docker` o en un secreto de orquestador (no commitear credenciales al repositorio).

Comando típico (después de construir la imagen):

   docker build -t laravel-app:latest .
   docker-compose -f docker-compose-prod.yml up -d

## Breve explicación del Dockerfile

- Base: `php:8.3-cli-bullseye` — una imagen PHP ligera orientada a CLI (útil para RoadRunner/Octane).
- Dependencias del sistema: paquetes para PostgreSQL, GD, ZIP y herramientas como `git` y `unzip` para poder instalar extensiones y construir dependencias.
- Extensiones PHP: `pdo_pgsql`, `pcntl`, `sockets`, `gd`, `zip` y `redis` (vía PECL). `pcntl` y `sockets` son importantes para Octane/Horizon.
- Composer: se copia el binario desde la imagen oficial de Composer para instalar dependencias.
- Caching de capas: primero copia `composer.json` y `composer.lock` para aprovechar la caché de Docker al instalar dependencias.
- RoadRunner / Octane: el Dockerfile ejecuta `php artisan octane:install` y el `CMD` por defecto inicia Octane (RoadRunner) escuchando en el puerto 8000.
- Permisos: se ajustan permisos en `storage` y `bootstrap/cache` para que `www-data` pueda escribir.

Esta configuración está pensada para producir una imagen optimizada lista para producción: dependencias instaladas sin `--dev`, autoload optimizado y servidor Octane para rendimiento.

## Variables de entorno importantes

- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — configuración de PostgreSQL.
- `REDIS_HOST`, `REDIS_PASSWORD`, `QUEUE_CONNECTION` — configuración de Redis y colas.
- `APP_ENV`, `APP_DEBUG`, `APP_KEY` — entorno y seguridad de la aplicación.

Siempre revisa `.env.example` y adapta las variables antes de ejecutar migraciones o seeders.

## Comandos útiles de mantenimiento

- `php artisan migrate` — aplicar migraciones.
- `php artisan migrate:rollback` — revertir última migración.
- `php artisan db:seed` — ejecutar seeders.
- `php artisan horizon` — ejecutar Laravel Horizon (colores y monitoreo de colas).
- `php artisan octane:start` — iniciar Octane manualmente (en contenedores se ejecuta en el CMD).

## Solución de problemas comunes

- Si fallan migraciones por conexión: revisa `DB_*` en `.env` y puertos del contenedor Postgres.
- Permisos: si ves errores de escritura, ejecuta `chown -R www-data:www-data storage bootstrap/cache` dentro del contenedor o en el host según corresponda.
- Dependencias PHP faltantes: asegúrate de que el Dockerfile o tu entorno local tenga las extensiones requeridas (`pdo_pgsql`, `gd`, `zip`).

## Buenas prácticas al mover el proyecto a otro equipo

1. Copia el repo y ejecuta `composer install` y `npm install`.
2. Copia `.env.example` a `.env` y ajusta variables (no compartir secretos).
3. Si usas Docker, ejecuta `docker-compose -f docker-compose-dev.yml up -d` y luego ejecuta migraciones desde el host o dentro del contenedor:

   docker exec -it <container_name> php artisan migrate --seed

4. Para producción, construye la imagen con `docker build -t laravel-app:latest .` y despliega usando `docker-compose-prod.yml`.

## Contacto

Si necesitas ayuda con la puesta en marcha, indica el sistema operativo, la versión de Docker y el log del error más reciente. Mantendré este README actualizado según cambie la infraestructura.

---

Archivo actualizado: [backend/README.md](backend/README.md#L1)
