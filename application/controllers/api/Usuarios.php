<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH. 'libraries/REST_Controller.php';
require APPPATH. 'libraries/Format.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

class Usuarios extends REST_Controller
{
    
  public function __construct()
  {
    parent::__construct("rest");
    $this->load->model(array('usuarios_model'));
  }

  public function index_get()
  {
    if(!is_null($this->usuarios_model->findAll())){
      $this->response($this->usuarios_model->findAll(), 200);
    }else{
      $this->response(array('error' =>  'No existen registros'), 404);
    }
  }

  public function index_post()
  {
    $username = $this->post('username');
    $password = $this->post('password');

    if(isset($username) && isset($password)){
      if(!is_null($this->usuarios_model->searchUser($username, $password))){
        $this->response($this->usuarios_model->searchUser($username, $password), 200);
      }else{
        $this->response(array('error' =>  'No existen registros'), 404);
      }
    }else{
      $this->response(array('error' =>  'EL usuario o la contraseña estan vacías',
                            'username' => $username,
                            'password' => $password), REST_Controller::HTTP_BAD_REQUEST);
    }
  }

  public function login_post(){
    $username = $this->post('username');
    $password = $this->post('password');

    if(isset($username) && isset($password)){
      if($this->usuarios_model->login($username, $password) == true){
        $this->response(array('found' => 'true'), 200);
      }else{
        $this->response(array('found' =>  'false'), 404);
      }
    }else{
      $this->response(array('error' =>  'EL usuario o la contraseña estan vacías',
                            'username' => $username,
                            'password' => $password), REST_Controller::HTTP_BAD_REQUEST);
    }
  }
}
