<?php
class CuadreCajaModel extends Model
{
    protected string $table = 'cuadres_caja';

    public function getByFechaAgencia(string $fecha, int $agenciaId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM cuadres_caja WHERE fecha = ? AND agencia_id = ? LIMIT 1"
        );
        $stmt->execute([$fecha, $agenciaId]);
        return $stmt->fetch();
    }

    public function save(array $data, int $id = 0): int|bool
    {
        $numFields = [
            'punto_banco1', 'punto_banco2', 'punto_banco3',
            'efectivo_bs', 'efectivo_usd', 'pago_movil',
            'premios_pagados', 'compras', 'otros',
        ];
        $fields = [
            'fecha'      => $data['fecha'],
            'agencia_id' => (int)$data['agencia_id'],
            'usuario_id' => (int)$data['usuario_id'],
        ];
        foreach ($numFields as $f) {
            $fields[$f] = (float)($data[$f] ?? 0);
        }
        // Calcular totales
        $fields['total_bs'] = $fields['punto_banco1'] + $fields['punto_banco2']
            + $fields['punto_banco3'] + $fields['efectivo_bs']
            + $fields['pago_movil'] + $fields['premios_pagados']
            + $fields['compras'] + $fields['otros'];
        $fields['total_usd']       = $fields['efectivo_usd'];
        $fields['observaciones']   = trim($data['observaciones'] ?? '');

        if ($id > 0) {
            return $this->update($id, $fields);
        }
        return $this->insert($fields);
    }

    public function saveOrUpdate(array $data): int
    {
        $existing = $this->getByFechaAgencia($data['fecha'], (int)$data['agencia_id']);
        if ($existing) {
            $this->save($data, (int)$existing['id']);
            return (int)$existing['id'];
        }
        return (int)$this->save($data);
    }

    public function getAllWithAgencia(string $desde = '', string $hasta = ''): array
    {
        $where = '';
        $params = [];
        if ($desde && $hasta) {
            $where = "WHERE c.fecha BETWEEN ? AND ?";
            $params = [$desde, $hasta];
        }
        $stmt = $this->db->prepare(
            "SELECT c.*, a.nombre AS agencia_nombre
             FROM cuadres_caja c
             JOIN agencias a ON a.id = c.agencia_id
             {$where}
             ORDER BY c.fecha DESC, a.nombre"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getConciliacion(string $fecha): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                a.id AS agencia_id,
                a.nombre AS agencia,
                COALESCE(SUM(v.total_bs),  0) AS ventas_bs,
                COALESCE(SUM(v.total_usd), 0) AS ventas_usd,
                COALESCE(MAX(c.total_bs),  0) AS caja_bs,
                COALESCE(MAX(c.total_usd), 0) AS caja_usd
             FROM agencias a
             LEFT JOIN ventas v       ON v.agencia_id = a.id  AND v.fecha = ?
             LEFT JOIN cuadres_caja c ON c.agencia_id = a.id  AND c.fecha = ?
             WHERE a.estado = 1
             GROUP BY a.id
             ORDER BY a.nombre"
        );
        $stmt->execute([$fecha, $fecha]);
        return $stmt->fetchAll();
    }

    public function getPendientes(string $fecha): int
    {
        // Agencias activas sin cuadre en esa fecha
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM agencias a
             WHERE a.estado = 1
               AND NOT EXISTS (
                   SELECT 1 FROM cuadres_caja c
                   WHERE c.agencia_id = a.id AND c.fecha = ?
               )"
        );
        $stmt->execute([$fecha]);
        return (int)$stmt->fetchColumn();
    }
}
