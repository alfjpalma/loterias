<?php
require_once dirname(__DIR__) . '/config/config.php';

header('Content-Type: application/json');
Auth::check();

$agenciaId = (int)($_GET['agencia_id'] ?? 0);
if (!$agenciaId) {
    echo json_encode([]);
    exit;
}

$model = new TaquillaModel();
$taquillas = $model->getByAgencia($agenciaId);
echo json_encode($taquillas);
