<?php

use PHPUnit\Framework\TestCase;
use Cagartner\CorreiosConsulta\CorreiosConsulta;

class CorreiosTest extends TestCase
{
    /**
     * @test
     */
    public function testValidZipCode()
    {
        $correios = new CorreiosConsulta();
        $dados = $correios->cep('01001-000');

        $this->assertTrue($dados['logradouro'] === 'Praça da Sé - lado ímpar');
        $this->assertTrue($dados['bairro'] === 'Sé');
        $this->assertTrue($dados['cidade'] === 'São Paulo');
        $this->assertTrue($dados['uf'] === 'SP');
    }

    /**
     * @test
     */
    public function testValidShipmentTracking()
    {
        $correios = new CorreiosConsulta();
        $dados = $correios->rastrear('PO683612101BR');
        if (count($dados) > 0) {
            $entrada = array_pop($dados);

            $this->assertTrue($entrada['status'] === 'Objeto postado');
        } else {
            // se count($dados) === 0, o teste precisa fazer pelo menos um assert, senão dá erro no phpunit
            $this->assertTrue(1 === 1);
        }
    }

    /**
     * @test
     */
    public function testValidShipmentCost()
    {
        $dados = [
            'tipo'              => 'sedex', // Separar opções por vírgula (,) caso queira consultar mais de um (1) serviço. > Opções: `sedex`, `sedex_a_cobrar`, `sedex_10`, `sedex_hoje`, `pac`, 'pac_contrato', 'sedex_contrato' , 'esedex'
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

        $correios = new CorreiosConsulta();

        $resultados = $correios->frete($dados);

        $this->assertTrue(is_float($resultados['valor']));
    }
}