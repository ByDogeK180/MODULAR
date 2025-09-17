// assets/api/predicciones_auto.js
(function () {
  const DATASET_URL = "assets/api/dataset_admin.php";

  // UMBRALES / PARÁMETROS
  const THR = 0.5;                 // umbral de riesgo del ensamble
  const PASS_THRESHOLD = 6.0;      // nota mínima para aprobar el ciclo
  const TOTAL_PERIODOS_DEFAULT = 3;// #periodos por ciclo (ajústalo a tu escuela)

  // Estado global
  let RAW = [];                  // dataset completo sin filtrar (del backend)
  let DATA = [];                 // dataset filtrado (ciclo/periodo)
  const FILTERS = { ciclo_id: 'all', periodo_id: 'all' };

  // Datos ML (solo 2 features: asistencia, parciales)
  let X = [], y = [];
  let cartModel = null, rfModel = null;

  // Gráficas
  let cmChart = null, distChart = null;

  // --------- HELPERS UI ----------
  const $ = (id) => document.getElementById(id);
  const setStatus = (msg, err = false) => {
    const el = $("status");
    if (el) {
      el.textContent = msg;
      el.style.color = err ? "#e74c3c" : "#6b7280";
    }
  };
  const fmtPct = (x) => (x * 100).toFixed(1) + "%";
  const clamp01 = (v) => (Number.isFinite(+v) ? Math.max(0, Math.min(1, +v)) : 0);

  function clearTables() {
    ['statTotal','statRiesgo','statRate'].forEach(id => { const el=$(id); if(el) el.textContent='—'; });
    const tb1=$('tbRiesgo'); if(tb1) tb1.innerHTML='';
    const tb2=$('tbResumen'); if(tb2) tb2.innerHTML='';
    if (cmChart){ cmChart.destroy(); cmChart=null; }
    if (distChart){ distChart.destroy(); distChart=null; }
  }

  // --------- FILTROS ----------
  function filterData(raw){
    let arr = raw.slice();
    if (FILTERS.ciclo_id !== 'all') {
      arr = arr.filter(r => String(r.ciclo_id) === String(FILTERS.ciclo_id));
    }
    if (FILTERS.periodo_id !== 'all') {
      arr = arr.filter(r => String(r.periodo_id) === String(FILTERS.periodo_id));
    }
    return arr;
  }

  function buildFilters() {
    const $ciclo = $('cicloSelect');
    const $per   = $('periodoSelect');
    if (!$ciclo || !$per) return;

    // Ciclos únicos
    const ciclosMap = new Map();
    RAW.forEach(r => {
      if (r.ciclo_id != null) ciclosMap.set(String(r.ciclo_id), r.ciclo_nombre || r.ciclo_id);
    });
    $ciclo.innerHTML = `<option value="all">Todos los ciclos</option>`;
    for (const [id, name] of ciclosMap.entries()) {
      $ciclo.innerHTML += `<option value="${id}">${name}</option>`;
    }
    $ciclo.value = FILTERS.ciclo_id;

    function refreshPeriodos(run=true){
      const base = (FILTERS.ciclo_id === 'all') ? RAW : RAW.filter(r => String(r.ciclo_id) === String(FILTERS.ciclo_id));
      const perMap = new Map();
      base.forEach(r => {
        if (r.periodo_id != null) perMap.set(String(r.periodo_id), r.periodo_nombre || r.periodo_id);
      });
      const sorted = Array.from(perMap.entries())
        .sort((a,b)=> String(a[0]).localeCompare(String(b[0]), undefined, {numeric:true, sensitivity:'base'}));

      $per.innerHTML = `<option value="all">Todos los periodos</option>`;
      for (const [id, name] of sorted) $per.innerHTML += `<option value="${id}">${name}</option>`;

      // por defecto: último periodo disponible
      if (sorted.length) {
        const latest = String(sorted[sorted.length-1][0]);
        FILTERS.periodo_id = latest;
        $per.value = latest;
      } else {
        FILTERS.periodo_id = 'all';
        $per.value = 'all';
      }
      if (run) runPipeline();
    }

    $ciclo.onchange = () => { FILTERS.ciclo_id = $ciclo.value; refreshPeriodos(true); };
    $per.onchange   = () => { FILTERS.periodo_id = $per.value; runPipeline(); };
    const $btn = $('btnReset');
    if ($btn) $btn.onclick = () => { FILTERS.ciclo_id = 'all'; $ciclo.value = 'all'; refreshPeriodos(true); };

    refreshPeriodos(false);
  }

  // --------- PROYECCIÓN A FIN DE CICLO ----------
  function detectTotalPeriods(cicloId, raw){
    const set = new Set(
      raw.filter(r => String(r.ciclo_id) === String(cicloId))
         .map(r => r.periodo_id)
         .filter(v => v !== null && v !== undefined)
    );
    return set.size || TOTAL_PERIODOS_DEFAULT;
  }

  function cycleProjectionForRow(row, raw, totalDefault = TOTAL_PERIODOS_DEFAULT, target = PASS_THRESHOLD){
    const cicloId = row.ciclo_id;
    const total = totalDefault || detectTotalPeriods(cicloId, raw);

    // filas del mismo alumno-materia-ciclo
    const same = raw.filter(
      r => String(r.ciclo_id) === String(cicloId)
        && String(r.estudiante_id) === String(row.estudiante_id)
        && String(r.materia_id) === String(row.materia_id)
    );

    let sum = 0, done = 0;
    for (const r of same) {
      const v = Number(r.parciales_avg);
      if (Number.isFinite(v)) { sum += v; done++; }
    }

    if (done === 0) {
      return { needed: target, feasible: target <= 10, tot: total, done: 0, rem: total };
    }

    const remaining = Math.max(0, total - done);
    if (remaining === 0) {
      const finalAvg = sum / done;
      return { needed: 0, feasible: finalAvg >= target, tot: total, done, rem: 0, finalAvg };
    }

    let needed = (target * total - sum) / remaining;
    if (!Number.isFinite(needed)) needed = 10;
    const neededClamped = Math.min(10, Math.max(0, needed));
    const feasible = needed <= 10;

    return { needed: neededClamped, feasible, tot: total, done, rem: remaining };
  }

  // --------- MÉTRICAS Y GRÁFICAS ----------
  const confusion = (yT, yP) => {
    let TP = 0, TN = 0, FP = 0, FN = 0;
    for (let i = 0; i < yT.length; i++) {
      if (yT[i] === 1 && yP[i] === 1) TP++;
      else if (yT[i] === 0 && yP[i] === 0) TN++;
      else if (yT[i] === 0 && yP[i] === 1) FP++;
      else if (yT[i] === 1 && yP[i] === 0) FN++;
    }
    return { TP, TN, FP, FN };
  };

  function drawCM(cm) {
    const ctx = $("cmChart");
    if (!ctx || typeof Chart === "undefined") return;
    const data = {
      labels: ['TP', 'TN', 'FP', 'FN'],
      datasets: [{ label: 'Conteo', data: [cm.TP, cm.TN, cm.FP, cm.FN] }]
    };
    if (cmChart) cmChart.destroy();
    cmChart = new Chart(ctx, { type: 'bar', data });
  }

  function drawDist(y) {
    const ctx = $("distChart");
    if (!ctx || typeof Chart === "undefined") return;
    const zeros = y.filter(v => v === 0).length;
    const ones = y.filter(v => v === 1).length;
    const data = { labels: ['0 (No repr.)', '1 (Repr.)'], datasets: [{ data: [zeros, ones] }] };
    if (distChart) distChart.destroy();
    distChart = new Chart(ctx, { type: 'doughnut', data });
  }

  // --------- MODELOS ----------
  function getCARTCtor() {
    const c1 = (window.ML && window.ML.Cart && window.ML.Cart.DecisionTreeClassifier);
    const c2 = (window.ML && window.ML.DecisionTreeClassifier);
    return (typeof c1 === "function") ? c1 :
           (typeof c2 === "function") ? c2 : null;
  }

  // Votación segura de RF para 1 ejemplo -> prob ∈ [0,1]
  function rfProbOne(x) {
    if (!rfModel || !Array.isArray(rfModel.estimators)) return 0;
    let votes = 0, n = 0;
    for (const t of rfModel.estimators) {
      if (!t || typeof t.predict !== "function") continue;
      const p = t.predict(x)[0];
      const val = (p === true) ? 1 : (p === false) ? 0 : Number(p);
      if (Number.isFinite(val)) { votes += (val >= 0.5 ? 1 : 0); n++; }
    }
    return n > 0 ? votes / n : 0;
  }

  function trainAndEvaluate() {
    // split robusto 80/20 (evita sets vacíos)
    const idx = [...X.keys()].sort(() => Math.random() - 0.5);
    let tr = [], te = [];
    if (idx.length <= 1) {
      tr = idx.slice(); te = idx.slice();
    } else {
      let cut = Math.floor(idx.length * 0.8);
      if (cut <= 0) cut = 1;
      if (cut >= idx.length) cut = idx.length - 1;
      tr = idx.slice(0, cut);
      te = idx.slice(cut);
    }
    const Xtr = tr.map(i => X[i]), ytr = tr.map(i => y[i]);
    const Xte = te.map(i => X[i]), yte = te.map(i => y[i]);

    if (!Xtr.length || !ytr.length) {
      setStatus("Dataset insuficiente para entrenar (necesitas ≥2 filas).", true);
      return;
    }

    // ===== CART =====
    const CARTCtor = getCARTCtor();
    if (CARTCtor) {
      setStatus("Entrenando CART…");
      try {
        cartModel = new CARTCtor({ gainFunction: 'gini', maxDepth: 5, minNumSamples: 2 });
        cartModel.train(Xtr, ytr);
      } catch (e) {
        console.warn("CART falló, uso RF(1) como árbol:", e);
        setStatus("CART no disponible, usando RF(1) como árbol", true);
        cartModel = new ML.RandomForestClassifier({ nEstimators: 1, maxFeatures: 2, replacement: true, seed: 7 });
        cartModel.train(Xtr, ytr);
      }
    } else {
      console.warn("No encontré DecisionTreeClassifier; uso RF(1) como árbol.");
      setStatus("CART no encontrado, usando RF(1) como árbol", true);
      cartModel = new ML.RandomForestClassifier({ nEstimators: 1, maxFeatures: 2, replacement: true, seed: 7 });
      cartModel.train(Xtr, ytr);
    }
    const yCart = cartModel.predict(Xte).map(p => (p === true) ? 1 : (p === false) ? 0 : (Number(p) >= 0.5 ? 1 : 0));

    // ===== RANDOM FOREST =====
    setStatus("Entrenando Random Forest…");
    rfModel = new ML.RandomForestClassifier({ nEstimators: 200, maxFeatures: 2, replacement: true, seed: 42 });
    rfModel.train(Xtr, ytr);

    // Colapsar la matriz de votos del RF a vector por mayoría
    const rfAll = rfModel.predict(Xte); // [nÁrboles][nMuestras]
    const nTrees = rfAll.length;
    const yRf = Array(Xte.length).fill(0);
    for (let t = 0; t < nTrees; t++) {
      const predsT = rfAll[t];
      for (let j = 0; j < predsT.length; j++) {
        const v = (predsT[j] === true) ? 1 : (predsT[j] === false) ? 0 : Number(predsT[j]);
        yRf[j] += (Number.isFinite(v) && v >= 0.5) ? 1 : 0;
      }
    }
    for (let j = 0; j < yRf.length; j++) yRf[j] = (nTrees ? (yRf[j] / nTrees) : 0) >= 0.5 ? 1 : 0;

    // Ensamble CART + RF (mayoría de 2 modelos)
    const yEns = yCart.map((yc, i) => (yc + yRf[i]) >= 1 ? 1 : 0);

    drawCM(confusion(yte, yEns));
    drawDist(y);
  }

  // --------- PREDICCIÓN ----------
  function predictMany(rows) {
    return rows.map(r => {
      const x = [[
        Number(r.asistencia_pct || 0),
        Number(r.parciales_avg || 0)
      ]];

      // CART -> 0/1 como prob
      let cartPred = 0;
      try {
        const p = cartModel && cartModel.predict ? cartModel.predict(x)[0] : 0;
        cartPred = (p === true) ? 1 : (p === false) ? 0 : Number(p);
      } catch { cartPred = 0; }
      const probCart = clamp01(cartPred);

      // RF -> prob por votos de árboles
      const probRf = clamp01(rfProbOne(x));

      // Ensamble (promedio simple)
      let probFinal = clamp01((probCart + probRf) / 2);
      let pred = probFinal >= THR ? 1 : 0;

      // Proyección a FIN DE CICLO (usa TODOS los periodos del ciclo del alumno)
      const proj = cycleProjectionForRow(r, RAW, TOTAL_PERIODOS_DEFAULT, PASS_THRESHOLD);
      const estadoCiclo = (proj.rem === 0)
        ? (proj.feasible ? 'Aprobado (cierre)' : 'Reprobado (cierre)')
        : (proj.feasible ? 'Aún puede pasar' : 'Muy difícil');

      return {
        ...r,
        prob_cart: probCart,
        prob_rf: probRf,
        prob_final: probFinal,
        pred,
        req_avg_rest: proj.needed,
        ciclo_done: proj.done,
        ciclo_tot: proj.tot,
        ciclo_rem: proj.rem,
        estado_ciclo: estadoCiclo
      };
    });
  }

  // --------- RENDER ----------
  function renderTables(PREDS) {
    // Totales por ALUMNO ÚNICO
    const alumnosUnicos = new Set(PREDS.map(r => r.estudiante_id)).size;

    // Riesgo por alumno: si en alguna materia su prob_final >= THR
    const maxProbPorAlumno = {};
    for (const r of PREDS) {
      const id = r.estudiante_id;
      const p  = Number(r.prob_final) || 0;
      if (!(id in maxProbPorAlumno)) maxProbPorAlumno[id] = p;
      else maxProbPorAlumno[id] = Math.max(maxProbPorAlumno[id], p);
    }
    const alumnosEnRiesgo = Object.values(maxProbPorAlumno).filter(p => p >= THR).length;

    if ($("statTotal"))  $("statTotal").textContent  = alumnosUnicos;
    if ($("statRiesgo")) $("statRiesgo").textContent = alumnosEnRiesgo;
    if ($("statRate"))   $("statRate").textContent   = fmtPct(alumnosUnicos ? alumnosEnRiesgo / alumnosUnicos : 0);

    // Tabla de riesgo (por alumno-materia)
    const tbody = $("tbRiesgo"); if (tbody) tbody.innerHTML = "";
    const ordenados = [...PREDS].sort((a, b) => b.prob_final - a.prob_final).slice(0, 100);
    ordenados.forEach(r => {
      const alumno = (r.alumno_nombre || '') + ' ' + (r.alumno_apellido || '');
      const clase = `${r.grado || '-'}-${r.grupo || '-'}`;
      const materia = r.materia_nombre || '-';
      const periodo = r.periodo_nombre || r.periodo_id;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${alumno}</td><td>${materia}</td><td>${periodo}</td><td>${clase}</td>
        <td>${(Number(r.asistencia_pct || 0) * 100).toFixed(1)}%</td>
        <td>${r.incidentes_count == null ? 0 : r.incidentes_count}</td>
        <td>${Number(r.parciales_avg || 0).toFixed(2)}</td>
        <td>${Number(r.prob_cart).toFixed(2)}</td>
        <td>${Number(r.prob_rf).toFixed(2)}</td>
        <td>${Number(r.prob_final).toFixed(2)}</td>
        <td>${r.pred ? '<span class="badge bg-danger">Reprobará</span>' : '<span class="badge bg-success">No reprobará</span>'}</td>
        <td>${Number(r.req_avg_rest ?? 0).toFixed(2)}</td>
        <td>${
          r.estado_ciclo === 'Aún puede pasar'
            ? '<span class="badge bg-info text-dark">Aún puede pasar</span>'
            : (r.estado_ciclo === 'Muy difícil'
                ? '<span class="badge bg-warning text-dark">Muy difícil</span>'
                : (r.estado_ciclo.includes('Aprobado')
                    ? '<span class="badge bg-success">Aprobado (cierre)</span>'
                    : '<span class="badge bg-danger">Reprobado (cierre)</span>'))
        }</td>`;
      tbody && tbody.appendChild(tr);
    });

    // Resumen por periodo/clase/materia
    const byKey = {};
    PREDS.forEach(r => {
      const key = `${r.periodo_nombre || r.periodo_id}||${r.grado || '-'}-${r.grupo || '-'}||${r.materia_nombre || '-'}`;
      if (!byKey[key]) byKey[key] = { total: 0, riesgo: 0 };
      byKey[key].total++;
      byKey[key].riesgo += (r.pred === 1 ? 1 : 0);
    });
    const tbRes = $("tbResumen"); if (tbRes) tbRes.innerHTML = "";
    Object.entries(byKey).forEach(([k, agg]) => {
      const [periodo, clase, materia] = k.split("||");
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${periodo}</td><td>${clase}</td><td>${materia}</td><td>${agg.total}</td><td>${agg.riesgo}</td><td>${fmtPct(agg.total ? agg.riesgo / agg.total : 0)}</td>`;
      tbRes && tbRes.appendChild(tr);
    });
  }

  // --------- PICK TRAIN SET ----------
  function pickTrainSet(raw, filters){
    // prioriza entrenar con el mismo ciclo seleccionado
    let base = raw;
    if (filters.ciclo_id !== 'all') {
      base = raw.filter(r => String(r.ciclo_id) === String(filters.ciclo_id));
    }
    const withYSameCycle = base.filter(r => r.y_reprobado !== null && r.y_reprobado !== undefined);
    if (withYSameCycle.length >= 2) return withYSameCycle;

    // fallback: con cualquier ciclo que tenga etiqueta
    const withYAny = raw.filter(r => r.y_reprobado !== null && r.y_reprobado !== undefined);
    return withYAny;
  }

  // --------- PIPELINE ----------
  function runPipeline(){
    DATA = filterData(RAW);           // lo que se va a predecir / mostrar

    if (!DATA.length) {
      setStatus("Sin datos para ese filtro", true);
      clearTables();
      return;
    }

    // TRAIN: con etiqueta disponible (y_reprobado)
    const TRAIN = pickTrainSet(RAW, FILTERS);
    X = TRAIN.map(r => [ Number(r.asistencia_pct || 0), Number(r.parciales_avg || 0) ]);
    y = TRAIN.map(r => Number(r.y_reprobado || 0));

    if (X.length >= 2) {
      setStatus("Entrenando modelos…");
      trainAndEvaluate();
    } else {
      setStatus("Muy pocos ejemplos con etiqueta para entrenar (≥2). Se mostrarán predicciones si es posible.", true);
    }

    setStatus("Prediciendo alumnos…");
    const PREDS = predictMany(DATA);
    renderTables(PREDS);
    setStatus("Listo ✅");
  }

  // --------- INIT ----------
  async function init() {
    try {
      setStatus("Cargando datos…");
      const res = await fetch(DATASET_URL);
      const js = await res.json();
      if (!js.ok) throw new Error(js.error || "Error de API");

      RAW = js.data || [];
      if (!RAW.length) { setStatus("Dataset vacío", true); return; }

      buildFilters(); // construye selects y ejecuta la primera corrida
      runPipeline();
    } catch (e) {
      console.error(e);
      setStatus("Error: " + e.message, true);
    }
  }

  document.addEventListener("DOMContentLoaded", init);
})();
