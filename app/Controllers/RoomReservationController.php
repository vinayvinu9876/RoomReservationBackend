<?php

namespace App\Controllers;
use App\Models\RoomReservationModel;

class RoomReservationController extends BaseController{
    public function index(){
        echo "Hello from room reservation controller";
    }
    
    public function create(){

        if($this->request->getMethod()!=="post"){
            echo view('util/error_messages.php',["errors"=>["Request method is not post"]]);
            return;
        }

        $roomReservationModel = new RoomReservationModel();

        $fields = ["room_id","start_timestamp","end_timestamp","reservation_description","reservation_requirements","reserved_by","status"];

        $data = getRequestData($fields,$this->request);

        $validationRes = validateFields($data,"room_reservation_create");

        if(!$validationRes["success"]){
            echo view("util/error_messages.php",["errors"=>$validationRes["errors"]]);
            return;
        }

        $roomReservationModel->save($data);
        echo view("util/success.php",["message"=>"Reservation succesfull"]);
        return;
    }

    public function update($reservation_id){
        
    }
}

?>