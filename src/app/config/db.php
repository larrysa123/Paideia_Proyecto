<?php
// db.php - Archivo de conexión a la base de datos (Configurado para entorno local/servidor)

class Conexion {
    private static $host = '127.0.0.1'; 
    private static $db_name = 'paideia_db'; 
    private static $username = 'root';
    private static $password = '';
    private static $charset = 'utf8mb4';
    private static $conn;

    public static function conectar() {
        if (self::$conn == null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=" . self::$charset;
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                
                self::$conn = new PDO($dsn, self::$username, self::$password, $options);
                
            } catch(PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>