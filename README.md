<!-- AI:BEGIN id=readme sha=06294874e1d0 -->
# coipo_moodle

## Descripción

Proyecto basado en Moodle con plugins personalizados para funcionalidades específicas.

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

El proyecto contiene principalmente plugins de Moodle organizados en la carpeta `plugins/`, junto con configuración de Docker, documentación y manuales.

### Componentes principales

- **Plugins de Moodle**:
  - `plugins/admin/tool/mergeusers`: Herramienta para fusionar usuarios
  - `plugins/blocks/configurable_reports`: Bloque para informes configurables
  - `plugins/mod/customcert`: Módulo para certificados personalizados
  - `plugins/theme/academi`: Tema personalizado
  - `plugins/theme/boost_magnific`: Tema basado en Boost con modificaciones

- **Configuración de Docker**:
  - `docker-compose.yml`: Configuración principal de Docker
  - `docker-compose.migracion.yml`: Configuración para migración
  - `Dockerfile`: Definición del contenedor Docker
  - Archivos de configuración Apache y PHP en `docker/`

- **Documentación**:
  - `manuales/`: Contiene manuales de usuario
  - `docs/`: Documentación técnica
  - `INSUMO/`: Documentación de insumos y guías

## Instalación

El proyecto utiliza Docker para su despliegue. La configuración principal se encuentra en `docker-compose.yml`.

## Configuración

Las variables de entorno se pueden configurar mediante el archivo `.env.example` como plantilla.

## Ejecución

Para ejecutar el proyecto, se debe utilizar Docker Comando:

```bash
docker-compose up -d
```

## API

El proyecto incluye un endpoint específico:

- `FETCH ${M.cfg.wwwroot}/theme/boost_magnific/_editor/model/?lang=${frontpage.lang}`: Endpoint utilizado en el archivo `plugins/theme/boost_magnific/amd/src/frontpage.js`

## Base de datos

El proyecto incluye un script de inicialización de base de datos en `db/setup_bd.sql`.

## Desarrollo

Los plugins de Moodle incluyen configuración para CI/CD en GitHub Actions:

- `plugins/admin/tool/mergeusers/.github/workflows/`
- `plugins/blocks/configurable_reports/.github/workflows/`
- `plugins/mod/customcert/.github/workflows/`
- `plugins/theme/academi/.github/workflows/`
- `plugins/theme/boost_magnific/.github/workflows/`

## Pruebas

Los plugins incluyen pruebas unitarias, por ejemplo:

- `plugins/admin/tool/mergeusers/tests/`
- `plugins/blocks/configurable_reports/tests/`

## Despliegue

El despliegue se gestiona mediante GitHub Actions con el workflow `deploy-prod.yml` ubicado en `.github/workflows/`.

## Limitaciones conocidas

No se documentan limitaciones conocidas en el código proporcionado.
<!-- AI:END id=readme -->

<!-- ai-readme-fingerprint: sha256:8490c1c2f4383a3c3b1aa15b4dfc6a31365a8101497f55571701865e8906e635 -->
