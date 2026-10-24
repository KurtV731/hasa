-- HASA sharing.2: eine Freigabe je Konto und Runde, alle Galaxien gemeinsam.
-- Alte sharing.1-Tabellen bleiben unveraendert, keine automatische Erweiterung alter Freigaben.
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS hasa_round_sharing (
 owner_user_id BIGINT UNSIGNED NOT NULL,
 round_number SMALLINT UNSIGNED NOT NULL,
 audience ENUM('private','selected','alliance','participants') NOT NULL DEFAULT 'private',
 share_reports TINYINT(1) NOT NULL DEFAULT 0,
 revision INT UNSIGNED NOT NULL DEFAULT 1,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY(owner_user_id,round_number),
 FOREIGN KEY(owner_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE,
 CHECK(share_reports IN (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS hasa_round_recipients (
 owner_user_id BIGINT UNSIGNED NOT NULL,
 round_number SMALLINT UNSIGNED NOT NULL,
 recipient_user_id BIGINT UNSIGNED NOT NULL,
 PRIMARY KEY(owner_user_id,round_number,recipient_user_id),
 FOREIGN KEY(owner_user_id,round_number) REFERENCES hasa_round_sharing(owner_user_id,round_number) ON DELETE CASCADE,
 FOREIGN KEY(recipient_user_id) REFERENCES hasa_users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
