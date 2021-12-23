# TimSmsProfessionalClient
Semplice Libreria PHP Unofficial Client per invio singoli SMS tramite API del servizio timsmsprofessional.it

Libreria NON ufficiale liberamente creata, non sussite nessun collegamento con la compagnia TIM
https://timsmsprofessional.it è un servizio a pagamento della compagnia telefonica italiana TIM

## Caratteristiche
* Invio di singoli SMS utilizzando le API di timsmsprofessional.it
* Ricerca del messaggio inviato per recuperare lo stato di effettiva consegna
* Inserita una funzione per il controllo e la formattazione del numero telefonico prima dell'invio con controllo che rientri nei di prefissi telefonici attribuiti al servizio mobile italiano alla data del 01/12/2021

## Requisiti
- PHP 5.6+
- PHP Curl extension
- apimatic/unirest-php: 2.0+

## Installazione
Per installare con Composer aggiungere `iw5edv/tim-sms-professional-client` al tuo `composer.json` file:

```json
{
    "require": {
        "iw5edv/tim-sms-professional-client"
    }
}
```

oppure eseguendo il seguente comando:

```shell
composer require iw5edv/tim-sms-professional-client
```

## Configurazione
Configurare i dati personali username, password, token e alias
modificando il file `/src/TimSmsProfessionalClient.php`
```php
.....
  private $username  = ' ';
  private $password  = ' ';
  private $token     = ' ';
  private $alias     = ' ';  //numero completo da dove inviare i messaggi o Alias Certificato caricato sul proprio profilo
.....
```
Oppure creando il clinet con il construct
```php
$client = new iw5edv\TimSmsProfessionalClient([
    'username'	=> ' ',
    'password'	=> ' ',
    'token'	=> ' ',
    'alias'	=> ' '
]);
```

# Utilizzo
## Inviare un singolo SMS
```php
$to = '+391234567890';
$text = 'Prova';
$result = \iw5edv\TimSmsProfessionalClient::InvioSms($to, $text)

echo '<pre>'; print_r($result); echo '</pre>';
```
*oppure*
```php
$client = new iw5edv\TimSmsProfessionalClient();
$to = '+391234567890';
$text = 'Prova';
$result = $client->InvioSms($to, $text);

echo '<pre>'; print_r($result); echo '</pre>';
```

### Risposta
Per le caratteristiche della risposta vedere la libreria https://github.com/apimatic/unirest-php

Alla ricezione di una risposta Unirest restituisce il risultato sotto forma di `Object`
- `code` - HTTP Response Status Code (Example `200`)
- `headers` - HTTP Response Headers
- `body` - Parsed response body where applicable, for example JSON responses are parsed to Objects / Associative Arrays.
- `raw_body` - Un-parsed response body
```php
$result->code;        // HTTP Status code
$result->headers;     // Headers
$result->body;        // Parsed body
$result->raw_body;    // Unparsed body
```

## Ricerca SMS
Per la ricerca dell'SMS è necessario recuperare ID della campagna al momento dell'invio

*Un esempio di codice per recuperare ID dalla risposta dell'invio*
```php
$position = strpos($result->body->message, ':');
$position ++;
$code = substr($result->body->message, $position);
```
### Ricerca SMS inviato utilizzando ID precedentemente recuperato
```php
$code = '00000000';  //id campagna precedentemente recuperato
$result = \iw5edv\TimSmsProfessionalClient::cercaSms($code);

echo '<pre>'; print_r($result); echo '</pre>';
```
*oppure*
```php
$client = new iw5edv\TimSmsProfessionalClient();
$code = '00000000';  //id campagna precedentemente recuperato
$result = $client->cercaSms($code);

echo '<pre>'; print_r($result); echo '</pre>';
```

## Controllo e Formattazione numero telefonico
- Controllo del numero telefonico secondo gli standar ITALIANI
- Formattazione nella forma giusta per invio sms `+39 3` `123456789`

    - inizio numero con prefisso internazionale +39
    - controllo che rientri in un dei prefissi assegnati in Italia al 01/12/2021
    - controlla la formalità del numero 10 cifre
    - ammette i numeri a 9 cifre controllando che faccia parte dei prefissi assegnati

Esempio di utilizzo per il solo controllo
```php
$to = '+391234567890';  //numero da testare
if($to_format=\iw5edv\TimSmsProfessionalClient::formatNumeroIta($to)){
    echo 'Numero Corretto ';
    echo $to_format;
} else {
	echo 'Numero Errato';
}
```
Esempio di controllo e formattazione con invio se controllo andato a buon fine
```php
$to = '+391234567890';  //numero da testare
$text = 'Prova';
if($to_format=\iw5edv\TimSmsProfessionalClient::formatNumeroIta($to)){
	$result = \iw5edv\TimSmsProfessionalClient::InvioSms($to_format, $text);
	echo '<pre>'; print_r($result); echo '</pre>';
} else {
	echo 'Numero Errato - SMS Non inviato!';
}
```
