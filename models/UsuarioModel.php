<?php
class UsuarioModel extends Model
{
    protected string $table = 'usuarios';

    public function findByUsuario(string $usuario): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE usuario = ? AND estado = 1 LIMIT 1"
        );
        $stmt->execute([$usuario]);
        return $stmt->fetch();
    }

    public function save(array $data, int $id = 0): int|bool
    {
        $fields = [
            'nombre'  => trim($data['nombre']),
            'usuario' => trim($data['usuario']),
            'rol'     => $data['rol']    === 'administrador' ? 'administrador' : 'operador',
            'estado'  => (int)($data['estado'] ?? 1),
        ];
        if (!empty($data['password'])) {
            $fields['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        }
        if ($id > 0) {
            return $this->update($id, $fields);
        }
        if (empty($fields['password'])) {
            return false;
        }
        return $this->insert($fields);
    }

    public function usernameExists(string $usuario, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM usuarios WHERE usuario = ? AND id != ?"
        );
        $stmt->execute([$usuario, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
