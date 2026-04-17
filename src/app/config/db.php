<?php
// db.php - Archivo de conexión a la base de datos (Versión MVC Mejorada)

class Conexion {
    private static $host = 'localhost';
    private static $db_name = 'paideia_db'; // Tu base de datos
    private static $username = 'root';
    private static $password = '';
    private static $charset = 'utf8mb4';
    private static $conn;

    public static function conectar() {
        // Si la conexión ya existe, no la volvemos a crear (Patrón Singleton)
        if (self::$conn == null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=" . self::$charset;
                
                // ¡Tus opciones pro integradas!
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                
                self::$conn = new PDO($dsn, self::$username, self::$password, $options);
                
            } catch(PDOException $e) {
                // Frenamos la ejecución y mostramos el error si algo falla
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>