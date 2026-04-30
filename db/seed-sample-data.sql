-- Sample Data for Lelis Realty

-- Insert sample sellers
INSERT INTO sellers (name, email, phone, address, city, state, zip, property_count, status, created_at) VALUES
('John Mitchell', 'john.mitchell@example.com', '555-0101', '1420 Emerald Grove', 'Beverly Hills', 'CA', '90210', 3, 'active', NOW()),
('Sarah Chen', 'sarah.chen@example.com', '555-0102', '88 Downtown Ave', 'San Francisco', 'CA', '94107', 2, 'active', NOW()),
('Elizabeth Warren', 'elizabeth.w@example.com', '555-0103', '200 E 66th St', 'New York', 'NY', '10065', 1, 'active', NOW());

-- Insert sample properties
INSERT INTO properties (title, description, address, city, state, zip, bedrooms, bathrooms, sqft, property_type, price, status, seller_id, images, created_at) VALUES
('The Glass House Reserve', 'Architectural masterpiece nestled in the rolling hills of Carmel Valley. Features expansive floor-to-ceiling smart glass walls that frame panoramic views of the surrounding landscape.', '1420 Ridgeview Drive', 'Beverly Hills', 'CA', '90210', 6, 7.5, 12000, 'residential', 8500000, 'available', 1, '/images/property-1.jpg', NOW()),
('Urban Oasis Loft', 'Contemporary luxury living in the heart of the city. Soaring ceilings, designer finishes, and direct access to exclusive shopping and cultural venues.', '800 Market St', 'San Francisco', 'CA', '94107', 4, 3.5, 4500, 'commercial', 5200000, 'available', 2, '/images/property-2.jpg', NOW()),
('Heritage Penthouse', 'Stunning penthouse with private rooftop terrace offering 360-degree city views. Premium building amenities and white-glove concierge service.', '200 E 66th St', 'New York', 'NY', '10065', 3, 2.5, 3800, 'residential', 7400000, 'available', 3, '/images/property-3.jpg', NOW());

-- Insert sample buyers
INSERT INTO buyers (name, email, phone, preferences, budget_min, budget_max, status, created_at) VALUES
('Michael Johnson', 'michael.j@example.com', '555-0201', 'Luxury estates, gated communities', 2000000, 10000000, 'active', NOW()),
('Amanda Rodriguez', 'amanda.r@example.com', '555-0202', 'Downtown apartments, urban living', 1500000, 5000000, 'active', NOW());

-- Insert sample inquiries
INSERT INTO inquiries (property_id, buyer_name, buyer_email, buyer_phone, message, status, created_at) VALUES
(1, 'Michael Johnson', 'michael.j@example.com', '555-0201', 'Very interested in this property. Would like to schedule a private viewing.', 'pending', NOW()),
(2, 'Amanda Rodriguez', 'amanda.r@example.com', '555-0202', 'Is this property still available? Any flexibility on price?', 'pending', NOW());

-- Insert sample appointments
INSERT INTO appointments (property_id, seller_id, buyer_name, buyer_email, appointment_time, type, status, notes, created_at) VALUES
(1, 1, 'Michael Johnson', 'michael.j@example.com', DATE_ADD(NOW(), INTERVAL 3 DAY), 'viewing', 'scheduled', 'Client interested in property for investment purposes', NOW()),
(2, 2, 'Amanda Rodriguez', 'amanda.r@example.com', DATE_ADD(NOW(), INTERVAL 5 DAY), 'consultation', 'scheduled', 'Initial consultation about market trends', NOW());
