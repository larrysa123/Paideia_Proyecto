<?php
// db.php - Archivo de conexión a la base de datos
$host = 'localhost';
$db   = 'paideia_db';
$user = 'root';        // Usuario por defecto en XAMPP
$pass = '';            // Contraseña por defecto en XAMPP (vacía)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Si llegas aquí, la conexión funciona en silencio.
} catch (\PDOException $e) {
    // Si falla, te dirá por qué (solo para desarrollo)
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>