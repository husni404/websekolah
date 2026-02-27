-- SMK Madani - Database Schema
-- Engine: MySQL / MariaDB (XAMPP)
-- Charset: utf8mb4

CREATE DATABASE IF NOT EXISTS indb
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE smky8462_webpertama;

-- Users (admin / staff / guru)
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL, -- bcrypt hash
  role ENUM('admin','staff','guru') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Guru / Staff / Kepala Sekolah
CREATE TABLE IF NOT EXISTS guru (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nip VARCHAR(30) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  mapel VARCHAR(100) NULL,
  jabatan VARCHAR(100) NULL,
  kategori ENUM('guru','staff','kepala') NOT NULL DEFAULT 'guru',
  tugas_tambahan VARCHAR(150) NULL,
  foto VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Kelas
CREATE TABLE IF NOT EXISTS kelas (
  id_kelas INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kelas VARCHAR(50) NOT NULL UNIQUE,
  id_guru INT UNSIGNED NULL, -- wali kelas (guru.id)
  kuota INT UNSIGNED NOT NULL DEFAULT 36,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_kelas_guru
    FOREIGN KEY (id_guru) REFERENCES guru(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jurusan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  singkatan VARCHAR(20) NULL,
  tagline VARCHAR(150) NULL,
  icon VARCHAR(16) NULL,
  color_from VARCHAR(50) NULL,
  color_to VARCHAR(50) NULL,
  highlights TEXT NULL,
  logo_path VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Siswa
CREATE TABLE IF NOT EXISTS siswa (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nisn VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  id_kelas INT UNSIGNED NULL,
  alamat TEXT NULL,
  foto VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_siswa_kelas
    FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- Konten: info/berita/mading/event
CREATE TABLE IF NOT EXISTS konten (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  tipe ENUM('info','berita','mading','event') NOT NULL DEFAULT 'info',
  slug VARCHAR(220) NOT NULL UNIQUE,
  ringkasan TEXT NULL,
  file VARCHAR(255) NULL,
  tgl_upload DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  is_reel TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- Helpful indexes
CREATE INDEX idx_siswa_id_kelas ON siswa(id_kelas);
CREATE INDEX idx_konten_tipe_tgl ON konten(tipe, tgl_upload);
CREATE INDEX idx_konten_reel ON konten(is_reel);


