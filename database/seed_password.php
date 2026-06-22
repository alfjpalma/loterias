<?php
/**
 * Ejecutar este script UNA VEZ para actualizar el hash del admin
 * URL: http://localhost/loterias/database/seed_password.php
 */
require_once dirname(__DIR__) . '/config/config.php';

$hash = password_hash('Admin2024!', PASSWORD_BCRYPT, ['cost' => 12]);
$pdo  = getDB();
$stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE usuario = 'admin'");
$stmt->execute([$hash]);

echo '<b>Contraseña actualizada.</b><br>';
echo 'Usuario: <b>admin</b><br>';
echo 'Contraseña: <b>Admin2024!</b><br>';
echo '<br><a href="' . BASE_URL . '/login">Ir al login</a>';
