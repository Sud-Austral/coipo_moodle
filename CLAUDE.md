# CLAUDE.md — coipo_moodle

Migración de la plataforma Moodle de CONAF, hoy alojada por la empresa externa **Lazzos**, a
infraestructura propia. Lo pidió la **Dirección Ejecutiva** (nivel nacional). Encargado
técnico: Luis Monsalve.

No es una aplicación nueva: es un Moodle en producción que hay que mover.

---

## Dominios: de dónde viene y a dónde va

| Dominio | Rol |
|---|---|
| `campus.conaf.cl` | **Origen.** Es el sitio del que salió el volcado (20.994 referencias dentro de la base). Queda descontinuado |
| `academia.conaf.cl` | **Destino definitivo.** Es a donde hay que migrar |
| `cursos.conaf.cl` | Aparece **cero** veces en el volcado. Fue el nombre con que se planteó el encargo, pero no es ninguno de los dos |

Consecuencia operativa: el contenido de los cursos trae 20.994 enlaces absolutos a
`campus.conaf.cl` incrustados. **Reescribirlos a `academia.conaf.cl` es un paso obligatorio**
de la migración, no opcional (ver `docs/MIGRACION.md`). Por eso el rol de base de datos se
llama `academia` / `academia_prod`.

---

## Inventario verificado — esta es la fuente de verdad

Todo lo de abajo fue comprobado leyendo los archivos, no supuesto. **El supuesto inicial de
"1 curso y 1 usuario" era falso.**

| Qué | Valor real |
|---|---|
| Volcado de la base | `bitnami_moodle.sql` — **295 MB**, dump de **MariaDB** 10.11.15, base origen `bitnami_moodle`, 492 tablas, InnoDB utf8mb4. **No trae `CREATE DATABASE` ni `USE`**: el nombre de la base es decisión nuestra |
| Versión de la base | Moodle **4.4.2** (Build 20240812) |
| Código fuente disponible | Moodle **4.5.10** (Build 20260216), branch 405 |
| moodledata | **10,9 GB** en `C:\c\moodledata` · `filedir` 10,84 GB · 256 subcarpetas · 15.994 archivos |
| Escala (conteos reales, medidos tras cargar el volcado) | **2.798 usuarios** activos · **36 filas** en `mdl_course` (35 cursos + el curso "sitio") · **3.474 matrículas** · **178.833 filas** en `mdl_files` |
| `mdl_task_log` | **0 filas.** Su `AUTO_INCREMENT` llega a 11 millones, pero Moodle ya purgó el historial en el origen — el contador solo dice cuántas filas existieron alguna vez |
| Tema activo | `boost_magnific` (recuperado y vendorizado) |
| Requisitos de Moodle 4.5 | PHP ≥ 8.1 y **bloquea 8.4** · PostgreSQL ≥ 13 · MariaDB ≥ 10.6.7 |

**Son datos personales de ~2.800 funcionarios.** Aplica la Ley 19.628. Eso condiciona todo lo
demás.

### Plugins no-core — resueltos, los cuatro en `plugins/`

| Componente | En la base | Vendorizado | Origen |
|---|---|---|---|
| `mod_customcert` | 2024042217 | 2024042217 (19 elementos) | el árbol de código original |
| `tool_mergeusers` | 2025020504 | 2026052700 · `supported [405,502]` | `ndunand/moodle-tool_mergeusers`, rama `MOODLE_405_STABLE` |
| `block_configurable_reports` | 2024051300 | 2027050401 · `supported [400,500]` | `jleyva/moodle-block_configurablereports` |
| `theme_boost_magnific` | 2024073000 | 2026062801 · requiere 4.4 | `EduardoKrausME/moodle-theme_boost_magnific` |
| `theme_moove`, `academi`, `almondb`, `degrade` | varias | No | Inactivos y sin datos propios: se ignoran |

`configurable_reports` y `mergeusers` **tienen tablas con datos**. Sin el plugin instalado,
`dbtransfer` no copia sus tablas a PostgreSQL y esos datos se pierden **sin ningún mensaje de
error** — por eso era imprescindible recuperarlos antes de la fase F4.

Regla al vendorizar: la versión debe ser **igual o superior** a la registrada en la base. Si
fuera menor, Moodle entiende que se está degradando el plugin y se niega a arrancar.

---

## Decisiones tomadas

1. **PostgreSQL 17 como motor final**, aunque el volcado sea MariaDB. Implica un paso
   intermedio inevitable: un MariaDB temporal que solo sirve para leer el volcado, y luego
   `admin/tool/dbtransfer` para pasar a PostgreSQL. No se usa `pgloader` ni conversión directa
   del `.sql`: deja las secuencias en 1 y el primer `INSERT` de Moodle revienta.
2. **Moodle 4.5.10**, asumiendo el upgrade 4.4.2 → 4.5.10 como paso controlado y con respaldo
   previo. El orden es obligatorio: `dbtransfer` valida el esquema contra el código en disco,
   así que **primero el upgrade, después la migración**.
3. **moodledata fuera del contenedor**, por bind mount con lectura y escritura.
4. **Corre en el servidor CONAF 172.31.2.41**, con el PostgreSQL 17 compartido de 172.31.2.40.
   Rol `academia`, base `academia_prod`, puerto `8115`.

---

## Arquitectura

```
Nginx del servidor (HTTP plano por ahora, sin certificado)
        │
        ▼
   app (único con puerto, 8115) ──┐      cron (sin puerto, user 33:33)
   Apache + PHP 8.3               │      supercronic → admin/cli/cron.php
   Moodle 4.5.10                  │
        │                         │
        └──── bind mount ─────────┴──► /opt/moodledata/coipo_moodle  (10,9 GB, UID 33)
        │
        ▼
   PostgreSQL 17 compartido — 172.31.2.40 (fuera de Docker)
```

