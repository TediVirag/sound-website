<?php
/**
 * Database Setup Script - Updated for PostgreSQL 9.4
 */

require_once 'includes/config.php';
require_once 'includes/db.php';

echo "<!DOCTYPE html><html><head><title>Database Setup</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}";
echo ".success{color:green;}.error{color:red;}.warning{color:orange;}</style></head><body>";
echo "<h1>Database Setup</h1>";

try {
    $conn = getDbPDO();
    
    echo "<h2>Creating Tables...</h2>";
    
    // Create submissions table
    try {
        $conn->exec("
            CREATE TABLE IF NOT EXISTS submissions (
                user_code VARCHAR(50) PRIMARY KEY,
                age INTEGER NOT NULL CHECK (age > 0 AND age <= 120),
                gender VARCHAR(50) NOT NULL,
                highest_education VARCHAR(100) NOT NULL,
                submitted_before BOOLEAN NOT NULL,
                feedback TEXT,
                timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
        echo "<p class='success'>✓ Created 'submissions' table</p>";
    } catch (Exception $e) {
        echo "<p class='warning'>⚠ Submissions table: " . $e->getMessage() . "</p>";
    }
    
    // Create sound_samples table
    try {
        $conn->exec("
            CREATE TABLE IF NOT EXISTS sound_samples (
                sound_code VARCHAR(50) NOT NULL,
                result_num NUMERIC NOT NULL DEFAULT 0,
                PRIMARY KEY (sound_code)
            )
        ");
        echo "<p class='success'>✓ Created 'sound_samples' table</p>";
    } catch (Exception $e) {
        echo "<p class='warning'>⚠ Sound_samples table: " . $e->getMessage() . "</p>";
    }
    
    // Create results table
    try {
        $conn->exec("
            CREATE TABLE IF NOT EXISTS results (
                id SERIAL PRIMARY KEY,
                user_code VARCHAR(50) NOT NULL,
                sound_code VARCHAR(50) NOT NULL,
                emotion1 VARCHAR(50) NOT NULL,
                rating1 NUMERIC(5,2) NOT NULL,
                emotion2 VARCHAR(50),
                rating2 NUMERIC(5,2),
                FOREIGN KEY (user_code) REFERENCES submissions(user_code) ON DELETE CASCADE,
                FOREIGN KEY (sound_code) REFERENCES sound_samples(sound_code) ON DELETE CASCADE
            )
        ");
        echo "<p class='success'>✓ Created 'results' table</p>";
    } catch (Exception $e) {
        echo "<p class='warning'>⚠ Results table: " . $e->getMessage() . "</p>";
    }
    
    // Create indexes (PostgreSQL 9.4 compatible)
    echo "<h2>Creating Indexes...</h2>";
    
    // Check and create idx_submissions_timestamp
    try {
        $stmt = $conn->query("
            SELECT 1 FROM pg_indexes 
            WHERE indexname = 'idx_submissions_timestamp'
        ");
        if (!$stmt->fetch()) {
            $conn->exec("CREATE INDEX idx_submissions_timestamp ON submissions(timestamp DESC)");
            echo "<p class='success'>✓ Created index: idx_submissions_timestamp</p>";
        } else {
            echo "<p class='success'>✓ Index already exists: idx_submissions_timestamp</p>";
        }
    } catch (Exception $e) {
        echo "<p class='warning'>⚠ Index idx_submissions_timestamp: " . $e->getMessage() . "</p>";
    }
    
    // Check and create idx_results_code
    try {
        $stmt = $conn->query("
            SELECT 1 FROM pg_indexes 
            WHERE indexname = 'idx_results_code'
        ");
        if (!$stmt->fetch()) {
            $conn->exec("CREATE INDEX idx_results_code ON results(sound_code)");
            echo "<p class='success'>✓ Created index: idx_results_code</p>";
        } else {
            echo "<p class='success'>✓ Index already exists: idx_results_code</p>";
        }
    } catch (Exception $e) {
        echo "<p class='warning'>⚠ Index idx_results_code: " . $e->getMessage() . "</p>";
    }
    
    // Populate sound_samples table
    echo "<h2>Populating Sound Samples...</h2>";
    
    $soundsDir = SOUND_FOLDER_PATH;
    
    if (!is_dir($soundsDir)) {
        echo "<p class='error'>✗ Sounds directory not found: $soundsDir</p>";
    } else {
        $soundFiles = glob($soundsDir . '/*.wav');
        
        if (empty($soundFiles)) {
            echo "<p class='warning'>⚠ No .wav files found</p>";
        } else {
            sort($soundFiles);
            
            $addedCount = 0;
            $skippedCount = 0;
            
            foreach ($soundFiles as $filePath) {
                $filename = basename($filePath);
                $soundCode = pathinfo($filename, PATHINFO_FILENAME);
                
                try {
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM sound_samples WHERE sound_code = ?");
                    $stmt->execute([$soundCode]);
                    $exists = $stmt->fetchColumn() > 0;
                    
                    if ($exists) {
                        $skippedCount++;
                    } else {
                        $stmt = $conn->prepare("INSERT INTO sound_samples (sound_code, result_num) VALUES (?, 0)");
                        $stmt->execute([$soundCode]);
                        echo "<p class='success'>✓ Added: $soundCode</p>";
                        $addedCount++;
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>✗ Error processing $soundCode: " . $e->getMessage() . "</p>";
                }
            }
            
            echo "<h3>Summary:</h3>";
            echo "<ul>";
            echo "<li><strong>Added:</strong> $addedCount</li>";
            echo "<li><strong>Skipped:</strong> $skippedCount</li>";
            echo "<li><strong>Total files:</strong> " . count($soundFiles) . "</li>";
            echo "</ul>";
        }
    }
    
    echo "<h2 class='success'>✓ Database Setup Complete!</h2>";
    echo "<p class='error'><strong>IMPORTANT: Delete this setup.php file now for security!</strong></p>";
    
    // Verification
    echo "<h2>Verification</h2>";
    $stmt = $conn->query("SELECT COUNT(*) as total FROM sound_samples");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p class='success'>✓ Database connected successfully</p>";
    echo "<p>Sound samples in database: <strong>" . $result['total'] . "</strong></p>";
    
} catch (Exception $e) {
    echo "<h2 class='error'>✗ Database Setup Failed</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
?>