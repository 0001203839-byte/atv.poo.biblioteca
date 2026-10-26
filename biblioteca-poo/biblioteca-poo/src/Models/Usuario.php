<?php
namespace App\Models;
abstract class Usuario
{
protected string $matricula;
protected string $nome;
protected string $email;
protected string $telefone;
protected string $status;
protected int $quantidadeMaximaEmprestimos;
private int $emprestimosAtivos = 0;
public function __construct(string $matricula, string $nome, string $email, string $telefone = '', string $status = 'ATIVO')
{
$this->matricula = $matricula;
$this->nome = $nome;
$this->email = $email;
$this->telefone = $telefone;
$this->status = strtoupper($status);
}
public function consultarDados(): array
{
return ['matrícula' => $this->matricula, 'nome' => $this->nome, 'e-mail' => $this->email, 'telefone' => $this->telefone, 'status' => $this->status, 'ativos' => $this->emprestimosAtivos, 'limite' => $this->quantidadeMaximaEmprestimos];
}
public function podeRealizarEmprestimo(): bool
{
return $this->status === 'ATIVO' && $this->emprestimosAtivos < $this->quantidadeMaximaEmprestimos;
}
public function consultarLimiteEmprestimos(): int { return $this->quantidadeMaximaEmprestimos; }
public function consultarEmprestimosAtivos(): int { return $this->emprestimosAtivos; }
public function getNome(): string { return $this->nome; }
public function getMatricula(): string { return $this->matricula; }
public function getStatus(): string { return $this->status; }
public function adicionarEmprestimoAtivo(): void { $this->emprestimosAtivos++; }
public function removerEmprestimoAtivo(): void
{
if ($this->emprestimosAtivos < 1) { throw new \App\Exceptions\EmprestimoInvalidoException('O usuário não possui empréstimo ativo.'); }
$this->emprestimosAtivos--;
}
abstract public function diasAdicionaisPrazo(): int;
}
