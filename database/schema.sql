-- ============================================================
-- Sistema de Control de Agencias de Loterías
-- Schema MySQL 8 — ejecutar en XAMPP/phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS loterias_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE loterias_db;

-- ----------------------------------------------------------------
-- USUARIOS
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)  NOT NULL,
    usuario     VARCHAR(50)   NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    rol         ENUM('administrador','operador') NOT NULL DEFAULT 'operador',
    estado      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario),
    INDEX idx_rol     (rol)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- AGENCIAS
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS agencias (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150)  NOT NULL,
    direccion   TEXT,
    telefono    VARCHAR(25),
    estado      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TAQUILLAS
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS taquillas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agencia_id  INT UNSIGNED  NOT NULL,
    nombre      VARCHAR(100)  NOT NULL,
    descripcion TEXT,
    estado      TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_taquilla_agencia FOREIGN KEY (agencia_id)
        REFERENCES agencias(id) ON DELETE CASCADE,
    INDEX idx_agencia (agencia_id),
    INDEX idx_estado  (estado)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- SISTEMAS (Matrix, Premier, Lotipos, etc.)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sistemas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100)  NOT NULL,
    descripcion TEXT,
    estado      TINYINT(1)    NOT NULL DEFAULT 1,
    orden       TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado),
    INDEX idx_orden  (orden)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- VENTAS (cabecera por taquilla/fecha)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ventas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fecha       DATE          NOT NULL,
    agencia_id  INT UNSIGNED  NOT NULL,
    taquilla_id INT UNSIGNED  NOT NULL,
    total_bs    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_usd   DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    usuario_id  INT UNSIGNED  NOT NULL,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_venta_agencia   FOREIGN KEY (agencia_id)  REFERENCES agencias(id),
    CONSTRAINT fk_venta_taquilla  FOREIGN KEY (taquilla_id) REFERENCES taquillas(id),
    CONSTRAINT fk_venta_usuario   FOREIGN KEY (usuario_id)  REFERENCES usuarios(id),
    UNIQUE KEY uq_venta_fecha_taquilla (fecha, taquilla_id),
    INDEX idx_fecha      (fecha),
    INDEX idx_agencia_id (agencia_id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- DETALLE VENTAS (por sistema dentro de la venta)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS detalle_ventas (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    venta_id    INT UNSIGNED  NOT NULL,
    sistema_id  INT UNSIGNED  NOT NULL,
    total_bs    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_usd   DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT fk_dv_venta   FOREIGN KEY (venta_id)   REFERENCES ventas(id)   ON DELETE CASCADE,
    CONSTRAINT fk_dv_sistema FOREIGN KEY (sistema_id) REFERENCES sistemas(id),
    INDEX idx_venta_id   (venta_id),
    INDEX idx_sistema_id (sistema_id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- CUADRES DE CAJA (por agencia/fecha)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cuadres_caja (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fecha           DATE          NOT NULL,
    agencia_id      INT UNSIGNED  NOT NULL,
    punto_banco1    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    punto_banco2    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    punto_banco3    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    efectivo_bs     DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    efectivo_usd    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    pago_movil      DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    premios_pagados DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    compras         DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    otros           DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_bs        DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_usd       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    observaciones   TEXT,
    usuario_id      INT UNSIGNED  NOT NULL,
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cc_agencia  FOREIGN KEY (agencia_id) REFERENCES agencias(id),
    CONSTRAINT fk_cc_usuario  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    UNIQUE KEY uq_cuadre_fecha_agencia (fecha, agencia_id),
    INDEX idx_fecha      (fecha),
    INDEX idx_agencia_id (agencia_id)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- AUDITORÍA
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS auditoria (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id      INT UNSIGNED,
    accion          VARCHAR(100)  NOT NULL,
    tabla           VARCHAR(60),
    registro_id     INT UNSIGNED,
    datos_anteriores JSON,
    datos_nuevos     JSON,
    ip              VARCHAR(45),
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tabla     (tabla),
    INDEX idx_fecha     (created_at)
) ENGINE=InnoDB;

-- ================================================================
-- DATOS INICIALES
-- ================================================================

-- Usuario administrador (password: Admin2024!)
INSERT INTO usuarios (nombre, usuario, password, rol) VALUES
('Administrador', 'admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador');

-- Sistemas de lotería
INSERT INTO sistemas (nombre, descripcion, orden) VALUES
('Matrix',  'Sistema Matrix',   1),
('Premier', 'Sistema Premier',  2),
('Lotipos', 'Sistema Lotipos',  3),
('Gato',    'Sistema Gato',     4),
('Gana',    'Sistema Gana',     5),
('Max P',   'Sistema Max P',    6),
('Parley',  'Sistema Parley',   7),
('Otros',   'Otros sistemas',   8);

-- Agencia de ejemplo
INSERT INTO agencias (nombre, direccion, telefono) VALUES
('Agencia Central', 'Av. Principal, Local 1', '0412-0000000');

-- Taquillas de ejemplo
INSERT INTO taquillas (agencia_id, nombre, descripcion) VALUES
(1, 'Taquilla 1', 'Taquilla principal'),
(1, 'Taquilla 2', 'Taquilla secundaria');
