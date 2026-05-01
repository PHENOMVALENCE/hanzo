SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS production_updates;
DROP TABLE IF EXISTS shipping_updates;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS quotations;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS factory_products;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS factories;
DROP TABLE IF EXISTS buyers;
DROP TABLE IF EXISTS admins;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(180) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'admin',
  status ENUM('active','suspended') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE buyers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(180) NOT NULL,
  company_name VARCHAR(200) DEFAULT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  phone VARCHAR(50) DEFAULT NULL,
  country VARCHAR(80) DEFAULT NULL,
  city VARCHAR(80) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  status ENUM('active','pending','suspended') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE factories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  factory_name VARCHAR(200) NOT NULL,
  contact_person VARCHAR(150) DEFAULT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  phone VARCHAR(50) DEFAULT NULL,
  city VARCHAR(80) DEFAULT NULL,
  province VARCHAR(80) DEFAULT NULL,
  main_products VARCHAR(255) DEFAULT NULL,
  production_capacity VARCHAR(255) DEFAULT NULL,
  export_experience VARCHAR(255) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  status ENUM('active','invited','suspended') NOT NULL DEFAULT 'invited',
  invited_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_factories_invited_by (invited_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  image VARCHAR(255) DEFAULT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  factory_id INT UNSIGNED DEFAULT NULL,
  category_id INT UNSIGNED NOT NULL,
  product_name VARCHAR(200) NOT NULL,
  description TEXT,
  moq INT UNSIGNED NOT NULL DEFAULT 1,
  min_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  max_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  main_image VARCHAR(255) DEFAULT NULL,
  status ENUM('active','draft','archived') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_products_factory (factory_id),
  INDEX idx_products_category (category_id),
  INDEX idx_products_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_product_images_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_code VARCHAR(60) NOT NULL UNIQUE,
  buyer_id INT UNSIGNED NOT NULL,
  factory_id INT UNSIGNED DEFAULT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  price_range VARCHAR(120) DEFAULT NULL,
  delivery_location VARCHAR(255) DEFAULT NULL,
  status ENUM('pending','assigned','quoted','accepted','in_production','quality_control','shipped','in_customs','delivered','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_orders_buyer (buyer_id),
  INDEX idx_orders_factory (factory_id),
  INDEX idx_orders_product (product_id),
  INDEX idx_orders_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  unit_price DECIMAL(12,2) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_order_items_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quotations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quote_code VARCHAR(60) NOT NULL UNIQUE,
  order_id INT UNSIGNED NOT NULL,
  product_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  china_local_shipping DECIMAL(12,2) NOT NULL DEFAULT 0,
  export_handling DECIMAL(12,2) NOT NULL DEFAULT 0,
  freight_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  insurance_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  clearing_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  local_delivery_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  hanzo_margin DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_landed_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('draft','sent','accepted','rejected','expired') NOT NULL DEFAULT 'draft',
  valid_until DATE DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_quotations_order (order_id),
  INDEX idx_quotations_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  buyer_id INT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  currency ENUM('USD','TZS') NOT NULL DEFAULT 'USD',
  payment_type VARCHAR(80) DEFAULT NULL,
  method VARCHAR(80) DEFAULT NULL,
  reference VARCHAR(120) DEFAULT NULL,
  proof_file VARCHAR(255) DEFAULT NULL,
  status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  verified_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_payments_order (order_id),
  INDEX idx_payments_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipping_updates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  status_title VARCHAR(120) NOT NULL,
  description TEXT,
  location VARCHAR(120) DEFAULT NULL,
  tracking_number VARCHAR(120) DEFAULT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_shipping_updates_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE production_updates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  factory_id INT UNSIGNED NOT NULL,
  status_title VARCHAR(120) NOT NULL,
  description TEXT,
  photo VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_production_updates_order (order_id),
  INDEX idx_production_updates_factory (factory_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  document_type VARCHAR(80) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_by VARCHAR(40) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_documents_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE factory_products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  factory_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_factory_products_factory (factory_id),
  INDEX idx_factory_products_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  target_role ENUM('admin','buyer','factory') NOT NULL,
  target_id INT UNSIGNED NOT NULL,
  related_order_id INT UNSIGNED DEFAULT NULL,
  title VARCHAR(150) NOT NULL,
  message TEXT,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notifications_target (target_role, target_id),
  INDEX idx_notifications_target_read (target_role, target_id, is_read),
  INDEX idx_notifications_order (related_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admins (full_name, email, password, role, status) VALUES
('HANZO Super Admin', 'admin@hanzo.com', '$2y$10$DGelHuAuI.fsj7iJzKkzd.V/fQZARqH7RYWw3/9LUmp4/2oCtzEK6', 'super_admin', 'active');

INSERT INTO buyers (full_name, company_name, email, phone, country, city, password, status) VALUES
('Amina Juma', 'Apex Retail Ltd', 'buyer@hanzo.com', '+255700111222', 'Tanzania', 'Dar es Salaam', '$2y$10$DGelHuAuI.fsj7iJzKkzd.V/fQZARqH7RYWw3/9LUmp4/2oCtzEK6', 'active');

INSERT INTO factories (factory_name, contact_person, email, phone, city, province, main_products, production_capacity, export_experience, password, status, invited_by) VALUES
('Guangzhou Mobility Tech', 'Li Wei', 'factory@hanzo.com', '+86-20-8800112', 'Guangzhou', 'Guangdong', 'Electric bikes, accessories', '1200 units/month', '8 years export to Africa', '$2y$10$DGelHuAuI.fsj7iJzKkzd.V/fQZARqH7RYWw3/9LUmp4/2oCtzEK6', 'active', 1);

INSERT INTO categories (name, description, status) VALUES
('Fashion', 'Garments and textile products', 'active'),
('Packaging', 'Packaging and branding materials', 'active'),
('Consumer Goods', 'Home and consumer products', 'active'),
('Machinery', 'Industrial machinery and equipment', 'active');

INSERT INTO products (factory_id, category_id, product_name, description, moq, min_price, max_price, main_image, status) VALUES
(1, 4, 'Food Processing Machine', 'Multi-purpose processing machine for SME food plants.', 1, 5500.00, 12000.00, 'uploads/products/seed-10.png', 'active'),
(1, 3, 'Lighting Decoration', 'LED decoration set for retail and events.', 200, 12.50, 28.00, 'uploads/products/seed-3.png', 'active'),
(1, 4, 'Electric Bike', 'Urban electric bike line with battery options.', 10, 580.00, 740.00, 'uploads/products/seed-1.png', 'active'),
(1, 2, 'Packaging Bottles', 'PET and HDPE bottles for FMCG brands.', 10000, 0.08, 0.22, 'uploads/products/seed-7.png', 'active');

INSERT INTO product_images (product_id, image_path) VALUES
(1, 'uploads/products/seed-10.png'),
(2, 'uploads/products/seed-3.png'),
(3, 'uploads/products/seed-1.png'),
(4, 'uploads/products/seed-7.png');

INSERT INTO orders (order_code, buyer_id, factory_id, product_id, quantity, price_range, delivery_location, status) VALUES
('HNZ-ORD-0001', 1, 1, 1, 1, 'US$5,500 - US$12,000', 'Dar es Salaam, Tanzania', 'quoted');

INSERT INTO quotations (quote_code, order_id, product_cost, china_local_shipping, export_handling, freight_cost, insurance_cost, clearing_cost, local_delivery_cost, hanzo_margin, total_landed_cost, status, valid_until) VALUES
('HNZ-Q-0001', 1, 5500, 300, 180, 1200, 80, 650, 260, 500, 8670, 'sent', DATE_ADD(CURDATE(), INTERVAL 14 DAY));

