<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

try {
    $conn = getDbPDO();
    $stmt = $conn->query('SELECT 1');
    $stmt->fetch();
    
    echo json_encode([
        'status' => 'healthy',
        'database' => 'connected'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'unhealthy',
        'error' => $e->getMessage()
    ]);
}
?>