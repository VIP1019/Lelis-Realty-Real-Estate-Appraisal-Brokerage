<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  requireAuth('admin');
  if ($action === 'list') {
    getSellers();
  } elseif ($action === 'detail' && $id) {
    getSellerDetail($id);
  }
} elseif ($method === 'POST') {
  if ($action === 'submit') {
    submitSeller();
  }
} elseif ($method === 'PUT') {
  requireAuth('admin');
  if ($action === 'update' && $id) {
    updateSeller($id);
  }
}

function getSellers() {
  global $pdo;
  
  $status = $_GET['status'] ?? null;
  
  try {
    $query = 'SELECT * FROM sellers';
    $params = [];
    
    if ($status) {
      $query .= ' WHERE status = ?';
      $params[] = $status;
    }
    
    $query .= ' ORDER BY created_at DESC LIMIT 100';
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $sellers = $stmt->fetchAll();
    
    response(['sellers' => $sellers]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to fetch sellers'], 500);
  }
}

function getSellerDetail($id) {
  global $pdo;
  
  try {
    $stmt = $pdo->prepare('SELECT * FROM sellers WHERE id = ?');
    $stmt->execute([$id]);
    $seller = $stmt->fetch();
    
    if (!$seller) {
      response(['error' => 'Seller not found'], 404);
    }
    
    response(['seller' => $seller]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to fetch seller'], 500);
  }
}

function submitSeller() {
  global $pdo;
  
  $input = json_decode(file_get_contents('php://input'), true);
  
  $required = ['name', 'email', 'phone', 'property_address', 'property_city', 'property_category'];
  foreach ($required as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
      response(['error' => "Missing required field: $field"], 400);
    }
  }
  
  try {
    $stmt = $pdo->prepare('
      INSERT INTO sellers (name, email, phone, property_address, property_city, 
                          property_category, property_type, asking_price, status, notes) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    
    $stmt->execute([
      $input['name'],
      $input['email'],
      $input['phone'],
      $input['property_address'],
      $input['property_city'],
      $input['property_category'],
      $input['property_type'] ?? null,
      $input['asking_price'] ?? null,
      'submitted',
      $input['notes'] ?? null
    ]);
    
    $sellerId = $pdo->lastInsertId();
    response(['success' => true, 'id' => $sellerId, 'message' => 'Your submission has been received'], 201);
  } catch (PDOException $e) {
    response(['error' => 'Failed to submit seller information'], 500);
  }
}

function updateSeller($id) {
  global $pdo;
  
  requireAuth('admin');
  $input = json_decode(file_get_contents('php://input'), true);
  
  try {
    $fields = [];
    $params = [];
    
    $allowedFields = ['status', 'notes'];
    
    foreach ($allowedFields as $field) {
      if (isset($input[$field])) {
        $fields[] = "$field = ?";
        $params[] = $input[$field];
      }
    }
    
    if (empty($fields)) {
      response(['error' => 'No fields to update'], 400);
    }
    
    $params[] = $id;
    $query = 'UPDATE sellers SET ' . implode(', ', $fields) . ' WHERE id = ?';
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    response(['success' => true]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to update seller'], 500);
  }
}
?>
