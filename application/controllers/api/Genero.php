<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH. 'libraries/Rest_Controller.php';
require APPPATH. 'libraries/Format.php';

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");

class Genero extends REST_Controller{
    public function __construct(){
        parent::__construct("rest");
        $this->load->model(array('usuarios_model'));
        // Load these helper to create JWT tokens
        $this->load->helper(['jwt', 'authorization']); 
    }

    public function index_options(){
        return $this->response(NULL,REST_Controller::HTTP_OK);
    }


    //METODO GET
    public function index_get($id=null){

        $tokenData = 'Hello World!';
        
        // Create a token
        $token = AUTHORIZATION::generateToken($tokenData);
        // Set HTTP status code
        $status = parent::HTTP_OK;
        // Prepare the response
        $response = ['status' => $status, 'token' => $token];
        // REST_Controller provide this method to send responses

        $this->response($response, $status);

        // if(!empty($id)){
        //     $data=$this->db->get_where("genero",['id'=>$id])->row_array();
        //     if($data==null){
        //         $this->response(["El registro con ID $id no existe"],REST_Controller::HTTP_NOT_FOUND);
        //     }
        // }else{
        //     $data=$this->db->get("genero")->result();
        // }
        // $this->response($data,REST_Controller::HTTP_OK);
    }

    public function login_post()
 {
        // Extract user data from POST request
        $usuario = array(
            'username' => $this->post('username'),
            'password' => $this->post('password')
        );

        // Check if valid user
        if( isset($usuario['username']) && isset($usuario['password']) ){
            if($this->usuarios_model->login($usuario['username'], $usuario['password']) == true){

            // Create a token from the user data and send it as reponse
            $token = AUTHORIZATION::generateToken(['username' => $usuario['username']]);
            // Prepare the response
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'token' => $token];
            $this->response($response, $status);

            }else {
                $this->response(['msg' => 'Invalid username or password!'], parent::HTTP_NOT_FOUND);
            }
            }else{
                $this->response(array('error' =>  'EL usuario o la contraseña estan vacías',
                                      'username' => $usuario['username'],
                                      'password' => $usuario['password']), REST_Controller::HTTP_NOT_FOUND);
              }
 }

 public function get_me_data_post()
{
    // Call the verification method and store the return value in the variable
    $response = $this->verify_request();
    if($response['status'] == 200){
        $response = array_merge($response, array('data' => $this->db->get("genero")->result()));
        $this->response($response, $response['status']);
    }else{
        $this->response($response, $response['status']);
    }
}

 private function verify_request()
 {
     // Get all the headers
     $headers = $this->input->request_headers();
     // Extract the token
     $token = $headers['Authorization'];
     // Use try-catch
     // JWT library throws exception if the token is not valid
     try {
         // Validate the token
         // Successfull validation will return the decoded user data else returns false
         $data = AUTHORIZATION::validateToken($token);
         if ($data === false) {
             $response = ['msg' => 'Unauthorized Access!', 'status' => parent::HTTP_UNAUTHORIZED];
             return $response;
         } else {
            $response = ['msg' => 'Successful Authorization!', 'status' => parent::HTTP_OK];
             return $response;
         }
     } catch (Exception $e) {
         // Token is invalid
         // Send the unathorized access message
         $response = ['msg' => 'Unauthorized Access! ', 'status' => parent::HTTP_UNAUTHORIZED];
         return $response;
     }
 }

    //METODO POST
    public function index_post(){
        $data=[
            'titulo'=>$this->post('titulo'),
        ];

        $this->db->insert('genero',$data);
        $this->db->select_max('id_genero');
        $this->db->from('genero');
        $query=$this->db->get()->row_array();
        $query=$this->db->get_where("genero",['id_genero'=>$query['id_genero']])->result();
        $this->response($query,REST_Controller::HTTP_CREATED);
    }

    //METODO PUT

    public function index_put($id){
        $data=$this->put();
        $this->db->update('genero',$data,array('id_genero'=>$id));
        $this->response("Registro Actualizado",REST_Controller::HTTP_OK);
    }

    //METODO DELETE
    public function index_delete($id){
        $this->db->delete('genero',array('id_genero'=>$id));
        $this->response("Registro Eliminado",REST_Controller::HTTP_OK);
    }
}

?>