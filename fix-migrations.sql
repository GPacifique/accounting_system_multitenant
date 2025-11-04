-- SQL Script to manually mark migrations as completed
-- Run this in your MySQL/MariaDB console if the PHP script doesn't work

-- Check current migrations
SELECT * FROM migrations ORDER BY id;

-- Mark problematic migrations as completed (only run if they don't exist)
INSERT IGNORE INTO migrations (migration, batch) VALUES
('2025_10_31_124000_create_orders_table', 1),
('2025_10_31_124100_create_order_items_table', 1),
('2025_10_31_150000_create_products_table', 1),
('2025_11_03_120000_create_worker_payments_table', 1);

-- Verify the migrations were added
SELECT * FROM migrations WHERE migration LIKE '2025_10_31%' OR migration LIKE '2025_11_03%';