<?php
defined('BASEPATH') or exit('No direct script access allowed');

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;

class Netsuite_oauth {
    // Add your class properties and methods here
  /** @var CI_Controller */
  protected $CI;
  protected $token_url;
  protected $client_id;
  protected $certificate_id;
  protected $private_key_pem;
  protected $scope;
  protected $token_ttl;
  protected $alg;

  public function __construct()
  {
    $this->CI = &get_instance();
    $this->CI->load->config('netsuite');

    $this->token_url = $this->CI->config->item('ns_token_url');
    $this->client_id = $this->CI->config->item('ns_client_id');
    $this->certificate_id = $this->CI->config->item('ns_certificate_id');
    $this->private_key_pem = $this->CI->config->item('ns_private_key_pem');
    $this->scope = $this->CI->config->item('ns_scope');
    $this->token_ttl = $this->CI->config->item('ns_token_ttl');
    $this->alg = $this->CI->config->item('ns_alg');
  }

  public function get_access_token()
  {
    // ใช้ CI cache หรือเก็บในไฟล์/DB ก็ได้
    $this->CI->load->driver('cache', ['adapter' => 'file']);

    $cache_key = 'netsuite_access_token_sb2';
    $cached = $this->CI->cache->get($cache_key);

    if ($cached && isset($cached['access_token'], $cached['expires_at']))
    {
      // refresh ล่วงหน้า 5 นาที
      if ($cached['expires_at'] - time() > 300)
      {
        return $cached['access_token'];
      }
    }

    // ถ้าไม่มี หรือใกล้หมดอายุ → ขอใหม่
    $access_token = $this->request_new_token();

    // เก็บ cache
    $this->CI->cache->save($cache_key, [
      'access_token' => $access_token,
      'expires_at'   => time() + $this->token_ttl,
    ], $this->token_ttl);

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

    if (!isset($data['access_token'])) {
      throw new Exception('Failed to obtain access token from Netsuite');
    }

    return $data['access_token'];
  }

  private static function b64url(string $data): string
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }
}