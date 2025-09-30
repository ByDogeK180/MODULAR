// assets/api/predicciones_auto.js
(function () {
  const DATASET_URL = "assets/api/dataset_admin.php";

  // ===== Colores para charts =====
  const COLORS = {
    tp: "#22c55e",  // verde
    tn: "#60a5fa",  // azul
    fp: "#f59e0b",  // ámbar
    fn: "#ef4444",  // rojo
    zero: "#60a5fa",
    one:  "#ef4444"
  };

  // ===== Parámetros =====
  const THR = 0.5;                  // umbral de riesgo
  const PASS_THRESHOLD = 6.0;       // nota mínima final
  const TOTAL_PERIODOS_DEFAULT = 3; // # periodos si no se detecta
  const ATTEND_RULE = 0.60;         // regla: asistencia < 60% => reprobado

  // ===== Estado global =====
  let RAW = [];   // dataset completo
  let DATA = [];  // dataset filtrado
  const FILTERS = { ciclo_key: 'all', periodo_id: 'all', clase_key: 'all' };

  // Datos ML
  let X = [], y = [];
  let cartModel = null, rfModel = null;

  // Gráficas
  let cmChart = null, distChart = null;

  // ===== Helpers =====
  const $ = (id) => document.getElementById(id);
  const setStatus = (msg, err = false) => {
    const el = $("status");
    if (el) { el.textContent = msg; el.style.color = err ? "#e74c3c" : "#6b7280"; }
  };
  const fmtPct  = (x) => (x * 100).toFixed(1) + "%";
  const clamp01 = (v) => (Number.isFinite(+v) ? Math.max(0, Math.min(1, +v)) : 0);
  const safeNum = (v, d=0) => Number.isFinite(+v) ? +v : d;

  // ===== Chart defaults =====
  function applyChartDefaults(){
    if (!window.Chart || !Chart.defaults) return;
    if (Chart.defaults.plugins && Chart.defaults.plugins.legend && Chart.defaults.plugins.legend.labels) {
      Chart.defaults.plugins.legend.labels.boxWidth = 12;
    } else if (Chart.defaults.global && Chart.defaults.global.legend && Chart.defaults.global.legend.labels) {
      Chart.defaults.global.legend.labels.boxWidth = 12;
    }
    if ('color' in Chart.defaults)      Chart.defaults.color = "#374151";
    if ('borderColor' in Chart.defaults) Chart.defaults.borderColor = "rgba(55,65,81,.15)";
  }

  // ===== Ciclos (tolerante a distintos nombres de campo) =====
  function getCicloId(r){
    return r.ciclo_id ?? r.cicloId ?? r.id_ciclo ?? r.cicloID ?? null;
  }
  function getCicloNombre(r){
    return r.ciclo_nombre ?? r.cicloNombre ?? r.nombre_ciclo ?? null;
  }
  // Key estable: usa id si existe; si no, usa nombre normalizado
  function getCicloKey(r){
    const id = getCicloId(r);
    if (id != null) return "id::" + String(id);
    const name = getCicloNombre(r);
    return (name != null) ? "name::" + String(name).trim().toLowerCase() : null;
  }

  // ===== Clase =====
  function getClaseKey(r){
    if (r.clase_id != null) return 'id::' + String(r.clase_id);
    const g  = (r.grado != null ? r.grado : '-');
    const gr = (r.grupo != null ? r.grupo : '-');
    return `gg::${g}-${gr}`;
  }
  function getClaseLabel(r){
    if (r.clase_nombre) return r.clase_nombre;
    if (r.clase_id != null && r.grado == null && r.grupo == null) return `Clase ${r.clase_id}`;
    const g  = (r.grado != null ? r.grado : '-');
    const gr = (r.grupo != null ? r.grupo : '-');
    return `${g}-${gr}`;
  }

  // ===== Filtrar por selects =====
  function filterData(raw){
    let arr = raw.slice();

    if (FILTERS.ciclo_key !== 'all') {
      arr = arr.filter(r => String(getCicloKey(r)) === String(FILTERS.ciclo_key));
    }
    if (FILTERS.periodo_id !== 'all') {
      const getPer = (r)=> r.periodo_id ?? r.periodoId ?? r.id_periodo ?? r.periodo ?? null;
      arr = arr.filter(r => String(getPer(r)) === String(FILTERS.periodo_id));
    }
    if (FILTERS.clase_key !== 'all') {
      arr = arr.filter(r => getClaseKey(r) === FILTERS.clase_key);
    }
    return arr;
  }

  function buildFilters() {
    const $ciclo = $('cicloSelect');
    const $per   = $('periodoSelect');
    const $clase = $('claseSelect');
    if (!$ciclo || !$per || !$clase) return;

    // --- CICLOS ---
    const ciclosMap = new Map(); // key -> label
    RAW.forEach(r => {
      const key = getCicloKey(r);
      if (!key) return;
      const label = getCicloNombre(r) ?? (getCicloId(r)!=null ? `Ciclo ${getCicloId(r)}` : "Ciclo");
      ciclosMap.set(String(key), String(label));
    });

    $ciclo.innerHTML = `<option value="all">Todos los ciclos</option>`;
    Array.from(ciclosMap.entries())
      .sort((a,b)=> String(a[1]).localeCompare(String(b[1]), undefined, {numeric:true, sensitivity:'base'}))
      .forEach(([key, label]) => $ciclo.innerHTML += `<option value="${key}">${label}</option>`);
    if (!ciclosMap.has(FILTERS.ciclo_key)) FILTERS.ciclo_key = 'all';
    $ciclo.value = FILTERS.ciclo_key;

    // --- Periodos dependientes ---
    function refreshPeriodos(run=true){
      let base = RAW;
      if (FILTERS.ciclo_key !== 'all') {
        base = RAW.filter(r => String(getCicloKey(r)) === String(FILTERS.ciclo_key));
      }
      const perMap = new Map(); // id -> nombre
      base.forEach(r => {
        const pid = r.periodo_id ?? r.periodoId ?? r.id_periodo ?? r.periodo ?? null;
        if (pid != null) perMap.set(String(pid), r.periodo_nombre ?? r.periodoNombre ?? r.nombre_periodo ?? String(pid));
      });

      const sorted = Array.from(perMap.entries())
        .sort((a,b)=> String(a[0]).localeCompare(String(b[0]), undefined, {numeric:true, sensitivity:'base'}));

      $per.innerHTML = `<option value="all">Todos los periodos</option>`;
      for (const [id, name] of sorted) $per.innerHTML += `<option value="${id}">${name}</option>`;

      // ✅ NO auto-seleccionar: obligamos al usuario a elegir el periodo
      FILTERS.periodo_id = 'all';
      $per.value = 'all';

      refreshClases(run);
    }

    function refreshClases(run=true){
      let base = RAW;
      if (FILTERS.ciclo_key !== 'all') {
        base = base.filter(r => String(getCicloKey(r)) === String(FILTERS.ciclo_key));
      }
      if (FILTERS.periodo_id !== 'all') {
        const getPer = (r)=> r.periodo_id ?? r.periodoId ?? r.id_periodo ?? r.periodo ?? null;
        base = base.filter(r => String(getPer(r)) === String(FILTERS.periodo_id));
      }

      const clsMap = new Map(); // key -> label
      base.forEach(r => {
        const key = getClaseKey(r);
        const label = getClaseLabel(r);
        if (key) clsMap.set(key, label);
      });

      const sorted = Array.from(clsMap.entries())
        .sort((a,b)=> String(a[1]).localeCompare(String(b[1]), undefined, {numeric:true, sensitivity:'base'}));

      $clase.innerHTML = `<option value="all">Todas las clases</option>`;
      for (const [k, label] of sorted) $clase.innerHTML += `<option value="${k}">${label}</option>`;

      FILTERS.clase_key = 'all';
      $clase.value = 'all';

      // ✅ Solo correr si ya hay ciclo y periodo definidos
      if (run && FILTERS.ciclo_key !== 'all' && FILTERS.periodo_id !== 'all') {
        runPipeline();
      } else {
        setStatus("Selecciona CICLO y PERIODO para ver resultados");
        // limpiar tablas/plots
        const tb1=$('tbRiesgo'); if(tb1) tb1.innerHTML='';
        const tb2=$('tbResumen'); if(tb2) tb2.innerHTML='';
        ['statTotal','statRiesgo','statRate'].forEach(id => { const el=$(id); if(el) el.textContent='—'; });
        if (cmChart){ cmChart.destroy(); cmChart=null; }
        if (distChart){ distChart.destroy(); distChart=null; }
      }
    }

    // eventos
    $ciclo.onchange = () => { 
      FILTERS.ciclo_key = $ciclo.value; 
      refreshPeriodos(true); 
    };
    $per.onchange   = () => { 
      FILTERS.periodo_id = $per.value; 
      refreshClases(true); 
    };
    $clase.onchange = () => { 
      FILTERS.clase_key = $clase.value; 
      if (FILTERS.ciclo_key !== 'all' && FILTERS.periodo_id !== 'all') {
        runPipeline();
      }
    };

    const $btn = $('btnReset');
    if ($btn) $btn.onclick = () => {
      FILTERS.ciclo_key  = 'all';
      FILTERS.periodo_id = 'all';
      FILTERS.clase_key  = 'all';
      $ciclo.value = 'all';
      refreshPeriodos(true);
    };

    // arranque inicial: NO mostramos datos
    refreshPeriodos(false);
    refreshClases(false);
  }

  // ===== Detección de #periodos por ciclo =====
  function detectTotalPeriodsByKey(cicloKey, raw){
    if (cicloKey === 'all') return 0;
    const set = new Set(
      raw.filter(r => String(getCicloKey(r)) === String(cicloKey))
         .map(r => r.periodo_id ?? r.periodoId ?? r.id_periodo ?? r.periodo ?? null)
         .filter(v => v !== null && v !== undefined)
    );
    return set.size || 0;
  }

  function cycleProjectionForRow(row, cicloKey, raw, totalDefault = TOTAL_PERIODOS_DEFAULT, target = PASS_THRESHOLD){
    const total = detectTotalPeriodsByKey(cicloKey, raw) || totalDefault;

    // filas del mismo alumno-materia-ciclo
    const same = raw.filter(
      r => String(getCicloKey(r)) === String(cicloKey)
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

  // ===== Confusión y gráficas =====
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

    const labels = [
      "Aciertos (Reprobados)",
      "Aciertos (Aprobados)",
      "Falsos Reprobados",
      "Falsos Aprobados"
    ];
    const values = [cm.TP, cm.TN, cm.FP, cm.FN];
    const bg = [COLORS.tp + "33", COLORS.tn + "33", COLORS.fp + "33", COLORS.fn + "33"];
    const border = [COLORS.tp, COLORS.tn, COLORS.fp, COLORS.fn];

    if (cmChart) cmChart.destroy();
    cmChart = new Chart(ctx, {
      type: "bar",
      data: { labels, datasets: [{ label: "Conteo", data: values, backgroundColor: bg, borderColor: border, borderWidth: 1.5, borderRadius: 6 }] },
      options: {
        plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: "rgba(55,65,81,.08)" } } }
      }
    });
  }

  function drawDist(y) {
    const ctx = $("distChart");
    if (!ctx || typeof Chart === "undefined") return;

    const zeros = y.filter(v => v === 0).length;
    const ones  = y.filter(v => v === 1).length;

    if (distChart) distChart.destroy();
    distChart = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: ["Aprobados (0)", "Reprobados (1)"],
        datasets: [{
          data: [zeros, ones],
          backgroundColor: [COLORS.zero, COLORS.one],
          borderColor: "#ffffff",
          borderWidth: 2,
          hoverOffset: 6
        }]
      },
      options: { plugins: { legend: { position: "top" } }, cutout: "60%" }
    });
  }

  // ===== Modelos =====
  function getCARTCtor() {
    const c1 = (window.ML && window.ML.Cart && window.ML.Cart.DecisionTreeClassifier);
    const c2 = (window.ML && window.ML.DecisionTreeClassifier);
    return (typeof c1 === "function") ? c1 :
           (typeof c2 === "function") ? c2 : null;
  }

  // RF: prob de una fila por votación
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
    const idx = [...X.keys()].sort(() => Math.random() - 0.5);
    let tr = [], te = [];
    if (idx.length <= 1) { tr = idx.slice(); te = idx.slice(); }
    else {
      let cut = Math.floor(idx.length * 0.8);
      if (cut <= 0) cut = 1;
      if (cut >= idx.length) cut = idx.length - 1;
      tr = idx.slice(0, cut);
      te = idx.slice(cut);
    }
    const Xtr = tr.map(i => X[i]), ytr = tr.map(i => y[i]);

    if (!Xtr.length || !ytr.length) {
      setStatus("Dataset insuficiente para entrenar (necesitas ≥2 filas).", true);
      return;
    }

    // CART (o RF de 1 árbol si no está el CART)
    const CARTCtor = getCARTCtor();
    if (CARTCtor) {
      setStatus("Entrenando CART…");
      try {
        cartModel = new CARTCtor({ gainFunction: 'gini', maxDepth: 5, minNumSamples: 2 });
        cartModel.train(Xtr, ytr);
      } catch (e) {
        console.warn("CART falló, uso RF(1):", e);
        setStatus("CART no disponible, usando RF(1) como árbol", true);
        cartModel = new ML.RandomForestClassifier({ nEstimators: 1, maxFeatures: 2, replacement: true, seed: 7 });
        cartModel.train(Xtr, ytr);
      }
    } else {
      console.warn("No DecisionTreeClassifier; uso RF(1).");
      setStatus("CART no encontrado, usando RF(1) como árbol", true);
      cartModel = new ML.RandomForestClassifier({ nEstimators: 1, maxFeatures: 2, replacement: true, seed: 7 });
      cartModel.train(Xtr, ytr);
    }

    // Random Forest
    setStatus("Entrenando Random Forest…");
    rfModel = new ML.RandomForestClassifier({ nEstimators: 200, maxFeatures: 2, replacement: true, seed: 42 });
    rfModel.train(Xtr, ytr);
  }

  // ===== Predicción =====
  function predictMany(rows) {
    return rows.map(r => {
      const x = [[ safeNum(r.asistencia_pct), safeNum(r.parciales_avg) ]];

      // CART -> 0/1 como "prob"
      let cartPred = 0;
      try {
        const p = cartModel && cartModel.predict ? cartModel.predict(x)[0] : 0;
        cartPred = (p === true) ? 1 : (p === false) ? 0 : Number(p);
      } catch { cartPred = 0; }
      const probCart = clamp01(cartPred);

      // RF -> voto promedio
      const probRf = clamp01(rfProbOne(x));

      // Ensamble
      let probFinal = clamp01((probCart + probRf) / 2);
      let pred = probFinal >= THR ? 1 : 0;

      // Regla por asistencia
      const asis = safeNum(r.asistencia_pct);
      if (asis < ATTEND_RULE) {
        pred = 1;
        probFinal = Math.max(probFinal, THR);
      }

      // Proyección fin de ciclo (por ciclo_key actual o detectado)
      const cKey = getCicloKey(r);
      const proj = cycleProjectionForRow(r, cKey, RAW, TOTAL_PERIODOS_DEFAULT, PASS_THRESHOLD);
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

  // ===== Gráficas (agregado por alumno único) =====
  function updateChartsForFilter() {
    const LAB = DATA.filter(r => r.y_reprobado !== null && r.y_reprobado !== undefined);
    const byStudent = new Map();

    for (const r of LAB) {
      const sid = String(r.estudiante_id);
      const ytrue = safeNum(r.y_reprobado);
      const x = [[ safeNum(r.asistencia_pct), safeNum(r.parciales_avg) ]];

      let cartPred = 0;
      try {
        const p = cartModel && cartModel.predict ? cartModel.predict(x)[0] : 0;
        cartPred = (p === true) ? 1 : (p === false) ? 0 : Number(p);
      } catch { cartPred = 0; }
      const probCart = clamp01(cartPred);
      const probRf = clamp01(rfProbOne(x));
      let probFinal = clamp01((probCart + probRf) / 2);
      let ypred = probFinal >= THR ? 1 : 0;

      const asis = safeNum(r.asistencia_pct);
      if (asis < ATTEND_RULE) {
        ypred = 1;
        probFinal = Math.max(probFinal, THR);
      }

      const cur = byStudent.get(sid) || { yTrue: 0, yPred: 0 };
      cur.yTrue = Math.max(cur.yTrue, ytrue);
      cur.yPred = Math.max(cur.yPred, ypred);
      byStudent.set(sid, cur);
    }

    const yTrueArr = [], yPredArr = [];
    byStudent.forEach(v => { yTrueArr.push(v.yTrue); yPredArr.push(v.yPred); });

    if (!yTrueArr.length) {
      drawCM({ TP:0, TN:0, FP:0, FN:0 });
      drawDist([]);
      return;
    }

    drawCM(confusion(yTrueArr, yPredArr));
    drawDist(yTrueArr);
  }

  // ===== Render de tablas =====
  function renderTables(PREDS) {
    // === Agrupar a 1 fila por ALUMNO ===
    const byStu = new Map();
    for (const r of PREDS) {
      const id = String(r.estudiante_id);
      const fullName = `${r.alumno_nombre || ""} ${r.alumno_apellido || ""}`.trim();

      const cur = byStu.get(id) || {
        alumno: fullName,
        n: 0,
        asisSum: 0,
        promSum: 0,
        maxProb: -1,
        riesgo: 0,
        worstRow: null,   // materia con PEOR promedio
        worstAvg: +Infinity,
      };

      const asis = safeNum(r.asistencia_pct);
      if (Number.isFinite(asis)) cur.asisSum += asis;

      const prom = safeNum(r.parciales_avg);
      if (Number.isFinite(prom)) {
        cur.promSum += prom;
        if (prom < cur.worstAvg) { cur.worstAvg = prom; cur.worstRow = r; }
      }

      const prob = safeNum(r.prob_final);
      if (prob > cur.maxProb) cur.maxProb = prob;

      cur.riesgo = Math.max(cur.riesgo, Number(r.pred || 0)); // si alguna materia predice 1
      cur.n += 1;

      byStu.set(id, cur);
    }

    // KPIs (alumno único)
    const alumnosUnicos   = byStu.size;
    const alumnosEnRiesgo = Array.from(byStu.values()).filter(v => v.riesgo === 1).length;

    if ($("statTotal"))  $("statTotal").textContent  = alumnosUnicos;
    if ($("statRiesgo")) $("statRiesgo").textContent = alumnosEnRiesgo;
    if ($("statRate"))   $("statRate").textContent   = fmtPct(alumnosUnicos ? alumnosEnRiesgo / alumnosUnicos : 0);

    // Construir filas agregadas (materia = peor promedio)
    const rows = Array.from(byStu.values()).map(v => {
      const worst = v.worstRow || {}; // si todos iguales, toma cualquiera
      const asisProm   = v.n ? v.asisSum / v.n : 0;
      const promGlobal = v.n ? v.promSum / v.n : 0;

      return {
        alumno: v.alumno,
        materia: worst.materia_nombre || '-',               // info contextual
        periodo: worst.periodo_nombre || worst.periodo_id || '-', // contexto
        clase: (worst.grado != null || worst.grupo != null)
          ? `${worst.grado || '-'}-${worst.grupo || '-'}`
          : getClaseLabel(worst),
        asis: asisProm,
        prom: promGlobal,
        prob: clamp01(v.maxProb),                           // para ordenar por riesgo
        riesgo: v.riesgo ? 1 : 0,
        reqRest: safeNum(worst.req_avg_rest, 0),            // contexto de esa materia
        estado: worst.estado_ciclo || ""
      };
    });

    // ordenar por prob. máxima (desc) y limitar
    rows.sort((a,b) => b.prob - a.prob);
    const top = rows.slice(0, 100);

    // Pintar tabla "Top alumnos…"
    const tbody = $("tbRiesgo"); if (tbody) tbody.innerHTML = "";
    top.forEach(r => {
      const asisTxt = (r.asis * 100).toFixed(1) + "%";
      const asisBadge = r.asis >= ATTEND_RULE
        ? `<span class="badge-soft badge-soft-success">${asisTxt} ✓</span>`
        : `<span class="badge-soft badge-soft-danger">${asisTxt} ✗</span>`;

      const riesgoBadge = r.riesgo
        ? '<span class="badge-soft badge-soft-danger">Reprobará</span>'
        : '<span class="badge-soft badge-soft-success">No reprobará</span>';

      const est = String(r.estado || "").normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase();
      let estadoBadge =
        est.includes("aun puede pasar") ? '<span class="badge-soft badge-soft-info">Aún puede pasar</span>' :
        est.includes("muy dificil")     ? '<span class="badge-soft badge-soft-warning">Muy difícil</span>' :
        est.includes("aprobado")        ? '<span class="badge-soft badge-soft-success">Aprobado (cierre)</span>' :
        est.includes("reprobado")       ? '<span class="badge-soft badge-soft-danger">Reprobado (cierre)</span>' :
                                          '<span class="badge-soft badge-soft-info">—</span>';

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${r.alumno}</td>
        <td>${r.materia}</td>
        <td>${r.periodo}</td>
        <td>${r.clase}</td>
        <td>${asisBadge}</td>
        <td>${r.prom.toFixed(2)}</td>
        <td>${r.prob.toFixed(2)}</td>
        <td>${riesgoBadge}</td>
        <td>${r.reqRest.toFixed(2)}</td>
        <td>${estadoBadge}</td>
      `;
      tbody && tbody.appendChild(tr);
    });

    // === Resumen por clase / periodo (SIN materia) ===
    const byKey = {};
    PREDS.forEach(r => {
      const periodo =
        r.periodo_nombre ?? r.periodoNombre ?? r.nombre_periodo ??
        r.periodo_id ?? r.periodo ?? '-';

      const clase = (r.grado != null || r.grupo != null)
        ? `${r.grado || '-'}-${r.grupo || '-'}`
        : getClaseLabel(r);

      const key = `${periodo}||${clase}`;
      if (!byKey[key]) byKey[key] = { total: 0, riesgo: 0 };
      byKey[key].total++;
      byKey[key].riesgo += (r.pred === 1 ? 1 : 0);
    });

    const tbRes = $("tbResumen"); if (tbRes) tbRes.innerHTML = "";
    Object.entries(byKey).forEach(([k, agg]) => {
      const [periodo, clase] = k.split("||");
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${periodo}</td>
        <td>${clase}</td>
        <td>${agg.total}</td>
        <td>${agg.riesgo}</td>
        <td>${fmtPct(agg.total ? agg.riesgo / agg.total : 0)}</td>`;
      tbRes && tbRes.appendChild(tr);
    });
  }

  // ===== Dataset para entrenar =====
  function pickTrainSet(raw, filters){
    let base = raw;
    if (filters.ciclo_key !== 'all') {
      base = raw.filter(r => String(getCicloKey(r)) === String(filters.ciclo_key));
    }
    const getY = (r)=> r.y_reprobado !== null && r.y_reprobado !== undefined;
    const withYSameCycle = base.filter(getY);
    if (withYSameCycle.length >= 2) return withYSameCycle;
    return raw.filter(getY);
  }

  // ===== Pipeline =====
  function runPipeline(){
    // ✅ Guardia: no ejecutar si faltan filtros clave
    if (FILTERS.ciclo_key === 'all' || FILTERS.periodo_id === 'all') {
      setStatus("Selecciona CICLO y PERIODO para ver resultados");
      return;
    }

    DATA = filterData(RAW);

    if (!DATA.length) {
      setStatus("Sin datos para ese filtro", true);
      ['statTotal','statRiesgo','statRate'].forEach(id => { const el=$(id); if(el) el.textContent='—'; });
      const tb1=$('tbRiesgo'); if(tb1) tb1.innerHTML='';
      const tb2=$('tbResumen'); if(tb2) tb2.innerHTML='';
      if (cmChart){ cmChart.destroy(); cmChart=null; }
      if (distChart){ distChart.destroy(); distChart=null; }
      return;
    }

    // Entrenar con y_reprobado
    const TRAIN = pickTrainSet(RAW, FILTERS);
    X = TRAIN.map(r => [ safeNum(r.asistencia_pct), safeNum(r.parciales_avg) ]);
    y = TRAIN.map(r => safeNum(r.y_reprobado));

    if (X.length >= 2) {
      setStatus("Entrenando modelos…");
      trainAndEvaluate();
    } else {
      setStatus("Muy pocos ejemplos con etiqueta para entrenar (≥2). Se mostrarán predicciones si es posible.", true);
    }

    setStatus("Prediciendo alumnos…");
    const PREDS = predictMany(DATA);
    renderTables(PREDS);

    updateChartsForFilter();

    setStatus("");
  }

  // ===== Init =====
  async function init() {
    try {
      setStatus("Cargando datos…");
      const res = await fetch(DATASET_URL);
      const js = await res.json();
      if (!js.ok) throw new Error(js.error || "Error de API");

      RAW = js.data || [];
      if (!RAW.length) { setStatus("Dataset vacío", true); return; }

      applyChartDefaults();
      buildFilters();
      // ❌ NO ejecutar runPipeline aquí
      setStatus("Selecciona CICLO y PERIODO para ver resultados");
    } catch (e) {
      console.error(e);
      setStatus("Error: " + e.message, true);
    }
  }

  document.addEventListener("DOMContentLoaded", init);
})();
