<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends CI_Model {

  public function __construct()
  {
    parent::__construct();
  }

  public function findAll(){
    return $this->db->get('usuarios')->result();
  }

  public function searchUser($username, $password){
    $this->db->where('username', $username);
    $this->db->where('password', $password);
    return $this->db->get('usuarios')->row();
  }

  public function login($username, $password){
    $this->db->where('username', $username);
    $this->db->where('password', $password);

    if(!is_null($this->db->get('usuarios')->row())){
      return true;
      }else{
        return false;
      }
    }
}
