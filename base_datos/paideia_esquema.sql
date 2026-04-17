-- 1. TABLA ROL
CREATE TABLE Rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol ENUM('alumno', 'profesor', 'administrador') NOT NULL
) ENGINE=InnoDB;

-- Insertamos los roles básicos obligatorios
INSERT INTO Rol (nombre_rol) VALUES ('alumno'), ('profesor'), ('administrador');

-- 2. TABLA USUARIO
CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    foto VARCHAR(255),
    id_rol INT NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES Rol(id_rol) ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. TABLA CURSO
CREATE TABLE Curso (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    id_profesor INT NOT NULL, -- Añadido para vincular al creador del curso
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    imagen VARCHAR(255),
    estado ENUM('pendiente', 'publicado') DEFAULT 'pendiente',
    valoracion_media DECIMAL(3,2) DEFAULT 0.00, -- Extraído de tu diagrama E/R
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_profesor) REFERENCES Usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. TABLA INSCRIPCION
CREATE TABLE Inscripcion (
    id_usuario INT NOT NULL,
    id_curso INT NOT NULL,
    fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP, -- Ajustado al nombre de tu diagrama
    progreso DECIMAL(5,2) DEFAULT 0.00,
    PRIMARY KEY (id_usuario, id_curso),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_curso) REFERENCES Curso(id_curso) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. TABLA PEDIDO (Gestión del carrito)
CREATE TABLE Pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) NOT NULL,
    metodo_pago VARCHAR(50),
    estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente',
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. TABLA DETALLE_PEDIDO
CREATE TABLE Detalle_Pedido (
    id_pedido INT NOT NULL,
    id_curso INT NOT NULL,
    precio_uni DECIMAL(10, 2) NOT NULL,
    cantidad INT DEFAULT 1,
    PRIMARY KEY (id_pedido, id_curso),
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido) ON DELETE CASCADE,
    FOREIGN KEY (id_curso) REFERENCES Curso(id_curso) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 7. TABLA VIDEO
CREATE TABLE Video (
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    id_curso INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    url_youtube VARCHAR(255) NOT NULL, -- Ajustado al nombre de tu diagrama
    orden INT NOT NULL,
    valoracion_media DECIMAL(3,2) DEFAULT 0.00, -- Extraído de tu diagrama E/R
    FOREIGN KEY (id_curso) REFERENCES Curso(id_curso) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. TABLA COMENTARIO_VIDEO
CREATE TABLE Comentario_Video (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(50),
    id_usuario INT NOT NULL,
    id_video INT NOT NULL,
    id_padre INT DEFAULT NULL, 
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_video) REFERENCES Video(id_video) ON DELETE CASCADE,
    FOREIGN KEY (id_padre) REFERENCES Comentario_Video(id_comentario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. TABLA COMENTARIO_CURSO
CREATE TABLE Comentario_Curso (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(50),
    id_usuario INT NOT NULL,
    id_curso INT NOT NULL,
    id_padre INT DEFAULT NULL, 
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_curso) REFERENCES Curso(id_curso) ON DELETE CASCADE,
    FOREIGN KEY (id_padre) REFERENCES Comentario_Curso(id_comentario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. VALORACION_CURSO (Estrellas)
CREATE TABLE Valoracion_Curso (
    id_usuario INT NOT NULL,
    id_curso INT NOT NULL,
    estrellas INT CHECK (estrellas BETWEEN 1 AND 5),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_curso),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_curso) REFERENCES Curso(id_curso) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. VALORACION_VIDEO (Estrellas)
CREATE TABLE Valoracion_Video (
    id_usuario INT NOT NULL,
    id_video INT NOT NULL,
    estrellas INT CHECK (estrellas BETWEEN 1 AND 5),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_video),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_video) REFERENCES Video(id_video) ON DELETE CASCADE
) ENGINE=InnoDB;