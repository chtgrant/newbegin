<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CryptoController extends AbstractController
{
    /**
     * @Route("/crypto/{coin}")
     */
    public function index($coin = 'bitcoin')
    {
        $cryptoInfo = $this->getCryptoCurrencyInformation($coin);

        // Envía la información de las criptomonedas a una vista Twig
        return $this->render("crypto/list.html.twig", [
            "crypto" => $cryptoInfo, // Array with all crypto info
            "coin" => ucfirst($coin) // Name of the Crypto
        ]);
    }

    /**
     * Obtiene la información que ofrece la API gratuita de CoinMarketCap de una criptomoneda.
     * De manera predeterminada se usa el dolar americano como moneda de conversión.
     * 
     *
     * @param string $currencyId Identificador de la moneda
     * @param string $convertCurrency
     * @see https://coinmarketcap.com/api/
     * @return mixed 
     */
    private function getCryptoCurrencyInformation($currencyId, $convertCurrency = "USD"){

        $url = 'https://api.coingecko.com/api/v3/coins/'.$currencyId.'/tickers';

        $headers = [
        'Accepts: application/json',
        'X-CMC_PRO_API_KEY: b54bcf4d-1bca-4e8e-9a24-22ff2c3d462c'
        ];
        
        $request = "{$url}"; // create the request URL 

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