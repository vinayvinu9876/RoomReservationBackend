<?php

namespace App\Controllers;
use App\Models\RoomReservationModel;

class RoomReservation extends BaseController{
    public function index(){
        echo "Hello from room reservation controller";
    }
    
    public function reserve(){
        $roomReservationModel = new RoomReservationModel();
        
        $fields = ["room_id","meeting_title","headed_by","reservation_description","reserved_by_email","start_timestamp","end_timestamp","priority_id","attendees_email","no_of_attendees"];

        $data = getRequestData($fields,$this->request);

        $validationRes = validateFields($data,"reserve_room");

        //echo "<pre>";print_r($data);die;

        if($validationRes["success"]==false){
            $resultPayload = [
                "status" => "failure",
                "message" => array_pop($validationRes["errors"])
            ];

            echo json_encode($resultPayload);
            return;
        }

        if($data["start_timestamp"] > $data["end_timestamp"]){
            echo json_encode([
                "status" => "failure",
                "message" => "Start is greater than end"
            ]);
            return;
        }

        try{
            $data["status"]             = "reserved";
            $data["created_at"]         = date('Y-m-d H:i:s');
            $data["start_timestamp"]    = date('Y-m-d H:i:s',$data["start_timestamp"]);
            $data["end_timestamp"]      = date('Y-m-d H:i:s',$data["end_timestamp"]);

            if($roomReservationModel->check_if_available($data["start_timestamp"],$data["end_timestamp"],$data["room_id"])){
                $resultPayload = [
                    "status" => "failure",
                    "message" => "Room is not available at the designated time"
                ];
                echo json_encode($resultPayload);
                return;                
            } 

            if($roomReservationModel->check_if_overlapping_downtime($data["start_timestamp"],$data["end_timestamp"],$data["room_id"])){
                $resultPayload = [
                    "status" => "failure",
                    "message" => "Sorry, the room has maintenance work during your schedule"
                ];
            }

            $roomReservationModel->insert($data);

            $resultPayload = [
                "status" => "success"
            ];

            echo json_encode($resultPayload);
        }
        catch(Exception $e){
            echo json_encode([
                "status" => "failure",
                "message" => $e.message()
            ]);
        }
    }

    public function get_meeting_list_for_admin($pageNo=1){

        $limit = 10;
        $offset = ($pageNo-1) * $limit;

        $fields = ["searchText","start","end","sort","room_id"];

        $data = getRequestData($fields,$this->request);

        foreach($fields as $field){
            if(!array_key_exists($field,$data)){
                $data[$field] = null;
            }
        }

        //echo "<pre>";print_r($data);die;

        $roomReservationModel = new RoomReservationModel();

        $result = $roomReservationModel->get_meeting_list_pagination_admin($limit,$offset,$data);

        echo json_encode(
            [
                "status" => "success",
                "data" => $result
            ]
        );
    }
    

    
}

?>