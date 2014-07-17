<?php namespace Cagartner\CorreiosConsulta;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return 'correios_consulta';
    }
}
