-- 1. TABLA ROL
-- Separamos los roles en una tabla propia para normalizar (Usuario N - 1 Rol)
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
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    imagen VARCHAR(255),
    estado ENUM('pendiente', 'publicado') DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. TABLA INSCRIPCION (Corrección Tutor: CLAVE COMPUESTA)
-- No usamos un id_inscripcion inventado. La clave es la pareja usuario+curso.
CREATE TABLE Inscripcion (
    id_usuario INT NOT NULL,
    id_curso INT NOT NULL,
    fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    progreso DECIMAL(5,2) DEFAULT 0.00, -- Ej: 50.5% completado
    PRIMARY KEY (id_usuario, id_curso),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_curso) REFERENCES Curso(id_curso) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. TABLA VIDEO
CREATE TABLE Video (
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    id_curso INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    url_video VARCHAR(255) NOT NULL, -- URL de Youtube o archivo local
    orden INT NOT NULL, -- Para saber si es el video 1, 2, 3...
    FOREIGN KEY (id_curso) REFERENCES Curso(id_curso) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. TABLA COMENTARIO (Sistema de Hilos)
-- id_padre NULL = Pregunta nueva. 
-- id_padre con número = Respuesta a esa pregunta.
CREATE TABLE Comentario (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    contenido TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT NOT NULL,
    id_video INT NOT NULL,
    id_padre INT DEFAULT NULL, 
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_video) REFERENCES Video(id_video) ON DELETE CASCADE,
    FOREIGN KEY (id_padre) REFERENCES Comentario(id_comentario) ON DELETE CASCADE
) ENGINE=InnoDB;