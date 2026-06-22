<?php
class VentasController
{
    private VentaModel    $model;
    private AgenciaModel  $agenciaModel;
    private TaquillaModel $taquillaModel;
    private SistemaModel  $sistemaModel;

    public function __construct()
    {
        $this->model         = new VentaModel();
        $this->agenciaModel  = new AgenciaModel();
        $this->taquillaModel = new TaquillaModel();
        $this->sistemaModel  = new SistemaModel();
    }

    public function index(): void
    {
        Auth::check();
        $desde    = $_GET['desde'] ?? date('Y-m-01');
        $hasta    = $_GET['hasta'] ?? date('Y-m-d');
        $agencias = $this->agenciaModel->getActivas();
        $ventas   = $this->model->getReportePorTaquilla($desde, $hasta);
        require ROOT_PATH . '/views/ventas/index.php';
    }

    public function form(): void
    {
        Auth::check();
        $agencias  = $this->agenciaModel->getActivas();
        $sistemas  = $this->sistemaModel->getActivos();
        $fecha     = $_GET['fecha']      ?? date('Y-m-d');
        $agenciaId = (int)($_GET['agencia_id'] ?? 0);
        $taquillaId = (int)($_GET['taquilla_id'] ?? 0);
        $taquillas = $agenciaId ? $this->taquillaModel->getByAgencia($agenciaId) : [];

        $ventaExistente = null;
        $detalle        = [];
        if ($taquillaId) {
            $ventaExistente = $this->model->getByFechaTaquilla($fecha, $taquillaId);
            if ($ventaExistente) {
                $detalle = $this->model->getDetalle((int)$ventaExistente['id']);
            }
        }
        require ROOT_PATH . '/views/ventas/form.php';
    }

    public function store(): void
    {
        Auth::check();
        Csrf::check();

        $fecha      = $_POST['fecha']       ?? '';
        $agenciaId  = (int)($_POST['agencia_id']  ?? 0);
        $taquillaId = (int)($_POST['taquilla_id'] ?? 0);

        if (!$fecha || !$agenciaId || !$taquillaId) {
            Flash::set('error', 'Fecha, agencia y taquilla son requeridos.');
            header('Location: ' . BASE_URL . '/ventas/form'); exit;
        }

        $detalles = [];
        if (isset($_POST['detalle']) && is_array($_POST['detalle'])) {
            foreach ($_POST['detalle'] as $d) {
                $detalles[] = [
                    'sistema_id' => (int)$d['sistema_id'],
                    'total_bs'   => (float)str_replace(',', '.', $d['total_bs']  ?? 0),
                    'total_usd'  => (float)str_replace(',', '.', $d['total_usd'] ?? 0),
                ];
            }
        }

        $ventaId = $this->model->saveVentaCompleta(
            $fecha, $agenciaId, $taquillaId, Auth::id(), $detalles
        );
        (new AuditoriaModel())->log('GUARDAR_VENTA', 'ventas', $ventaId);
        Flash::set('success', 'Venta registrada correctamente.');
        header('Location: ' . BASE_URL . '/ventas/form?fecha=' . $fecha
            . '&agencia_id=' . $agenciaId . '&taquilla_id=' . $taquillaId);
        exit;
    }
}
