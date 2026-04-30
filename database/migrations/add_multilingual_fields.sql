-- HANZO multilingual schema update
-- Adds language-specific product/category fields and user preferred language.
-- English remains primary/default fallback.

-- Products: multilingual names/descriptions
ALTER TABLE products
  ADD COLUMN IF NOT EXISTS name_en VARCHAR(200) NULL AFTER product_name,
  ADD COLUMN IF NOT EXISTS name_sw VARCHAR(200) NULL AFTER name_en,
  ADD COLUMN IF NOT EXISTS name_zh VARCHAR(200) NULL AFTER name_sw,
  ADD COLUMN IF NOT EXISTS description_en TEXT NULL AFTER description,
  ADD COLUMN IF NOT EXISTS description_sw TEXT NULL AFTER description_en,
  ADD COLUMN IF NOT EXISTS description_zh TEXT NULL AFTER description_sw;

-- Categories: multilingual names
ALTER TABLE categories
  ADD COLUMN IF NOT EXISTS name_en VARCHAR(150) NULL AFTER name,
  ADD COLUMN IF NOT EXISTS name_sw VARCHAR(150) NULL AFTER name_en,
  ADD COLUMN IF NOT EXISTS name_zh VARCHAR(150) NULL AFTER name_sw;

-- Persist user language preference by role tables
ALTER TABLE buyers
  ADD COLUMN IF NOT EXISTS preferred_language VARCHAR(5) NOT NULL DEFAULT 'en' AFTER status;

ALTER TABLE admins
  ADD COLUMN IF NOT EXISTS preferred_language VARCHAR(5) NOT NULL DEFAULT 'en' AFTER status;

ALTER TABLE factories
  ADD COLUMN IF NOT EXISTS preferred_language VARCHAR(5) NOT NULL DEFAULT 'en' AFTER status;

-- Backfill EN columns from existing legacy text fields if empty.
UPDATE products
SET name_en = COALESCE(NULLIF(name_en, ''), product_name),
    description_en = COALESCE(NULLIF(description_en, ''), description);

UPDATE categories
SET name_en = COALESCE(NULLIF(name_en, ''), name);

