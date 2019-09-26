<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH. 'libraries/Rest_Controller.php';
class Libro extends REST_Controller{
    public function __construct(){
        parent::__construct("rest");
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    }

    public function index_options(){
        return $this->response(NULL,REST_Controller::HTTP_OK);
    }

    //METODO GET
    public function index_get($id=null){
        if(!empty($id)){
            $this->db->select('l.*, g.titulo as genero');
            $this->db->from('libro as l');
            $this->db->join('genero as g', 'l.id_genero = g.id_genero');
            $data=$this->db->get_where("libro",['l.isbn'=>$id])->row_array();
            if($data==null){
                $this->response(["El registro con ID $id no existe"],REST_Controller::HTTP_NOT_FOUND);
            }
        }else{
            $this->db->select('l.*, g.titulo as genero');
            $this->db->from('libro as l');
            $this->db->join('genero as g', 'l.id_genero = g.id_genero');
            $data=$this->db->get("libro")->result();

        }
        $this->response($data,REST_Controller::HTTP_OK);
    }

    //METODO POST
    public function index_post(){
        $data=[
            'titulo'=>$this->post('titulo'),
        ];

        $this->db->insert('libro',$data);
        $this->db->select_max('isbn');
        $this->db->from('libro');
        $query=$this->db->get()->row_array();
        $query=$this->db->get_where("libro",['isbn'=>$query['isbn']])->result();
        $this->response($query,REST_Controller::HTTP_CREATED);
    }

    //METODO PUT

    public function index_put($id){
        $data=$this->put();
        $this->db->update('libro',$data,array('isbn'=>$id));
        $this->response("Registro Actualizado",REST_Controller::HTTP_OK);
    }

    //METODO DELETE
    public function index_delete($id){
        $this->db->delete('libro',array('isbn'=>$id));
        $this->response("Registro Eliminado",REST_Controller::HTTP_OK);
    }
}

?>