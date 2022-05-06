<?php

namespace App\Controllers;
use App\Models\RoomDownTimeModel;

class RoomdownTime extends BaseController{
    public function index(){
        echo "Hello from room down time";
        return;
    }

    public function create(){
        $roomDownTimeModel = new RoomDownTimeModel();

        $fields = ["room_id","desc","start","end","day","status"];

        $data = getRequestData($fields,$this->request);

        $validationRes = validateFields($data,"room_down_time_create");

        if($this->request->getMethod()!=="post"){
            echo view("util/error_messages.php",["errors"=>["The request method must be post"]]);
            return;
        }

        if(!$validationRes["success"]){
            $data["errors"] = $validationRes["errors"];
            echo view("util/error_messages.php",$data);
            return;
        }

        $matchingRecord = $roomDownTimeModel->getOverlappingRecords($data["day"],$data["room_id"],$data["start"],$data["end"]);

        if(count($matchingRecord)>0){
            echo view("util/error_messages.php",["errors"=>["The room down time overlaps with other records"]]);
            return;
        }

        $roomDownTimeModel->save($data);
        echo view("util/success.php",["message"=>"Room down time added succesfully"]);
        return;
    }

    public function update($id=null){
        $roomDownTimeModel = new RoomDownTimeModel();

        if($id===null){
            echo view("util/error_messages.php",["errors"=>["ID cannot be null"]]);
            return;
        }

        $params = ["desc","start","end", "day","status"];

        $data = getRequestData($params,$this->request);

        $data = [
            "id" => $id
        ]; 

        $validationRes = validateFields($data,"room_down_time_update");
        
        if($this->request->getMethod()!=="post"){
            echo view('util/error_messages.php',["errors"=>["Method request must be post"]]);
            return;
        }

        if(!$validationRes["success"]){
            echo view("util/error_messages.php",["errors"=>$validationRes["errors"]]);
            return;
        }

        
        try{
            $data["updated_at"] = date('Y-m-d H:i:s');
            $roomDownTimeModel->updateRoomDowntime($data);
            echo view("util/success.php",["message"=>"Room down time updated succesfully"]);
            return;
        }
        catch(\Exception $e){
            echo view("util/error_messages.php",["errors"=>[$e->getMessage()]]);
            return;
        }
    }

    public function read($room_id=null,$day=null,$status=null){
        $roomDownTimeModel = new RoomDownTimeModel();
        
        if($room_id===null){
            echo view("util/error_messages.php",["errors"=>["Room id cannot be empty"]]);
            return;
        }

        $validationRes = validateFields(["room_id"=>$room_id,"status"=>$status],"room_down_time_read");

        if(!$validateRes["success"]){
            echo view("util/error_messages.php",["errors"=>$validationRes["errors"]]);
            return;
        }
        
        if($status!==null && $day!==null){
            $downTimeData = $roomDownTimeModel->where(["room_id"=>$room_id,"status"=>$status,"day"=>$day])->findAll();
            echo view("util/success.php",["message"=>json_encode($downTimeData)]);
            return;
        }
        if($status!==null && $day===null){
            $downTimeData = $roomDownTimeModel->where(["room_id"=>$room_id,"status"=>$status])->findAll();
            echo view("util/success.php",["message"=>json_encode($downTimeData)]);
            return;
        }
        if($status===null && $day!==null){
            $downTimeData = $roomDownTimeModel->where(["room_id"=>$room_id,"day"=>$day])->findAll();
            echo view("util/success.php",["message"=>json_encode($downTimeData)]);
            return;
        }

        $downTimeData = $roomDownTimeModel->where(["room_id"=>$room_id])->findAll();
        echo view("util/success.php",["message"=>json_encode($downTimeData)]);
    }

    
}


?>