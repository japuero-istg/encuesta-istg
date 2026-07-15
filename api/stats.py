from fastapi import APIRouter, Depends, HTTPException
from fastapi.responses import JSONResponse
from fastapi.security import HTTPBasic, HTTPBasicCredentials
import database
import config
from analysis.likert import (
    media, desviacion_estandar, frecuencias, porcentaje_acuerdo,
    alpha_cronbach, BLOQUES,
)

router = APIRouter()
security = HTTPBasic()


def verificar_auth(creds: HTTPBasicCredentials = Depends(security)):
    if creds.username != config.ADMIN_USER or creds.password != config.ADMIN_PASS:
        raise HTTPException(status_code=401, detail="No autorizado", headers={"WWW-Authenticate": "Basic"})
    return creds


@router.get("/api/stats")
async def stats(bloque: str | None = None, _=Depends(verificar_auth)):
    rows = await database.execute("SELECT * FROM respuestas ORDER BY created_at DESC")
    if not rows:
        return {"total": 0, "bloques": {}, "demografia": {}}

    total = len(rows)
    bloques_result = {}

    bloques_a_consultar = [bloque] if bloque and bloque in BLOQUES else list(BLOQUES.keys())

    for b_key in bloques_a_consultar:
        b_info = BLOQUES[b_key]
        stats_items = {}
        matrices = []

        for item in b_info["items"]:
            vals = [r[item] for r in rows if r[item] is not None]
            stats_items[item] = {
                "texto": b_info["texto"][item],
                "media": media(vals),
                "desviacion_estandar": desviacion_estandar(vals),
                "frecuencias": frecuencias(vals),
                "porcentaje_acuerdo": porcentaje_acuerdo(vals),
                "n": len(vals),
            }
            matrices.append(vals)

        n_cols = len(matrices[0]) if matrices else 0
        transpuesta = [[matrices[i][j] for i in range(len(matrices))] for j in range(n_cols)]

        bloques_result[b_key] = {
            "titulo": b_info["titulo"],
            "items": stats_items,
            "alpha_cronbach": alpha_cronbach(transpuesta),
            "n": total,
        }

    # Demografía + Filtrado
    profesiones = {}
    edades = {}
    barrios = {}
    tiene_app = {"si": 0, "no": 0}
    es_emprendedor = {"si": 0, "no": 0}
    for r in rows:
        p = r["profesion"]
        profesiones[p] = profesiones.get(p, 0) + 1
        e = r["edad_rango"]
        edades[e] = edades.get(e, 0) + 1
        b = r["barrio"]
        barrios[b] = barrios.get(b, 0) + 1
        ta = r["tiene_app"]
        tiene_app[ta] = tiene_app.get(ta, 0) + 1
        ee = r["es_emprendedor"]
        es_emprendedor[ee] = es_emprendedor.get(ee, 0) + 1

    return {
        "total": total,
        "bloques": bloques_result,
        "demografia": {
            "profesiones": profesiones,
            "edades": edades,
            "barrios": barrios,
            "tiene_app": tiene_app,
            "es_emprendedor": es_emprendedor,
        },
    }
