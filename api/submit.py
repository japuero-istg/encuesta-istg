from fastapi import APIRouter, Request
from fastapi.responses import JSONResponse
import database
from models import RespuestaEncuesta, BARRIOS_GUAYAQUIL

router = APIRouter()


@router.post("/api/submit")
async def submit(respuesta: RespuestaEncuesta, request: Request):
    if respuesta.barrio not in BARRIOS_GUAYAQUIL:
        return JSONResponse(status_code=400, content={"ok": False, "error": "Barrio no válido"})

    if respuesta.email:
        respuesta.email = respuesta.email.strip().lower()

    ua = request.headers.get("user-agent", "")

    query = """
        INSERT INTO respuestas (
            email, tiene_app, es_emprendedor, profesion, edad_rango, barrio,
            p1, p2, p3, p4, p5, p6, p7, p7b, p8, p9,
            p10_mejoras, user_agent, duracion_segundos
        ) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19)
        ON CONFLICT (email) DO UPDATE SET
            tiene_app = EXCLUDED.tiene_app,
            es_emprendedor = EXCLUDED.es_emprendedor,
            profesion = EXCLUDED.profesion,
            edad_rango = EXCLUDED.edad_rango,
            barrio = EXCLUDED.barrio,
            p1 = EXCLUDED.p1, p2 = EXCLUDED.p2, p3 = EXCLUDED.p3,
            p4 = EXCLUDED.p4, p5 = EXCLUDED.p5, p6 = EXCLUDED.p6,
            p7 = EXCLUDED.p7, p7b = EXCLUDED.p7b, p8 = EXCLUDED.p8, p9 = EXCLUDED.p9,
            p10_mejoras = EXCLUDED.p10_mejoras,
            created_at = NOW()
        RETURNING id
    """

    rid = await database.execute_insert(
        query,
        respuesta.email,
        respuesta.tiene_app,
        respuesta.es_emprendedor,
        respuesta.profesion,
        respuesta.edad_rango,
        respuesta.barrio,
        respuesta.p1, respuesta.p2, respuesta.p3,
        respuesta.p4, respuesta.p5, respuesta.p6, respuesta.p7, respuesta.p7b,
        respuesta.p8, respuesta.p9,
        respuesta.p10_mejoras[:1000],
        ua,
        respuesta.duracion_segundos,
    )

    return {"ok": True, "id": str(rid)}
