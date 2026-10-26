<?php
namespace App\Models;
class Livro extends Material
{
private string $isbn;
private int $numeroPaginas;
private string $editora;
private string $genero;
public function __construct(string $codigo, string $titulo, string $autor, int $anoPublicacao, int $quantidadeExemplares, string $isbn, int $numeroPaginas, string $editora, string $genero)
{
parent::__construct($codigo, $titulo, $autor, $anoPublicacao, $quantidadeExemplares);
$this->isbn = $isbn;
$this->numeroPaginas = $numeroPaginas;
$this->editora = $editora;
$this->genero = $genero;
}
public function calcularPrazoMaximoEmprestimo(): int { return 14; }
}
