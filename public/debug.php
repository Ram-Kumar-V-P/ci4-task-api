<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Define some basic paths
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
    define('ROOTPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    
    echo "<h1>Debug Information</h1>";
    echo "<h2>Environment Check:</h2>";
    echo "<pre>";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo "Current Working Directory: " . getcwd() . "\n";
    echo "FCPATH: " . FCPATH . "\n";
    echo "ROOTPATH: " . ROOTPATH . "\n";
    
    // Check if key files exist
    echo "\nFile Existence Check:\n";
    $files = [
        ROOTPATH . 'app/Config/Paths.php',
        ROOTPATH . 'app/Helpers/response_helper.php',
        ROOTPATH . 'app/Controllers/AuthController.php',
        ROOTPATH . '.env'
    ];
    
    foreach ($files as $file) {
        echo $file . ': ' . (file_exists($file) ? 'Exists' : 'Missing') . "\n";
    }
    
    // Try to include the Paths config
    echo "\nTrying to load Paths config...\n";
    require ROOTPATH . 'app/Config/Paths.php';
    echo "Paths config loaded successfully\n";
    
    $paths = new Config\Paths();
    echo "systemDirectory: " . $paths->systemDirectory . "\n";
    
    // Try to load bootstrap
    echo "\nTrying to load bootstrap...\n";
    require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
    echo "Bootstrap loaded successfully\n";
    
    // Try to load environment
    echo "\nTrying to load environment...\n";
    require_once SYSTEMPATH . 'Config/DotEnv.php';
    (new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
    echo "Environment loaded successfully\n";
    
    // Display some environment variables
    echo "\nEnvironment Variables:\n";
    echo "CI_ENVIRONMENT: " . (getenv('CI_ENVIRONMENT') ?: 'Not set') . "\n";
    echo "app.baseURL: " . (getenv('app.baseURL') ?: 'Not set') . "\n";
    
    echo "</pre>";
    
} catch (Throwable $e) {
    echo "<h1>Error Information</h1>";
    echo "<pre>";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
