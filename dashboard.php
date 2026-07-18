<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/analysis.php';

check_auth();

$rows = db_query("SELECT * FROM respuestas ORDER BY created_at DESC");
$total = count($rows);
$statsData = [];

if ($total > 0) {
    foreach ($BLOQUES as $bk => $info) {
        $stats_items = [];
        $matrices = [];
        foreach ($info['items'] as $item) {
            $vals = [];
            foreach ($rows as $r) {
                if (isset($r[$item]) && $r[$item] !== null) {
                    $vals[] = intval($r[$item]);
                }
            }
            $stats_items[$item] = [
                'texto' => $info['texto'][$item] ?? $item,
                'media' => media($vals),
                'desviacion_estandar' => desviacion_estandar($vals),
                'frecuencias' => frecuencias($vals),
                'porcentaje_acuerdo' => porcentaje_acuerdo($vals),
                'n' => count($vals),
            ];
            $matrices[] = $vals;
        }

        $n_cols = count($matrices[0] ?? []);
        $transpuesta = [];
        for ($j = 0; $j < $n_cols; $j++) {
            $transpuesta[$j] = [];
            for ($i = 0; $i < count($matrices); $i++) {
                $transpuesta[$j][] = $matrices[$i][$j];
            }
        }

        $statsData['bloques'][$bk] = [
            'titulo' => $info['titulo'],
            'items' => $stats_items,
            'alpha_cronbach' => alpha_cronbach($transpuesta),
            'n' => $total,
        ];
    }

    $profesiones = [];
    $edades = [];
    $barrios = [];
    $tiene_app = ['si' => 0, 'no' => 0];
    $es_emprendedor = ['si' => 0, 'no' => 0];
    foreach ($rows as $r) {
        $p = $r['profesion'];
        $profesiones[$p] = ($profesiones[$p] ?? 0) + 1;
        $e = $r['edad_rango'];
        $edades[$e] = ($edades[$e] ?? 0) + 1;
        $b = $r['barrio'];
        $barrios[$b] = ($barrios[$b] ?? 0) + 1;
        $ta = $r['tiene_app'];
        $tiene_app[$ta] = ($tiene_app[$ta] ?? 0) + 1;
        $ee = $r['es_emprendedor'];
        $es_emprendedor[$ee] = ($es_emprendedor[$ee] ?? 0) + 1;
    }

    $statsData['demografia'] = [
        'profesiones' => $profesiones,
        'edades' => $edades,
        'barrios' => $barrios,
        'tiene_app' => $tiene_app,
        'es_emprendedor' => $es_emprendedor,
    ];
}

$statsData['total'] = $total;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — EmprendeISTG</title>
    <link rel="stylesheet" href="static/css/encuesta.css">
    <script src="https://cdn.plot.ly/plotly-2.35.0.min.js"></script>
