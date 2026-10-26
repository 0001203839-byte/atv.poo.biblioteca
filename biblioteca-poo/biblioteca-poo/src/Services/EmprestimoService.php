<?php
namespace App\Services;
use App\Exceptions\LimiteEmprestimoException;
use App\Exceptions\MaterialIndisponivelException;
use App\Exceptions\UsuarioBloqueadoException;
use App\Interfaces\MultaInterface;
use App\Models\Aluno;
use App\Models\Emprestimo;
use App\Models\Historico;
use App\Models\Material;
use App\Models\Professor;
use App\Models\Usuario;
use App\Multas\MultaPadrao;
use App\Multas\MultaProfessor;
class EmprestimoService
{
private Historico $historico;
private array $emprestimos = [];
private int $proximoCodigo = 1;
public function __construct(Historico $historico) { $this->historico = $historico; }
public function criarEmprestimo(Usuario $usuario, Material $material, ?\DateTimeImmutable $data = null): Emprestimo
    {
if ($usuario->getStatus() !== 'ATIVO') { throw new UsuarioBloqueadoException('Usuário bloqueado não pode realizar empréstimos.'); }
if (!$usuario->podeRealizarEmprestimo()) { throw new LimiteEmprestimoException('O usuário atingiu o limite de empréstimos ativos.'); }
if (!$material->consultarDisponibilidade()) { throw new MaterialIndisponivelException('Material indisponível.'); }
$data ??= new \DateTimeImmutable('today');
$prazo = $material->calcularPrazoMaximoEmprestimo() + $usuario->diasAdicionaisPrazo();
$prevista = $data->modify('+' . $prazo . ' days');
$material->emprestarExemplar();
$usuario->adicionarEmprestimoAtivo();
$emprestimo = new Emprestimo('EMP' . str_pad((string)$this->proximoCodigo++, 4, '0', STR_PAD_LEFT), $usuario, $material, $data, $prevista);
$this->emprestimos[] = $emprestimo;
$this->historico->registrar('EMPRÉSTIMO REALIZADO', $usuario, $material, 'Devolução prevista: ' . $prevista->format('d/m/Y'));
return $emprestimo;
}
public function registrarDevolucao(Emprestimo $emprestimo, ?\DateTimeImmutable $data = null): array
{
$data ??= new \DateTimeImmutable('today');
$diasAtraso = max(0, (int)$emprestimo->getDataPrevistaDevolucao()->diff($data)->format('%r%a'));
$multa = $this->selecionarMulta($emprestimo->getUsuario())->calcularMulta($diasAtraso);
$emprestimo->registrarDevolucao($data);
$emprestimo->getMaterial()->devolverExemplar();
$emprestimo->getUsuario()->removerEmprestimoAtivo();
$this->historico->registrar('DEVOLUÇÃO REALIZADA', $emprestimo->getUsuario(), $emprestimo->getMaterial(), 'Dias de atraso: ' . $diasAtraso);
if ($diasAtraso > 0) { $this->historico->registrar('MATERIAL DEVOLVIDO COM ATRASO', $emprestimo->getUsuario(), $emprestimo->getMaterial(), 'Atraso de ' . $diasAtraso . ' dia(s).'); }
if ($multa > 0) { $this->historico->registrar('MULTA APLICADA', $emprestimo->getUsuario(), $emprestimo->getMaterial(), 'R$ ' . number_format($multa, 2, ',', '.')); }
return ['diasAtraso' => $diasAtraso, 'multa' => $multa, 'status' => $emprestimo->getStatus()];
}
public function cancelarEmprestimo(Emprestimo $emprestimo): void
{
$emprestimo->cancelar();
$emprestimo->getMaterial()->devolverExemplar();
$emprestimo->getUsuario()->removerEmprestimoAtivo();
$this->historico->registrar('EMPRÉSTIMO CANCELADO', $emprestimo->getUsuario(), $emprestimo->getMaterial());
}
public function calcularAtraso(Emprestimo $emprestimo, ?\DateTimeImmutable $data = null): int
{
$data ??= new \DateTimeImmutable('today');
return max(0, (int)$emprestimo->getDataPrevistaDevolucao()->diff($data)->format('%r%a'));
}
public function calcularMulta(Emprestimo $emprestimo, int $diasAtraso): float
{
return $this->selecionarMulta($emprestimo->getUsuario())->calcularMulta($diasAtraso);
}
public function listarEmprestimos(): array { return $this->emprestimos; }
public function getHistorico(): Historico { return $this->historico; }

private function selecionarMulta(Usuario $usuario): MultaInterface
{
return $usuario instanceof Professor ? new MultaProfessor() : new MultaPadrao();
}
}
