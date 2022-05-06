<?php
namespace App\Controllers;
use App\Models\RoomModel;
use \Hermawan\DataTables\DataTable;

class Room extends BaseController{

    public function index(){
        helper('url');
        $fields = ["Room Name","Room Desc","Capacity","Status","Updated on"];
        echo view('dashboard/components/header/index.php',["title"=>"Rooms"]);
        echo view("dashboard/pages/rooms.php",["title"=>"Rooms","fields"=>$fields,"roomData" => []]);
    }

    public function ajaxDataTables(){
        $db = db_connect();
        $builder = $db->table('rooms')->select('room_name,room_capacity,status,updated_at');
          
        return DataTable::of($builder)
               ->addNumbering() //it will return data output with numbering on first column
               ->add('action', function($row){
                    return '<button type="button" class="btn btn-gradient-danger btn-rounded btn-icon">
                                <i class="mdi mdi-eye"></i>
                            </button>';
                }, 'last')
               ->toJson();
    }


    public function view(){
        $roomModel = new RoomModel();

        $roomdata = $roomModel->where(["room_id >"=>0])->findAll();

        $fields = ["Room Name","Room Desc","Capacity","Status","Updated on"];

        echo $roomdata;
    }

    public function read(){    
        $roomModel = new RoomModel();

        $status=$this->request->getPost('status');
        $search=$this->request->getPost('search');

        log_message("info","search = ".$search." status  =  ".$status);

        if($status===null && $search===null){
            $data["rooms"] = $roomModel->findAll();
        }
        else if($status===null && $search!==null){
            $data["rooms"] = $roomModel->like(["room_name"=>"%".$search."%"])->findAll();
        }
        else if($status!==null && $search===null){
            log_message("info","from status != null and search == null");
            $data["rooms"] = $roomModel->where(["status"=>$status])->findAll();
        }
        else{
            $data["rooms"] = $roomModel->like("room_name","%".$search."%")->where(["status"=>$status])->findAll();
        }
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header('Content-Type: application/json');
        echo json_encode(["status"=>"success","data"=>$data]);
    }

    public function create(){

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header('Content-Type: application/json');
    
        if($this->request->getMethod()!=='post'){
            echo json_encode(["status"=>"failure","message"=>"Request method must be post"]);
            return;
        }

        $roomModel = new RoomModel();
        
        $fields = ["room_name","room_desc",'room_capacity','status'];
        
        $data = getRequestData($fields,$this->request);
        
        $validationRes = validateFields($data,"room_create");

        $responseData = [];
    
        if($validationRes["success"]){
            $roomModel->save($data);
            $responseData = ["status"=>"success"];
        }
        else{
            $data["errors"] = $validationRes["errors"];
            $responseData = ["status"=>"failure","message"=>array_pop($data["errors"])];
        }
        
        echo json_encode($responseData);
    }

    public function update($id=null){
        $roomModel = new RoomModel();

        $fields = ["room_name","room_desc","room_capacity","status"];

        $data = getRequestData($fields,$this->request);

        $data = [
            'room_id' => $id
        ];

        $validationRes = validateFields($data,"room_update");

        if($validationRes["success"]===false){
            echo view("util/error_messages.php",['errors' => $validationRes["errors"]]);
            return;
        }

        try{
            $data["updated_at"] = date('Y-m-d H:i:s');
            $roomModel->update($data["room_id"],$data);
            echo view("util/success.php",["message"=>"Room updated succesfully"]);
        }
        catch(Exception $e){
            echo view("util/error_messages.php",["errors"=>[$e.message()]]);
        }
    }
}

?>