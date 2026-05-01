-- Payment currency: buyer declares whether amount is in USD or Tanzanian shillings (TZS).
ALTER TABLE payments
  ADD COLUMN currency ENUM('USD', 'TZS') NOT NULL DEFAULT 'USD' AFTER amount;
