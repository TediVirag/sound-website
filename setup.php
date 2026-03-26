<?php
/**
 * Database Setup Script - Updated for PostgreSQL 9.4
 * Needs _file_pairings.txt and input_jsons inside static folder!!!
 * how to run setup.php: https://tedivirag.web.elte.hu/setup.php
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
    
    // Create sound_samples table with new columns
    try {
        $conn->exec("
            CREATE TABLE IF NOT EXISTS sound_samples (
                sound_code VARCHAR(50) NOT NULL,
                result_num NUMERIC NOT NULL DEFAULT 0,
                emotion VARCHAR(50),
                strength VARCHAR(50),
                json TEXT,
                PRIMARY KEY (sound_code)
            )
        ");
        echo "<p class='success'>✓ Created 'sound_samples' table</p>";
    } catch (Exception $e) {
        echo "<p class='warning'>⚠ Sound_samples table: " . $e->getMessage() . "</p>";
    }
    
    // Add new columns if they don't exist (for existing installations)
    echo "<h2>Updating Table Structure...</h2>";
    
    $columnsToAdd = [
        'emotion' => 'VARCHAR(50)',
        'strength' => 'VARCHAR(50)',
        'json' => 'TEXT'
    ];
    
    foreach ($columnsToAdd as $columnName => $columnType) {
        try {
            $stmt = $conn->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'sound_samples' 
                AND column_name = '$columnName'
            ");
            
            if (!$stmt->fetch()) {
                $conn->exec("ALTER TABLE sound_samples ADD COLUMN $columnName $columnType");
                echo "<p class='success'>✓ Added column: $columnName</p>";
            } else {
                echo "<p class='success'>✓ Column already exists: $columnName</p>";
            }
        } catch (Exception $e) {
            echo "<p class='warning'>⚠ Column $columnName: " . $e->getMessage() . "</p>";
        }
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
    
    // Populate sound_samples table from file pairings
    echo "<h2>Populating Sound Samples...</h2>";
    
    $pairingsFile = 'static/_file_pairings.txt';
    
    if (!file_exists($pairingsFile)) {
        echo "<p class='error'>✗ File pairings file not found: $pairingsFile</p>";
    } else {
        $pairingsContent = file_get_contents($pairingsFile);
        $lines = explode("\n", $pairingsContent);
        
        $addedCount = 0;
        $updatedCount = 0;
        $errorCount = 0;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Parse the line: "anger_1_1_8BIS8Z32.wav → 8BIS8Z32.wav"
            if (preg_match('/^(.+?)\s*→\s*(.+)$/', $line, $matches)) {
                $fullFilename = trim($matches[1]);
                $shortFilename = trim($matches[2]);
                
                // Extract sound code (without .wav extension)
                $soundCode = pathinfo($shortFilename, PATHINFO_FILENAME);
                
                // Parse the full filename: emotion_strength_counter_code.wav
                if (preg_match('/^([a-z]+)_(\d+)_(\d+)_([a-zA-Z0-9]+)\.wav$/', $fullFilename, $fileMatches)) {
                    $emotion = $fileMatches[1];
                    $strength = $fileMatches[2];
                    $counter = $fileMatches[3];
                    
                    // Construct JSON file path
                    $jsonPath = "static/input_jsons/{$emotion}_{$strength}_{$counter}.json";
                    
                    $jsonContent = null;
                    if (file_exists($jsonPath)) {
                        $jsonContent = file_get_contents($jsonPath);
                        if ($jsonContent === false) {
                            echo "<p class='warning'>⚠ Could not read JSON file: $jsonPath</p>";
                            $jsonContent = null;
                        }
                    } else {
                        echo "<p class='warning'>⚠ JSON file not found: $jsonPath</p>";
                    }
                    
                    try {
                        // Check if sound_code exists
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM sound_samples WHERE sound_code = ?");
                        $stmt->execute([$soundCode]);
                        $exists = $stmt->fetchColumn() > 0;
                        
                        if ($exists) {
                            // Update existing record
                            $stmt = $conn->prepare("
                                UPDATE sound_samples 
                                SET emotion = ?, strength = ?, json = ? 
                                WHERE sound_code = ?
                            ");
                            $stmt->execute([$emotion, $strength, $jsonContent, $soundCode]);
                            $updatedCount++;
                        } else {
                            // Insert new record
                            $stmt = $conn->prepare("
                                INSERT INTO sound_samples (sound_code, result_num, emotion, strength, json) 
                                VALUES (?, 0, ?, ?, ?)
                            ");
                            $stmt->execute([$soundCode, $emotion, $strength, $jsonContent]);
                            echo "<p class='success'>✓ Added: $soundCode (emotion: $emotion, strength: $strength)</p>";
                            $addedCount++;
                        }
                    } catch (Exception $e) {
                        echo "<p class='error'>✗ Error processing $soundCode: " . $e->getMessage() . "</p>";
                        $errorCount++;
                    }
                } else {
                    echo "<p class='warning'>⚠ Could not parse filename format: $fullFilename</p>";
                    $errorCount++;
                }
            }
        }
        
        echo "<h3>Summary:</h3>";
        echo "<ul>";
        echo "<li><strong>Added:</strong> $addedCount</li>";
        echo "<li><strong>Updated:</strong> $updatedCount</li>";
        echo "<li><strong>Errors:</strong> $errorCount</li>";
        echo "<li><strong>Total lines processed:</strong> " . count($lines) . "</li>";
        echo "</ul>";
    }
    
    echo "<h2 class='success'>✓ Database Setup Complete!</h2>";
    echo "<p class='error'><strong>IMPORTANT: Delete this setup.php file now for security!</strong></p>";
    
    // Verification
    echo "<h2>Verification</h2>";
    $stmt = $conn->query("SELECT COUNT(*) as total FROM sound_samples");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p class='success'>✓ Database connected successfully</p>";
    echo "<p>Sound samples in database: <strong>" . $result['total'] . "</strong></p>";
    
    // Show sample of data with new columns
    echo "<h3>Sample Data:</h3>";
    $stmt = $conn->query("SELECT sound_code, emotion, strength, CASE WHEN json IS NULL THEN 'No' ELSE 'Yes' END as has_json FROM sound_samples LIMIT 5");
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Sound Code</th><th>Emotion</th><th>Strength</th><th>Has JSON</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['sound_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['emotion']) . "</td>";
        echo "<td>" . htmlspecialchars($row['strength']) . "</td>";
        echo "<td>" . htmlspecialchars($row['has_json']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h2 class='error'>✗ Database Setup Failed</h2>";
    echo "<p class='error'>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
?>