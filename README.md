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
