USE smk_madani;

ALTER TABLE guru
  ADD COLUMN kategori ENUM('guru','staff','kepala') NOT NULL DEFAULT 'guru' AFTER jabatan,
  ADD COLUMN tugas_tambahan VARCHAR(150) NULL AFTER kategori;

