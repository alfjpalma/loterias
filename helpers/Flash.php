<?php
class Flash
{
    public static function set(string $type, string $msg): void
    {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }

    public static function get(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $f = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $f;
        }
        return null;
    }

    public static function render(): string
    {
        $f = self::get();
        if (!$f) return '';
        $map = [
            'success' => 'success',
            'error'   => 'danger',
            'warning' => 'warning',
            'info'    => 'info',
        ];
        $cls = $map[$f['type']] ?? 'info';
        $msg = htmlspecialchars($f['msg'], ENT_QUOTES);
        return <<<HTML
        <div class="alert alert-{$cls} alert-dismissible fade show" role="alert">
            {$msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        HTML;
    }
}
