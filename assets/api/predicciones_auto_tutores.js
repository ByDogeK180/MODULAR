// assets/api/predicciones_auto_tutores.js
(function () {
  const DATASET_URL = "assets/api/dataset_tutores.php";

  // ===== Colores =====
  const COLORS = {
    tp: "#22c55e",
    tn: "#60a5fa",
    fp: "#f59e0b",
    fn: "#ef4444",
    zero: "#60a5fa",
    one:  "#ef4444"
  };

  // ===== Parámetros =====
  const THR = 0.5;                  // umbral de riesgo
  const PASS_THRESHOLD = 6.0;       // nota mínima final
  const TOTAL_PERIODOS_DEFAULT = 3; // # periodos si no se detecta
  const ATTEND_RULE = 0.60;         // regla: asistencia < 60% => reprobado

  // ===== Estado =====
  let RAW = [];
  let DATA = [];
  const FILTERS = { ciclo_key: 'all', periodo_id: 'all', clase_key: 'all' };

  // ML
  let X = [], y = [];
  let cartModel = null, rfModel = null;

  // Charts
  let cmChart = null, distChart = null;

  // ===== Helpers =====
  const $ = (id) => document.getElementById(id);
  const setStatus = (msg, err=false) => {
    const el = $("status");
    if (el) { el.textContent = msg; el.style.color = err ? "#e74c3c" : "#6b7280"; }
  };
  const fmtPct  = (x) => (x * 100).toFixed(1) + "%";
  const clamp01 = (v) => (Number.isFinite(+v) ? Math.max(0, Math.min(1, +v)) : 0);
  const safeNum = (v, d=0) => Number.isFinite(+v) ? +v : d;

  // ===== Chart defaults ===== (igual a docentes)
  function applyChartDefaults() {
    if (!window.Chart || !Chart.defaults) return;
    if (Chart.defaults.plugins?.legend?.labels) {
      Chart.defaults.plugins.legend.labels.boxWidth = 12;
    } else if (Chart.defaults.global?.legend?.labels) {
      Chart.defaults.global.legend.labels.boxWidth = 12;
    }
    if ('color' in Chart.defaults)      Chart.defaults.color = "#374151";
    if ('borderColor' in Chart.defaults) Chart.defaults.borderColor = "rgba(55,65,81,.15)";
  }

  // ===== Ciclo =====
  function getCicloId(r){ return r.ciclo_id ?? r.cicloId ?? r.id_ciclo ?? r.cicloID ?? null; }
  function getCicloNombre(r){ return r.ciclo_nombre ?? r.cicloNombre ?? r.nombre_ciclo ?? null; }
  function getCicloKey(r){
    const id = getCicloId(r);
    if (id != null) return "id::" + String(id);
    const name = getCicloNombre(r);
    return (name != null) ? "name::" + String(name).trim().toLowerCase() : null;
  }

  // ===== Clase =====
  function getClaseKey(r){
    if (r.clase_id != null) return 'id::' + String(r.clase_id);
    const g  = (r.grado ?? '-');
    const gr = (r.grupo ?? '-');
    return `gg::${g}-${gr}`;
  }
  function getClaseLabel(r){
    if (r.clase_nombre) return r.clase_nombre;
    if (r.clase_id != null && r.grado == null && r.grupo == null) return `Clase ${r.clase_id}`;
    const g  = (r.grado ?? '-');
    const gr = (r.grupo ?? '-');
    return `${g}-${gr}`;
  }

  // ===== Filtros (Ciclo -> Periodo -> Clase) =====
  function filterData(raw){
    const isActive = (v) => v !== undefined && v !== null && v !== '' && v !== 'all';
    let arr = raw.slice();

    if (isActive(FILTERS.ciclo_key)) {
      arr = arr.filter(r => String(getCicloKey(r)) === String(FILTERS.ciclo_key));
    }
    if (isActive(FILTERS.periodo_id)) {
      const getPer = (r)=> r.periodo_id ?? r.periodoId ?? r.id_periodo ?? r.periodo ?? null;
      arr = arr.filter(r => String(getPer(r)) === String(FILTERS.periodo_id));
    }
    if (isActive(FILTERS.clase_key)) {
      arr = arr.filter(r => getClaseKey(r) === FILTERS.clase_key);
    }
    return arr;
  }

  function buildFilters(skipDefaults=false){
    const $ciclo = $('cicloSelect');
    const $per   = $('periodoSelect');
    const $clase = $('claseSelect');
    if (!$ciclo || !$per || !$clase) return;

    // Ciclos
    const ciclosMap = new Map();
    RAW.forEach(r => {
      const key = getCicloKey(r);
      if (!key) return;
      const label = getCicloNombre(r) ?? (getCicloId(r)!=null ? `Ciclo ${getCicloId(r)}` : "Ciclo");
      ciclosMap.set(String(key), String(label));
    });

    $ciclo.innerHTML = `<option value="" disabled selected>Seleccione un ciclo</option>`;
    Array.from(ciclosMap.entries())
      .sort((a,b)=> String(a[1]).localeCompare(String(b[1]), undefined, {numeric:true, sensitivity:'base'}))
      .forEach(([key, label]) => $ciclo.innerHTML += `<option value="${key}">${label}</option>`);

    // Periodos dependientes
    function refreshPeriodos(run=true, skip=false){
      let base = RAW;
      if (FILTERS.ciclo_key !== 'all') {
        base = base.filter(r => String(getCicloKey(r)) === String(FILTERS.ciclo_key));
      }
      const perMap = new Map();
      base.forEach(r => {
        const pid = r.periodo_id ?? r.periodoId ?? r.id_periodo ?? r.periodo ?? null;
        if (pid != null) perMap.set(String(pid), r.periodo_nombre ?? r.periodoNombre ?? r.nombre_periodo ?? String(pid));
      });
      const sorted = Array.from(perMap.entries())
        .sort((a,b)=> String(a[0]).localeCompare(String(b[0]), undefined, {numeric:true, sensitivity:'base'}));
      $per.innerHTML = `<option value="" disabled selected>Seleccione un periodo</option>`;
      sorted.forEach(([id, name]) => $per.innerHTML += `<option value="${id}">${name}</option>`);
      if (skip) { FILTERS.periodo_id = ''; $per.value = ''; }
    }

    // Clases dependientes
    function refreshClases(run=true, skip=false){
      let base = RAW;
      if (FILTERS.ciclo_key !== 'all') {
        base = base.filter(r => String(getCicloKey(r)) === String(FILTERS.ciclo_key));
      }
      if (FILTERS.periodo_id !== 'all') {
        const getPer = (r)=> r.periodo_id ?? r.periodoId ?? r.id_periodo ?? r.periodo ?? null;
        base = base.filter(r => String(getPer(r)) === String(FILTERS.periodo_id));
      }
      const clsMap = new Map();
      base.forEach(r => { const k = getClaseKey(r); if (k) clsMap.set(k, getClaseLabel(r)); });
      const sorted = Array.from(clsMap.entries())
        .sort((a,b)=> String(a[1]).localeCompare(String(b[1]), undefined, {numeric:true, sensitivity:'base'}));
      $clase.innerHTML = `<option value="" disabled selected>Seleccione una clase</option>`;
      sorted.forEach(([k, label]) => $clase.innerHTML += `<option value="${k}">${label}</option>`);
      if (skip) { FILTERS.clase_key = ''; $clase.value = ''; }
    }

    // Eventos
    $ciclo.onchange = () => { FILTERS.ciclo_key = $ciclo.value;  refreshPeriodos(true,false); };
    $per.onchange   = () => { FILTERS.periodo_id = $per.value;   refreshClases(true,false);   };
    $clase.onchange = () => { FILTERS.clase_key = $clase.value;  runPipeline();               };

    const $btn = $('btnReset');
    if ($btn) $btn.onclick = () => {
      FILTERS.ciclo_key  = '';
      FILTERS.periodo_id = '';
      FILTERS.clase_key  = '';
      buildFilters(true);
      $ciclo.value = ''; $per.value = ''; $clase.value = '';
      clearUI(); setStatus("Seleccione un ciclo/período/clase para ver datos");
    };

    clearUI();
    setStatus("Seleccione un ciclo/período/clase para ver datos");
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
    const same = raw.filter(
      r => String(getCicloKey(r)) === String(cicloKey)
        && String(r.estudiante_id) === String(row.estudiante_id)
        && String(r.materia_id)    === String(row.materia_id)
    );
    let sum = 0, done = 0;
    for (const r of same) {
      const v = Number(r.parciales_avg);
      if (Number.isFinite(v)) { sum += v; done++; }
    }
    if (done === 0) return { needed: target, feasible: target <= 10, tot: total, done: 0, rem: total };
    const remaining = Math.max(0, total - done);
    if (remaining === 0) {
      const finalAvg = sum / done;
      return { needed: 0, feasible: finalAvg >= target, tot: total, done, rem: 0, finalAvg };
    }
    let needed = (target * total - sum) / remaining;
    if (!Number.isFinite(needed)) needed = 10;
    const needClamp = Math.min(10, Math.max(0, needed));
    return { needed: needClamp, feasible: needed <= 10, tot: total, done, rem: remaining };
  }

  // ===== Métricas =====
  const confusion = (yT, yP) => {
    let TP=0,TN=0,FP=0,FN=0;
    for (let i=0;i<yT.length;i++){
      if (yT[i]===1 && yP[i]===1) TP++;
      else if (yT[i]===0 && yP[i]===0) TN++;
      else if (yT[i]===0 && yP[i]===1) FP++;
      else if (yT[i]===1 && yP[i]===0) FN++;
    }
    return {TP,TN,FP,FN};
  };

  function drawCM(cm){
    const ctx = $("cmChart");
    if (!ctx || typeof Chart === "undefined") return;
    const labels = ["Aciertos (Reprobados)","Aciertos (Aprobados)","Falsos Reprobados","Falsos Aprobados"];
    const values = [cm.TP, cm.TN, cm.FP, cm.FN];
    const bg = [COLORS.tp+"33", COLORS.tn+"33", COLORS.fp+"33", COLORS.fn+"33"];
    const border = [COLORS.tp, COLORS.tn, COLORS.fp, COLORS.fn];
    if (cmChart) cmChart.destroy();
    cmChart = new Chart(ctx, {
      type: "bar",
      data: { labels, datasets: [{ label: "Conteo", data: values, backgroundColor: bg, borderColor: border, borderWidth: 1.5, borderRadius: 6 }] },
      options: { plugins:{legend:{display:false}}, scales:{ x:{grid:{display:false}}, y:{beginAtZero:true, grid:{color:"rgba(55,65,81,.08)"}} } }
    });
  }

  function drawDist(yArr){
    const ctx = $("distChart");
    if (!ctx || typeof Chart === "undefined") return;
    const zeros = yArr.filter(v => v===0).length;
    const ones  = yArr.filter(v => v===1).length;
    if (distChart) distChart.destroy();
    distChart = new Chart(ctx, {
      type: "doughnut",
      data: { labels:["Aprobados (0)","Reprobados (1)"], datasets:[{ data:[zeros,ones], backgroundColor:[COLORS.zero, COLORS.one], borderColor:"#fff", borderWidth:2, hoverOffset:6 }] },
      options: { plugins:{legend:{position:"top"}}, cutout:"60%" }
    });
  }

  // ===== Modelos ===== (mismo pipeline que docentes)
  function getCARTCtor(){
    const c1 = (window.ML && window.ML.Cart && window.ML.Cart.DecisionTreeClassifier);
    const c2 = (window.ML && window.ML.DecisionTreeClassifier);
    return (typeof c1 === "function") ? c1 :
           (typeof c2 === "function") ? c2 : null;
  }

  function rfProbOne(x){
    if (!rfModel || !Array.isArray(rfModel.estimators)) return 0;
    let votes = 0, n = 0;
    for (const t of rfModel.estimators) {
      if (!t || typeof t.predict !== "function") continue;
      const p = t.predict(x)[0];
      const val = (p === true) ? 1 : (p === false) ? 0 : Number(p);
      if (Number.isFinite(val)) { votes += (val >= 0.5 ? 1 : 0); n++; }
    }
    return n>0 ? votes/n : 0;
  }

  function trainAndEvaluate(){
    const idx = [...X.keys()].sort(()=> Math.random()-0.5);
    let tr=[], te=[];
    if (idx.length <= 1) { tr=idx.slice(); te=idx.slice(); }
    else {
      let cut = Math.floor(idx.length*0.8);
      cut = Math.min(Math.max(cut,1), idx.length-1);
      tr = idx.slice(0,cut); te = idx.slice(cut);
    }
    const Xtr = tr.map(i=>X[i]), ytr = tr.map(i=>y[i]);

    if (!Xtr.length || !ytr.length) { setStatus("Dataset insuficiente para entrenar (≥2 filas).", true); return; }

    const CART = getCARTCtor();
    if (CART) {
      setStatus("Entrenando CART…");
      try {
        cartModel = new CART({ gainFunction:'gini', maxDepth:5, minNumSamples:2 });
        cartModel.train(Xtr, ytr);
      } catch (e) {
        console.warn("CART falló, uso RF(1):", e);
        setStatus("CART no disponible, usando RF(1) como árbol", true);
        cartModel = new ML.RandomForestClassifier({ nEstimators:1, maxFeatures:2, replacement:true, seed:7 });
        cartModel.train(Xtr, ytr);
      }
    } else {
      setStatus("CART no encontrado, usando RF(1) como árbol", true);
      cartModel = new ML.RandomForestClassifier({ nEstimators:1, maxFeatures:2, replacement:true, seed:7 });
      cartModel.train(Xtr, ytr);
    }

    setStatus("Entrenando Random Forest…");
    rfModel = new ML.RandomForestClassifier({ nEstimators:200, maxFeatures:2, replacement:true, seed:42 });
    rfModel.train(Xtr, ytr);
  }

  // ===== Predicción fila a fila =====
  function predictMany(rows){
    return rows.map(r => {
      const x = [[ safeNum(r.asistencia_pct), safeNum(r.parciales_avg) ]];

      // CART -> 0/1 como “prob”
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

      // Regla dura por asistencia
      const asis = safeNum(r.asistencia_pct);
      if (asis < ATTEND_RULE) { pred = 1; probFinal = Math.max(probFinal, THR); }

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

  // ===== Gráficas agregadas por alumno (1 por hijo) =====
  function updateChartsForFilter(){
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
      const probRf   = clamp01(rfProbOne(x));
      let probFinal  = clamp01((probCart + probRf) / 2);
      let ypred      = probFinal >= THR ? 1 : 0;

      const asis = safeNum(r.asistencia_pct);
      if (asis < ATTEND_RULE) { ypred = 1; probFinal = Math.max(probFinal, THR); }

      const cur = byStudent.get(sid) || { yTrue: 0, yPred: 0 };
      cur.yTrue = Math.max(cur.yTrue, ytrue);
      cur.yPred = Math.max(cur.yPred, ypred);
      byStudent.set(sid, cur);
    }

    const yTrueArr = [], yPredArr = [];
    byStudent.forEach(v => { yTrueArr.push(v.yTrue); yPredArr.push(v.yPred); });

    if (!yTrueArr.length) { drawCM({TP:0,TN:0,FP:0,FN:0}); drawDist([]); return; }
    drawCM(confusion(yTrueArr, yPredArr));
    drawDist(yTrueArr);
  }

  // ===== Tablas (un renglón por hijo, mostrando SU peor materia) =====
  function renderTables(PREDS){
    // Agregar a 1 fila por alumno
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
        worstRow: null,
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

      cur.riesgo = Math.max(cur.riesgo, Number(r.pred || 0));
      cur.n += 1;

      byStu.set(id, cur);
    }

    // KPIs
    const alumnosUnicos   = byStu.size;
    const alumnosEnRiesgo = Array.from(byStu.values()).filter(v => v.riesgo === 1).length;
    if ($("statTotal"))  $("statTotal").textContent  = alumnosUnicos;
    if ($("statRiesgo")) $("statRiesgo").textContent = alumnosEnRiesgo;
    if ($("statRate"))   $("statRate").textContent   = fmtPct(alumnosUnicos ? alumnosEnRiesgo/alumnosUnicos : 0);

    // Filas para tabla "tbRiesgo"
    const rows = Array.from(byStu.values()).map(v => {
      const worst = v.worstRow || {};
      const asisProm   = v.n ? v.asisSum / v.n : 0;
      const promGlobal = v.n ? v.promSum / v.n : 0;
      return {
        alumno: v.alumno || "—",
        materia: worst.materia_nombre || "—",
        periodo: worst.periodo_nombre || (worst.periodo_id ?? "—"),
        clase: (worst.grado != null || worst.grupo != null) ? `${worst.grado || '-'}-${worst.grupo || '-'}` : getClaseLabel(worst),
        asis: asisProm,
        prom: promGlobal,
        prob: v.maxProb,
        riesgo: v.riesgo,
        reqRest: (worst.req_avg_rest ?? 0),
        estadoCiclo: (worst.estado_ciclo || "—"),
      };
    });

    const tbody = $("tbRiesgo"); if (tbody) tbody.innerHTML = "";
    rows.forEach(r => {
      const riesgoBadge = r.riesgo ? '<span class="badge-soft badge-soft-danger">Alto</span>'
                                   : '<span class="badge-soft badge-soft-success">Bajo</span>';
      const asisBadge =
        r.asis >= 0.9 ? '<span class="badge-soft badge-soft-success">Alta</span>' :
        r.asis >= 0.7 ? '<span class="badge-soft badge-soft-warning">Media</span>' :
                        '<span class="badge-soft badge-soft-danger">Baja</span>';
      const est = (r.estadoCiclo || "").toLowerCase();
      const estadoBadge =
        est.includes("aún puede")    ? '<span class="badge-soft badge-soft-info">Aún puede pasar</span>' :
        est.includes("muy difícil")  ? '<span class="badge-soft badge-soft-warning">Muy difícil</span>' :
        est.includes("aprobado")     ? '<span class="badge-soft badge-soft-success">Aprobado (cierre)</span>' :
        est.includes("reprobado")    ? '<span class="badge-soft badge-soft-danger">Reprobado (cierre)</span>' :
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

    // Resumen por Clase / Periodo (sin materia)
    const byKey = {};
    rows.forEach(r => {
      const key = `${r.periodo}||${r.clase}`;
      if (!byKey[key]) byKey[key] = { total: 0, riesgo: 0 };
      byKey[key].total++;
      byKey[key].riesgo += (r.riesgo ? 1 : 0);
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
        <td>${fmtPct(agg.total ? agg.riesgo/agg.total : 0)}</td>`;
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
    const withY = base.filter(getY);
    return (withY.length >= 2) ? withY : raw.filter(getY);
  }

  // ===== UI helpers =====
  function clearUI(){
    ['statTotal','statRiesgo','statRate'].forEach(id => { const el=$(id); if (el) el.textContent='—'; });
    const tb1=$('tbRiesgo');  if (tb1) tb1.innerHTML='';
    const tb2=$('tbResumen'); if (tb2) tb2.innerHTML='';
    if (cmChart) { cmChart.destroy(); cmChart=null; }
    if (distChart) { distChart.destroy(); distChart=null; }
  }

  // ===== Pipeline =====
  function runPipeline() {
    // Entrena con lo disponible (independiente de “Ver detalle de”)
    const TRAIN = pickTrainSet(RAW, FILTERS);
    X = TRAIN.map(r => [safeNum(r.asistencia_pct), safeNum(r.parciales_avg)]);
    y = TRAIN.map(r => Number(r.y_reprobado || 0));
    trainAndEvaluate();

    // KPIs y gráfico globales: SIEMPRE con todos los hijos
    const PREDS_ALL = predictMany(RAW);
    renderResumenHijos(PREDS_ALL);

    // Detalle: filtros superiores + filtro “Ver detalle de”
    let filtered = filterData(RAW);
    const PREDS = predictMany(filtered);
    DATA = applyHijoFilter(PREDS);

    renderTables(DATA);
    updateChartsForFilter();
    renderRecomendaciones(DATA);
    setStatus(`Listo: ${DATA.length} filas (después de filtros).`);
  }

  async function load(){
    try {
      applyChartDefaults();
      setStatus("Cargando dataset…");
      const res = await fetch(DATASET_URL, { credentials: 'include' });
      const js = await res.json();
      if (!js || js.ok === false) throw new Error(js?.error || "Error de carga");
      RAW = Array.isArray(js.data) ? js.data : [];

      buildFilters(true);

      // enlazar cambio del selector de hijo (una vez cargado el DOM)
      const sel = $("selectHijo");
      if (sel && !sel.dataset.bound) { sel.addEventListener('change', runPipeline); sel.dataset.bound = "1"; }

      // poblar todo inmediatamente
      runPipeline();
    } catch (e) {
      console.error(e);
      setStatus("Error: "+e.message, true);
    }
  }

  // ======== RESUMEN GLOBAL DE HIJOS ========
  let chartHijos = null;

  function renderResumenHijos(rows) {
    const byStu = new Map();
    rows.forEach(r => {
      const id = String(r.estudiante_id);
      const nombre = `${r.alumno_nombre || ""} ${r.alumno_apellido || ""}`.trim();
      const cur = byStu.get(id) || { nombre, riesgo: 0, prob: 0 };
      cur.riesgo = Math.max(cur.riesgo, Number(r.pred || 0));
      cur.prob   = Math.max(cur.prob, Number(r.prob_final || 0));
      byStu.set(id, cur);
    });

    // KPIs globales
    $("statHijosTotal").textContent  = byStu.size;
    const enRiesgo = Array.from(byStu.values()).filter(v => v.riesgo === 1).length;
    $("statHijosRiesgo").textContent = enRiesgo;
    $("statHijosRate").textContent   = byStu.size ? ((enRiesgo/byStu.size)*100).toFixed(1) + "%" : "—";

    // Selector SIEMPRE con todos los hijos del tutor (independiente de otros filtros)
    const $sel = $("selectHijo");
    if ($sel) {
      const prev = $sel.value;
      $sel.innerHTML = '<option value="">Todos los hijos</option>';
      Array.from(byStu.entries()).forEach(([id, v]) => {
        const opt = document.createElement("option");
        opt.value = id;
        opt.textContent = v.nombre;
        $sel.appendChild(opt);
      });
      // conservar selección si sigue válida
      if (prev && [...$sel.options].some(o => o.value === prev)) $sel.value = prev;
    }

    // Gráfico comparativo (global)
    const ctx = $("chartHijos");
    if (ctx) {
      if (chartHijos) chartHijos.destroy();
      chartHijos = new Chart(ctx, {
        type: "bar",
        data: {
          labels: Array.from(byStu.values()).map(v => v.nombre),
          datasets: [{
            label: "Prob. de Riesgo",
            data: Array.from(byStu.values()).map(v => (v.prob * 100).toFixed(1)),
            backgroundColor: Array.from(byStu.values()).map(v =>
              v.riesgo ? "rgba(239,68,68,0.7)" : "rgba(34,197,94,0.7)"
            ),
            borderWidth: 1,
            borderRadius: 8,
            barPercentage: 0.6,
            categoryPercentage: 0.6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: true } },
          scales: { y: { beginAtZero: true, max: 100 }, x: { grid: { display: false } } }
        }
      });
    }
  }

  // ======== FILTRO POR HIJO ========
  function applyHijoFilter(rows) {
    const $sel = $("selectHijo");
    if (!$sel) return rows;
    const selId = $sel.value;
    if (!selId) return rows; // todos
    return rows.filter(r => String(r.estudiante_id) === selId);
  }

  function renderRecomendaciones(rows) {
    const cont = $("recomendacionesContainer");
    if (!cont) return;
    cont.innerHTML = "";

    // Agrupamos por alumno
    const byStu = new Map();
    rows.forEach(r => {
      const id = String(r.estudiante_id);
      const nombre = `${r.alumno_nombre || ""} ${r.alumno_apellido || ""}`.trim();
      const cur = byStu.get(id) || { nombre, riesgo: 0, prom: 0, asis: 0, n: 0 };
      cur.riesgo = Math.max(cur.riesgo, Number(r.pred || 0));
      cur.prom += Number(r.parciales_avg || 0);
      cur.asis += Number(r.asistencia_pct || 0);
      cur.n++;
      byStu.set(id, cur);
    });

    byStu.forEach(v => {
      const prom = v.n ? v.prom / v.n : 0;
      const asis = v.n ? v.asis / v.n : 0;
      let mensaje = "";
      let badge = "";
      let icon = "";

      // Reglas simples
      if (v.riesgo) {
        if (asis < 0.6) {
          mensaje = `${v.nombre} necesita mejorar su asistencia.`;
          badge = "danger"; icon = "fa-user-clock";
        } else if (prom < 6) {
          mensaje = `${v.nombre} debe reforzar materias con bajo promedio.`;
          badge = "warning"; icon = "fa-book-open";
        } else {
          mensaje = `${v.nombre} presenta riesgo, se recomienda acompañamiento cercano.`;
          badge = "danger"; icon = "fa-exclamation-circle";
        }
      } else {
        mensaje = `${v.nombre} se encuentra con buen desempeño, ¡felicidades!`;
        badge = "success"; icon = "fa-thumbs-up";
      }

      // Crear tarjeta
      const col = document.createElement("div");
      col.className = "col-md-6";
      col.innerHTML = `
        <div class="alert alert-${badge} d-flex align-items-center shadow-sm" role="alert">
          <i class="fas ${icon} me-2"></i>
          <div>${mensaje}</div>
        </div>
      `;
      cont.appendChild(col);
    });
  }

  document.addEventListener('DOMContentLoaded', load);
})();
