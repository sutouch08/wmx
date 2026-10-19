<?php
defined('BASEPATH') or exit('No direct script access allowed');

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;

class Netsuite_client
{
    // Add your class properties and methods here  
  protected \CI_Controller $ci;
  protected string $ns_base_url;
  protected string $token_url;
  protected string $client_id;
  protected string $certificate_id;
  protected string $private_key_pem;
  protected string $scope;
  protected int $token_ttl = 3600;
  protected string $alg;
  protected int $timeoutSec = 60; //-- cURL timeout in seconds
  private ?string $access_token = null;

  public function __construct()
  {
    $this->ci = &get_instance();
    $this->ci->load->config('netsuite');

    $this->token_url = $this->ci->config->item('ns_token_url');
    $this->client_id = $this->ci->config->item('ns_client_id');
    $this->certificate_id = $this->ci->config->item('ns_certificate_id');
    $this->private_key_pem = $this->ci->config->item('ns_private_key_pem');
    $this->scope = $this->ci->config->item('ns_scope');
    $this->token_ttl = $this->ci->config->item('ns_token_ttl');
    $this->alg = $this->ci->config->item('ns_alg');
    $this->ns_base_url = $this->ci->config->item('ns_base_url');
  }

  public function get_access_token()
  {
    // ใช้ CI cache หรือเก็บในไฟล์/DB ก็ได้
    $this->ci->load->driver('cache', ['adapter' => 'file']);

    $cache_key = 'netsuite_access_token_sb2';
    $cached = $this->ci->cache->get($cache_key);

    if ($cached && isset($cached['access_token'], $cached['expires_at']))
    {
      // refresh ล่วงหน้า 5 นาที
      if ($cached['expires_at'] - time() > 300)
      {
        $this->access_token = $cached['access_token'];
        return $cached['access_token'];
      }
    }

    // ถ้าไม่มี หรือใกล้หมดอายุ → ขอใหม่
    $access_token = $this->request_new_token();

    // เก็บ cache
    $this->ci->cache->save($cache_key, [
      'access_token' => $access_token,
      'expires_at'   => time() + $this->token_ttl,
    ], $this->token_ttl);

    $this->access_token = $access_token;
    return $access_token;
  }

  protected function request_new_token()
  {
    // Implement the logic to request a new access token from Netsuite
    // using the client_id, certificate_id, private_key_pem, scope, token_ttl, and alg
    // Return the new access token
    $iat = time();
    $exp = $iat + $this->token_ttl;

    // header (PS256 + kid)
    $header = [
      'alg' => $this->alg,
      'typ' => 'JWT',
      'kid' => $this->certificate_id,
    ];

    // payload
    $payload = [
      'iss' => $this->client_id,
      'scope' => $this->scope,
      'aud' => $this->token_url,
      'iat' => $iat,
      'exp' => $exp,
    ];

    $signingInput = self::b64url(json_encode($header, JSON_UNESCAPED_SLASHES))
      . '.' . self::b64url(json_encode($payload, JSON_UNESCAPED_SLASHES));

    // PS256 = RSASSA-PSS, hash SHA-256, MGF1 SHA-256, salt length = 32 (ค่า default ของ phpseclib)
    $key = PublicKeyLoader::loadPrivateKey($this->private_key_pem)
      ->withPadding(RSA::SIGNATURE_PSS)
      ->withHash('sha256')
      ->withMGFHash('sha256');

    $signature = $key->sign($signingInput);

    $jwt = $signingInput . '.' . self::b64url($signature);

    // เรียก token endpoint  
    $post_fields = http_build_query([
      'grant_type' => 'client_credentials',
      'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
      'client_assertion' => $jwt,
    ]);

    $ch = curl_init($this->token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/x-www-form-urlencoded',
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($response === false)
    {
      $error = curl_error($ch);
      throw new Exception('cURL error: ' . $error);
    }

    $data = json_decode($response, true);

    if ($http_code !== 200)
    {
      // debug invalid_client / อื่น ๆ
      throw new Exception('Token endpoint error: HTTP ' . $http_code . ' - ' . $response);
    }

    if (!isset($data['access_token']))
    {
      throw new Exception('Failed to obtain access token from Netsuite');
    }

    $this->access_token = $data['access_token'];
    return $this->access_token;
  }


  // -------------------------------------------------------------- restlet

  /**
   * เรียก RESTlet ด้วย script id (ชื่อ customscript_…) — deploy id = แทน customscript_ ด้วย customdeploy_
   * คืน [httpStatus, decodedJson|null, rawBody]
   */
  public function callRestlet(string $scriptId, array $body, string $method = 'POST'): array
  {
    $deployId = preg_replace('/^customscript_/', 'customdeploy_', $scriptId);
    $url = $this->ns_base_url . '?' . http_build_query(['script' => $scriptId, 'deploy' => $deployId]);

    $headers = [
      'Authorization: Bearer ' . $this->get_access_token(),
      'Content-Type: application/json',
      'Accept: application/json',
    ];

    [$status, $raw] = $method === 'GET'
      ? $this->httpGet($url . '&' . http_build_query($body), $headers)
      : $this->httpPost($url, json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $headers);

    // token หมดอายุกลางทาง → ล้าง cache แล้วลองใหม่ 1 ครั้ง
    if ($status === 401 && $this->access_token !== null)
    {
      $this->access_token = null;
      return $this->callRestlet($scriptId, $body, $method);
    }

    return [$status, json_decode($raw), $raw];
  }

  private function httpPost(string $url, string $payload, array $headers): array
  {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_POST           => true,
      CURLOPT_POSTFIELDS     => $payload,
      CURLOPT_HTTPHEADER     => $headers,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => $this->timeoutSec,
    ]);
    return $this->exec($ch);
  }

  private function httpGet(string $url, array $headers): array
  {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER     => $headers,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => $this->timeoutSec,
    ]);
    return $this->exec($ch);
  }

  private function exec($ch): array
  {
    $body = curl_exec($ch);

    if ($body === false)
    {
      throw new RuntimeException('cURL error: ' . curl_error($ch));
    }

    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    return [$status, (string) $body];
  }

  private static function b64url(string $data): string
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }
}