</head>
<body>
    <div class="site-wrapper">
        <header class="site-header">
            <div class="header-content">
                <div class="header-left">
                    <img src="static/img/logo_istg_instituto.png" alt="ISTG" class="header-logo">
                    <div class="header-text">
                        <span class="header-instituto">Instituto Superior Tecnológico Guayaquil</span>
                    </div>
                </div>
                <div class="header-right">
                    <img src="static/img/logo_istg.png" alt="EmprendeISTG" class="header-logo">
                </div>
            </div>
        </header>

        <main class="site-main">
            <div class="dashboard-container">
                <header class="dashboard-header">
                    <h1>Dashboard EmprendeISTG</h1>
                    <p>Panel de análisis de respuestas de la encuesta</p>
                    <div class="export-buttons">
                        <a href="export.php?format=csv" class="btn btn-sm" target="_blank">Exportar CSV</a>
                        <a href="export.php?format=json" class="btn btn-sm" target="_blank">Exportar JSON</a>
                        <button class="btn btn-sm btn-png" onclick="descargarTodasPNG()">Descargar todas (PNG)</button>
                    </div>
                </header>

                <div class="stats-summary">
                    <div class="stat-card"><h3 id="totalRespuestas"><?php echo $total; ?></h3><p>Total respuestas</p></div>
                    <div class="stat-card"><h3 id="alphaA"><?php echo $statsData['bloques']['A']['alpha_cronbach'] ?? '—'; ?></h3><p>α Bloque A</p></div>
                    <div class="stat-card"><h3 id="alphaB"><?php echo $statsData['bloques']['B']['alpha_cronbach'] ?? '—'; ?></h3><p>α Bloque B</p></div>
                    <div class="stat-card"><h3 id="alphaC"><?php echo $statsData['bloques']['C']['alpha_cronbach'] ?? '—'; ?></h3><p>α Bloque C</p></div>
                </div>

                <section class="bloques-section">
                    <h2>Algoritmo ConsolidarEscalaLikert — Matriz de Frecuencias</h2>
                    <p class="step-hint">Matriz de conteos [Ítems × Opciones] con frecuencias absolutas y porcentajes.</p>
                    <div class="charts-grid">
                        <div class="chart-box"><div id="matrizConteoA"></div></div>
                        <div class="chart-box"><div id="matrizConteoB"></div></div>
                        <div class="chart-box"><div id="matrizConteoC"></div></div>
                    </div>
                    <div id="matrizConsolidada"></div>
                </section>

                <section class="demografia-section">
                    <h2>Distribución Demográfica</h2>
                    <div class="charts-grid">
                        <div class="chart-box"><div id="chartProfesion"></div></div>
                        <div class="chart-box"><div id="chartEdad"></div></div>
                        <div class="chart-box"><div id="chartBarrio"></div></div>
                    </div>
                </section>

                <section class="bloques-section">
                    <h2>Bloque A — Diagnóstico de Accesibilidad (P1-P3)</h2>
                    <div id="bloqueATable"></div>
                    <div class="charts-grid">
                        <div class="chart-box chart-wide"><div id="chartBarA"></div></div>
                        <div class="chart-box"><div id="chartAcuerdoA"></div></div>
                    </div>
                    <div class="charts-grid">
                        <div class="chart-box"><div id="chartP1"></div></div>
                        <div class="chart-box"><div id="chartP2"></div></div>
                        <div class="chart-box"><div id="chartP3"></div></div>
                    </div>
                </section>

                <section class="bloques-section">
                    <h2>Bloque B — Usabilidad de la PWA (P4-P7)</h2>
                    <div id="bloqueBTable"></div>
                    <div class="charts-grid">
                        <div class="chart-box chart-wide"><div id="chartBarB"></div></div>
                        <div class="chart-box"><div id="chartAcuerdoB"></div></div>
                    </div>
                    <div class="charts-grid">
                        <div class="chart-box"><div id="chartP4"></div></div>
                        <div class="chart-box"><div id="chartP5"></div></div>
                        <div class="chart-box"><div id="chartP6"></div></div>
                        <div class="chart-box"><div id="chartP7"></div></div>
                    </div>
                </section>

                <section class="bloques-section">
                    <h2>Bloque C — Adopción e Impacto (P8-P9)</h2>
                    <div id="bloqueCTable"></div>
                    <div class="charts-grid">
                        <div class="chart-box chart-wide"><div id="chartBarC"></div></div>
                        <div class="chart-box"><div id="chartAcuerdoC"></div></div>
                    </div>
                    <div class="charts-grid">
                        <div class="chart-box"><div id="chartP8"></div></div>
                        <div class="chart-box"><div id="chartP9"></div></div>
                    </div>
                </section>

                <section class="bloques-section">
                    <h2>Resumen General — Todas las Preguntas</h2>
                    <div class="charts-grid">
                        <div class="chart-box chart-wide"><div id="chartAllMedia"></div></div>
                    </div>
                    <div class="charts-grid">
                        <div class="chart-box chart-wide"><div id="chartAllAcuerdo"></div></div>
                    </div>
                </section>
            </div>
        </main>

        <footer class="site-footer">
            <a href="https://pucusoft.pages.dev/" target="_blank" rel="noopener noreferrer" class="footer-link">
                <img src="static/img/pucusoft.png" alt="Pucusoft" class="footer-logo">
                <span>Desarrollado por Pucusoft</span>
            </a>
        </footer>
    </div>

<script>
var STATS_DATA = <?php echo json_encode($statsData); ?>;

var COLOR_LIKERT = {1:'#dc2626',2:'#f97316',3:'#eab308',4:'#22c55e',5:'#15803d'};
var ETIQUETAS = {1:'Total. desacuerdo',2:'Desacuerdo',3:'Neutral',4:'De acuerdo',5:'Total. acuerdo'};
var PREGUNTAS_TEXTO = {
    p1:'Acceso no-Android', p2:'Multiplataforma indispensable', p3:'Limitación interacción',
    p4:'UI atractiva y moderna', p5:'Navegación intuitiva', p6:'Funcionalidades rápidas',
    p7:'Rendimiento cross-device',
    p8:'Preferencia PWA (espacio)', p9:'Visibilidad comercial'
};

