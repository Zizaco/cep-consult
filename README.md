# CorreiosConsulta (Laravel 4 Package)

----------------------
Package para consulta de serviços diretamente no site dos correios, sem usar apis de terceiros.

Baseado nos seguintes repositórios:
- https://github.com/feliperoberto/correios-cep
- https://github.com/Zizaco/cep-consult

Consultas disponíveis:
- CEP
- Frete
- Rastreio

### Instalação

In the `require` key of `composer.json` file add the following

    "cagartner/correios-consulta": "0.1.*"

Run the Composer update comand

    $ composer update

In your `config/app.php` add `'Cagartner\CorreiosConsulta\ServiceProvider'` to the end of the `$providers` array

    'providers' => array(

        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        ...
        'Cagartner\CorreiosConsulta\ServiceProvider',

    ),

Then at the end of `config/app.php` add `'Correios'    => 'Cagartner\CorreiosConsulta\Facade'` to the `$aliases` array

    'aliases' => array(

        'App'        => 'Illuminate\Support\Facades\App',
        'Artisan'    => 'Illuminate\Support\Facades\Artisan',
        ...
        'Correios'    => 'Cagartner\CorreiosConsulta\Facade',

    ),

### Utilização

#### CEP:

Passar apenas o valor do CEP, pode ser formatado, somente números e como string.

~~~
<?php
    echo Correios::cep('89062086');
    
    /*
        Retorno:
        Array
        (
            [cliente] => 
            [logradouro] => Rua Lindolfo Kuhnen
            [bairro] => Itoupava Central
            [cep] => 89062086
            [cidade] => Blumenau
            [uf] => SC
        )
    */

?>
~~~

#### Rastrear

Passar o código de rastreio informado pelos Correios

~~~
<?php
    echo Correios::rastrear('AA123456789BR');
    
    /*
        Retorno:
        Array
        (
            [0] => Array
                (
                    [data] => 16/04/2014 18:56
                    [local] => CDD ITAPETININGA - ITAPETININGA/SP
                    [status] => Entrega Efetuada
                )

            [1] => Array
                (
                    [data] => 16/04/2014 09:14
                    [local] => CDD ITAPETININGA - ITAPETININGA/SP
                    [status] => Â 
                )

        )
    */

?>
~~~

#### Cálculo de Frete:

 ~~~
<?php
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

    echo Correios::frete($dados);
    
    /*
        Retorno:
        Array
        (
            [codigo] => 40010
            [valor] => 14.9
            [prazo] => 1
            [mao_propria] => 0
            [aviso_recebimento] => 0
            [valor_declarado] => 0
            [entrega_domiciliar] => 1
            [entrega_sabado] => 1
            [erro] => Array
                (
                    [codigo] => 0
                    [mensagem] => 
                )

        )
    */

?>
~~~
