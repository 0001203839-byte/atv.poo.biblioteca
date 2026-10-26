<?php
namespace App\Models;
class Historico
{
private array $registros = [];
public function registrar(string $acao, Usuario $usuario, Material $material, string $observacao = ''): void
{
$agora = new \DateTimeImmutable();
$this->registros[] = ['data' => $agora->format('d/m/Y'), 'hora' => $agora->format('H:i:s'), 'ação' => $acao, 'usuário' => $usuario->getNome(), 'material' => $material->getTitulo(), 'observação' => $observacao];
}
public function listar(): array { return $this->registros; }
}
