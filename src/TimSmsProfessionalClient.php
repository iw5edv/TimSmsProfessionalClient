<?php
/************************************************
 * Libreria Client invio singoli SMS
 * tramite API di TIM sms Professional
 * 
 * by IW5EDV Gabriele
 * www.iw5edv.it
 * per Misericordie della Toscana
 *
 * rev 21/12/2021
 * 
 * utilizza libreria apimatic/unirest-php
 ************************************************/

namespace iw5edv;

class TimSmsProfessionalClient {
	
	private const USERNAME	= 'misetoscapi';
	private const PASSWORD	= 'Salaoperativa5!';
	private const TOKEN		= '5CD1C8EA9B97050F589D08682021AAD3F23D9532';
	private const ALIAS		= 'Mise-Tosc';
	
	private const BASE_URL = 'https://smartconsole.telecomitalia.it/ssc2-api/rest/';
	private const HEADERS = array('Accept' => 'application/json');
	
	function __construct() {
    
	}//function __construct

	public function invioSms($msisdn, $testo){
		$data = Array ( 
			'username'	=> self::USERNAME,
			'password'	=> self::PASSWORD,
			'token'		=> self::TOKEN,
			'alias'		=> self::ALIAS,
			'testo'		=> $testo, 
			'msisdn'	=> $msisdn,
			'sr'		=> '1'
			); 
		$body = \Unirest\Request\Body::multipart($data);
			
		return \Unirest\Request::post(self::BASE_URL . 'send/sms/msisdn/static/immediate/single', self::HEADERS, $body);
	
	}//public function invioSms


	public function cercaSms($codesms){
		$data = Array ( 
			'username'			=> self::USERNAME,
			'password'			=> self::PASSWORD,
			'token'				=> self::TOKEN,
			'alias'				=> self::ALIAS,
			'codiceOperazione'	=> $codesms,
			'numeroRicorrenza'	=> '0',
			'offset'			=> '1',
			'limit'				=> '10'
		);
		$body = \Unirest\Request\Body::form($data);
		
		return \Unirest\Request::post(self::BASE_URL . 'search', self::HEADERS, $body);
		
	}//public function verificasms
	
	
	/*************************************************************************************
	 * Controllo del numero telefonico secondo gli standar Italiani
	 * lo formatta nella forma giusta per invio sms MSSID
	 * 
	 * inizio numero con prefisso internazionale +39
	 * controllo che rientri in un dei prefissi assegnati in Italia al 01/12/2021
	 * controlla la formalità del numero 10 cifre
	 * ammette i numeri a 9 cifre controllando che faccia parte dei prefissi assegnati
	 **************************************************************************************/
	public function formatNumeroIta($numero) {
		// gia con prefisso internazionale +39
		$pattern = '/^(([+])39)+(31[3]|32[01234789]|33[13489]|34[0-9]|35[01235]|36[1236]|37[0135-9]|38[012389]|39[01237])\d{7}$|^(([+])39)+(33[0567]|36[08])\d{6,7}$/';
		if(preg_match($pattern, $numero)){
			return $numero;
		}
		// senza prefisso internazionale
		$pattern = '/^(31[3]|32[01234789]|33[13489]|34[0-9]|35[01235]|36[1236]|37[0135-9]|38[012389]|39[01237])\d{7}$|^(33[0567]|36[08])\d{6,7}$/';
		if(preg_match($pattern, $numero)) {
			return '+39' . $numero;
		}
		// con prefisso internazionale senza il +
		$pattern = '/^(39)+(31[3]|32[01234789]|33[13489]|34[0-9]|35[01235]|36[1236]|37[0135-9]|38[012389]|39[01237])\d{7}$|^(39)+(33[0567]|36[08])\d{6,7}$/';
		if(preg_match($pattern, $numero)) {
			return '+' . $numero;
		}
		// con prefisso internazionale che inizia con 00
		$pattern = '/^(0039)+(31[3]|32[01234789]|33[13489]|34[0-9]|35[01235]|36[1236]|37[0135-9]|38[012389]|39[01237])\d{7}$|^(0039)+(33[0567]|36[08])\d{6,7}$/';
		if(preg_match($pattern, $numero)) {
			return preg_replace('/^00/', '+', $numero);
		}
		// tutti gli altri casi numero errato o non interpretabile
		return false;
	}//public function formatNumeroIta

}//class
?>