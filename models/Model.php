<?php
abstract class Model
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = getDB();
    }

    public function findAll(string $orderBy = 'id', string $dir = 'ASC'): array
    {
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` ORDER BY `{$orderBy}` {$dir}");
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?"
        );
        return $stmt->execute([$id]);
    }

    protected function insert(array $data): int
    {
        $cols = implode('`, `', array_keys($data));
        $plc  = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->table}` (`{$cols}`) VALUES ({$plc})"
        );
        $stmt->execute(array_values($data));
        return (int)$this->db->lastInsertId();
    }

    protected function update(int $id, array $data): bool
    {
        $set  = implode(' = ?, ', array_map(fn($c) => "`{$c}`", array_keys($data))) . ' = ?';
        $vals = array_values($data);
        $vals[] = $id;
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}` SET {$set} WHERE `{$this->primaryKey}` = ?"
        );
        return $stmt->execute($vals);
    }

    public function count(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM `{$this->table}`"
        )->fetchColumn();
    }

    public function countWhere(string $col, mixed $val): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM `{$this->table}` WHERE `{$col}` = ?"
        );
        $stmt->execute([$val]);
        return (int)$stmt->fetchColumn();
    }
}
