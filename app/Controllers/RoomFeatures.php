<?php

namespace App\Controllers;
use App\Models\RoomFeaturesModel;

class RoomFeatures extends BaseController{

    public function index(){
        echo "Hello from Room Features";
        return;
    }

    public function delete($room_feature_id=null){

        if($room_feature_id===null){
            echo json_encode(["status"=>"failure","message"=>"Room feature id cannot be null"]);
            return;
        }

        $roomfeatureModel = new RoomFeaturesModel();

        try{

            $featureData = $roomfeatureModel->where("id",$room_feature_id)->findAll();
        

            if(count($featureData)===0){
                echo json_encode(["status"=>"failure","message"=>"Feature data doesn't exist"]);
                return;
            }

            $roomfeatureModel->where("id",$room_feature_id)->delete();

            echo json_encode(["status"=>"success"]);
        }
        catch(\Exception $e){
            log_message("error",$e->getMessage());
            echo json_encode(["status"=>"failure","message"=>$e->getMessage()]);
        }

    }


    public function create(){
        $roomfeatureModel = new RoomFeaturesModel();

        $fields = ["feature_id","room_id","total_available","status"];

        $data = getRequestData($fields,$this->request);

        $validationRes = validateFields($data,"room_features_create");

        if($this->request->getMethod()==="post" && $validationRes["success"]){
            $roomfeatureModel->save($data);
            echo json_encode(["status"=>"success"]);
            return;
        }
        else{
            $data["errors"] = $validationRes["errors"];
            echo json_encode(["status"=>"failure","message"=>array_pop($validationRes["errors"])]);
            return;
        }
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


    public function add_or_remove(){

        $fields = [ "room_feature_id","value"];
        $data = getRequestData($fields,$this->request);

        $result = ["status"=>null,"message"=>null];

        if(!$data["room_feature_id"]){
            $result["status"] = "failure";
            $result["message"] = "Room Feature id is not valid";
            echo json_encode($result);
            return;
        }

        if(!$data["value"] || $data["value"]==0){
            $result["status"] = "failure";
            $result["message"] = "Value is not valid";
            echo json_encode($result);
            return;
        }

        $roomfeatureModel = new RoomFeaturesModel();

        try{
            $roomfeatureModel->add_or_remove_feature($data["room_feature_id"],$data["value"]);
            $result["status"] = "success";
            echo json_encode($result);
        }
        catch(\Exception $e){
            $result["status"] = "failure";
            $result["message"] = $e->getMessage();

            echo json_encode($result);
            return;
        }
        
        

    }

}

?>