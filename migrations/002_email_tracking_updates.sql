-- Add tracking metadata and queue fields
ALTER TABLE email_messages
  ADD COLUMN tracking_id varchar(64) DEFAULT NULL AFTER tracking_token,
  ADD COLUMN gmail_message_id varchar(128) DEFAULT NULL AFTER message_id,
  ADD COLUMN gmail_thread_id varchar(128) DEFAULT NULL AFTER gmail_message_id,
  ADD COLUMN clicked_at timestamp NULL DEFAULT NULL AFTER replied_at,
  ADD COLUMN bounced_at timestamp NULL DEFAULT NULL AFTER clicked_at,
  ADD COLUMN last_checked_at timestamp NULL DEFAULT NULL AFTER last_event_at,
  ADD COLUMN updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

CREATE INDEX idx_email_messages_gmail_thread ON email_messages (gmail_thread_id);
CREATE INDEX idx_email_messages_gmail_message ON email_messages (gmail_message_id);
CREATE INDEX idx_email_messages_status ON email_messages (status);

ALTER TABLE invitation_queue
  ADD COLUMN attempts int(11) DEFAULT 0 AFTER status,
  ADD COLUMN locked_at timestamp NULL DEFAULT NULL AFTER attempts,
  ADD COLUMN locked_by varchar(64) DEFAULT NULL AFTER locked_at,
  ADD COLUMN last_attempt_at timestamp NULL DEFAULT NULL AFTER locked_by;

CREATE INDEX idx_invitation_queue_status ON invitation_queue (status);
CREATE INDEX idx_invitation_queue_locked ON invitation_queue (locked_at);

CREATE TABLE email_events (
  id int(11) NOT NULL AUTO_INCREMENT,
  email_message_id int(11) NOT NULL,
  event_type varchar(20) NOT NULL,
  event_key varchar(128) NOT NULL,
  event_at timestamp NOT NULL DEFAULT current_timestamp(),
  meta_json text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY uniq_email_event (email_message_id, event_type, event_key),
  KEY idx_email_events_message (email_message_id),
  CONSTRAINT fk_email_events_message
    FOREIGN KEY (email_message_id) REFERENCES email_messages(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
