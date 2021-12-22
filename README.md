# TimSmsProfessionalClient
Semplice Libreria PHP Unofficial Client per invio singoli SMS tramite API del servizio timsmsprofessional.it

Libreria NON ufficiale liberamente creata, non sussite nessun collegamento con la compagnia TIM
https://timsmsprofessional.it è un servizio a pagamento della compagnia telefonica italiana TIM

## Caratteristiche
* Invio di singoli SMS utilizzando le API di timsmsprofessional.it
* Ricerca del messaggio inviato per recuperare lo stato di effettiva consegna
* Inserita una funzione per il controllo e la formattazione del numero telefonico prima dell'invio con controllo del reng di prefissi telefonici attribuiti al servizio mobile italiano alla data dal 21/12/2021

## Requisiti
- PHP 5.6+
- PHP Curl extension
- apimatic/unirest-php: 2.0+

## Installazione
Per installare con Composer aggiungere `iw5edv/tim-sms-professional-client` al tuo `composer.json` file:

```json
{
    "require": {
        "apimatic/unirest-php": "^2.0"
    }
}
```

oppure eseguendo il seguente comando:

```shell
composer require iw5edv/tim-sms-professional-client
```

## Configurazione
Configurare i dati personali username, password, token e alias
modificando il file `src/TimSmsProfessionalClient.php`
```php
.....
  private const USERNAME  = ' ';
  private const PASSWORD  = ' ';
  private const TOKEN     = ' ';
  private const ALIAS     = ' ';  //numero completo da dove inviare i messaggi o Alias Certificato caricato sul proprio profilo
.....
```

# Utilizzo
## Inviare un singolo SMS
```php
$client = new iw5edv\TimSmsProfessionalClient();
$to = '+391234567890';
$text = 'Prova';
$result = $client->InvioSms($to, $text);

echo '<pre>'; print_r($result); echo '</pre>';
```
*oppure*
```php
$to = '+391234567890';
$text = 'Prova';
$result = \iw5edv\TimSmsProfessionalClient::InvioSms($to, $text)

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

```php
$client = new iw5edv\TimSmsProfessionalClient();
$code = '00000000';
$result = $client->cercaSms($code);

echo '<pre>'; print_r($result); echo '</pre>';
```
*oppure*
```php
$code = '00000000';
$result = \iw5edv\TimSmsProfessionalClient::cercaSms($code);

echo '<pre>'; print_r($result); echo '</pre>';
```
