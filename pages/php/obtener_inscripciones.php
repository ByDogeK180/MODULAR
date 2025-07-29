<?php
// File: obtener_inscripciones.php

require 'conecta.php';
header('Content-Type: application/json');

$con = conecta();
if (!$con) {
    echo json_encode([]);
    exit;
}

// Si viene ciclo_id, devolvemos inscripciones por ciclo
if (isset($_GET['ciclo_id'])) {
    $ciclo_id = intval($_GET['ciclo_id']);
    if ($ciclo_id <= 0) {
        echo json_encode([]);
        exit;
    }

    $stmt = $con->prepare("
        SELECT estudiante_id, clase_id
        FROM inscripciones
        WHERE ciclo_id = ?
    ");
    $stmt->bind_param("i", $ciclo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $inscripciones = [];
    while ($row = $result->fetch_assoc()) {
        $inscripciones[] = $row;
    }

    echo json_encode($inscripciones);
    exit;
}

// Si viene clase_id, devolvemos inscripciones por clase
if (isset($_GET['clase_id'])) {
    $clase_id = intval($_GET['clase_id']);
    if ($clase_id <= 0) {
        echo json_encode([]);
        exit;
    }

    $stmt = $con->prepare("
        SELECT inscripcion_id, estudiante_id
        FROM inscripciones
        WHERE clase_id = ?
    ");
    $stmt->bind_param("i", $clase_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $inscripciones = [];
    while ($row = $result->fetch_assoc()) {
        $inscripciones[] = $row;
    }

    echo json_encode($inscripciones);
    exit;
}

// Si no hay parámetros, devuelve todas las inscripciones
$res = $con->query("
    SELECT estudiante_id, clase_id
    FROM inscripciones
");
$all = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $all[] = $row;
    }
}

echo json_encode($all);
