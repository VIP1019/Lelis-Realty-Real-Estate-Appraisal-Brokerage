<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

if ($method === 'POST') {
  if ($action === 'login') {
    handleLogin();
  } elseif ($action === 'logout') {
    handleLogout();
  }
} elseif ($method === 'GET') {
  if ($action === 'status') {
    handleStatus();
  }
}

function handleLogin() {
  global $pdo;
  
  $input = json_decode(file_get_contents('php://input'), true);
  
  if (!isset($input['username']) || !isset($input['password'])) {
    response(['error' => 'Username and password required'], 400);
  }
  
  try {
    $stmt = $pdo->prepare('SELECT id, password, role, first_name, last_name, email FROM users WHERE username = ? AND status = ?');
    $stmt->execute([$input['username'], 'active']);
    $user = $stmt->fetch();
    
    if ($user && password_verify($input['password'], $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $input['username'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
      $_SESSION['email'] = $user['email'];
      
      response([
        'success' => true,
        'user' => [
          'id' => $user['id'],
          'username' => $input['username'],
          'name' => $_SESSION['name'],
          'role' => $user['role']
        ]
      ]);
    } else {
      response(['error' => 'Invalid username or password'], 401);
    }
  } catch (PDOException $e) {
    response(['error' => 'Login failed'], 500);
  }
}

function handleLogout() {
  session_destroy();
  response(['success' => true]);
}

function handleStatus() {
  if (isLoggedIn()) {
    response([
      'loggedIn' => true,
      'user' => [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role'],
        'name' => $_SESSION['name']
      ]
    ]);
  } else {
    response(['loggedIn' => false]);
  }
}
?>
