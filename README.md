# coipo_moodle

Moodle 4.5.10 de CONAF (sitio `academia.conaf.cl`) migrado desde el hosting externo de Lazzos a infraestructura propia:
imagen Docker `php:8.3-apache`, base PostgreSQL 17 compartida (fuera de Docker) y `moodledata` por bind mount. El detalle
de la migración, el inventario verificado y los pendientes están en [`CLAUDE.md`](CLAUDE.md).

## En breve (VERIFICADO leyendo el repo; no ejecuté el despliegue)

- Dos servicios en `docker-compose.yml`: `app` (Apache + PHP, único con puerto, `APP_PORT`) y `cron` (supercronic ejecutando
  `admin/cli/cron.php --keep-alive=0` cada minuto, `docker/moodle-crontab`).
- Toda la configuración entra por variables de entorno (`docker/config.php`); `.env.example` es la lista de referencia
  (el `.env` real vive sólo en el servidor, `/opt/apps/coipo_moodle/`, y nunca se commitea). Hay un `docker-compose.migracion.yml`
  con su `.env.migracion.example` para la fase de conversión desde MariaDB.
- Los plugins no-core están vendorizados en `plugins/` y se copian en el `Dockerfile` (`mod_customcert`, `tool_mergeusers`,
  `block_configurable_reports`, `theme_boost_magnific`, `theme_academi`). No se instalan por la interfaz web: se perderían en el
  siguiente despliegue.
- Despliegue: push a `main` ejecuta `.github/workflows/deploy-prod.yml` (reusable de `infra-docker-base`).
- Salud: `docker/health.php`. Sin pruebas propias del proyecto; `plugins/*/tests` son de los plugins de terceros.
- Datos personales de unos 2.800 funcionarios: nada de volcados, `.env` ni credenciales en el repositorio.

## Documentación

| | |
|---|---|
| [`CLAUDE.md`](CLAUDE.md) | guía para agentes, inventario, decisiones, arquitectura, pendientes |
| [`docs/TRASLADO.md`](docs/TRASLADO.md), [`docs/MIGRACION.md`](docs/MIGRACION.md), [`docs/TLS-HTTPS.md`](docs/TLS-HTTPS.md) | traslado, migración por fases, HTTPS |
| [`insumos/00-PROBLEMA.md`](insumos/00-PROBLEMA.md), [`insumos/01-SOLUCION.md`](insumos/01-SOLUCION.md) | problema y solución (generados) |

---

El bloque siguiente lo regenera automáticamente el workflow `readme.yml`; es genérico y no sustituye lo de arriba.

<!-- AI:BEGIN id=readme sha=0a37faad0483 -->
# coipo_moodle

## Descripción

Proyecto basado en Moodle con múltiples plugins personalizados.

## Stack técnico

- PHP
- SCSS
- HTML
- CSS
- JSON
- JavaScript
- Markdown
- YAML
- Text
- SQL
- INI
- Docker

## Estructura del proyecto

El proyecto contiene principalmente plugins de Moodle organizados en la carpeta `plugins`, incluyendo:

- Bloques: `configurable_reports`
- Herramientas de administración: `mergeusers`
- Módulos: `customcert`
- Temas: `academi`, `boost_magnific`

Además, incluye carpetas para documentación (`docs`, `manuales`), configuración de Docker (`docker`), y archivos de despliegue en `.github/workflows`.

## Instalación

El proyecto utiliza Docker para su despliegue. Los archivos de configuración incluyen:

- `Dockerfile`
- `docker-compose.yml`
- `docker-compose.migracion.yml`

## Configuración

Se proporcionan archivos de ejemplo para variables de entorno:

- `.env.example`
- `.env.migracion.example`

## Ejecución

El proyecto puede ser ejecutado utilizando Docker Compose con el archivo `docker-compose.yml`.

## API

Se ha detectado un endpoint específico:

- `${M.cfg.wwwroot}/theme/boost_magnific/_editor/model/?lang=${frontpage.lang}`

## Base de datos

El proyecto incluye un script de configuración de base de datos:

- `db/setup_bd.sql`

## Desarrollo

El proyecto incluye flujos de trabajo de GitHub Actions para CI/CD en varios plugins:

- `deploy-prod.yml`
- `readme.yml`
- `moodle-ci.yml`
- `moodle-release.yml`
- `ci.yml`

## Pruebas

Se incluyen archivos de prueba en varios plugins, principalmente en la carpeta `tests` de cada componente.

## Despliegue

El despliegue se gestiona a través de GitHub Actions y Docker. Se proporcionan guías y scripts específicos para el despliegue en producción.

## Limitaciones conocidas

No se documentan limitaciones conocidas en el contexto proporcionado.
<!-- AI:END id=readme -->

<!-- ai-readme-fingerprint: sha256:8490c1c2f4383a3c3b1aa15b4dfc6a31365a8101497f55571701865e8906e635 -->
