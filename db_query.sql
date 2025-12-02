-- 02-12-2025 --
ALTER TABLE prc_log ADD INDEX idx_prc_id (prc_id);
ALTER TABLE prc_log ADD INDEX idx_operator_id (operator_id);
ALTER TABLE prc_log ADD INDEX idx_process_id (process_id);
ALTER TABLE prc_master ADD INDEX idx_item_id (item_id);
ALTER TABLE outsource ADD INDEX idx_party_id (party_id);
ALTER TABLE production_inspection ADD INDEX idx_ref_id (ref_id);

ALTER TABLE prc_log ADD INDEX idx_rqc_status (rqc_status);
ALTER TABLE prc_log ADD INDEX idx_trans_type (trans_type);
ALTER TABLE prc_log ADD INDEX idx_process_by (process_by);
ALTER TABLE prc_log ADD INDEX idx_qty (qty);
ALTER TABLE prc_log ADD INDEX idx_trans_date (trans_date);

ALTER TABLE prc_master ADD INDEX idx_prc_number (prc_number);
ALTER TABLE outsource ADD INDEX idx_ch_number (ch_number);
ALTER TABLE party_master ADD INDEX idx_party_name (party_name);
ALTER TABLE item_master ADD INDEX idx_item_name (item_name);
ALTER TABLE process_master ADD INDEX idx_process_name (process_name);