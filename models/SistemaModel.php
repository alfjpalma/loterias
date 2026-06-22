<?php
class SistemaModel extends Model
{
    protected string $table = 'sistemas';

    public function getActivos(): array
    {
        $stmt = $this->db->query(
            "SELECT * FROM sistemas WHERE estado = 1 ORDER BY orden, nombre"
        );
        return $stmt->fetchAll();
    }

    public function save(array $data, int $id = 0): int|bool
    {
        $fields = [
            'nombre'      => trim($data['nombre']),
            'descripcion' => trim($data['descripcion'] ?? ''),
            'estado'      => (int)($data['estado'] ?? 1),
            'orden'       => (int)($data['orden'] ?? 0),
        ];
        if ($id > 0) {
            return $this->update($id, $fields);
        }
        return $this->insert($fields);
    }
}
