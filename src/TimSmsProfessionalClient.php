<?php
/************************************************
 * Libreria Client invio singoli SMS
 * tramite API di TIM sms Professional
 * 
 * by IW5EDV Gabriele
 * www.iw5edv.it
 * per Misericordie della Toscana
 *
 * rev dev 24/12/2021
 * 
 * utilizza libreria apimatic/unirest-php
 ************************************************/

namespace iw5edv;

class TimSmsProfessionalClient {
	
	private $username	= '';
	private $password	= '';
	private $token		= '';
	private $alias		= '';  //numero completo da dove inviare i messaggi o Alias Certificato caricato sul proprio profilo
	
	private const BASE_URL	= 'https://smartconsole.telecomitalia.it/ssc2-api/rest/';
	private const HEADERS	= array('Accept' => 'application/json');
	
	private $result;

	

	/**
	 * @param array $params ('username' => '', 'password' => '', 'token' => '', 'alias' => '')
	 * 
	 * @return void
	 */
	function __construct($params = array()) {
		if (!empty($params)){
			$this->username = $params['username'];
			$this->password = $params['password'];
			$this->token	= $params['token'];
			$this->alias	= $params['alias'];
		}
	}//function __construct



	/**
	 * Funzione per invio SMS
	 * 
	 * @param string $msisdn Numero cellulare dove inviare con prefizzo internazionale +
	 * @param string $testo Testo del messaggio
	 * 
	 * @return Object Unirest\Response
	 */
	public function invioSms($msisdn, $testo) {
		$data = Array ( 
			'username'	=> $this->username,
			'password'	=> $this->password,
			'token'		=> $this->token,
			'alias'		=> $this->alias,
			'testo'		=> $testo, 
			'msisdn'	=> $msisdn,
			'sr'		=> '1'
			); 
		$body = \Unirest\Request\Body::multipart($data);
			
		$this->result = \Unirest\Request::post(self::BASE_URL . 'send/sms/msisdn/static/immediate/single', self::HEADERS, $body);
		return $this->result;
	}//public function invioSms


	/**
	 * Funzione per verifica se operazione andata a buon fine
	 * 
	 * @return Bolean
	 */
	public function getStatus() {
		if (empty($this->result)) {
			return false;
		} elseif ($this->result->code != 200) {
			return false;
		} elseif ($this->result->body->status == 'OK') {
			return true;
		}
		return false;
	}//public function getStatus



	/**
	 * Funzione per il recupero del' ID campagna dopo l'invio SMS
	 * 
	 * @return string
	 * @return false in caso di Object non di INVIOMESSAGGI
	 */
	public function getCode() {
		if ($this->result->body->name != 'INVIOMESSAGGI') {
			return false;
		}
		$position = strpos($this->result->body->message, ':');
		$position ++;
		return substr($this->result->body->message, $position);
	}//public function getCode



	/**
	 * Funzione per ricerca dati ed esito invio SMS
	 * 
	 * @param string $codesms Codice campagna per recupero stato SMS
	 * 
	 * @return Object Unirest\Response
	 */
	public function cercaSms($codesms) {
		$data = Array ( 
			'username'		=> $this->username,
			'password'		=> $this->password,
			'token'			=> $this->token,
			'alias'			=> $this->alias,
			'codiceOperazione'	=> $codesms,
			'numeroRicorrenza'	=> '0',
			'offset'		=> '1',
			'limit'			=> '10'
		);
		$body = \Unirest\Request\Body::form($data);
		$this->result = \Unirest\Request::post(self::BASE_URL . 'search', self::HEADERS, $body);
		return $this->result;
	}//public function verificasms



	/**
	 * Funzione per il recupero dell'esito di ricezione dell'SMS
	 * 
	 * @return Bolean
	 */
	public function getEsitoBol() {
		if ($this->result->body->name != 'RICERCAINVIO') {
			return false;
		}
		$esitosms = false;
		$items = $this->result->body->items;
		foreach ($items as $item)
		{
			if ($item->esito === 'Ricevuto')
			{
				$esitosms = true;
			}
		}
		return $esitosms;
	}//public function getEsitoBol



	/**
	 * Funzione per il recupero dell'esito di ricezione dell'SMS
	 * 
	 * @return String
	 */
	public function getEsitoStr() {
		if ($this->result->body->name != 'RICERCAINVIO') {
			return 'ERRORE';
		}
		$esitosms = 'Non Ricevuto';
		$items = $this->result->body->items;
		foreach ($items as $item)
		{
			if ($item->esito === 'Ricevuto')
			{
				$esitosms = 'Ricevuto';
			}
		}
		return $esitosms;
	}//public function getEsitoStr

	

	/**
	 * Controllo del numero telefonico secondo gli standar Italiani
	 * inizio numero con prefisso internazionale +39
	 * controllo che rientri in un dei prefissi assegnati in Italia al 01/12/2021
	 * controlla la formalità del numero 10 cifre
	 * ammette i numeri a 9 cifre controllando che faccia parte dei prefissi assegnati
	 * 
	 * @param string $numero numero cellulare da controllare e formattare
	 * 
	 * @return string formattato nella forma giusta per invio sms MSSID
	 * @return false nel caso il numero dato in entrata non sia valido
	 */
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
