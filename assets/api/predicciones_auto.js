// assets/api/predicciones_auto.js
(function () {
  const DATASET_URL = "assets/api/dataset_admin.php";

  let DATA = [], X = [], y = [];
  let cartModel = null, rfModel = null;
  let cmChart = null, distChart = null;

  // Helpers UI
  const $ = (id) => document.getElementById(id);
  const setStatus = (msg, err = false) => {
    const el = $("status");
    if (el) {
      el.textContent = msg;
      el.style.color = err ? "#e74c3c" : "#8aa0b2";
    }
  };
  const fmtPct = (x) => (x * 100).toFixed(1) + "%";

  // Métricas y gráficas
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
    const data = {
      labels: ['0 (No repr.)', '1 (Repr.)'],
      datasets: [{ data: [zeros, ones] }]
    };
    if (distChart) distChart.destroy();
    distChart = new Chart(ctx, { type: 'doughnut', data });
  }

  // --------- ENTRENAMIENTO ----------
  function getCARTCtor() {
    // Intentos en orden: ML.Cart.DecisionTreeClassifier, ML.DecisionTreeClassifier
    const c1 = (window.ML && window.ML.Cart && window.ML.Cart.DecisionTreeClassifier);
    const c2 = (window.ML && window.ML.DecisionTreeClassifier);
    return (typeof c1 === "function") ? c1 :
           (typeof c2 === "function") ? c2 : null;
  }

  function trainAndEvaluate() {
    // split 80/20
    const idx = [...X.keys()].sort(() => Math.random() - 0.5);
    const cut = Math.floor(idx.length * 0.8);
    const tr = idx.slice(0, cut), te = idx.slice(cut);
    const Xtr = tr.map(i => X[i]), ytr = tr.map(i => y[i]);
    const Xte = te.map(i => X[i]), yte = te.map(i => y[i]);

    // ===== CART (o fallback) =====
    const CARTCtor = getCARTCtor();
    if (CARTCtor) {
      setStatus("Entrenando CART…");
      try {
        cartModel = new CARTCtor({
          gainFunction: 'gini',
          maxDepth: 5,
          minNumSamples: 5
        });
        cartModel.train(Xtr, ytr);
      } catch (e) {
        // Si falla por minificado raro, usamos RF con 1 árbol
        console.warn("CART falló, uso RF(1) como árbol:", e);
        setStatus("CART no disponible, usando RF(1) como árbol", true);
        cartModel = new ML.RandomForestClassifier({
          nEstimators: 1, maxFeatures: 2, replacement: true, seed: 7
        });
        cartModel.train(Xtr, ytr);
      }
    } else {
      // Fallback cuando no existe la clase de CART
      console.warn("No encontré DecisionTreeClassifier; uso RF(1) como árbol.");
      setStatus("CART no encontrado, usando RF(1) como árbol", true);
      cartModel = new ML.RandomForestClassifier({
        nEstimators: 1, maxFeatures: 2, replacement: true, seed: 7
      });
      cartModel.train(Xtr, ytr);
    }
    const yCart = cartModel.predict(Xte);

    // ===== Random Forest =====
    setStatus("Entrenando Random Forest…");
    rfModel = new ML.RandomForestClassifier({
      nEstimators: 200,
      maxFeatures: 2,
      replacement: true,
      seed: 42
    });
    rfModel.train(Xtr, ytr);
    const yRf = rfModel.predict(Xte);

    // Ensamble mayoritario
    const yEns = yCart.map((yc, i) => (yc + yRf[i]) >= 1 ? 1 : 0);

    drawCM(confusion(yte, yEns));
    drawDist(y);
  }

  // --------- PREDICCIÓN MASIVA ----------
  function predictAll() {
    return DATA.map(r => {
      const x = [[
        Number(r.asistencia_pct || 0),
        Number(r.incidentes_count || 0),
        Number(r.parciales_avg || 0)
      ]];

      const cartPred = cartModel.predict(x)[0];
      const probCart = cartPred * 1.0;

      const votes = rfModel.estimators.map(t => t.predict(x)[0]);
      const probRf = votes.length ? votes.reduce((a, b) => a + b, 0) / votes.length : 0;

      const probFinal = (probCart + probRf) / 2;
      const pred = probFinal >= 0.5 ? 1 : 0;

      return {
        ...r,
        prob_cart: probCart,
        prob_rf: probRf,
        prob_final: probFinal,
        pred
      };
    });
  }

  // --------- RENDER ----------
  function renderTables(PREDS) {
    const total = PREDS.length, riesgo = PREDS.filter(r => r.pred === 1).length;
    if ($("statTotal")) $("statTotal").textContent = total;
    if ($("statRiesgo")) $("statRiesgo").textContent = riesgo;
    if ($("statRate")) $("statRate").textContent = fmtPct(total ? riesgo / total : 0);

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
        <td>${(Number(r.asistencia_pct || 0) * 100).toFixed(1)}%</td><td>${r.incidentes_count || 0}</td>
        <td>${Number(r.parciales_avg || 0).toFixed(2)}</td>
        <td>${Number(r.prob_cart).toFixed(2)}</td>
        <td>${Number(r.prob_rf).toFixed(2)}</td>
        <td>${Number(r.prob_final).toFixed(2)}</td>
        <td>${r.pred ? '<span class="badge badge-danger">Reprobará</span>' : '<span class="badge badge-success">No reprobará</span>'}</td>`;
      tbody && tbody.appendChild(tr);
    });

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

  // --------- INIT ----------
  async function init() {
    try {
      setStatus("Cargando datos…");
      const res = await fetch(DATASET_URL);
      const js = await res.json();
      if (!js.ok) throw new Error(js.error || "Error de API");
      DATA = js.data || [];
      if (!DATA.length) { setStatus("Dataset vacío", true); return; }

      X = DATA.map(r => [Number(r.asistencia_pct || 0), Number(r.incidentes_count || 0), Number(r.parciales_avg || 0)]);
      y = DATA.map(r => Number(r.y_reprobado || 0));

      setStatus("Entrenando modelos…");
      trainAndEvaluate();
      setStatus("Prediciendo alumnos…");
      const PREDS = predictAll();
      renderTables(PREDS);
      setStatus("Listo ✅");
    } catch (e) {
      console.error(e);
      setStatus("Error: " + e.message, true);
    }
  }

  document.addEventListener("DOMContentLoaded", init);
})();
