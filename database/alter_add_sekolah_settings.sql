USE smk_madani;

CREATE TABLE IF NOT EXISTS sekolah_settings (
  id TINYINT UNSIGNED NOT NULL PRIMARY KEY DEFAULT 1,
  nama VARCHAR(150) NOT NULL,
  tagline VARCHAR(255) NULL,
  deskripsi TEXT NULL,
  alamat TEXT NULL,
  telp VARCHAR(50) NULL,
  email VARCHAR(100) NULL,
  website VARCHAR(150) NULL,
  logo_path VARCHAR(255) NULL,
  map_embed_url TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO sekolah_settings (id, nama, tagline)
VALUES (1, 'SMK Madani', 'Future Ready Vocational School');

