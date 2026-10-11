CREATE DATABASE QuanLySanPham;
USE QuanLySanPham;

CREATE TABLE SanPham (
    MaSP INT AUTO_INCREMENT PRIMARY KEY,
    TenSP VARCHAR(100) NOT NULL,
    Loai VARCHAR(50),
    ThuongHieu VARCHAR(100),
    Gia DECIMAL(12,2) NOT NULL,
    SoLuong INT NOT NULL
);

INSERT INTO SanPham (TenSP, Loai, ThuongHieu, Gia, SoLuong) VALUES
('Son Romand', 'Son môi', 'Romand', 180000, 20),
('Sữa rửa mặt', 'Chăm sóc da', 'COSRX', 220000, 15),
('Kem chống nắng', 'Chăm sóc da', 'Skin1004', 250000, 10),
('Nước tẩy trang', 'Tẩy trang', 'Bioderma', 190000, 12),
('Kem dưỡng ẩm', 'Chăm sóc da', 'CeraVe', 320000, 8);