<?php
class VentaModel extends Model
{
    protected string $table = 'ventas';

    public function getByFechaAgencia(string $fecha, int $agenciaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT v.*, t.nombre AS taquilla_nombre
             FROM ventas v
             JOIN taquillas t ON t.id = v.taquilla_id
             WHERE v.fecha = ? AND v.agencia_id = ?
             ORDER BY t.nombre"
        );
        $stmt->execute([$fecha, $agenciaId]);
        return $stmt->fetchAll();
    }

    public function getByFechaTaquilla(string $fecha, int $taquillaId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM ventas WHERE fecha = ? AND taquilla_id = ? LIMIT 1"
        );
        $stmt->execute([$fecha, $taquillaId]);
        return $stmt->fetch();
    }

    public function getDetalle(int $ventaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT dv.*, s.nombre AS sistema_nombre
             FROM detalle_ventas dv
             JOIN sistemas s ON s.id = dv.sistema_id
             WHERE dv.venta_id = ?
             ORDER BY s.orden, s.nombre"
        );
        $stmt->execute([$ventaId]);
        return $stmt->fetchAll();
    }

    public function saveVentaCompleta(
        string $fecha,
        int $agenciaId,
        int $taquillaId,
        int $usuarioId,
        array $detalles
    ): int {
        $this->db->beginTransaction();
        try {
            $totalBs  = array_sum(array_column($detalles, 'total_bs'));
            $totalUsd = array_sum(array_column($detalles, 'total_usd'));

            // Upsert cabecera
            $existing = $this->getByFechaTaquilla($fecha, $taquillaId);
            if ($existing) {
                $ventaId = (int)$existing['id'];
                $this->update($ventaId, [
                    'total_bs'   => $totalBs,
                    'total_usd'  => $totalUsd,
                    'usuario_id' => $usuarioId,
                ]);
                $this->db->prepare(
                    "DELETE FROM detalle_ventas WHERE venta_id = ?"
                )->execute([$ventaId]);
            } else {
                $ventaId = $this->insert([
                    'fecha'       => $fecha,
                    'agencia_id'  => $agenciaId,
                    'taquilla_id' => $taquillaId,
                    'total_bs'    => $totalBs,
                    'total_usd'   => $totalUsd,
                    'usuario_id'  => $usuarioId,
                ]);
            }

            // Insertar detalles
            $stmt = $this->db->prepare(
                "INSERT INTO detalle_ventas (venta_id, sistema_id, total_bs, total_usd)
                 VALUES (?, ?, ?, ?)"
            );
            foreach ($detalles as $d) {
                $bs  = (float)($d['total_bs']  ?? 0);
                $usd = (float)($d['total_usd'] ?? 0);
                if ($bs > 0 || $usd > 0) {
                    $stmt->execute([$ventaId, (int)$d['sistema_id'], $bs, $usd]);
                }
            }

            $this->db->commit();
            return $ventaId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getTotalesPorAgenciaFecha(string $fecha, int $agenciaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_bs), 0) AS total_bs,
                    COALESCE(SUM(total_usd),0) AS total_usd
             FROM ventas
             WHERE fecha = ? AND agencia_id = ?"
        );
        $stmt->execute([$fecha, $agenciaId]);
        return $stmt->fetch();
    }

    public function getReportePorAgencia(string $desde, string $hasta): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.nombre AS agencia,
                    COALESCE(SUM(v.total_bs), 0)  AS total_bs,
                    COALESCE(SUM(v.total_usd), 0) AS total_usd,
                    COUNT(DISTINCT v.fecha) AS dias
             FROM ventas v
             JOIN agencias a ON a.id = v.agencia_id
             WHERE v.fecha BETWEEN ? AND ?
             GROUP BY a.id
             ORDER BY a.nombre"
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    public function getReportePorTaquilla(string $desde, string $hasta): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.nombre AS agencia, t.nombre AS taquilla,
                    COALESCE(SUM(v.total_bs), 0)  AS total_bs,
                    COALESCE(SUM(v.total_usd), 0) AS total_usd
             FROM ventas v
             JOIN agencias a ON a.id = v.agencia_id
             JOIN taquillas t ON t.id = v.taquilla_id
             WHERE v.fecha BETWEEN ? AND ?
             GROUP BY v.taquilla_id
             ORDER BY a.nombre, t.nombre"
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    public function getReportePorSistema(string $desde, string $hasta): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.nombre AS sistema,
                    COALESCE(SUM(dv.total_bs), 0)  AS total_bs,
                    COALESCE(SUM(dv.total_usd), 0) AS total_usd
             FROM detalle_ventas dv
             JOIN sistemas s ON s.id = dv.sistema_id
             JOIN ventas v ON v.id = dv.venta_id
             WHERE v.fecha BETWEEN ? AND ?
             GROUP BY dv.sistema_id
             ORDER BY total_bs DESC"
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    public function getVentasHoy(): array
    {
        $hoy = date('Y-m-d');
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_bs),0) AS bs, COALESCE(SUM(total_usd),0) AS usd
             FROM ventas WHERE fecha = ?"
        );
        $stmt->execute([$hoy]);
        return $stmt->fetch();
    }

    public function getRankingTaquillas(string $desde, string $hasta, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.nombre AS taquilla, a.nombre AS agencia,
                    COALESCE(SUM(v.total_bs), 0) AS total_bs,
                    COALESCE(SUM(v.total_usd),0) AS total_usd
             FROM ventas v
             JOIN taquillas t ON t.id = v.taquilla_id
             JOIN agencias  a ON a.id = v.agencia_id
             WHERE v.fecha BETWEEN ? AND ?
             GROUP BY v.taquilla_id
             ORDER BY total_bs DESC
             LIMIT ?"
        );
        $stmt->execute([$desde, $hasta, $limit]);
        return $stmt->fetchAll();
    }

    public function getVentasPorAgenciaChart(string $desde, string $hasta): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.nombre AS label,
                    COALESCE(SUM(v.total_bs),0)  AS bs,
                    COALESCE(SUM(v.total_usd),0) AS usd
             FROM ventas v
             JOIN agencias a ON a.id = v.agencia_id
             WHERE v.fecha BETWEEN ? AND ?
             GROUP BY a.id ORDER BY a.nombre"
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }

    public function getVentasPorSistemaChart(string $desde, string $hasta): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.nombre AS label,
                    COALESCE(SUM(dv.total_bs),0) AS bs
             FROM detalle_ventas dv
             JOIN sistemas s ON s.id = dv.sistema_id
             JOIN ventas v   ON v.id = dv.venta_id
             WHERE v.fecha BETWEEN ? AND ?
             GROUP BY dv.sistema_id ORDER BY bs DESC"
        );
        $stmt->execute([$desde, $hasta]);
        return $stmt->fetchAll();
    }
}
