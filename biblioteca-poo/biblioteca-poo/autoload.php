<?php
spl_autoload_register(function (string $classe): void {
$prefixo = 'App\\';
if (strncmp($classe, $prefixo, strlen($prefixo)) !== 0) { return; }
$caminho = __DIR__ . '/src/' . str_replace('\\', '/', substr($classe, strlen($prefixo))) . '.php';
if (is_file($caminho)) { require $caminho; }
});
