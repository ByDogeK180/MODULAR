    <?php
    header('Content-Type: application/json');
    require_once 'conecta.php';
    $conexion = conecta();

    session_start();
    $docente_id = $_SESSION['usuario_id'] ?? 0;

    if ($docente_id === 0) {
    echo json_encode(['error' => 'No hay sesión activa']);
    exit;
    }


    $sql = "
    SELECT DISTINCT c.clase_id, CONCAT(c.grado, '° ', c.grupo) AS nombre
    FROM clase_asignacion ca
    INNER JOIN clases c ON ca.clase_id = c.clase_id
    WHERE ca.docente_id = ?
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $docente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $clases = [];
    while ($row = $result->fetch_assoc()) {
    $clases[] = $row;
    }

    echo json_encode($clases);
    ?>
