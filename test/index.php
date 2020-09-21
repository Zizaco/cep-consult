<?php

require "../vendor/autoload.php";

use \Cagartner\CorreiosConsulta\CorreiosConsulta;

$consulta = new CorreiosConsulta;

echo "<h1>CEP: 89062086</h1>";
echo "<pre>";
print_r($consulta->cep('89062086'));
echo "</pre>";
echo "<hr>";

echo "<h1>Rastrear: OK254268175BR</h1>";
echo "<pre>";
print_r($consulta->rastrear('OK254268175BR'));
echo "</pre>";
echo "<hr>";

echo "<h1>FRETE:</h1>";

$dados = [
	'tipo'              => 'sedex', // opções: `sedex`, `sedex_a_cobrar`, `sedex_10`, `sedex_hoje`, `pac`
	'formato'           => 'caixa', // opções: `caixa`, `rolo`, `envelope`
	'cep_destino'       => '89062086', // Obrigatório
	'cep_origem'        => '89062080', // Obrigatorio
	//'empresa'         => '', // Código da empresa junto aos correios, não obrigatório.
	//'senha'           => '', // Senha da empresa junto aos correios, não obrigatório.
	'peso'              => '1', // Peso em kilos
	'comprimento'       => '16', // Em centímetros
	'altura'            => '11', // Em centímetros
	'largura'           => '11', // Em centímetros
	'diametro'          => '0', // Em centímetros, no caso de rolo
	// 'mao_propria'       => '1', // Não obrigatórios
	// 'valor_declarado'   => '1', // Não obrigatórios
	// 'aviso_recebimento' => '1', // Não obrigatórios
];

echo "<pre>";
print_r($consulta->frete($dados));
echo "</pre>";
echo "<hr>";
exit;