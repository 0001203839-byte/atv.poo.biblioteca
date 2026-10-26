# Sistema de Gestão de Biblioteca — PHP POO
Aplicação acadêmica em PHP puro para demonstrar empréstimos, devoluções, controle de exemplares/acessos, atrasos, multas e histórico usando Programação Orientada a Objetos.
## Requisitos
- PHP 8.0 ou superior
- Laragon com Apache
- VS Code (opcional, para editar)
- Não utiliza banco de dados nem dependências externas; os dados são mantidos em memória durante cada execução.
## Execução no Laragon
1. Copie a pasta `biblioteca-poo` para `C:\laragon\www\`.
2. Inicie o Apache no Laragon.
3. Acesse `http://localhost/biblioteca-poo/`.
4. Também é possível executar os cenários pelo terminal: `php index.php`.
O projeto não persiste informações entre recarregamentos, pois o foco é demonstrar as regras de POO solicitadas.
## Estrutura
``text
biblioteca-poo/
├── src/
│   ├── Exceptions/
│   ├── Interfaces/
│   ├── Models/
│   ├── Multas/
│   └── Services/
├── autoload.php
├── index.php
├── README.md
└── diagrama-uml.mmd
```
## Classes implementadas

- `Material` abstrata e `Livro`, `Revista`, `EBook`.
- `Usuario` abstrata e `Aluno`, `Professor`.
- `Emprestimo` e `Historico`.
- `EmprestimoService` para as regras e operações.
- `MultaInterface`, `MultaPadrao` e `MultaProfessor`.
- Exceções específicas para material indisponível, limite, usuário bloqueado e empréstimo inválido.
## Regras implementadas
- Livro: 14 dias; revista: 7 dias; e-book: 21 dias.
- Professor recebe 7 dias adicionais e paga R$ 1,00 por dia de atraso; aluno paga R$ 2,00.
- Limite: 3 empréstimos ativos para aluno e 5 para professor.
- Material sem estoque/acessos, usuário bloqueado, limite excedido e devolução repetida são rejeitados.
- E-book controla acessos simultâneos com a mesma lógica de disponibilidade, sem ultrapassar o limite.
- Empréstimo/devolução atualizam estoque/acessos e quantidade ativa do usuário. Multa é zero sem atraso.
- Cada movimentação relevante gera registro no histórico.
## Conceitos de POO
- **Encapsulamento:** atributos são `private` ou `protected`, acessados por métodos.
- **Herança:** Livro, Revista e EBook herdam de Material; Aluno e Professor herdam de Usuario.
- **Abstração:** Material e Usuario são classes abstratas com comportamentos especializados.
- **Polimorfismo:** os materiais calculam prazos diferentes; o serviço usa implementações diferentes de `MultaInterface`.
- **Composição/associação:** Emprestimo mantém referências ao Usuario e Material; o serviço é composto com Historico.
- **Interface:** MultaInterface define o contrato de cálculo da multa.
- **Namespaces e autoload:** namespaces `App\...` e carregamento por `spl_autoload_register()` em `autoload.php`.
## Testes
Ao abrir `index.php`, a demonstração executa os casos previstos no enunciado: empréstimo da aluna e do professor, devolução com cinco dias de atraso, indisponibilidade, quarto empréstimo de aluno, usuário bloqueado, devolução repetida, limite de acesso de e-book, devolução no prazo e comparação de multas. A saída dos casos e o histórico são exibidos na tela. As mesmas verificações podem ser executadas com `php index.php`.
## Diagrama UML
O arquivo `diagrama-uml.mmd` contém o diagrama de classes em Mermaid. Ele pode ser visualizado em extensões Mermaid do VS Code ou em um visualizador compatível.
