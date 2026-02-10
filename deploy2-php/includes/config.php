<?php
// Application configuration
define('SOUND_FOLDER', '/static/sound_files');
define('SOUND_FOLDER_PATH', __DIR__ . '/../static/sound_files');
define('ITEMS_PER_PAGE', 10);

// Database Configuration
define('DB_NAME', 'p-soundgen');

// Emotions list
define('EMOTIONS', [
    'Happy',
    'Sad', 
    'Angry',
    'Fearful',
    'Disgusted',
    'Surprised',
    'Neutral'
]);

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
?>