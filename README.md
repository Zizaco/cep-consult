# CepConsult (Laravel4 Package)

----------------------
This package has the objective to consult the CEP within one or more web APIs that will retrieve the address of the given number.
Esse pacote tem o objetivo de consultar o numero CEP em uma ou mais APIs web que irão retornar o endereço do numero dado.

### Required setup

In the `require` key of `composer.json` file add the following

    "zizaco/cep-consult": "dev-master"

Run the Composer update comand

    $ composer update

In your `config/app.php` add `'Zizaco\CepConsult\ServiceProvider'` to the end of the `$providers` array

    'providers' => array(

        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        ...
        'Zizaco\CepConsult\ServiceProvider',

    ),

Then at the end of `config/app.php` add `'CepConsult'    => 'Zizaco\CepConsult\Facade'` to the `$aliases` array

    'aliases' => array(

        'App'        => 'Illuminate\Support\Facades\App',
        'Artisan'    => 'Illuminate\Support\Facades\Artisan',
        ...
        'CepConsult'    => 'Zizaco\CepConsult\Facade',

    ),

Now you are ready to go:

    // Simply do
    CepConsult::getAddress('13.015-904');

    // Or
    CepConsult::getAddress('13015904');
