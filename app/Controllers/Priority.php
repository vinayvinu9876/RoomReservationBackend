<?php

namespace App\Controllers;
use App\Models\PriorityModel;
use \Hermawan\DataTables\DataTable;

class Priority extends BaseController{

    public function index(){
        echo "Hello from priority";
        return;
    }


    public function ajaxDataTables(){
        $db = db_connect();
        $builder = $db->table('priority')->select('priority_no,name,desc,updated_on,status');
          
        return DataTable::of($builder)
               ->addNumbering() //it will return data output with numbering on first column
               ->add('action', function($row){
                    return '<button type="button" class="btn btn-gradient-danger btn-rounded btn-icon">
                                <i class="mdi mdi-eye"></i>
                            </button>';
                }, 'last')
               ->toJson();
    }

    public function create(){
        $priorityModel = new PriorityModel();

        $fields = ["name","desc","priority_no","status"];

        $data = getRequestData($fields,$this->request);

        log_message("info",implode($data));

        $validationRes = validateFields($data,"priority_create");

        if($this->request->getMethod()==="post" && $validationRes["success"]){
            $priorityModel->save($data);
            echo view("util/success.php",["message"=>"Priority added succesfully"]);
            return;
        }
        else{
            $data["errors"] = $validationRes["errors"];
            echo view("util/error_messages.php",$data);
            return;
        }
    }

    public function update($id=null){
        $priorityModel = new PriorityModel();

        if($id===null){
            echo view("util/error_messages.php",["errors"=>["Id is required"]]);
            return;
        }

        if($this->request->getMethod()!=="post"){
            echo view("util/error_messages.php",["errors"=>["The request method must be post"]]);
            return;
        }

        $fields = ["name","desc","priority_no","status"];

        $data = getRequestData($fields,$this->request);

        $data["id"] = $id;

        $validationRes = validateFields($data,"priority_update");

        if(!$validationRes["success"]){
            echo view("util/error_messages.php",["errors"=>$validationRes["errors"]]);
            return;
        }

        $data["updated_on"] = date('Y-m-d H:i:s');
        $priorityModel->update($id,$data);
        echo view("util/success.php",["message"=>"Priority updated succesfully"]);
        return;
    }

    public function read($id=null){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header('Content-Type: application/json');

        $priorityModel = new PriorityModel();

        if($id!==null){
            $result = $priorityModel->where(["id"=>$id])->findAll();
            $resultData = ["status"=>"success","data"=>$result];
            echo json_encode($resultData);
            return;
        }

        $result = $priorityModel->where(["id > "=>0])->findAll(); // cause id starts from 1 ha ha
        $resultData = ["status"=>"success","data"=>$result];
        echo json_encode($resultData);
        return;
    }

}

?>