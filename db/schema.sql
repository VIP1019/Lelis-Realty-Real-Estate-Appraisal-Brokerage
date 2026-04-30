-- Lelis Realty Database Schema
-- MySQL 5.7+

-- Users Table (for admin and staff authentication)
CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(100) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'agent', 'broker') DEFAULT 'agent',
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  phone VARCHAR(20),
  profile_image VARCHAR(255),
  status ENUM('active', 'inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role)
);

-- Properties Table
CREATE TABLE IF NOT EXISTS properties (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description LONGTEXT,
  property_type ENUM('residential', 'commercial') DEFAULT 'residential',
  status ENUM('available', 'pending', 'sold') DEFAULT 'available',
  address VARCHAR(255) NOT NULL,
  city VARCHAR(100) NOT NULL,
  state VARCHAR(100),
  zip_code VARCHAR(20),
  price DECIMAL(15, 2) NOT NULL,
  bedrooms INT,
  bathrooms DECIMAL(3, 1),
  square_feet INT,
  lot_size VARCHAR(100),
  year_built INT,
  features LONGTEXT,
  broker_id INT,
  agent_id INT,
  featured BOOLEAN DEFAULT FALSE,
  views INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (broker_id) REFERENCES users(id),
  FOREIGN KEY (agent_id) REFERENCES users(id),
  INDEX idx_status (status),
  INDEX idx_city (city),
  INDEX idx_price (price),
  INDEX idx_type (property_type)
);

-- Property Images Table
CREATE TABLE IF NOT EXISTS property_images (
  id INT PRIMARY KEY AUTO_INCREMENT,
  property_id INT NOT NULL,
  image_url VARCHAR(255) NOT NULL,
  is_primary BOOLEAN DEFAULT FALSE,
  display_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  INDEX idx_property (property_id)
);

-- Sellers Table
CREATE TABLE IF NOT EXISTS sellers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  property_address VARCHAR(255),
  property_city VARCHAR(100),
  property_category ENUM('residential', 'commercial') DEFAULT 'residential',
  property_type VARCHAR(100),
  asking_price DECIMAL(15, 2),
  status ENUM('submitted', 'under_review', 'approved', 'rejected') DEFAULT 'submitted',
  notes LONGTEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_email (email)
);

-- Buyers Table
CREATE TABLE IF NOT EXISTS buyers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  preferences LONGTEXT,
  budget_min DECIMAL(15, 2),
  budget_max DECIMAL(15, 2),
  source ENUM('website', 'referral', 'social', 'agent') DEFAULT 'website',
  status ENUM('active', 'inactive', 'closed') DEFAULT 'active',
  notes LONGTEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_email (email)
);

-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  property_id INT,
  agent_id INT,
  buyer_name VARCHAR(100),
  buyer_email VARCHAR(100),
  buyer_phone VARCHAR(20),
  appointment_date DATETIME NOT NULL,
  status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',
  notes LONGTEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id),
  FOREIGN KEY (agent_id) REFERENCES users(id),
  INDEX idx_date (appointment_date),
  INDEX idx_status (status)
);

-- Inquiries Table
CREATE TABLE IF NOT EXISTS inquiries (
  id INT PRIMARY KEY AUTO_INCREMENT,
  property_id INT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  message LONGTEXT,
  inquiry_type VARCHAR(100),
  status ENUM('new', 'contacted', 'resolved') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id),
  INDEX idx_status (status),
  INDEX idx_date (created_at)
);

-- Purchases Table (for tracking closed transactions)
CREATE TABLE IF NOT EXISTS purchases (
  id INT PRIMARY KEY AUTO_INCREMENT,
  property_id INT NOT NULL,
  buyer_name VARCHAR(100),
  seller_name VARCHAR(100),
  agent_id INT,
  sale_price DECIMAL(15, 2),
  closing_date DATE,
  status ENUM('pending', 'closed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id),
  FOREIGN KEY (agent_id) REFERENCES users(id),
  INDEX idx_closing (closing_date)
);

-- Blog/Articles Table
CREATE TABLE IF NOT EXISTS articles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  content LONGTEXT NOT NULL,
  author_id INT,
  category VARCHAR(100),
  featured_image VARCHAR(255),
  status ENUM('published', 'draft') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (author_id) REFERENCES users(id),
  INDEX idx_category (category),
  INDEX idx_status (status),
  INDEX idx_slug (slug)
);
