<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Netsuite_test extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  public function test()
  {
    $this->load->library('Netsuite_oauth');
    $access_token = $this->netsuite_oauth->get_access_token();
    echo "Access token: " . $access_token;
  }

  public function call_restlet()
  {
    $this->load->library('Netsuite_oauth');

    // ดึง access token (มี cache)
    $access_token = $this->netsuite_oauth->get_access_token();

    $restlet_url = 'https://9724922-sb2.suitetalk.api.netsuite.com/services/rest/record/v1/some-endpoint'; // เปลี่ยนตามจริง

    $ch = curl_init($restlet_url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
      ],
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false)
    {
      $error = curl_error($ch);
      curl_close($ch);
      show_error('cURL error: ' . $error);
    }

    curl_close($ch);

    if ($http_code === 401)
    {
      // INVALID_LOGIN → ตรวจ kid/iss/aud/alg และ Login Audit Trail บน SB2
      log_message('error', 'NetSuite RESTlet 401 INVALID_LOGIN: ' . $response);
    }

    // ดูผลลัพธ์
    header('Content-Type: application/json');
    echo $response;
  }
}
