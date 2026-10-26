<?php
namespace App\Models;
class Professor extends Usuario
{
protected int $quantidadeMaximaEmprestimos = 5;
public function diasAdicionaisPrazo(): int { return 7; }
}
