<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/db.php';

function generateUniqueCode($length = 10) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    if (empty($data['age']) || empty($data['gender']) || 
        empty($data['highest_education']) || !isset($data['submitted_before']) ||
        empty($data['results'])) {
        throw new Exception('Missing required fields');
    }
    
    // Generate unique user code
    $userCode = generateUniqueCode();
    
    // Start transaction
    $conn = getDbPDO();
    $conn->beginTransaction();
    
    try {
        // Convert submitted_before to integer (0 or 1) for PostgreSQL boolean
        $submittedBefore = $data['submitted_before'];
        if (is_string($submittedBefore)) {
            $submittedBefore = ($submittedBefore === 'true' || $submittedBefore === '1');
        }
        $submittedBefore = $submittedBefore ? 1 : 0;  // Convert to 1 or 0
        
        // Insert into submissions table
        $stmt = $conn->prepare("
            INSERT INTO submissions 
            (user_code, age, gender, highest_education, submitted_before, feedback)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userCode,
            intval($data['age']),
            $data['gender'],
            $data['highest_education'],
            $submittedBefore,  // Integer 0 or 1
            !empty($data['feedback']) ? $data['feedback'] : null
        ]);
        
        // Insert results
        $stmtResult = $conn->prepare("
            INSERT INTO results 
            (user_code, sound_code, emotion1, rating1, emotion2, rating2)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmtUpdate = $conn->prepare("
            UPDATE sound_samples 
            SET result_num = result_num + 1
            WHERE sound_code = ?
        ");

        // Reusable convert function
        $convertEmotion = fn($e) => match($e) {
            'Surprise'  => 'surprise',
            'Sadness'   => 'sad',
            'Happiness' => 'happy',
            'Anger'     => 'angry',
            'Disgust'   => 'disgust',
            'Fear'      => 'fear',
            default     => strtolower($e) // Fallback if no match is found
        };
        
        foreach ($data['results'] as $result) {
            // Validate result data
            if (empty($result['sound_code']) || empty($result['emotion1']) || empty($result['rating1'])) {
                throw new Exception('Invalid result data');
            }
            
            $stmtResult->execute([
                $userCode,
                $result['sound_code'],
                $convertEmotion($result['emotion1']),
                floatval($result['rating1']),
                !empty($result['emotion2']) ? $convertEmotion($result['emotion2']) : null,
                !empty($result['rating2']) ? floatval($result['rating2']) : null
            ]);
            
            $stmtUpdate->execute([$result['sound_code']]);
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your submission!',
            'code' => $userCode
        ]);
        
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>