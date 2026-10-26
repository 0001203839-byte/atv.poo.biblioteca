<?php
namespace App\Models;
abstract class Material
{
protected string $codigo;
protected string $titulo;
protected string $autor;
protected int $anoPublicacao;
protected int $quantidadeExemplares;
protected int $quantidadeDisponivel;
public function __construct(string $codigo, string $titulo, string $autor, int $anoPublicacao, int $quantidadeExemplares)
{
if ($quantidadeExemplares < 0) {
throw new \InvalidArgumentException('A quantidade não pode ser negativa.');
}
$this->codigo = $codigo;
$this->titulo = $titulo;
$this->autor = $autor;
$this->anoPublicacao = $anoPublicacao;
$this->quantidadeExemplares = $quantidadeExemplares;
$this->quantidadeDisponivel = $quantidadeExemplares;
}
public function consultarDisponibilidade(): bool
{
return $this->consultarQuantidadeDisponivel() > 0;
}
public function emprestarExemplar(): void
{
if (!$this->consultarDisponibilidade()) {
throw new \App\Exceptions\MaterialIndisponivelException('Material sem exemplares ou acessos disponíveis: ' . $this->titulo);
}
$this->quantidadeDisponivel--;
}
public function devolverExemplar(): void
{
if ($this->quantidadeDisponivel >= $this->quantidadeExemplares) {
throw new \App\Exceptions\EmprestimoInvalidoException('A quantidade disponível já está no limite.');
}
$this->quantidadeDisponivel++;
}
public function consultarQuantidadeDisponivel(): int
{
return $this->quantidadeDisponivel;
}
public function exibirInformacoes(): array
{
return ['código' => $this->codigo, 'título' => $this->titulo, 'autor' => $this->autor, 'ano' => $this->anoPublicacao, 'disponíveis' => $this->quantidadeDisponivel, 'tipo' => static::class];
}
abstract public function calcularPrazoMaximoEmprestimo(): int;
public function getCodigo(): string { return $this->codigo; }
public function getTitulo(): string { return $this->titulo; }
public function getAutor(): string { return $this->autor; }
}