**Una sola imagen para las dos fases.** `docker/config.php` lee todo de variables de entorno,
así el mismo artefacto apunta a MariaDB durante la conversión y a PostgreSQL en el estado
final. Lo que cambia es el `.env`, no la imagen.

### Rutas en el servidor

```
/opt/apps/coipo_moodle/          repositorio desplegado (código, compose, .env)
/opt/moodledata/coipo_moodle/    los 10,9 GB — FUERA del directorio de deploy
/opt/migracion/coipo_moodle/     el volcado, temporal
```

`moodledata` va fuera de `/opt/apps/` a propósito: el deploy sincroniza esa carpeta y un
borrado accidental se llevaría el contenido de los 40 cursos.

---

## Reglas de higiene — no negociables

- **Nada de datos ni secretos en el repositorio.** El volcado (295 MB) supera el límite de
  100 MB de GitHub y contiene datos personales. `.gitignore` bloquea `*.sql`, `Moodle/`,
  `moodledata/`, `.env` y comprimidos; `.dockerignore` evita que viajen al contexto de build.
- `INSUMO/setup_bd.sql` **contiene una contraseña real de producción** del rol `iam` (de otro
  proyecto). Está ignorado, pero **esa clave debe rotarse**. Se parece demasiado a la del rol
  `academia`: si una se filtra, la otra es adivinable.
- Las claves se generan con `openssl rand -hex 32`, viven solo en el `.env` del servidor con
  `chmod 600`, y los archivos que las contuvieron se borran con `shred -u`.
- Los comandos de Moodle dentro del contenedor se ejecutan **siempre con `-u www-data`**. Si
  se corren como root, los archivos que Moodle crea en `moodledata` quedan con dueño root y
  el sitio deja de poder escribir. Es el error más común y el más confuso.
- **`MOODLE_NOEMAILEVER=true` mientras esto sea un entorno de pruebas.** Son ~2.800 correos
  institucionales reales: sin ese freno, el cron envía resúmenes de foro y avisos de
  contraseña a funcionarios de verdad.
- `.gitattributes` fuerza `eol=lf`. Con la normalización automática anterior, el crontab y los
  scripts entraban a la imagen con `\r` y fallaban con "bad interpreter".

---

## Cómo verificar (regla del proyecto)

"Funciona" significa **haber visto el resultado**, no que el contenedor levantó ni que el
comando no dio error. El mínimo para este sistema:

1. `curl -sI http://172.31.2.41:8115/login/index.php` → **200**.
2. Entrar por navegador como administrador (es el usuario id = 2).
3. Abrir un curso y **abrir un PDF o SCORM** → confirma que `moodledata` se lee.
4. Subir un archivo desde la web y encontrarlo en `/opt/moodledata/coipo_moodle/filedir/`
   **en el host** → confirma que se escribe fuera del contenedor.
5. Contar: 492 tablas, ~2.800 usuarios, ~40 cursos. Si no cuadran, no está listo.
6. `php admin/cli/cfg.php --name=dbtype` → `pgsql`.

**Lo que parece un fallo y no lo es**: mientras no se reescriban los enlaces, las imágenes
incrustadas apuntan a `campus.conaf.cl`. Si hay acceso a esa red se cargarán desde el sitio
antiguo; si no, saldrán rotas. Se resuelve en el paso de reescritura de URLs.

---

## Documentos

| Documento | Contenido |
|---|---|
| `docs/TRASLADO.md` | Fase F0: mover el volcado y los 10,9 GB al servidor. Lo ejecuta Luis |
| `docs/MIGRACION.md` | Fases F1–F5: imagen, carga, upgrade, paso a PostgreSQL, sitio final |
| `INSUMO/` | Referencia de la infraestructura CONAF: patrón Docker, deploy, PostgreSQL |

`docs/entrevista-avance.md` guarda el levantamiento de requisitos, pausado por el cambio de
prioridades.

---

## Estado y pendientes

**Hecho**: inventario verificado, `Dockerfile`, `docker/`, los dos `docker-compose`,
`.env.example`, `db/setup_bd.sql`, `.gitignore`, `.dockerignore`, `.gitattributes`,
`plugins/mod/customcert/` y la documentación.

**Nada de esto se ha ejecutado**: no hay Docker ni PHP en el equipo de Luis, así que la imagen
no se ha construido ni probado. La primera verificación real ocurre en el servidor.

Pendientes, por orden de importancia:

1. Confirmar que 172.31.2.41 tiene **salida a internet**: el build descarga Moodle desde
   GitHub. Si no, ver la alternativa al final de `docs/MIGRACION.md`.
2. **DNS de `academia.conaf.cl`** apuntando al servidor, y `MOODLE_WWWROOT` definitivo.
3. **Reescribir los 20.994 enlaces** `campus.conaf.cl` → `academia.conaf.cl` una vez el sitio
   funcione sobre PostgreSQL (paso obligatorio, `docs/MIGRACION.md`).
4. Certificado `*.conaf.cl`: mientras no exista, `MOODLE_SSLPROXY=false`.
5. `.github/workflows/deploy-prod.yml` (Guía 6 de `INSUMO/`), **solo cuando el sitio funcione**.
6. Rotar la contraseña del rol `iam` que quedó expuesta en `INSUMO/setup_bd.sql`.
7. Correo a Lazzos: ya no bloquea la migración, pero sigue pendiente para el traspaso formal,
   DNS, certificado y fecha de corte.
8. Retomar el levantamiento de requisitos (áreas 3 a 8).
