<?php
/**
 * Database Connection Helper
 * Connects to existing p-soundgen database
 */

/**
 * Get database connection using PDO (Recommended)
 * @return PDO Database connection
 * @throws Exception if connection fails
 */
function getDbPDO() {
    try {
        // Get Caesar settings for credentials
        $settings = _CS::load_caesar_settings();
        
        // Check if PostgreSQL is available
        if (!$settings->isPgsqlAvailable()) {
            throw new Exception("PostgreSQL database is not enabled for this account");
        }
        
        // Get connection parameters
        $host = $settings->getPgsqlHost();
        $port = $settings->getPgsqlPort();
        $user = $settings->getPgsqlUser();
        $password = $settings->getPgsqlPswd();
        
        // IMPORTANT: Use your actual database name
        $dbname = 'p-soundgen';  // Your existing database!
        
        // Build DSN
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        
        // Create PDO connection
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Database PDO connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed: " . $e->getMessage());
    } catch (Exception $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Get database connection using pg_connect (Alternative)
 * @return resource Database connection
 * @throws Exception if connection fails
 */
function getDbConnection() {
    try {
        // Get Caesar settings
        $settings = _CS::load_caesar_settings();
        
        if (!$settings->isPgsqlAvailable()) {
            throw new Exception("PostgreSQL database is not enabled");
        }
        
        // Build connection string with p-soundgen database
        $connString = sprintf(
            "host=%s port=%s dbname=%s user=%s password=%s",
            $settings->getPgsqlHost(),
            $settings->getPgsqlPort(),
            'p-soundgen',  // Your existing database!
            $settings->getPgsqlUser(),
            $settings->getPgsqlPswd()
        );
        
        $conn = pg_connect($connString);
        
        if (!$conn) {
            throw new Exception("Failed to connect to database");
        }
        
        return $conn;
    } catch (Exception $e) {
        error_log("Database pg_connect failed: " . $e->getMessage());
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Test database connection
 * @return bool True if connection successful
 */
function testDbConnection() {
    try {
        $pdo = getDbPDO();
        $stmt = $pdo->query("SELECT 1");
        return $stmt !== false;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Get database info (for debugging)
 * @return array Connection parameters
 */
function getDbInfo() {
    try {
        $settings = _CS::load_caesar_settings();
        
        return [
            'available' => $settings->isPgsqlAvailable(),
            'host' => $settings->getPgsqlHost(),
            'port' => $settings->getPgsqlPort(),
            'database' => 'p-soundgen',  // Your actual database
            'user' => $settings->getPgsqlUser(),
            'auto_detected_db' => $settings->getPgsqlDatabase()  // What Caesar thinks
        ];
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}
?>