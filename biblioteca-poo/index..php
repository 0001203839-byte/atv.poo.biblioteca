<?php
require_once __DIR__ . '/autoload.php';
use App\Exceptions\EmprestimoInvalidoException;
use App\Exceptions\LimiteEmprestimoException;
use App\Exceptions\MaterialIndisponivelException;
use App\Exceptions\UsuarioBloqueadoException;
use App\Models\Aluno;
use App\Models\EBook;
use App\Models\Historico;
use App\Models\Livro;
use App\Models\Professor;
use App\Models\Revista;
use App\Services\EmprestimoService;
$historico = new Historico();
$service = new EmprestimoService($historico);
$aluno = new Aluno('A001', 'Ana Souza', 'ana@email.com');
$professor = new Professor('P001', 'Carlos Mendes', 'carlos@email.com');
$livro = new Livro('L001', 'Programação Orientada a Objetos com PHP', 'João Silva', 2024, 3, '978000000001', 350, 'Tech', 'Programação');
$revista = new Revista('R001', 'Tecnologia Hoje', 'Equipe Editorial', 2026, 2, 145, 12, 'Outubro');
$ebook = new EBook('E001', 'APIs REST com PHP', 'Maria Lima', 2025, 10, 'PDF', 5.2, 'https://exemplo.test/ebook');
$hoje = new DateTimeImmutable('2026-10-01');
$saida = [];
try {
$emprestimoAluno = $service->criarEmprestimo($aluno, $livro, $hoje);
$saida[] = ['seção' => 'EMPRÉSTIMO', 'usuário' => $aluno->getNome(), 'tipo' => 'Aluno', 'material' => $livro->getTitulo(), 'data' => $hoje->format('d/m/Y'), 'prevista' => $emprestimoAluno->getDataPrevistaDevolucao()->format('d/m/Y'), 'status' => $emprestimoAluno->getStatus()];
$emprestimoProfessor = $service->criarEmprestimo($professor, $livro, $hoje);
$saida[] = ['seção' => 'EMPRÉSTIMO DO PROFESSOR', 'usuário' => $professor->getNome(), 'material' => $livro->getTitulo(), 'prevista' => $emprestimoProfessor->getDataPrevistaDevolucao()->format('d/m/Y'), 'status' => $emprestimoProfessor->getStatus()];
$devolucao = $service->registrarDevolucao($emprestimoAluno, new DateTimeImmutable('2026-10-20'));
$saida[] = ['seção' => 'DEVOLUÇÃO', 'usuário' => $aluno->getNome(), 'material' => $livro->getTitulo(), 'diasAtraso' => $devolucao['diasAtraso'], 'multa' => 'R$ ' . number_format($devolucao['multa'], 2, ',', '.'), 'status' => $devolucao['status']];
$saida[] = ['seção' => 'TESTES DE REGRAS'];
try { $livroSemEstoque = new Livro('L002', 'Sem estoque', 'Autor', 2024, 0, 'ISBN0', 1, 'Editora', 'Teste'); $service->criarEmprestimo(new Aluno('A010', 'Teste', 't@x.test'), $livroSemEstoque, $hoje); $saida[] = 'FALHOU: material indisponível'; } catch (MaterialIndisponivelException $e) { $saida[] = 'OK: material indisponível bloqueado'; }
$alunoLimite = new Aluno('A011', 'Aluno limite', 'limite@x.test');
for ($i = 1; $i <= 3; $i++) { $service->criarEmprestimo($alunoLimite, new Livro('LL'.$i, 'Livro '.$i, 'Autor', 2024, 1, 'ISBN'.$i, 10, 'Editora', 'Teste'), $hoje); }
try { $service->criarEmprestimo($alunoLimite, new Livro('LL4', 'Livro 4', 'Autor', 2024, 1, 'ISBN4', 10, 'Editora', 'Teste'), $hoje); $saida[] = 'FALHOU: limite do aluno'; } catch (LimiteEmprestimoException $e) { $saida[] = 'OK: quarto empréstimo de aluno bloqueado'; }
try { $service->criarEmprestimo(new Aluno('A012', 'Bloqueado', 'b@x.test', '', 'BLOQUEADO'), $livro, $hoje); $saida[] = 'FALHOU: usuário bloqueado'; } catch (UsuarioBloqueadoException $e) { $saida[] = 'OK: usuário bloqueado'; }
try { $service->registrarDevolucao($emprestimoAluno, new DateTimeImmutable('2026-10-21')); $saida[] = 'FALHOU: devolução duplicada'; } catch (EmprestimoInvalidoException $e) { $saida[] = 'OK: devolução duplicada bloqueada'; }
$ebookComLimite = new EBook('E002', 'Acesso teste', 'Autor', 2025, 1, 'PDF', 1, 'https://exemplo.test');
$service->criarEmprestimo(new Aluno('A013', 'Leitor 1', '1@x.test'), $ebookComLimite, $hoje);
try { $service->criarEmprestimo(new Aluno('A014', 'Leitor 2', '2@x.test'), $ebookComLimite, $hoje); $saida[] = 'FALHOU: limite de acessos'; } catch (MaterialIndisponivelException $e) { $saida[] = 'OK: acessos simultâneos do e-book limitados'; }
$revistaEmp = $service->criarEmprestimo(new Aluno('A015', 'Devolução no prazo', 'n@x.test'), $revista, $hoje);
$dentroPrazo = $service->registrarDevolucao($revistaEmp, $revistaEmp->getDataPrevistaDevolucao());
$saida[] = 'OK: devolução no prazo, multa R$ ' . number_format($dentroPrazo['multa'], 2, ',', '.');
$saida[] = 'OK: multa comparativa aluno R$ ' . number_format($service->calcularMulta($emprestimoAluno, 5), 2, ',', '.') . ' / professor R$ ' . number_format($service->calcularMulta($emprestimoProfessor, 5), 2, ',', '.');
} catch (Throwable $e) {
$saida[] = 'ERRO: ' . $e->getMessage();
}
if (PHP_SAPI === 'cli') {
foreach ($saida as $item) { echo is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL : $item . PHP_EOL; }
echo PHP_EOL . 'HISTÓRICO' . PHP_EOL;
foreach ($historico->listar() as $registro) { echo json_encode($registro, JSON_UNESCAPED_UNICODE) . PHP_EOL; }
exit;
}
?>
<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sistema de Biblioteca</title><style>body{font-family:Arial,sans-serif;max-width:1000px;margin:40px auto;padding:0 20px;color:#18212f}h1{color:#164e63}.caixa{background:#f1f5f9;border-radius:10px;padding:20px;margin:14px 0}pre{white-space:pre-wrap;word-break:break-word}</style></head><body><h1>Sistema de Gestão de Biblioteca</h1><p>Exemplos e testes do sistema de empréstimos em PHP orientado a objetos.</p><h2>Execução e testes</h2><?php foreach ($saida as $item): ?><div class="caixa"><pre><?= htmlspecialchars(is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $item, ENT_QUOTES, 'UTF-8') ?></pre></div><?php endforeach; ?><h2>Histórico de movimentações</h2><?php foreach ($historico->listar() as $registro): ?><div class="caixa"><pre><?= htmlspecialchars(json_encode($registro, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8') ?></pre></div><?php endforeach; ?></body></html>
