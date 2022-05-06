<?php

namespace App\Models;
use CodeIgniter\Model;


class RoomReservationModel extends Model{
    protected $table = "room_reservation";
    protected $primaryKey = "reservation_id";
    protected $allowedFields = [
                                "room_id",
                                "start_timestamp",
                                "end_timestamp",
                                "reservation_description",
                                "reservation_requirements",
                                "reserved_by_email",
                                "priority_id",
                                "attendees_email",
                                "no_of_attendees",
                                "updated_at",   
                                "status"
                               ];

    public function addReservation($data){
        
    }
}

?>