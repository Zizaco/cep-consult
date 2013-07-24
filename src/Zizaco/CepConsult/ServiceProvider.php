<?php namespace Zizaco\CepConsult;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the CepConsult class into consulta_cep
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('consulta_cep', function(){
            return new \Zizaco\CepConsult\CepConsult;
        });
    }
}
