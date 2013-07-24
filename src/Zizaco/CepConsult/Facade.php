<?php namespace Zizaco\CepConsult;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return 'consulta_cep';
    }
}
