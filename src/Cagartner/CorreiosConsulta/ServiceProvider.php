<?php namespace Cagartner\CorreiosConsulta;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the CorreiosConsulta class into correios_consulta
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('correios_consulta', function(){
            return new \Cagartner\CorreiosConsulta\CorreiosConsulta;
        });
    }
}
