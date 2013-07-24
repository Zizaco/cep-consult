<?php namespace Zizaco\CepConsult;

use App;

class CepConsult
{

    /**
     * Return the address of the given CEP
     *
     * @param  string $cep CEP
     * @return array  Address information
     */
    public function getAddress($cep)
    {
        $cep = str_replace('.', '', str_replace('-', '', $cep));

        $address = "http://cep.correiocontrol.com.br/$cep.json";

        $curl = App::make('Zizaco\CepConsult\Curl'); // new Curl

        $data = $curl->request('GET', $address, 80);

        return $this->parseResponse($data);
    }

    /**
     * Parses the response into an usable format
     *
     * @param  array $response The response array of the cURL
     * @return array  Array containing the following keys: district, street, state, city, country.
     */
    protected function parseResponse($response)
    {
        $address = array(
            'district' => array_get($response, 'bairro'),
            'street' => array_get($response, 'logradouro'),
            'state' => array_get($response, 'uf'),
            'city' => array_get($response, 'localidade'),
            'country' => 'Brasil'
        );

        return $address;
    }
}
