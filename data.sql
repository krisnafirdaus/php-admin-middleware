-- Create database
CREATE DATABASE training_db;
USE training_db;

-- Users table with roles
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'peserta') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Peserta (participant) details
CREATE TABLE peserta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    nama_lengkap VARCHAR(100),
    email VARCHAR(100),
    no_telp VARCHAR(15),
    alamat TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Pelatih (trainer) details
CREATE TABLE pelatih (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_pelatih VARCHAR(100),
    keahlian VARCHAR(100),
    email VARCHAR(100),
    no_telp VARCHAR(15)
);

-- Program pelatihan
CREATE TABLE program_pelatihan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_program VARCHAR(100),
    deskripsi TEXT,
    durasi VARCHAR(50),
    pelatih_id INT,
    FOREIGN KEY (pelatih_id) REFERENCES pelatih(id)
);

-- Peserta Program (Many-to-Many relationship)
CREATE TABLE peserta_program (
    id INT PRIMARY KEY AUTO_INCREMENT,
    peserta_id INT,
    program_id INT,
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (peserta_id) REFERENCES peserta(id),
    FOREIGN KEY (program_id) REFERENCES program_pelatihan(id)
);

-- Berita/News
CREATE TABLE berita (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200),
    konten TEXT,
    tanggal_post TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);