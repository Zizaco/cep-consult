<?php namespace Zizaco\CepConsult;

class Curl
{
    public function request($method, $path, $port, $parameters=array())
    {
        $conn = curl_init();
        curl_setopt($conn, CURLOPT_URL, $path);
        curl_setopt($conn, CURLOPT_TIMEOUT, 30);
        curl_setopt($conn, CURLOPT_PORT, $port);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($conn, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($conn, CURLOPT_FORBID_REUSE , 1);

        if (is_array($parameters) && count($parameters) > 0)
            curl_setopt($conn, CURLOPT_POSTFIELDS, json_encode($parameters));
        else
            curl_setopt($conn, CURLOPT_POSTFIELDS, null);

        $data = null;
        $response = curl_exec($conn);

        if ($response !== false) {
            $data = json_decode($response, true);
            if (!$data) {
                $data = array('error' => $response, "code" => curl_getinfo($conn, CURLINFO_HTTP_CODE));
            }
        }

        curl_close($conn);

        if(isset($data['error']))
        {
            throw new \Exception($data['error']);
        }

        if(empty($data))
        {
            throw new \Exception("curl Could not reach the server: $path");
        }

        return $data;
    }
}
