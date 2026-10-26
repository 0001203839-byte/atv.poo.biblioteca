<?php
namespace App\Models;
class Emprestimo
{
public const ATIVO = 'ATIVO';
public const DEVOLVIDO = 'DEVOLVIDO';
public const ATRASADO = 'ATRASADO';
public const CANCELADO = 'CANCELADO';
private string $codigo;
private Usuario $usuario;
private Material $material;
private \DateTimeImmutable $dataEmprestimo;
private \DateTimeImmutable $dataPrevistaDevolucao;
private ?\DateTimeImmutable $dataRealDevolucao = null;
private string $status = self::ATIVO;
public function __construct(string $codigo, Usuario $usuario, Material $material, \DateTimeImmutable $dataEmprestimo, \DateTimeImmutable $dataPrevistaDevolucao)
{
$this->codigo = $codigo;
$this->usuario = $usuario;
$this->material = $material;
$this->dataEmprestimo = $dataEmprestimo;
$this->dataPrevistaDevolucao = $dataPrevistaDevolucao;
}
public function registrarDevolucao(\DateTimeImmutable $data): void
{
if ($this->status !== self::ATIVO) { throw new \App\Exceptions\EmprestimoInvalidoException('Este empréstimo não pode ser devolvido.'); }
$this->dataRealDevolucao = $data;
$this->status = self::DEVOLVIDO;
}
public function cancelar(): void
{
if ($this->status !== self::ATIVO) { throw new \App\Exceptions\EmprestimoInvalidoException('Somente empréstimos ativos podem ser cancelados.'); }
$this->status = self::CANCELADO;
}
public function getCodigo(): string { return $this->codigo; }
public function getUsuario(): Usuario { return $this->usuario; }
public function getMaterial(): Material { return $this->material; }
public function getDataEmprestimo(): \DateTimeImmutable { return $this->dataEmprestimo; }
public function getDataPrevistaDevolucao(): \DateTimeImmutable { return $this->dataPrevistaDevolucao; }
public function getDataRealDevolucao(): ?\DateTimeImmutable { return $this->dataRealDevolucao; }
public function getStatus(): string { return $this->status; }
}
