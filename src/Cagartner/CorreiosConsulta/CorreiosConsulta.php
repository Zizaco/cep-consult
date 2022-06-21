<?php

namespace Cagartner\CorreiosConsulta;

use Cagartner\CorreiosConsulta\Curl;
use PhpQuery\PhpQuery as phpQuery;

class CorreiosConsulta
{
    const FRETE_URL = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo';
    const CEP_URL = 'http://www.buscacep.correios.com.br/sistemas/buscacep/resultadoBuscaCepEndereco.cfm';
    const RASTREIO_URL = 'https://www2.correios.com.br/sistemas/rastreamento/resultado_semcontent.cfm';

    private static $tipos
        = [
            'sedex'          => '04014',
            'sedex_a_cobrar' => '40045',
            'sedex_10'       => '40215',
            'sedex_hoje'     => '40290',
            'pac'            => '04510',
            'pac_contrato'   => '04669',
            'sedex_contrato' => '04162',
            'esedex'         => '81019',
        ];

    public static function getTipos()
    {
        return self::$tipos;
    }

    /**
     * Verifica se e uma solicitacao de varios $tipos
     *
     * @param $valor string
     *
     * @return boolean
     */
    public static function getTipoIsArray($valor)
    {
        return count(explode(",", $valor)) > 1 ?: FALSE;
    }

    /**
     * @param $valor string
     *
     * @return string
     */
    public static function getTipoIndex($valor)
    {
        return array_search($valor, self::getTipos());
    }

    /**
     * Retorna todos os codigos em uma linha
     *
     * @param $valor string
     *
     * @return string
     */
    public static function getTipoInline($valor)
    {
        $explode = explode(",", $valor);
        $tipos   = [];

        foreach ($explode as $value) {
            $tipos[] = self::$tipos[$value];
        }

        return $tipos;
    }

    /**
     * @param       $dados
     * @param array $options
     *
     * @return array|mixed
     */
    public function frete($data, $options = [])
    {
        $endpoint = self::FRETE_URL;
        $tipos    = self::getTipoInline($data['tipo']);
        $return   = [];

        $formatos = [
            'caixa'    => 1,
            'rolo'     => 2,
            'envelope' => 3,
        ];

        foreach ($tipos as $tipo) {
            $dados            = $data;
            $dados['formato'] = $formatos[$dados['formato']];

            $dados['cep_destino'] = self::cleanPostcode($dados['cep_destino']);
            $dados['cep_origem']  = self::cleanPostcode($dados['cep_origem']);

            $params = [
                'nCdEmpresa'          => (isset($dados['empresa']) ? $dados['empresa'] : ''),
                'sDsSenha'            => (isset($dados['senha']) ? $dados['senha'] : ''),
                'nCdServico'          => $tipo,
                'sCepOrigem'          => $dados['cep_origem'],
                'sCepDestino'         => $dados['cep_destino'],
                'nVlPeso'             => $dados['peso'],
                'nCdFormato'          => $dados['formato'],
                'nVlComprimento'      => $dados['comprimento'],
                'nVlAltura'           => $dados['altura'],
                'nVlLargura'          => $dados['largura'],
                'nVlDiametro'         => $dados['diametro'],
                'sCdMaoPropria'       => (isset($dados['mao_propria']) && $dados['mao_propria'] ? 'S' : 'N'),
                'nVlValorDeclarado'   => (isset($dados['valor_declarado']) ? $dados['valor_declarado'] : 0),
                'sCdAvisoRecebimento' => (isset($dados['aviso_recebimento']) && $dados['aviso_recebimento'] ? 'S' : 'N'),
                'sDtCalculo'          => date('d/m/Y'),
            ];
            $curl   = new Curl();
            if ($result = $curl->simple($endpoint, $params)) {
                $result  = simplexml_load_string($result);
                $rates   = [];
                $collect = (array)$result->Servicos;

                $rate = $collect['cServico'];

                $return[] = [
                    'codigo'             => (int)$rate->Codigo,
                    'tipo'               => ucfirst(str_replace('_', '', self::getTipoIndex($rate->Codigo))),
                    'valor'              => self::cleanMoney($rate->Valor),
                    'prazo'              => self::cleanInteger($rate->PrazoEntrega),
                    'mao_propria'        => self::cleanMoney($rate->ValorMaoPropria),
                    'aviso_recebimento'  => self::cleanMoney($rate->ValorAvisoRecebimento),
                    'valor_declarado'    => self::cleanMoney($rate->ValorValorDeclarado),
                    'entrega_domiciliar' => $rate->EntregaDomiciliar === 'S',
                    'entrega_sabado'     => $rate->EntregaSabado === 'S',
                    'erro'               => ['codigo' => (float)$rate->Erro, 'mensagem' => $rate->MsgErro],
                ];

            }
        }


        return $return;
    }

