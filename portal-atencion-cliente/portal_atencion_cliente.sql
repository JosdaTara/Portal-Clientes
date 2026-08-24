-- =====================================================
-- BASE DE DATOS: PORTAL DE ATENCION AL CLIENTE
-- Motor: MySQL 8.x | Charset: utf8mb4
-- =====================================================

CREATE DATABASE IF NOT EXISTS portal_atencion_cliente
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE portal_atencion_cliente;

-- =====================================================
-- TABLA: usuarios
-- =====================================================
CREATE TABLE usuarios (
    id_usuario       INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(100)  NOT NULL,
    apellido         VARCHAR(100)  NOT NULL,
    email            VARCHAR(150)  NOT NULL UNIQUE,
    telefono         VARCHAR(20)   NULL,
    contrasena       VARCHAR(255)  NOT NULL,
    rol              ENUM('cliente', 'agente', 'administrador') NOT NULL DEFAULT 'cliente',
    estado_cuenta    ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    fecha_registro   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso    DATETIME      NULL
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: categorias
-- =====================================================
CREATE TABLE categorias (
    id_categoria     INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(100)  NOT NULL UNIQUE,
    descripcion      TEXT          NULL,
    estado           ENUM('activa', 'inactiva') NOT NULL DEFAULT 'activa'
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: solicitudes
-- =====================================================
CREATE TABLE solicitudes (
    id_solicitud         INT AUTO_INCREMENT PRIMARY KEY,
    numero_caso          VARCHAR(20)   NOT NULL UNIQUE,
    id_cliente           INT           NOT NULL,
    id_categoria         INT           NOT NULL,
    asunto               VARCHAR(200)  NOT NULL,
    descripcion          TEXT          NOT NULL,
    prioridad            ENUM('baja', 'media', 'alta', 'urgente') NOT NULL DEFAULT 'media',
    estado               ENUM('pendiente', 'en_proceso', 'atendida', 'cerrada') NOT NULL DEFAULT 'pendiente',
    fecha_creacion       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion  DATETIME      NULL ON UPDATE CURRENT_TIMESTAMP,
    fecha_cierre         DATETIME      NULL,

    FOREIGN KEY (id_cliente)   REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: seguimientos
-- =====================================================
CREATE TABLE seguimientos (
    id_seguimiento    INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud      INT           NOT NULL,
    id_usuario        INT           NOT NULL,
    comentario        TEXT          NOT NULL,
    estado_anterior   ENUM('pendiente', 'en_proceso', 'atendida', 'cerrada') NULL,
    estado_nuevo      ENUM('pendiente', 'en_proceso', 'atendida', 'cerrada') NULL,
    fecha_seguimiento DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_solicitud) REFERENCES solicitudes(id_solicitud) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_usuario)   REFERENCES usuarios(id_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABLA: reportes_mensuales
-- =====================================================
CREATE TABLE reportes_mensuales (
    id_reporte            INT AUTO_INCREMENT PRIMARY KEY,
    mes                   TINYINT       NOT NULL,
    anio                  SMALLINT      NOT NULL,
    total_solicitudes     INT           NOT NULL DEFAULT 0,
    total_pendientes      INT           NOT NULL DEFAULT 0,
    total_en_proceso      INT           NOT NULL DEFAULT 0,
    total_atendidas       INT           NOT NULL DEFAULT 0,
    total_cerradas        INT           NOT NULL DEFAULT 0,
    tiempo_promedio_horas DECIMAL(10,2) NULL,
    fecha_generacion      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    generado_por          INT           NULL,

    FOREIGN KEY (generado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL ON UPDATE CASCADE,
    UNIQUE KEY uq_mes_anio (mes, anio)
) ENGINE=InnoDB;

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Usuarios iniciales (contrasena: admin123 para los 3)
-- Si los hashes no funcionan, ejecutar desde PHP:
--   echo password_hash('admin123', PASSWORD_BCRYPT);
-- Y reemplazar en los VALUES de abajo.
-- Los hashes de abajo corresponden a 'admin123'
INSERT INTO usuarios (nombre, apellido, email, telefono, contrasena, rol) VALUES
('Admin', 'Sistema', 'admin@portal.com', '3000000000', '$2y$10$aEkpT8b9KXHbPv7IFUFCp.JH6Pc.U4Ob25vVb96BGUdihZzJte8D.', 'administrador'),
('Carlos', 'Ruiz', 'carlos.ruiz@portal.com', '3001111111', '$2y$10$aEkpT8b9KXHbPv7IFUFCp.JH6Pc.U4Ob25vVb96BGUdihZzJte8D.', 'agente'),
('Maria', 'Lopez', 'maria.lopez@email.com', '3002222222', '$2y$10$aEkpT8b9KXHbPv7IFUFCp.JH6Pc.U4Ob25vVb96BGUdihZzJte8D.', 'cliente');

-- Categorias iniciales
INSERT INTO categorias (nombre, descripcion) VALUES
('Consulta general', 'Preguntas sobre productos o servicios de la empresa'),
('Reclamo', 'Queja formal sobre un servicio o producto recibido'),
('Soporte tecnico', 'Solicitud de ayuda tecnica con un producto o servicio'),
('Sugerencia', 'Propuesta de mejora para productos, servicios o procesos'),
('Solicitud administrativa', 'Cambios en datos personales, facturacion, contratos, etc.');

-- Solicitudes de ejemplo
INSERT INTO solicitudes (numero_caso, id_cliente, id_categoria, asunto, descripcion, prioridad, estado, fecha_creacion, fecha_cierre) VALUES
('CASO-2026-00001', 3, 1, 'Consulta sobre plan premium', 'Quisiera informacion detallada sobre las ventajas del plan premium y sus costos mensuales.', 'media', 'cerrada', '2026-07-01 09:15:00', '2026-07-01 14:30:00'),
('CASO-2026-00002', 3, 2, 'Producto defectuoso recibido', 'Recibi el producto con el empaque danado y una pieza rota. Solicito reemplazo.', 'alta', 'cerrada', '2026-07-03 11:20:00', '2026-07-05 10:00:00'),
('CASO-2026-00003', 3, 3, 'No puedo iniciar sesion', 'Desde ayer no puedo acceder a mi cuenta. Me sale error de autenticacion.', 'alta', 'atendida', '2026-07-10 08:45:00', NULL),
('CASO-2026-00004', 3, 4, 'Sugerencia de mejora', 'Propongo implementar un chat en vivo para atencion inmediata al cliente.', 'baja', 'en_proceso', '2026-07-15 14:32:00', NULL),
('CASO-2026-00005', 3, 5, 'Cambio de direccion', 'Solicito actualizacion de mi direccion de facturacion.', 'media', 'pendiente', '2026-07-20 16:10:00', NULL);

-- Seguimientos de ejemplo
INSERT INTO seguimientos (id_solicitud, id_usuario, comentario, estado_anterior, estado_nuevo) VALUES
(1, 2, 'Se envio informacion detallada del plan premium por correo electronico.', 'pendiente', 'atendida'),
(1, 2, 'Cliente confirma recepcion de informacion. Caso cerrado.', 'atendida', 'cerrada'),
(2, 2, 'Se solicito al cliente fotos del producto danado.', 'pendiente', 'en_proceso'),
(2, 2, 'Fotos recibidas. Se autoriza reemplazo del producto.', 'en_proceso', 'atendida'),
(2, 2, 'Producto reemplazado entregado al cliente.', 'atendida', 'cerrada'),
(3, 2, 'Se reviso el problema de acceso. Se reseteo la contrasena del cliente.', 'pendiente', 'en_proceso'),
(3, 2, 'Cliente pudo acceder. Caso resuelto.', 'en_proceso', 'atendida'),
(4, 2, 'Sugerencia registrada para evaluacion del equipo directivo.', 'pendiente', 'en_proceso');
