<?php

function media(array $items): float {
    $n = count($items);
    if ($n === 0) return 0.0;
    return round(array_sum($items) / $n, 2);
}

function desviacion_estandar(array $items): float {
    $n = count($items);
    if ($n < 2) return 0.0;
    $m = media($items);
    $var = 0.0;
    foreach ($items as $x) {
        $var += ($x - $m) ** 2;
    }
    $var /= ($n - 1);
    return round(sqrt($var), 2);
}

function frecuencias(array $items): array {
    $freq = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    foreach ($items as $x) {
        if (isset($freq[$x])) {
            $freq[$x]++;
        }
    }
    return $freq;
}

function porcentaje_acuerdo(array $items): float {
    $n = count($items);
    if ($n === 0) return 0.0;
    $acuerdo = 0;
    foreach ($items as $x) {
        if ($x >= 4) $acuerdo++;
    }
    return round(($acuerdo / $n) * 100, 1);
}

function alpha_cronbach(array $matriz): float {
    $n_items = count($matriz);
    if ($n_items < 2) return 0.0;

    $n_sujetos = count($matriz[0] ?? []);
    if ($n_sujetos < 2) return 0.0;

    $varianzas = [];
    foreach ($matriz as $item) {
        $varianzas[] = desviacion_estandar($item) ** 2;
    }

    $sumas_fila = array_fill(0, $n_sujetos, 0.0);
    for ($j = 0; $j < $n_sujetos; $j++) {
        for ($i = 0; $i < $n_items; $i++) {
            $sumas_fila[$j] += $matriz[$i][$j];
        }
    }

    $var_total = desviacion_estandar($sumas_fila) ** 2;
    $sum_var_items = array_sum($varianzas);

    if ($var_total === 0.0) return 0.0;

    $alpha = ($n_items / ($n_items - 1)) * (1 - $sum_var_items / $var_total);
    return round($alpha, 3);
}

$BLOQUES = [
    'A' => [
        'titulo' => 'Diagnóstico de Accesibilidad y Limitaciones Tecnológicas',
        'items' => ['p1', 'p2', 'p3'],
        'texto' => [
            'p1' => 'Dificultad de acceso desde dispositivos no-Android',
            'p2' => 'Indispensabilidad de acceso multiplataforma vía navegador',
            'p3' => 'Limitación de interacción por falta de compatibilidad',
        ],
    ],
    'B' => [
        'titulo' => 'Usabilidad, Interfaz y Experiencia de la PWA',
        'items' => ['p4', 'p5', 'p6', 'p7'],
        'texto' => [
            'p4' => 'Interfaz visual atractiva y moderna',
            'p5' => 'Navegación intuitiva y fácil',
            'p6' => 'Funcionalidades rápidas y eficientes',
            'p7' => 'Rendimiento adecuado cross-device',
        ],
    ],
    'C' => [
        'titulo' => 'Intención de Adopción e Impacto en el Ecosistema',
        'items' => ['p8', 'p9'],
        'texto' => [
            'p8' => 'Preferencia PWA por no ocupar almacenamiento',
            'p9' => 'Incremento de visibilidad digital y éxito comercial',
        ],
    ],
];
