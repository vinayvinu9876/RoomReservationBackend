<?php

namespace App\Controllers;
use App\Models\RoomFeaturesModel;

class RoomFeatures extends BaseController{

    public function index(){
        echo "Hello from Room Features";
        return;
    }

    public function create(){
        $roomfeatureModel = new RoomFeaturesModel();

        $fields = ["feature_id","room_id","total_available","status"];

        $data = getRequestData($fields,$this->request);

        $validationRes = validateFields($data,"room_features_create");

        if($this->request->getMethod()==="post" && $validationRes["success"]){
            $roomfeatureModel->save($data);
            echo view("util/success.php",["message"=>"Room Features added succesfully"]);
        }
        else{
            $data["errors"] = $validationRes["errors"];
            echo view("util/error_messages.php",$data);
        }

        return;
    }

    public function update($id=null){
        $roomfeatureModel = new RoomFeaturesModel();

        $fields = ["status","total_available"];

        $data = getRequestData($fields,$this->request);

        $data = [
            "id" => $id
        ];

        $validationRes = validateFields($data,"room_features_update");

        if($this->request->getMethod()!=="post"){
            echo view("util/error_messages.php",["errors"=>["Request method must be post"]]);
            return;
        }

        if(!$validationRes["success"]){
            $res = [
                "errors" => $validationRes["errors"]
            ];
            echo view("util/error_messages.php",$res);
            return;
        }
        try{
            $data["updated_at"] = date('Y-m-d H:i:s');
            $roomfeatureModel->update($data["id"],$data);
            echo view("util/success.php",["message"=>"Room Feature updated succesfully"]);
        }
        catch(Exception $e){
            echo view("util/error_messages.php",["errors"=>[$e.message()]]);
        }
    }

    public function read($room_id=null,$status=null){
        $roomfeatureModel = new RoomFeaturesModel();

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header('Content-Type: application/json');
        
        if($room_id===null){
            $result = ["status"=>"failure","message"=>"Room id cannot be null"];
            echo json_encode($result);
            return;
        }

        $data = [];

        if($status!==null){
            $data["room_features"] = $roomfeatureModel->where(["room_id"=>$room_id,"status"=>$status])->findAll();
        }
        else{
            $data["room_features"] = $roomfeatureModel->where(["room_id"=>$room_id])->findAll();
        }

        echo json_encode(["status"=>"success","data"=>$data["room_features"]]);

        return;
    }

}

?>