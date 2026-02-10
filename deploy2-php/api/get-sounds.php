<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/db.php';

try {
    $count = isset($_GET['count']) ? intval($_GET['count']) : 10;
    
    // Check if sound directory exists
    if (!is_dir(SOUND_FOLDER_PATH)) {
        echo json_encode([
            'success' => false,
            'message' => 'Sounds directory not found'
        ]);
        exit;
    }
    
    // Get all .wav files
    $soundFiles = glob(SOUND_FOLDER_PATH . '/*.wav');
    
    if (empty($soundFiles)) {
        echo json_encode([
            'success' => false,
            'message' => 'No .wav files found'
        ]);
        exit;
    }
    
    // Get sound codes (filenames without extension)
    $soundCodes = array_map(function($file) {
        return pathinfo($file, PATHINFO_FILENAME);
    }, $soundFiles);
    
    // Get from database ordered by result_num
    $conn = getDbPDO();
    
    $placeholders = implode(',', array_fill(0, count($soundCodes), '?'));
    $stmt = $conn->prepare("
        SELECT sound_code, result_num
        FROM sound_samples
        WHERE sound_code IN ($placeholders)
        ORDER BY result_num ASC
    ");
    
    $stmt->execute($soundCodes);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Order sound files by result_num
    $orderedSounds = array_map(function($row) {
        return $row['sound_code'] . '.wav';
    }, $rows);
    
    // Limit to requested count
    $orderedSounds = array_slice($orderedSounds, 0, $count);
    
    // Generate full URLs
    $baseUrl = 'https://' . $_SERVER['HTTP_HOST'];
    $soundUrls = array_map(function($filename) use ($baseUrl) {
        return $baseUrl . SOUND_FOLDER . '/' . $filename;
    }, $orderedSounds);
    
    echo json_encode([
        'success' => true,
        'sounds' => $soundUrls,
        'count' => count($soundUrls)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>