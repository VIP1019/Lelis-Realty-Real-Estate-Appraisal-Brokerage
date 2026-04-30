<?php
// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'lelis_realty');

// App Configuration
define('APP_NAME', 'Lelis Realty');
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost:8000');
define('API_URL', BASE_URL . '/api');

// Session configuration
define('SESSION_TIMEOUT', 3600); // 1 hour

// Allowed image extensions
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Database Connection
try {
  $pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => FALSE
    ]
  );
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Database connection failed']);
  exit;
}

// Session initialization
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Helper Functions
function isLoggedIn() {
  return isset($_SESSION['user_id']);
}

function getUserRole() {
  return $_SESSION['role'] ?? null;
}

function requireAuth($requiredRole = null) {
  if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
  }
  
  if ($requiredRole && getUserRole() !== $requiredRole) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
  }
}

function response($data, $statusCode = 200) {
  http_response_code($statusCode);
  echo json_encode($data);
  exit;
}

function sanitizeInput($data) {
  if (is_array($data)) {
    return array_map('sanitizeInput', $data);
  }
  return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>