function consolidarEscalaLikert(items) {
    var keys = Object.keys(items);
    var NUM_ITEMS = keys.length;
    var OPCIONES = 5;
    var matriz_conteos = [];
    var matriz_porcentajes = [];
    var NUM_ENCUESTADOS = 0;

    for (var i = 0; i < NUM_ITEMS; i++) {
        matriz_conteos[i] = [];
        matriz_porcentajes[i] = [];
        for (var o = 1; o <= OPCIONES; o++) {
            matriz_conteos[i][o] = 0;
        }
    }

    var primerItem = items[keys[0]];
    NUM_ENCUESTADOS = primerItem.n || 0;

    for (var i = 0; i < NUM_ITEMS; i++) {
        var item = items[keys[i]];
        for (var o = 1; o <= OPCIONES; o++) {
            matriz_conteos[i][o] = item.frecuencias[o] || 0;
        }
    }

    for (var i = 0; i < NUM_ITEMS; i++) {
        for (var o = 1; o <= OPCIONES; o++) {
            matriz_porcentajes[i][o] = NUM_ENCUESTADOS > 0
                ? ((matriz_conteos[i][o] / NUM_ENCUESTADOS) * 100).toFixed(1)
                : 0;
        }
    }

    return { keys: keys, NUM_ITEMS: NUM_ITEMS, NUM_ENCUESTADOS: NUM_ENCUESTADOS, OPCIONES: OPCIONES, matriz_conteos: matriz_conteos, matriz_porcentajes: matriz_porcentajes };
}

function renderMatrizConsolidadaBlock(container, items, titulo) {
    var m = consolidarEscalaLikert(items);
    var html = '<h3 style="margin:1rem 0 0.5rem">' + titulo + ' — N = ' + m.NUM_ENCUESTADOS + '</h3>';
    html += '<div style="overflow-x:auto"><table class="stats-table matriz-table"><thead><tr><th>Ítem</th>';
    for (var o = 1; o <= m.OPCIONES; o++) {
        html += '<th colspan="2" style="text-align:center;color:' + COLOR_LIKERT[o] + '">' + o + '<br><small>' + ETIQUETAS[o] + '</small></th>';
    }
    html += '<th>M</th><th>DE</th><th>% Acuerdo</th></tr>';
    html += '<tr><th></th>';
    for (var o = 1; o <= m.OPCIONES; o++) {
        html += '<th style="font-weight:normal">n</th><th style="font-weight:normal">%</th>';
    }
    html += '<th></th><th></th><th></th></tr></thead><tbody>';

    for (var i = 0; i < m.NUM_ITEMS; i++) {
        var k = m.keys[i];
        var item = items[k];
        html += '<tr><td><strong>' + (PREGUNTAS_TEXTO[k] || k) + '</strong></td>';
        for (var o = 1; o <= m.OPCIONES; o++) {
            var pct = m.matriz_porcentajes[i][o];
            var bg = o <= 2 ? 'rgba(220,38,38,' + (pct/200) + ')' : o === 3 ? 'rgba(234,179,8,' + (pct/200) + ')' : 'rgba(34,197,94,' + (pct/200) + ')';
            html += '<td style="text-align:center;background:' + bg + '">' + m.matriz_conteos[i][o] + '</td>';
            html += '<td style="text-align:center;color:#64748b;background:' + bg + '">' + pct + '%</td>';
        }
        html += '<td style="text-align:center;font-weight:600">' + item.media + '</td>';
        html += '<td style="text-align:center">' + item.desviacion_estandar + '</td>';
        var colorAc = item.porcentaje_acuerdo >= 70 ? '#16a34a' : item.porcentaje_acuerdo >= 50 ? '#ca8a04' : '#dc2626';
        html += '<td style="text-align:center;font-weight:600;color:' + colorAc + '">' + item.porcentaje_acuerdo + '%</td>';
        html += '</tr>';
    }
    html += '</tbody></table></div>';
    container.innerHTML += html;
}

function renderMatrizHeatmap(divId, items, titulo) {
    var m = consolidarEscalaLikert(items);
    var z = [];
    var y = m.keys.map(function(k) { return PREGUNTAS_TEXTO[k] || k; });
    var x = ['1 Total. desacuerdo','2 Desacuerdo','3 Neutral','4 De acuerdo','5 Total. acuerdo'];

    for (var i = 0; i < m.NUM_ITEMS; i++) {
        var row = [];
        for (var o = 0; o < m.NUM_ITEMS; o++) {
            row.push(m.matriz_porcentajes[i][o + 1]);
        }
        z.push(row);
    }

    Plotly.newPlot(divId, [{
        z: z, x: x, y: y, type:'heatmap',
        colorscale:[[0,'#fef2f2'],[0.25,'#fca5a5'],[0.5,'#fef08a'],[0.75,'#86efac'],[1,'#15803d']],
        text: z.map(function(row) { return row.map(function(v) { return v + '%'; }); }),
        texttemplate:'%{text}',
        showscale:true,
        colorbar:{title:'%'}
    }], {title:titulo+' — Mapa de Calor', margin:{t:60,b:80,l:220,r:60}, height:Math.max(250,m.NUM_ITEMS*50+100), xaxis:{tickangle:-30}});
}

