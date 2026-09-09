<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Netsuite_oauth
{

  /** @var CI_Controller */
  protected $CI;

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

  /**
   * ดึง access token แบบมี cache
   */
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

  /**
   * สร้าง JWT (client assertion) ด้วย PS256 แล้วแลก token
   */
  protected function request_new_token()
  {
    $iat = time();
    $exp = $iat + $this->token_ttl;

    // payload
    $payload = [
      'iss' => $this->client_id,
      'scope' => $this->scope,
      'aud' => $this->token_url,
      'iat' => $iat,
      'exp' => $exp,
    ];

    // header (PS256 + kid)
    $header = [
      'alg' => $this->alg,
      'typ' => 'JWT',
      'kid' => $this->certificate_id,
    ];

    // สร้าง JWT ด้วย PS256 (RSA-PSS SHA-256)
    $jwt = JWT::encode(
      $payload,
      $this->private_key_pem,
      $this->alg,
      null,
      $header
    );

    // เรียก token endpoint
    $post_fields = http_build_query([
      'grant_type' => 'client_credentials',
      'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
      'client_assertion' => $jwt,
    ]);
   
    $ch = curl_init($this->token_url);
    curl_setopt_array($ch, [
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
      ],
      CURLOPT_POSTFIELDS => $post_fields,
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false)
    {
      $error = curl_error($ch);
      curl_close($ch);
      throw new Exception('cURL error: ' . $error);
    }

    curl_close($ch);

    $data = json_decode($response, true);

    //echo $response.'<br/>';

    if ($http_code !== 200)
    {
      // debug invalid_client / อื่น ๆ
      throw new Exception('Token endpoint error: HTTP ' . $http_code . ' - ' . $response);
    }

    if (!isset($data['access_token']))
    {
      throw new Exception('No access_token in response: ' . $response);
    }

    return $data['access_token'];
  }
}
