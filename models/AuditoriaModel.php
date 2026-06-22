<?php
class AuditoriaModel extends Model
{
    protected string $table = 'auditoria';

    public function log(
        string  $accion,
        string  $tabla = '',
        int     $registroId = 0,
        mixed   $anterior = null,
        mixed   $nuevo = null
    ): void {
        $this->insert([
            'usuario_id'       => Auth::id() ?: null,
            'accion'           => $accion,
            'tabla'            => $tabla,
            'registro_id'      => $registroId ?: null,
            'datos_anteriores' => $anterior ? json_encode($anterior) : null,
            'datos_nuevos'     => $nuevo    ? json_encode($nuevo)    : null,
            'ip'               => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }
}