function agregarBotonPNG(divId) {
    var container = document.getElementById(divId);
    if (!container) return;
    var btn = document.createElement('button');
    btn.className = 'btn btn-sm btn-png-inline';
    btn.textContent = 'PNG';
    btn.onclick = function() { Plotly.downloadImage(divId, {format:'png', width:1200, height:600, filename:divId}); };
    container.parentElement.style.position = 'relative';
    container.parentElement.appendChild(btn);
}

function descargarTodasPNG() {
    var divs = document.querySelectorAll('.dashboard-container [id^="chart"], .dashboard-container [id^="matriz"]');
    divs.forEach(function(d) {
        if (d.id && d.children.length > 0) {
            Plotly.downloadImage(d.id, {format:'png', width:1200, height:600, filename:'emprendeistg_' + d.id});
        }
    });
}

function makePie(divId, data, title) {
    var colors = ['#2563eb','#16a34a','#ea580c','#9333ea','#e11d48','#0891b2','#ca8a04'];
    Plotly.newPlot(divId, [{values:Object.values(data), labels:Object.keys(data), type:'pie', hole:0.4, marker:{colors:colors}}], {title:title, margin:{t:40,b:20,l:20,r:20}, height:300});
}

function makeHorizontalBar(divId, data, title) {
    var sorted = Object.entries(data).sort(function(a,b){return b[1]-a[1];});
    Plotly.newPlot(divId, [{y:sorted.map(function(e){return e[0];}), x:sorted.map(function(e){return e[1];}), type:'bar', orientation:'h', marker:{color:'#2563eb'}}], {title:title, margin:{t:40,b:20,l:150,r:20}, height:350, yaxis:{autorange:'reversed'}});
}

function makeLikertStacked(divId, items, bloque) {
    var labels = Object.keys(items);
    var texts = labels.map(function(k) { return PREGUNTAS_TEXTO[k] || k; });
    var traces = [];
    for (var v = 1; v <= 5; v++) {
        traces.push({y:texts, x:labels.map(function(k){return items[k].frecuencias[v]||0;}), name:''+v, type:'bar', orientation:'h', marker:{color:COLOR_LIKERT[v]}});
    }
    Plotly.newPlot(divId, traces, {barmode:'stack', title:'Distribución Likert — Bloque '+bloque, margin:{t:50,b:30,l:220,r:30}, height:Math.max(250, labels.length*60), xaxis:{title:'Frecuencia'}, legend:{orientation:'h',y:-0.2}});
}

function makeAcuerdoBar(divId, items, bloque) {
    var labels = Object.keys(items);
    var texts = labels.map(function(k) { return PREGUNTAS_TEXTO[k] || k; });
    var acuerdo = labels.map(function(k) { return items[k].porcentaje_acuerdo; });
    Plotly.newPlot(divId, [{x:texts, y:acuerdo, type:'bar', marker:{color:acuerdo.map(function(a){return a>=70?'#16a34a':a>=50?'#ca8a04':'#dc2626';})}}], {title:'% Acuerdo — Bloque '+bloque, yaxis:{title:'%',range:[0,100]}, margin:{t:50,b:100,l:50,r:30}, height:350, xaxis:{tickangle:-30}});
}

function makePieQuestion(divId, item, texto) {
    var freq = item.frecuencias;
    var labels = [1,2,3,4,5];
    var cats = [ETIQUETAS[1],ETIQUETAS[2],ETIQUETAS[3],ETIQUETAS[4],ETIQUETAS[5]];
    var colors = [COLOR_LIKERT[1],COLOR_LIKERT[2],COLOR_LIKERT[3],COLOR_LIKERT[4],COLOR_LIKERT[5]];
    var vals = labels.map(function(l) { return freq[l] || 0; });
    Plotly.newPlot(divId, [{values:vals, labels:cats, type:'pie', hole:0.5, marker:{colors:colors}, textinfo:'label+percent', textposition:'inside'}], {title:texto+'<br>M='+item.media+' | DE='+item.desviacion_estandar+' | Acuerdo='+item.porcentaje_acuerdo+'%', margin:{t:70,b:20,l:20,r:20}, height:320, showlegend:false});
}

