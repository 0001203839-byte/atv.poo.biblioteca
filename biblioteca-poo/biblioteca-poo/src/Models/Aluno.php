<?php
namespace App\Models;
class Aluno extends Usuario
{
protected int $quantidadeMaximaEmprestimos = 3;
public function diasAdicionaisPrazo(): int { return 0; }
}
