from pydantic import BaseModel, EmailStr, conint
from typing import Literal


class RespuestaEncuesta(BaseModel):
    email: EmailStr
    tiene_app: Literal['si', 'no']
    es_emprendedor: Literal['si', 'no']
    profesion: Literal[
        'Estudiante',
        'Graduado',
        'Docente',
        'Emprendedor',
        'Administrativo',
        'Otro',
    ]
    edad_rango: Literal['18-25', '26-35', '36-45', '46+']
    barrio: str
    p1: conint(ge=1, le=5)
    p2: conint(ge=1, le=5)
    p3: conint(ge=1, le=5)
    p4: conint(ge=1, le=5)
    p5: conint(ge=1, le=5)
    p6: conint(ge=1, le=5)
    p7: conint(ge=1, le=5)
    p7b: conint(ge=1, le=5)
    p8: conint(ge=1, le=5)
    p9: conint(ge=1, le=5)
    p10_mejoras: str = ""
    duracion_segundos: int = 0


BARRIOS_GUAYAQUIL = [
    "Centro Histórico",
    "Puerto Marítimo",
    "Ximena",
    "Febres Cordero",
    "Kennedy",
    "Caraguay",
    "Urdesa",
    "Víctor Emilio Estrada",
    "Garzota",
    "Altos de la Florida",
    "Prosperina",
    "Bastión Popular",
    "El Triunfo",
    "Vía a la Costa",
    "SurOeste",
    "Cooperativas",
    "Norte",
    "Sur",
]

PROFESIONES = [
    "Estudiante",
    "Graduado",
    "Docente",
    "Emprendedor",
    "Administrativo",
    "Otro",
]

EDADES_RANGO = ["18-25", "26-35", "36-45", "46+"]
