-- Buyer order tracking: link notifications to orders (run once on existing DBs).
-- If `related_order_id` already exists, skip this file.
ALTER TABLE notifications
  ADD COLUMN related_order_id INT UNSIGNED DEFAULT NULL AFTER target_id,
  ADD INDEX idx_notifications_target_read (target_role, target_id, is_read),
  ADD INDEX idx_notifications_order (related_order_id);
