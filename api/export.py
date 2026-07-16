import csv
import io
import json
from fastapi import APIRouter, Depends, HTTPException
from fastapi.responses import StreamingResponse, JSONResponse
from fastapi.security import HTTPBasic, HTTPBasicCredentials
import database
import config

router = APIRouter()
security = HTTPBasic()


def verificar_auth(creds: HTTPBasicCredentials = Depends(security)):
    if creds.username != config.ADMIN_USER or creds.password != config.ADMIN_PASS:
        raise HTTPException(status_code=401, detail="No autorizado", headers={"WWW-Authenticate": "Basic"})
    return creds


@router.get("/api/export")
async def exportar(format: str = "json", _=Depends(verificar_auth)):
    rows = await database.execute("SELECT * FROM respuestas ORDER BY created_at ASC")
    if not rows:
        return JSONResponse(status_code=404, content={"error": "Sin datos"})

    data = []
    for r in rows:
        data.append({
            "id": str(r["id"]),
            "created_at": r["created_at"].isoformat(),
            "email": r["email"],
            "tiene_app": r["tiene_app"],
            "es_emprendedor": r["es_emprendedor"],
            "profesion": r["profesion"],
            "edad_rango": r["edad_rango"],
            "barrio": r["barrio"],
            "p1": r["p1"], "p2": r["p2"], "p3": r["p3"],
            "p4": r["p4"], "p5": r["p5"], "p6": r["p6"],
            "p7": r["p7"],
            "p8": r["p8"], "p9": r["p9"],
            "p10_mejoras": r["p10_mejoras"],
            "duracion_segundos": r["duracion_segundos"],
            "version_encuesta": r["version_encuesta"],
        })

    if format == "csv":
        output = io.StringIO()
        writer = csv.DictWriter(output, fieldnames=data[0].keys())
        writer.writeheader()
        writer.writerows(data)
        output.seek(0)
        return StreamingResponse(
            iter([output.getvalue()]),
            media_type="text/csv",
            headers={"Content-Disposition": "attachment; filename=respuestas_encuesta.csv"},
        )

    return JSONResponse(content=data)