function makeTable(containerId, items) {
    var html = '<table class="stats-table"><thead><tr><th>Ítem</th><th>M</th><th>DE</th><th>% Acuerdo</th></tr></thead><tbody>';
    for (var k in items) {
        html += '<tr><td>'+(PREGUNTAS_TEXTO[k]||k)+'</td><td>'+items[k].media+'</td><td>'+items[k].desviacion_estandar+'</td><td>'+items[k].porcentaje_acuerdo+'%</td></tr>';
    }
    html += '</tbody></table>';
    document.getElementById(containerId).innerHTML = html;
}

function renderAll() {
    var d = STATS_DATA;

    if (d.total === 0) return;

    var consolidatedDiv = document.getElementById('matrizConsolidada');
    consolidatedDiv.innerHTML = '';

    var bloquesConfig = {A:{titulo:'Bloque A: Diagnóstico'}, B:{titulo:'Bloque B: Usabilidad'}, C:{titulo:'Bloque C: Adopción'}};
    for (var bk in bloquesConfig) {
        var b = d.bloques[bk];
        if (!b) continue;
        renderMatrizConsolidadaBlock(consolidatedDiv, b.items, bloquesConfig[bk].titulo);
        renderMatrizHeatmap('matrizConteo' + bk, b.items, bloquesConfig[bk].titulo);
    }

    if (d.demografia.profesiones) makePie('chartProfesion', d.demografia.profesiones, 'Profesión');
    if (d.demografia.edades) makePie('chartEdad', d.demografia.edades, 'Rango de Edad');
    if (d.demografia.barrios) makeHorizontalBar('chartBarrio', d.demografia.barrios, 'Barrio / Zona');

    var bloques = {A:{table:'bloqueATable',bar:'chartBarA',acuerdo:'chartAcuerdoA',charts:['chartP1','chartP2','chartP3']},
                   B:{table:'bloqueBTable',bar:'chartBarB',acuerdo:'chartAcuerdoB',charts:['chartP4','chartP5','chartP6','chartP7']},
                   C:{table:'bloqueCTable',bar:'chartBarC',acuerdo:'chartAcuerdoC',charts:['chartP8','chartP9']}};

    var allMedias = {};
    var allAcuerdos = {};

    for (var bk in bloques) {
        var b = d.bloques[bk];
        var cfg = bloques[bk];
        if (!b) continue;

        makeTable(cfg.table, b.items);
        makeLikertStacked(cfg.bar, b.items, bk);
        makeAcuerdoBar(cfg.acuerdo, b.items, bk);

        var keys = Object.keys(b.items);
        cfg.charts.forEach(function(divId, i) {
            var k = keys[i];
            if (k && b.items[k]) {
                makePieQuestion(divId, b.items[k], PREGUNTAS_TEXTO[k] || k);
                allMedias[PREGUNTAS_TEXTO[k]||k] = b.items[k].media;
                allAcuerdos[PREGUNTAS_TEXTO[k]||k] = b.items[k].porcentaje_acuerdo;
            }
        });
    }

    var sortedMedia = Object.entries(allMedias).sort(function(a,b){return b[1]-a[1];});
    Plotly.newPlot('chartAllMedia', [{y:sortedMedia.map(function(e){return e[0];}), x:sortedMedia.map(function(e){return e[1];}), type:'bar', orientation:'h', marker:{color:sortedMedia.map(function(e){return e[1]>=4?'#16a34a':e[1]>=3?'#ca8a04':'#dc2626';})}}], {title:'Media por Pregunta (todas)', margin:{t:50,b:30,l:220,r:30}, height:450, xaxis:{title:'Media (1-5)',range:[0,5.5]}});
    Plotly.newPlot('chartAllAcuerdo', [{y:sortedMedia.map(function(e){return e[0];}), x:sortedMedia.map(function(e){return allAcuerdos[e[0]];}), type:'bar', orientation:'h', marker:{color:sortedMedia.map(function(e){return allAcuerdos[e[0]]>=70?'#16a34a':allAcuerdos[e[0]]>=50?'#ca8a04':'#dc2626';})}}], {title:'% Acuerdo por Pregunta (todas)', margin:{t:50,b:30,l:220,r:30}, height:450, xaxis:{title:'% Acuerdo',range:[0,110]}});
}

renderAll();
</script>
</body>
</html>
