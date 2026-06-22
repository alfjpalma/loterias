<?php
class TaquillaModel extends Model
{
    protected string $table = 'taquillas';

    public function getByAgencia(int $agenciaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM taquillas WHERE agencia_id = ? AND estado = 1 ORDER BY nombre"
        );
        $stmt->execute([$agenciaId]);
        return $stmt->fetchAll();
    }

    public function getAllWithAgencia(): array
    {
        $stmt = $this->db->query(
            "SELECT t.*, a.nombre AS agencia_nombre
             FROM taquillas t
             JOIN agencias a ON a.id = t.agencia_id
             ORDER BY a.nombre, t.nombre"
        );
        return $stmt->fetchAll();
    }

    public function save(array $data, int $id = 0): int|bool
    {
        $fields = [
            'agencia_id'  => (int)$data['agencia_id'],
            'nombre'      => trim($data['nombre']),
            'descripcion' => trim($data['descripcion'] ?? ''),
            'estado'      => (int)($data['estado'] ?? 1),
        ];
        if ($id > 0) {
            return $this->update($id, $fields);
        }
        return $this->insert($fields);
    }
}
