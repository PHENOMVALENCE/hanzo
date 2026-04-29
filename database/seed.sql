SET NAMES utf8mb4;

INSERT INTO users (email, password_hash, full_name, role) VALUES
('admin@hanzo.co.tz', '$2y$10$DGelHuAuI.fsj7iJzKkzd.V/fQZARqH7RYWw3/9LUmp4/2oCtzEK6', 'HANZO Administrator', 'admin');

INSERT INTO categories (name, slug, description, sort_order) VALUES
('Fashion & Textile', 'fashion-textile', 'Apparel, fabrics, and textile machinery.', 1),
('Packaging & Branding', 'packaging-branding', 'Bottles, labels, and retail-ready packaging.', 2),
('Consumer Goods', 'consumer-goods', 'Household and lifestyle products.', 3),
('Machinery & Equipment', 'machinery-equipment', 'Industrial and processing machinery.', 4),
('Electronics', 'electronics', 'Components and finished electronic goods.', 5),
('Construction Materials', 'construction-materials', 'Structural and finishing materials.', 6),
('Auto & Motorcycle Parts', 'auto-motorcycle-parts', 'OEM and aftermarket vehicle parts.', 7),
('Beauty & Personal Care', 'beauty-personal-care', 'Cosmetics tools and personal care.', 8),
('Home & Kitchen', 'home-kitchen', 'Cookware and home essentials.', 9),
('Agriculture Equipment', 'agriculture-equipment', 'Farming tools and implements.', 10),
('Roofing & Building Materials', 'roofing-building-materials', 'Sheets, tiles, and weatherproofing.', 11),
('Bikes & Motorcycles', 'bikes-motorcycles', 'Two-wheel vehicles and accessories.', 12);

INSERT INTO products (category_id, name, slug, short_description, description, price_min, price_max, moq, unit, status, main_image, is_trending, is_hot) VALUES
(12, 'Electric Bike', 'electric-bike', 'City commuter e-bike with removable battery.', 'Verified factory supply for East Africa distributors. Battery certified, frame warranty options through HANZO.', 580.00, 740.00, 10, 'Piece', 'active', 'uploads/products/seed-1.png', 1, 1),
(4, 'Cutting Machinery', 'cutting-machinery', 'Precision cutting line for sheet materials.', 'Industrial cutting machinery suitable for metal and composite workshops. Installation support coordinated via HANZO.', 4200.00, 6800.00, 1, 'Set', 'active', 'uploads/products/seed-2.png', 1, 0),
(3, 'Lighting Decoration', 'lighting-decoration', 'LED decorative lighting for retail and hospitality.', 'Energy-efficient decorative lighting with MOQ-friendly packaging for regional importers.', 12.50, 28.00, 200, 'Piece', 'active', 'uploads/products/seed-3.png', 0, 1),
(2, 'Storage Cage', 'storage-cage', 'Collapsible wire storage cages for logistics.', 'Heavy-duty logistics cages for warehouses and distribution centres.', 85.00, 120.00, 50, 'Piece', 'active', 'uploads/products/seed-4.png', 1, 1),
(4, 'Diesel Generator Set', 'diesel-generator-set', 'Standby and prime power generator sets.', 'Sound-attenuated canopies available. Specifications matched to your site survey through HANZO.', 3200.00, 9500.00, 1, 'Set', 'active', 'uploads/products/seed-5.png', 0, 1),
(11, 'Roofing Sheet', 'roofing-sheet', 'Colour-coated steel roofing sheets.', 'Long-span roofing profiles suitable for commercial and residential projects in coastal climates.', 4.20, 6.80, 500, 'Sheet', 'active', 'uploads/products/seed-6.png', 1, 0),
(2, 'Packaging Bottles', 'packaging-bottles', 'PET and HDPE bottles for cosmetics and beverages.', 'Food-grade and cosmetic-grade bottles with custom cap options. Artwork handled through HANZO branding desk.', 0.08, 0.22, 10000, 'Piece', 'active', 'uploads/products/seed-7.png', 0, 1),
(8, 'Beauty Mirror', 'beauty-mirror', 'LED vanity mirrors for salons and retail.', 'Touch dimmer, anti-fog option. Retail-ready inner cartons.', 18.00, 45.00, 100, 'Piece', 'active', 'uploads/products/seed-8.png', 1, 0),
(1, 'Sewing Machine', 'sewing-machine', 'Industrial flat-bed sewing machines.', 'Suitable for garment workshops. Training and spare parts via HANZO service partners.', 380.00, 520.00, 5, 'Piece', 'active', 'uploads/products/seed-9.png', 0, 1),
(4, 'Food Processing Machine', 'food-processing-machine', 'Multi-purpose food processing lines.', 'Milling, mixing, and packaging options. Factory audit available before PO.', 5500.00, 12000.00, 1, 'Line', 'active', 'uploads/products/seed-10.png', 1, 1);

INSERT INTO product_images (product_id, path, sort_order) VALUES
(1, 'uploads/products/seed-1.png', 0),
(2, 'uploads/products/seed-2.png', 0),
(3, 'uploads/products/seed-3.png', 0),
(4, 'uploads/products/seed-4.png', 0),
(5, 'uploads/products/seed-5.png', 0);

INSERT INTO product_specifications (product_id, spec_label, spec_value, sort_order) VALUES
(1, 'Motor', '250W brushless hub', 1),
(1, 'Battery', '36V 10Ah removable', 2),
(1, 'Range', '40–55 km (estimate)', 3),
(2, 'Power', '3 kW servo', 1),
(2, 'Table size', '2500 x 1250 mm', 2),
(3, 'IP rating', 'IP44', 1),
(3, 'Colour temperature', '3000K / 4000K / 6000K', 2),
(4, 'Load capacity', '800 kg static', 1),
(4, 'Folded height', '280 mm', 2),
(5, 'Output', '20–500 kVA (configurable)', 1),
(5, 'Voltage', '400/230V three-phase', 2),
(6, 'Thickness', '0.35–0.50 mm', 1),
(6, 'Profile', 'IBR / corrugated', 2),
(7, 'Volume', '30–500 ml', 1),
(7, 'Material', 'PET / HDPE', 2),
(8, 'Diameter', '600 mm', 1),
(8, 'Power', 'USB 5V adapter', 2),
(9, 'Stitch types', '28 built-in', 1),
(9, 'Max speed', '5500 spm', 2),
(10, 'Throughput', 'Line-dependent', 1),
(10, 'Certification', 'CE (factory dependent)', 2);

INSERT INTO freight_rates (route_name, origin, destination, rate_per_kg, rate_per_cbm, valid_until) VALUES
('FCL hint — China to Dar', 'Shanghai', 'Dar es Salaam', NULL, 185.00, '2026-12-31'),
('Airfreight express', 'Guangzhou', 'Nairobi', 4.50, NULL, '2026-12-31');
