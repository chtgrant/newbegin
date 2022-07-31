<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Importante: Incluir GuzzleClient
use GuzzleHttp\Client;

class CryptoController extends AbstractController
{
    /**
     * @Route("/crypto/", name="homepage")
     */
    public function index($coin = 'bitcoin')
    {
        // Obtener información sobre la criptomoneda bitcoin
        $bitcoinInfo = $this->getCryptoCurrencyInformation($coin);

        // Información sobre ethereum pero en Euros en vez del dolar americano
        //$ethereumInfo = $this->getCryptoCurrencyInformation("ethereum", "EUR");

        // y así con más de 1010 criptomonedas

        // Envía la información de las criptomonedas a una vista Twig
        return $this->render("crypto/list.html.twig", [
            "bitcoin" => $bitcoinInfo,
            "coin" => ucfirst($coin)
            //"ethereum" => $ethereumInfo
        ]);
    }

    /**
     * Obtiene la información que ofrece la API gratuita de CoinMarketCap de una criptomoneda.
     * De manera predeterminada se usa el dolar americano como moneda de conversión.
     * 
     * ADVERTENCIA: No uses este código en producción, esto es solo para explicar como funciona la API
     * y cómo se obtiene la información. Lee el paso 3 para la implementación final
     *
     * @param string $currencyId Identificador de la moneda
     * @param string $convertCurrency
     * @see https://coinmarketcap.com/api/
     * @return mixed 
     */
    private function getCryptoCurrencyInformation($currencyId, $convertCurrency = "USD"){
        /*// Obtener un nuevo cliente Http de Guzzle
        $client = new Client();

        // Define la URL a la que se consultará con los parámetro necesarios
        $requestURL = "https://sandbox-api.coinmarketcap.com/v1/cryptocurrency/listings/latest";

        // Ejecutar la solicitud
        $singleCurrencyRequest = $client->request('GET', $requestURL);
        
        // Obtener el cuerpo de la respuesta en formato array
        $body = json_decode($singleCurrencyRequest->getBody() , true)[0];

        // Si hubo un error en la consulta, lanzar una excepción
        if(array_key_exists("error" , $body)){
            throw $this->createNotFoundException(sprintf('Fallo al solicitar información sobre la criptomoneda : $s', $body["error"]));
        }*/

        $url = 'https://api.coingecko.com/api/v3/coins/'.$currencyId.'/tickers';
        /*$parameters = [
        'start' => '1',
        'limit' => '5000',
        'convert' => 'USD'
        ];*/

        $headers = [
        'Accepts: application/json',
        'X-CMC_PRO_API_KEY: b54bcf4d-1bca-4e8e-9a24-22ff2c3d462c'
        ];
        //$qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}"; // create the request URL ?{$qs}


        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
        CURLOPT_URL => $request,            // set the request URL
        CURLOPT_HTTPHEADER => $headers,     // set the headers 
        CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        //print $response;
        //print_r(json_decode($response)); // print json decoded response
        curl_close($curl); // Close request

        // Retorna un array con la información sobre la moneda
        return json_decode($response);
    }
}