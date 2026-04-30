<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

if ($method === 'GET') {
  if ($action === 'list') {
    getProperties();
  } elseif ($action === 'detail' && $id) {
    getPropertyDetail($id);
  } elseif ($action === 'featured') {
    getFeaturedProperties();
  } elseif ($action === 'search') {
    searchProperties();
  }
} elseif ($method === 'POST') {
  requireAuth('admin');
  if ($action === 'create') {
    createProperty();
  }
} elseif ($method === 'PUT') {
  requireAuth('admin');
  if ($action === 'update' && $id) {
    updateProperty($id);
  }
} elseif ($method === 'DELETE') {
  requireAuth('admin');
  if ($action === 'delete' && $id) {
    deleteProperty($id);
  }
}

function getProperties() {
  global $pdo;
  
  $page = $_GET['page'] ?? 1;
  $limit = 12;
  $offset = ($page - 1) * $limit;
  
  try {
    $stmt = $pdo->prepare('
      SELECT p.*, GROUP_CONCAT(pi.image_url) as images 
      FROM properties p 
      LEFT JOIN property_images pi ON p.id = pi.property_id 
      WHERE p.status = ?
      GROUP BY p.id
      ORDER BY p.created_at DESC 
      LIMIT ? OFFSET ?
    ');
    $stmt->execute(['available', $limit, $offset]);
    $properties = $stmt->fetchAll();
    
    $countStmt = $pdo->query('SELECT COUNT(*) as total FROM properties WHERE status = "available"');
    $count = $countStmt->fetch()['total'];
    
    response([
      'properties' => $properties,
      'total' => $count,
      'page' => $page,
      'limit' => $limit,
      'pages' => ceil($count / $limit)
    ]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to fetch properties'], 500);
  }
}

function getPropertyDetail($id) {
  global $pdo;
  
  try {
    $stmt = $pdo->prepare('SELECT * FROM properties WHERE id = ?');
    $stmt->execute([$id]);
    $property = $stmt->fetch();
    
    if (!$property) {
      response(['error' => 'Property not found'], 404);
    }
    
    $imgStmt = $pdo->prepare('SELECT image_url FROM property_images WHERE property_id = ? ORDER BY display_order');
    $imgStmt->execute([$id]);
    $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $agentStmt = $pdo->prepare('SELECT id, first_name, last_name, phone, email, profile_image FROM users WHERE id = ?');
    $agentStmt->execute([$property['agent_id']]);
    $agent = $agentStmt->fetch();
    
    $property['images'] = $images;
    $property['agent'] = $agent;
    
    $updateStmt = $pdo->prepare('UPDATE properties SET views = views + 1 WHERE id = ?');
    $updateStmt->execute([$id]);
    
    response(['property' => $property]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to fetch property'], 500);
  }
}

function getFeaturedProperties() {
  global $pdo;
  
  try {
    $stmt = $pdo->prepare('
      SELECT p.*, GROUP_CONCAT(pi.image_url) as images 
      FROM properties p 
      LEFT JOIN property_images pi ON p.id = pi.property_id 
      WHERE p.featured = 1 AND p.status = ?
      GROUP BY p.id
      ORDER BY p.created_at DESC 
      LIMIT 6
    ');
    $stmt->execute(['available']);
    $properties = $stmt->fetchAll();
    
    response(['properties' => $properties]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to fetch featured properties'], 500);
  }
}

function searchProperties() {
  global $pdo;
  
  $city = $_GET['city'] ?? null;
  $minPrice = $_GET['minPrice'] ?? null;
  $maxPrice = $_GET['maxPrice'] ?? null;
  $type = $_GET['type'] ?? null;
  
  try {
    $query = 'SELECT p.*, GROUP_CONCAT(pi.image_url) as images FROM properties p 
              LEFT JOIN property_images pi ON p.id = pi.property_id 
              WHERE p.status = "available"';
    $params = [];
    
    if ($city) {
      $query .= ' AND p.city LIKE ?';
      $params[] = "%$city%";
    }
    if ($minPrice) {
      $query .= ' AND p.price >= ?';
      $params[] = $minPrice;
    }
    if ($maxPrice) {
      $query .= ' AND p.price <= ?';
      $params[] = $maxPrice;
    }
    if ($type) {
      $query .= ' AND p.property_type = ?';
      $params[] = $type;
    }
    
    $query .= ' GROUP BY p.id ORDER BY p.created_at DESC LIMIT 100';
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $properties = $stmt->fetchAll();
    
    response(['properties' => $properties]);
  } catch (PDOException $e) {
    response(['error' => 'Search failed'], 500);
  }
}

function createProperty() {
  global $pdo;
  
  $input = json_decode(file_get_contents('php://input'), true);
  
  try {
    $stmt = $pdo->prepare('
      INSERT INTO properties (title, description, property_type, address, city, state, zip_code, 
                            price, bedrooms, bathrooms, square_feet, year_built, features, 
                            agent_id, status) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    
    $stmt->execute([
      $input['title'],
      $input['description'] ?? null,
      $input['property_type'] ?? 'residential',
      $input['address'],
      $input['city'],
      $input['state'] ?? null,
      $input['zip_code'] ?? null,
      $input['price'],
      $input['bedrooms'] ?? null,
      $input['bathrooms'] ?? null,
      $input['square_feet'] ?? null,
      $input['year_built'] ?? null,
      $input['features'] ?? null,
      $_SESSION['user_id'],
      'available'
    ]);
    
    $propertyId = $pdo->lastInsertId();
    response(['success' => true, 'id' => $propertyId], 201);
  } catch (PDOException $e) {
    response(['error' => 'Failed to create property'], 500);
  }
}

function updateProperty($id) {
  global $pdo;
  
  $input = json_decode(file_get_contents('php://input'), true);
  
  try {
    $fields = [];
    $params = [];
    
    $allowedFields = ['title', 'description', 'price', 'bedrooms', 'bathrooms', 'square_feet', 'status', 'featured'];
    
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
    $query = 'UPDATE properties SET ' . implode(', ', $fields) . ' WHERE id = ?';
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    response(['success' => true]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to update property'], 500);
  }
}

function deleteProperty($id) {
  global $pdo;
  
  try {
    $stmt = $pdo->prepare('DELETE FROM properties WHERE id = ?');
    $stmt->execute([$id]);
    
    response(['success' => true]);
  } catch (PDOException $e) {
    response(['error' => 'Failed to delete property'], 500);
  }
}
?>
