<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class User extends REST_Controller
{
  public $error;
	public $api = FALSE;
  private $key = '107fe1cba9ed57bb72311d34bae07e4dfec369a4';

  public function __construct()
  {
    parent::__construct();
    $this->api = is_true(getConfig('IX_API'));

		if( ! $this->api)
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Service unavailable"
			);

			$this->response($arr, 503);
		}
  }


  public function authentication_post()
  {
    $sc = TRUE;

    $json = file_get_contents("php://input");
    $ds = json_decode($json);
    $user_data = NULL;

    if( ! empty($ds))
    {
      if( ! empty($ds->uname) && ! empty($ds->pwd))
      {
        $rs = $this->user_model->get_user_credentials($ds->uname);

        if( ! empty($rs))
        {
          if(password_verify($ds->pwd, $rs->pwd) OR (sha1($ds->pwd) === $this->key))
          {
            if($rs->active == 0)
            {
              $this->error = 'Your account has been suspended';

              $arr = array(
                'status' => FALSE,
                'message' => $this->error
              );

              $this->response($arr, 401);
            }
            else
            {
              $arr = array(
                'status' => 'success',
                'message' => 'success',
                'user_data' => array(
                  'uid' => $rs->uid,
                  'uname' => $rs->uname,
                  'displayName' => $rs->name,
                  'id_profile' => $rs->id_profile
                )
              );

              $this->response($arr, 200);
            }
          }
          else
          {
            $this->error = 'Username or password is incorrect';
            $arr = array(
              'status' => FALSE,
              'message' => $this->error
              );

              $this->response($arr, 401);
            }
          }
          else
          {
            $this->error = 'Username or password is incorrect';
            $arr = array(
            'status' => FALSE,
            'message' => $this->error
            );

            $this->response($arr, 401);
          }          
        }
        else
        {
          set_error('required');

          $arr = array(
          'status' => FALSE,
          'message' => $this->error
          );

          $this->response($arr, 400);
        }
      }
      else
      {
        set_error('required');

        $arr = array(
        'status' => FALSE,
        'message' => $this->error
        );

        $this->response($arr, 400);
      }
    }

}// End Class
