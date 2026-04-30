<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  requireAuth('admin');
  if ($action === 'list') {
    getInquiries();
  } elseif ($action === 'detail' && $id) {
    getInquiryDetail($id);
  }
} elseif ($method === 'POST') {
  if ($action === 'submit') {
    submitInquiry();
  }
} elseif ($method === 'PUT') {
  requireAuth('admin');
  if ($action === 'update' && $id) {
    updateInquiry($id);
  }
}

function getInquiries() {
  global $pdo;
  
  $status = $_GET['status'] ?? null;
  $propertyId = $_GET['property_id'] ?? null;
  
  try {
    $query = 'SELECT * FROM inquiries WHERE 1=1';
    $params = [];
    
    if ($status) {
      $query .= ' AND status = ?';
      $params[] = $status;
    }
    
    if ($propertyId) {
      $query .= ' AND property_id = ?';
      $params[] = $propertyId;
    }
    
    $query .= ' ORDER BY created_at DESC LIMIT 100';
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $inquiries = $stmt->fetchAll();
    
    response(['inquiries' => $inquiries]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to fetch inquiries'], 500);
  }
}

function getInquiryDetail($id) {
  global $pdo;
  
  try {
    $stmt = $pdo->prepare('SELECT * FROM inquiries WHERE id = ?');
    $stmt->execute([$id]);
    $inquiry = $stmt->fetch();
    
    if (!$inquiry) {
      response(['error' => 'Inquiry not found'], 404);
    }
    
    response(['inquiry' => $inquiry]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to fetch inquiry'], 500);
  }
}

function submitInquiry() {
  global $pdo;
  
  $input = json_decode(file_get_contents('php://input'), true);
  
  if (!isset($input['property_id']) || !isset($input['email'])) {
    response(['error' => 'Property ID and email are required'], 400);
  }
  
  try {
    $stmt = $pdo->prepare('
      INSERT INTO inquiries (property_id, name, email, phone, message, inquiry_type, status) 
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    
    $stmt->execute([
      $input['property_id'],
      $input['name'] ?? null,
      $input['email'],
      $input['phone'] ?? null,
      $input['message'] ?? null,
      $input['inquiry_type'] ?? 'general',
      'new'
    ]);
    
    $inquiryId = $pdo->lastInsertId();
    response(['success' => true, 'id' => $inquiryId], 201);
  } catch (PDOException $e) {
    response(['error' => 'Failed to submit inquiry'], 500);
  }
}

function updateInquiry($id) {
  global $pdo;
  
  requireAuth('admin');
  $input = json_decode(file_get_contents('php://input'), true);
  
  try {
    $fields = [];
    $params = [];
    
    $allowedFields = ['status'];
    
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
    $query = 'UPDATE inquiries SET ' . implode(', ', $fields) . ' WHERE id = ?';
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    response(['success' => true]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to update inquiry'], 500);
  }
}
?>
