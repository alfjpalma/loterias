<?php
class AgenciaModel extends Model
{
    protected string $table = 'agencias';

    public function getActivas(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM agencias WHERE estado = 1 ORDER BY nombre"
        );
        return $stmt->fetchAll();
    }

    public function save(array $data, int $id = 0): int|bool
    {
        $fields = [
            'nombre'    => trim($data['nombre']),
            'direccion' => trim($data['direccion'] ?? ''),
            'telefono'  => trim($data['telefono']  ?? ''),
            'estado'    => (int)($data['estado']   ?? 1),
        ];
        if ($id > 0) {
            return $this->update($id, $fields);
        }
        return $this->insert($fields);
    }

    public function getWithTaquillasCount(): array
    {
        $stmt = $this->db->query(
            "SELECT a.*, COUNT(t.id) AS total_taquillas
             FROM agencias a
             LEFT JOIN taquillas t ON t.agencia_id = a.id
             GROUP BY a.id
             ORDER BY a.nombre"
        );
        return $stmt->fetchAll();
    }
}