    /**
     * @param $cep
     *
     * @return array
     * @throws \Exception
     */
    public function cep($cep)
    {
        $data = [
            'relaxation' => $cep,
            'tipoCEP'    => 'ALL',
            'semelhante' => 'N',
        ];

        $curl = new Curl;

        $html = $curl->simple(self::CEP_URL, $data);

        phpQuery::newDocumentHTML($html, $charset = 'ISO-8859-1');

        $pq_form  = phpQuery::pq('');
        $pesquisa = [];
        if (phpQuery::pq('.tmptabela')) {
            $linha = 0;
            foreach (phpQuery::pq('.tmptabela tr') as $pq_div) {
                if ($linha) {
                    $itens = [];
                    foreach (phpQuery::pq('td', $pq_div) as $pq_td) {
                        $children  = $pq_td->childNodes;
                        $innerHTML = '';
                        foreach ($children as $child) {
                            $innerHTML .= $child->ownerDocument->saveXML($child);
                        }
                        $texto   = preg_replace("/&#?[a-z0-9]+;/i", "", $innerHTML);
                        $itens[] = trim($texto);
                    }
                    $dados               = [];
                    $dados['logradouro'] = trim($itens[0]);
                    $dados['bairro']     = trim($itens[1]);
                    $dados['cidade/uf']  = trim($itens[2]);
                    $dados['cep']        = trim($itens[3]);

                    $dados['cidade/uf'] = explode('/', $dados['cidade/uf']);

                    $dados['cidade'] = trim($dados['cidade/uf'][0]);

                    $dados['uf'] = trim($dados['cidade/uf'][1]);

                    unset($dados['cidade/uf']);

                    $pesquisa = $dados;
                }

                $linha++;
            }
        }
        return $pesquisa;
    }

    /**
     * @param $codigo
     *
     * @return array|bool
     * @throws \Exception
     */
    public function rastrear($codigo)
    {
        $curl = new Curl;

        $html = $curl->simple(self::RASTREIO_URL, [
            "Objetos" => $codigo
        ]);

        phpQuery::newDocumentHTML($html, $charset = 'utf-8');

        $rastreamento = [];
        $c            = 0;

        foreach (phpQuery::pq('tr') as $tr) {
            $c++;
            if (count(phpQuery::pq($tr)->find('td')) == 2) {
                [$data, $hora, $local] = explode("<br>", phpQuery::pq($tr)->find('td:eq(0)')->html());
                [$status, $encaminhado] = explode("<br>", phpQuery::pq($tr)->find('td:eq(1)')->html());

                $rastreamento[] = array('data' => strip_tags(trim($data)) . " " . strip_tags(trim($hora)), 'local' => strip_tags(trim($local)), 'status' => strip_tags(trim($status)));

                if (trim($encaminhado)) {
                    $rastreamento[count($rastreamento) - 1]['encaminhado'] = trim($encaminhado);
                }
            }
        }

        if (!count($rastreamento))
            return FALSE;

        return $rastreamento;
    }

    /**
     * @param $postcode
     *
     * @return string|string[]|null
     */
    protected static function cleanPostcode($postcode)
    {
        return preg_replace("/[^0-9]/", '', $postcode);
    }

    /**
     * @param $value
     *
     * @return float
     */
    protected function cleanMoney($value)
    {
        return (float)str_replace(',', '.', str_replace('.', '', $value));
    }

    /**
     * @param $value
     *
     * @return int
     */
    protected function cleanInteger($value)
    {
        return (int)str_replace(',', '.', $value);
    }
}
