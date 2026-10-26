<?php
namespace App\Models;
class EBook extends Material
{
private string $formatoArquivo;
private float $tamanhoArquivo;
private string $linkAcesso;
public function __construct(string $codigo, string $titulo, string $autor, int $anoPublicacao, int $acessosSimultaneos, string $formatoArquivo, float $tamanhoArquivo, string $linkAcesso)
{
parent::__construct($codigo, $titulo, $autor, $anoPublicacao, $acessosSimultaneos);
$this->formatoArquivo = $formatoArquivo;
$this->tamanhoArquivo = $tamanhoArquivo;
$this->linkAcesso = $linkAcesso;
}
public function calcularPrazoMaximoEmprestimo(): int { return 21; }
public function getLinkAcesso(): string { return $this->linkAcesso; }
}
