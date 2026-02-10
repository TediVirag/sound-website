<?php
/**
 * Database Connection Debug Script
 * Use this to check your database configuration
 * DELETE after debugging!
 */

echo "<!DOCTYPE html><html><head><title>Database Debug</title>";
echo "<style>body{font-family:monospace;padding:20px;}.success{color:green;}.error{color:red;}.info{color:blue;}</style></head><body>";
echo "<h1>Database Connection Debug</h1>";

require_once 'includes/db.php';

echo "<h2>Step 1: Check _CS Class</h2>";
try {
    if (class_exists('_CS')) {
        echo "<p class='success'>✓ _CS class exists</p>";
    } else {
        echo "<p class='error'>✗ _CS class not found</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error checking _CS: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

echo "<h2>Step 2: Load Caesar Settings</h2>";
try {
    $settings = _CS::load_caesar_settings();
    echo "<p class='success'>✓ Caesar settings loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error loading settings: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

echo "<h2>Step 3: Check PostgreSQL Availability</h2>";
try {
    $available = $settings->isPgsqlAvailable();
    if ($available) {
        echo "<p class='success'>✓ PostgreSQL is available for your account</p>";
    } else {
        echo "<p class='error'>✗ PostgreSQL is NOT enabled for your account</p>";
        echo "<p>Please enable it at: <a href='https://info.caesar.elte.hu'>https://info.caesar.elte.hu</a></p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error checking availability: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

echo "<h2>Step 4: Get Connection Parameters</h2>";
try {
    $info = getDbInfo();
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>Parameter</th><th>Value</th></tr>";
    foreach ($info as $key => $value) {
        echo "<tr><td><strong>" . htmlspecialchars($key) . "</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
    }
    echo "</table>";
    
    // Verify the database name
    if (isset($info['database'])) {
        echo "<p class='info'>ℹ Database name: <strong>" . htmlspecialchars($info['database']) . "</strong></p>";
        
        // Check if it's the expected database
        if ($info['database'] === 'p-soundgen') {
            echo "<p class='success'>✓ Database name matches expected value (p-soundgen)</p>";
        } else {
            echo "<p class='error'>✗ Database name is '" . htmlspecialchars($info['database']) . "' but expected 'p-soundgen'</p>";
            echo "<p class='info'>This might be correct if you're using a different database name.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error getting connection info: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

echo "<h2>Step 5: Test Connection</h2>";
try {
    $pdo = getDbPDO();
    echo "<p class='success'>✓ PDO connection successful!</p>";
    
    // Try a simple query
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "<p class='success'>✓ PostgreSQL version: " . htmlspecialchars($version) . "</p>";
    
    // Check if tables exist
    echo "<h3>Existing Tables:</h3>";
    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        ORDER BY table_name
    ");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<p class='info'>No tables found (this is expected if setup hasn't run yet)</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2 class='success'>✓ ALL CHECKS PASSED!</h2>";
    echo "<p>You can now run <a href='setup.php'>setup.php</a></p>";
    echo "<p class='error'><strong>REMEMBER: Delete this debug-db.php file after use!</strong></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Connection test failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>