# Línea base de lo publicado — coipo_moodle

> Generado el 2026-10-26 con la metodología «Mejorar una app ya publicada» (objetivo 527, versión con banda de ruido
> y hallazgos preexistentes). Solo lectura sobre lo publicado (GET). **Archivo nuevo, sin commit.**

## 1. Identificación
| | |
|---|---|
| Repositorio | coipo_moodle |
| Rama local / commit local | main / 307667b |
| Árbol de trabajo | 1 archivo sin commitear (`HUSKY_AUDIT.md`, no se tocó) |
| Sembrada (`.semilla`) | no (no existe `.semilla`) |
| Qué es | Moodle de CONAF (campus/academia) con plugins y guía de migración (CLAUDE.md, docs/MIGRACION.md) |

## 2. Entorno y evidencia del dominio
| Entorno | Dominio | Evidencia (archivo:línea) |
|---|---|---|
| producción (destino) | academia.conaf.cl | CLAUDE.md:16 y :20 |
| origen descontinuado | campus.conaf.cl | CLAUDE.md:15 |
| ninguno de los dos | cursos.conaf.cl | CLAUDE.md:17 (no se midió) |

## 3. CI y despliegue (gh run list)
`Run README Maintainer` success; `deploy-prod.yml` (main) **failure** en 9892cd7 y 90bc559 (últimos runs). La causa del fallo: no lo comprobé.
Commit publicado (/health): /health responde 117 B sin campo `commit` que el medidor pueda leer · coincide con la rama: no lo comprobé

## 4. Funcionalidad medida (2 pasadas × 5 repeticiones, solo GET)
academia.conaf.cl:
```
base: https://academia.conaf.cl | commit publicado: None | sin /openapi.json (estado 404)
salud                              200             117 B  med  0.048s  ruido 0.004s  estable=True
salud_detalle                      200             117 B  med  0.045s  ruido 0.004s  estable=True
raiz                               200           42135 B  med  0.152s  ruido 0.003s  estable=False
INVALIDA_ruta                      404             513 B  med  0.039s  ruido 0.003s  estable=True
INVALIDA_api                       404             513 B  med   0.04s  ruido 0.001s  estable=True
```

campus.conaf.cl:
```
base: https://campus.conaf.cl | commit publicado: None | sin /openapi.json (estado ERR:URLError)
salud                              ERR:URLError         0 B  med  0.024s  ruido 0.001s  estable=True  [IGUAL A RAIZ: fallback de SPA]
salud_detalle                      ERR:URLError         0 B  med  0.022s  ruido 0.018s  estable=True  [IGUAL A RAIZ: fallback de SPA]
raiz                               ERR:URLError         0 B  med  0.022s  ruido   0.0s  estable=True
INVALIDA_ruta                      ERR:URLError         0 B  med  0.022s  ruido 0.001s  estable=True  [IGUAL A RAIZ: fallback de SPA]
INVALIDA_api                       ERR:URLError         0 B  med  0.024s  ruido 0.002s  estable=True  [IGUAL A RAIZ: fallback de SPA]
```

Hallazgos de la medición:
- academia: `/health` y `/health/detalle` dan igual tamaño (117 B); `/` tiene hash inestable entre repeticiones (página dinámica).
- campus: `ERR:URLError` en todo, coherente con «queda descontinuado». Las marcas «IGUAL A RAIZ» son artefacto de cuerpo vacío.

## 5. Hallazgos preexistentes (lo raro que ya existía) y si es por diseño
| Hallazgo | ¿Por diseño? | Fuente |
|---|---|---|
| `/health` y `/health/detalle` mismo tamaño (117 B) | no lo comprobé (no se comparó el hash; solo el tamaño) | coipo_moodle/docker/apache-moodle.conf:11 (`Alias /health`) |
| deploy-prod en rojo con la app respondiendo 200 | no lo comprobé | gh run list |

## 6. Interfaz
No medida en esta pasada.

## 7. Defectos simples corregidos (sin commit)
| Archivo | Antes | Después | Cómo se verificó |
|---|---|---|---|
| — | — | — | Ninguno aplicado: se buscaron enlaces relativos rotos en todos los `.md` (script propio, sin hallazgos) y se compararon puertos/dominios del README con compose y workflows (coinciden). |

Defectos vistos y NO corregidos (con motivo): Ninguno.

## 8. No lo comprobé
- Autenticación y cursos (no se entra).
- Contenido de `/health` (no se guardan cuerpos).
