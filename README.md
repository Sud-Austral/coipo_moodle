<!-- AI:BEGIN id=readme sha=3fe7f780e500 -->
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
  - `docker-compose.migracion.yml`: Configuración para migraciones
  - `Dockerfile`: Definición del contenedor Docker
  - Archivos de configuración Apache y PHP en `docker/`

- **Documentación**:
  - `manuales/`: Documentación de usuario
  - `docs/`: Documentación técnica
  - `INSUMO/`: Documentación de implementación

## Instalación

El proyecto utiliza Docker para su despliegue. La configuración principal se encuentra en `docker-compose.yml`.

## Configuración

Las variables de entorno se pueden configurar mediante el archivo `.env.example` proporcionado.

## Ejecución

Para ejecutar el proyecto, se debe utilizar Docker Compose:

```bash
docker-compose up -d
```

## API

El proyecto incluye un endpoint específico:

- `FETCH ${M.cfg.wwwroot}/theme/boost_magnific/_editor/model/?lang=${frontpage.lang}`

## Base de datos

El proyecto incluye un script de inicialización de base de datos en `db/setup_bd.sql`.

## Desarrollo

El proyecto incluye flujos de trabajo de GitHub Actions para CI/CD en varios componentes:

- `.github/workflows/deploy-prod.yml`: Despliegue a producción
- `.github/workflows/readme.yml`: Generación de README
- Flujos de trabajo específicos para cada plugin en sus respectivas carpetas

## Pruebas

Cada plugin incluye sus propias pruebas unitarias y de configuración.

## Despliegue

El despliegue se realiza mediante Docker y GitHub Actions. Se incluyen guías de implementación en `INSUMO/`.

## Limitaciones conocidas

No se documentan limitaciones conocidas en el código proporcionado.
<!-- AI:END id=readme -->

<!-- ai-readme-fingerprint: sha256:8490c1c2f4383a3c3b1aa15b4dfc6a31365a8101497f55571701865e8906e635 -->
