import math


def media(items: list[int]) -> float:
    if not items:
        return 0.0
    return round(sum(items) / len(items), 2)


def desviacion_estandar(items: list[int]) -> float:
    if len(items) < 2:
        return 0.0
    m = media(items)
    var = sum((x - m) ** 2 for x in items) / (len(items) - 1)
    return round(math.sqrt(var), 2)


def frecuencias(items: list[int]) -> dict[int, int]:
    freq = {1: 0, 2: 0, 3: 0, 4: 0, 5: 0}
    for x in items:
        if x in freq:
            freq[x] += 1
    return freq


def porcentaje_acuerdo(items: list[int]) -> float:
    if not items:
        return 0.0
    acuerdo = sum(1 for x in items if x >= 4)
    return round((acuerdo / len(items)) * 100, 1)


def alpha_cronbach(matriz: list[list[int]]) -> float:
    n_items = len(matriz)
    if n_items < 2:
        return 0.0
    n_sujetos = len(matriz[0]) if matriz else 0
    if n_sujetos < 2:
        return 0.0

    varianzas = []
    for item in matriz:
        varianzas.append(desviacion_estandar(item) ** 2)

    sumas_fila = [0.0] * n_sujetos
    for j in range(n_sujetos):
        for i in range(n_items):
            sumas_fila[j] += matriz[i][j]

    var_total = desviacion_estandar(sumas_fila) ** 2
    sum_var_items = sum(varianzas)

    if var_total == 0:
        return 0.0

    alpha = (n_items / (n_items - 1)) * (1 - sum_var_items / var_total)
    return round(alpha, 3)


ITEMS_BLOQUE_A = ["p1", "p2", "p3"]
ITEMS_BLOQUE_B = ["p4", "p5", "p6", "p7", "p7b"]
ITEMS_BLOQUE_C = ["p8", "p9"]

BLOQUES = {
    "A": {
        "titulo": "Diagnóstico de Accesibilidad",
        "items": ITEMS_BLOQUE_A,
        "texto": {
            "p1": "Dificultad de acceso desde dispositivos no-Android",
            "p2": "Necesidad de acceso multiplataforma vía navegador",
            "p3": "Limitación de interacción por falta de compatibilidad",
        },
    },
    "B": {
        "titulo": "Usabilidad de la PWA",
        "items": ITEMS_BLOQUE_B,
        "texto": {
            "p4": "Interfaz visual atractiva y moderna",
            "p5": "Navegación intuitiva y fácil",
            "p6": "Funcionalidades rápidas y eficientes",
            "p7": "Rendimiento adecuado cross-device",
            "p7b": "Compatibilidad con navegador habitual",
        },
    },
    "C": {
        "titulo": "Adopción e Impacto",
        "items": ITEMS_BLOQUE_C,
        "texto": {
            "p8": "Preferencia PWA por no ocupar almacenamiento",
            "p9": "Incremento de visibilidad comercial",
        },
    },
}
