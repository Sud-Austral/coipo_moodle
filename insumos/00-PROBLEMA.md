# Problema, reconstruido desde el codigo

Este documento se deduce de lo que quedo escrito en el repositorio. Cada
afirmacion lleva su cita o su marca.

## Advertencia previa sobre la evidencia

De los 1.347 archivos del repositorio, solo 27 son propios. Los otros 1.320
viven bajo un unico directorio de complementos y el analizador los marca como
codigo de terceros que el repositorio arrastra. Lo que este repositorio hace
por si mismo cabe en esos 27 archivos, y son casi todos de empaquetado,
configuracion y documentacion. La inferencia sobre el problema es, por tanto,
mas debil aqui que en un repositorio con codigo de negocio propio.

## La cadena de inferencia, dicha en voz alta

Lo que el repositorio construye es un contenedor de aplicacion sobre una
imagen de lenguaje con servidor web incorporado [Dockerfile:10], con su
configuracion de servidor [docker/apache-moodle.conf], su configuracion de
interprete [docker/php.ini], su configuracion de aplicacion
[docker/config.php], su tarea periodica [docker/moodle-crontab] y su punto de
comprobacion de salud [docker/health.php]. Todo eso se orquesta desde un
archivo de composicion [docker-compose.yml] y se publica desde un flujo de
integracion continua [.github/workflows/deploy-prod.yml].

De ahi la primera inferencia: el problema no era construir una aplicacion,
sino operar una que ya existia. [INFERIDO]

La segunda inferencia es mas concreta y esta mejor sostenida. Hay un segundo
archivo de composicion dedicado a migracion
[docker-compose.migracion.yml], su propio ejemplo de variables de entorno
[.env.migracion.example] y dos documentos extensos, uno de migracion y otro
de traslado [docs/MIGRACION.md], [docs/TRASLADO.md]. Un aparato entero
paralelo al de operacion, solo para mover algo de un lugar a otro. Luego lo
que estaba roto tenia que ver con donde vivia la instalacion y con como
llevarla a otra parte sin perderla. [INFERIDO]

La tercera: el repositorio arrastra cinco complementos de terceros fijados
dentro del arbol, cada uno con su propio archivo de version
[plugins/theme/academi/version.php],
[plugins/theme/boost_magnific/version.php],
[plugins/mod/customcert/version.php],
[plugins/blocks/configurable_reports/version.php],
[plugins/admin/tool/mergeusers/version.php]. Que se hayan fijado dentro del
repositorio en vez de instalarse aparte sugiere que se necesitaba que la
instalacion volviera a levantarse igual en otra parte, con los mismos
complementos y las mismas versiones. [INFERIDO]

Sobre para que sirve cada uno de esos cinco, la unica base disponible es como
estan nombrados sus directorios y en que categoria de la plataforma se
alojan: dos bajo temas visuales, uno bajo actividades, uno bajo bloques y uno
bajo herramientas de administracion. Eso no basta para afirmar que
capacidades entregan, y no se afirma aqui. [PENDIENTE] que necesita el area
usuaria de cada uno.

## Quien sufre el problema

No hay en la evidencia ninguna definicion de roles propia de este
repositorio. Los unicos archivos de definicion de permisos que aparecen
pertenecen a complementos de terceros
[plugins/admin/tool/mergeusers/db/access.php] y
[plugins/blocks/configurable_reports/db/access.php], y describen los permisos
internos de esos complementos, no los roles del negocio.

- Rol que administra la plataforma: [PENDIENTE]
- Rol que dicta o carga contenido: [PENDIENTE]
- Rol que participa en los cursos: [PENDIENTE]
- Cuantas personas son en cada rol: [PENDIENTE]

Hay un documento de entrevista en el repositorio [docs/entrevista-avance.md]
que probablemente contenga parte de esta informacion, pero su contenido no
esta en la evidencia extraida y no corresponde suponerlo. [PENDIENTE]

## Como lo resolvian antes

El indicio mas fuerte es el propio aparato de migracion citado arriba: si hay
un procedimiento escrito y un montaje separado para trasladar la
instalacion, es porque existia una instalacion previa funcionando en otro
lugar. [INFERIDO] [docs/TRASLADO.md], [docker-compose.migracion.yml]

Hay ademas un archivo de preparacion de base de datos [db/setup_bd.sql], del
que el analizador solo extrajo un token en la linea 35 y ningun nombre de
tabla. Sirve para afirmar que la puesta en marcha incluye un paso de
preparacion del almacenamiento, y para nada mas. [INFERIDO]

- Donde estaba alojada la instalacion anterior: [PENDIENTE]
- Quien la administraba: [PENDIENTE]
- Cuanto tardo el traslado y cuando se hizo: [PENDIENTE]

## Que pasa si no se hace nada

[PENDIENTE] Sin excepcion. El codigo no lo responde y no se deduce de que el
sistema exista.

## Volumen

La evidencia no permite estimar volumen. La extraccion de tablas devolvio un
solo token, sin nombres de tabla, sin indices y sin tipos de columna
[db/setup_bd.sql:35]. El unico indicio indirecto de que se espera carga
sostenida es que existe una tarea periodica programada
[docker/moodle-crontab] y un punto de comprobacion de salud
[docker/health.php], es decir, que el servicio se penso para quedarse
encendido y ser vigilado. Eso habla de continuidad, no de tamano. [INFERIDO]

- Cantidad de usuarios registrados: [PENDIENTE]
- Cantidad de cursos activos: [PENDIENTE]
- Tamano de los datos a trasladar: [PENDIENTE]

## Quien decide que esta terminado

[PENDIENTE] Sin excepcion. Existe un documento propio de mejoras pendientes,
en dos formatos [mejoras.md] y [mejoras.pdf], lo que indica que la lista de
lo que falta se comparte con alguien fuera del equipo tecnico. [INFERIDO]
Quien es ese alguien y quien aprueba el cierre no esta escrito.

## Marco normativo

Si la plataforma registra participacion de personas identificadas, hay
obligaciones sobre tratamiento de datos personales, y si ademas emite algun
documento que acredite esa participacion, hay obligaciones sobre su validez.
Ninguna de las dos cosas esta escrita en la evidencia y ninguna se afirma
aqui. [VERIFICAR] que se registra, que se emite, que norma aplica y que
exige.
