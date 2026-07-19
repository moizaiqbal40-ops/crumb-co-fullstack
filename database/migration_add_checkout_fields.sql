-- Run this ONLY if you already imported schema.sql before and don't want to start over.
-- (If you haven't imported the database yet, just use the updated schema.sql instead — you don't need this file.)

USE crumb_co;

ALTER TABLE orders
  ADD COLUMN delivery_address VARCHAR(255) DEFAULT NULL AFTER customer_phone,
  ADD COLUMN payment_method VARCHAR(30) DEFAULT 'cod' AFTER delivery_address;
