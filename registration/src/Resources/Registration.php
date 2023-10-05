<?php /** @noinspection PhpMultipleClassDeclarationsInspection */


namespace KwantRegistration\Resources;



use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use KwantRegistration\Core\AppConfig;
use KwantRegistration\Http\JsonResponder;
use KwantRegistration\Http\RequestFactory;
use KwantRegistration\Http\RequestUtils;
use KwantRegistration\Http\ResponseUtils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Registration extends ResourceBase implements ResourceInterface {

  protected string $error = '';

  public function dispatch() {

    $request = RequestFactory::fromGlobals();
    $method = $request->getMethod();
    if (strtoupper($method) == 'POST') {
      $response = $this->process($request);
      $response = $response->withHeader('Access-Control-Allow-Origin', '*');
      ResponseUtils::output($response);
      return;
    }

    $token = RequestUtils::getPathSegment($request, 2);

    if (empty($token)) {
      $this->error404();
      return;
    }
    $this->templateVars['token'] = $token;
    if ($token == 'test') {

      include APP_ROOT . "/src/www/templates/test.phtml";
    } else {
      include APP_ROOT . "/src/www/templates/registration.phtml";
    }
  }

  protected function process(ServerRequestInterface $request): ResponseInterface {
    $responder = new JsonResponder(true);

    $this->logger->debug("Start Registration...");
    $postData = json_decode(json_encode($request->getParsedBody(), JSON_FORCE_OBJECT), true);

    $token = $postData['token']??'';
    if (empty($token)) {
      return $responder->success(['result' => 'error', 'error' => 'Token musi byc podany']);
    }

//    if ($token == 'W4aTwS9jAj2uE0LP') {
//      return $responder->success(['result' => 'success', 'message' => 'Rejestracja zakonczyla sie pomyslnie']);
//    }

    $response = $this->callRemote($token);
    if ($response) {
      if (isset($response['result']) && $response['result'] == 'success') {
        return $responder->success(['result' => 'success', 'message' => 'Rejestracja zakonczyla sie pomyslnie']);
      } elseif (isset($response['error'])) {
        if ($response['error'] == '404') {
          return $responder->success(['result' => 'error', 'error' => '']);
        } else {
          return $responder->success(['result' => 'error', 'error' => $response['error']]);
        }
      }
    }
    return $responder->success(['result' => 'error', 'error' => 'Błąd komunikacji z serwerem. Skontaktuj sie z działem obsługi.']);
  }

  private function callRemote(string $token):?array {
    $apiConf = AppConfig::getInstance()->getConfig()['api'];

    $apiKey = $apiConf['X-API-Key'];
    $url = $apiConf['endpoint'] . "/account/confirmRegistration";
    $data = json_encode(['token' => $token]);

    $client = new Client([
      'headers' => [
        'Content-Type' => 'application/json',
        'X-API-Key' => $apiKey
      ]
    ]);

    try {
      $this->logger->debug("\nPost data: $url, body: \n".print_r($data, true)."\n");
      $response = $client->post($url,
        ['body' => $data]
      );

      $json = (string)$response->getBody();
      $this->logger->debug("\$response : $json");
      return json_decode($json, true);
    } catch (GuzzleException $e) {
      $this->logger->error($e->getMessage());
      //echo "ERROR > ".$e->getMessage()."\n";

      $response =  ['error' => $e->getCode()];
      $this->logger->debug("\$response:\n".print_r($response, true)."\n");
      return $response;

      //$this->error = "Blad serwera. Skontaktuj sie z dzialem obslugi.<br>Kod bledu ".__LINE__."<br>";
    }
  }

  private function error404() {
    header("HTTP/1.0 404 Not Found");
    include APP_ROOT . "/src/www/templates/404.phtml";
    die();
  }

  /**
   * @return string
   *
   * @noinspection PhpUnusedPrivateMethodInspection
   */
  private function generateAutoregistrationLink(): string {

    $conf = AppConfig::getInstance()->getConfig()['autoRegistartion'];
    $registrationUrl = $conf['url'];
    $registrationKey = $conf['key'];

    $mockData = [
      'contractor_id' => '6002807',
      'email' => 'zamrazalnia.mika@gmail.com'
    ];

    $message = json_encode($mockData);
    $hash = $this->encryptAES($message, $registrationKey);

    return str_replace('{HASH}', $hash, $registrationUrl);
  }

  /**
   * Per ora non usato. serve per generare una chiave hash con i dati
   *
   * @param string $message
   * @param string $key
   * @return string
   */
  private function encryptAES(string $message, string $key): string {
    $aes = openssl_encrypt($message,'AES-128-ECB', $key, OPENSSL_RAW_DATA);
    return bin2hex($aes);
  }
}
