-- Add service support to cart table
-- This script modifies the cart table to support both templates and services

-- First, add service_id column
ALTER TABLE cart ADD COLUMN service_id INT NULL;

-- Add foreign key constraint for service_id
ALTER TABLE cart ADD CONSTRAINT fk_cart_service 
FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE;

-- Note: We'll keep the existing unique_user_template constraint
-- and handle the logic in PHP to ensure only one of template_id or service_id is set

-- For MySQL 8.0+ we could add a check constraint, but we'll handle this in PHP for compatibility
-- ALTER TABLE cart ADD CONSTRAINT check_template_or_service
-- CHECK (
--     (template_id IS NOT NULL AND service_id IS NULL) OR 
--     (template_id IS NULL AND service_id IS NOT NULL)
-- );
