<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ProfitfyController extends Controller
{
	private $id  = '';
	private $key = '';
	private $mainurl = 'https://bootcamp.profitfy.trade/api/v1/'; 

	public function amx_authorization_header($enderecourl, $method, $body) {
		/* fonte: https://www.tembtc.com.br/documentacao-tradeapi */

	    $id  = $this->id;
	    $key = $this->key;

	    date_default_timezone_set('America/Sao_Paulo');
	    $url 		= strtolower(urlencode($enderecourl));      
	    $content 	= empty($body) ? '' : base64_encode(md5($body, true));
	    $time 		= time();
	    $nonce 		= md5(uniqid(mt_rand(), true));
	    $data 		= implode('', [$id, strtoupper($method), $url, $time, $nonce, $content]);
	    $secret		= base64_decode($key);
	    $signature 	= base64_encode(hash_hmac('sha256', $data, $secret, true));

	    return 'amx ' . implode(':', [$id, $signature, $nonce, $time]);
	}

	public function criaDeposito($valorBTC){
		$url  = $this->mainurl.'private/deposits/cripto';
		$met  = 'POST';
		$body = '{"coin": "BTC",
				  "amount": '.$valorBTC.',
				  "reference": "Deposito via API"
				 }';

		$client = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => 'false')));

		$res = $client->request($met,$url,['headers' => ['authorization' => $this->amx_authorization_header($url,$met,$body),
     													 'accept'        => 'application/json',
     													 'Content-Type'  => 'application/json'],
     									   'body' => $body 
     									]);
		var_dump(json_decode($res->getBody(), true));
	}

	public function verificaRequisicaoRecebimento($idRecebimento){
		$client = new Client(array('curl' => array( CURLOPT_SSL_VERIFYPEER => 'false')));

	    $url  = $this->mainurl.'private/payment/cripto/'.$idRecebimento;
	    $met  = 'GET';
	    $body = '';
     	$res  = $client->request($met,$url,['headers' => ['authorization' => $this->amx_authorization_header($url,$met,$body),]]);

		var_dump(json_decode($res->getBody(), true));
	}

	public function verificaTodasRequisicaoRecebimento(){
	    $client = new Client(array('curl' => array( CURLOPT_SSL_VERIFYPEER => 'false')));

	    $url  = $this->mainurl.'private/payment/cripto/';
	    $met  = 'GET';
	    $body = '';
     	$res  = $client->request($met,$url,['headers' => ['authorization' => $this->amx_authorization_header($url,$met,$body),]]);

		var_dump(json_decode($res->getBody(), true));
	}

	public function criaRequisicaoRecebimento($valorBRL){
	    $client = new Client(array('curl' => array( CURLOPT_SSL_VERIFYPEER => 'false')));

	    $url = $this->mainurl.'private/payment/cripto/';
	    $met = 'POST';
     	$body = ('{
				  "coinfrom": "BRL",
				  "cointo": "BTC",
				  "Amount": '.$valorBRL.',
				  "Reference": "COBRANCA VIA API"
				}');
     	
		$client = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => 'false')));

		$res = $client->request($met,$url,['headers' => ['authorization' => $this->amx_authorization_header($url,$met,$body),
     													 'accept' => 'application/json',
     													 'Content-Type'  => 'application/json'],
     									   'body'=> $body]);
		
		var_dump(json_decode($res->getBody(), true));
	}

    public function index(){
		//$this->criaDeposito(0.025);
		//$this->verificaRequisicaoRecebimento(10000840);
		//$this->verificaTodasRequisicaoRecebimento();
		//$this->criaRequisicaoRecebimento(134.75);
    }

}