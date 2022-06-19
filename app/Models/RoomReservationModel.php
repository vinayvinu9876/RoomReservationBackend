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


    public function check_if_overlapping_downtime($start_timestamp,$end_timestamp,$room_id){

        $start_time = date("H:i:s",strtotime($start_timestamp));
        $end_time   = date("H:i:s",strtotime($end_timestamp));

        $dayofweek = strtolower(date('D', strtotime($start_timestamp)));


        $query = "
            SELECT
            *
            FROM 
            room_down_time
            where 
            (
                (
                    start <= '$start_time'
                    and 
                    end >= '$start_time' 
                )
                or
                (
                    start <= '$end_time'
                    and
                    end >= '$end_time'
                )
            )
            and
            (day = '$dayofweek')
            and 
            room_id = $room_id
            limit 1;
        ";


        $result = $this->db->query($query);

        if(count($result->getResult('array'))>0){
            return true;
        }
        
        return false;


    }

    public function check_if_available($start_timestamp,$end_timestamp,$room_id){
        

        $query = "SELECT 
                     * 
                    from 
                    $this->table 
                    where 
                    (room_id = $room_id) 
                    and
                    (
                        (
                            /* Check if start time is in-between any schedule */
                            start_timestamp <= '$start_timestamp' 
                            and
                            end_timestamp >= '$start_timestamp'
                        )
                        or
                        (
                            /* Check if end time is in-between any schedule */
                            start_timestamp <= '$end_timestamp'
                            and 
                            end_timestamp >= '$end_timestamp'
                        )
                    ) 
                    limit 1
                    ;";


        $result = $this->db->query($query);

        if(count($result->getResult('array'))>0){
            return true;
        }
        
        return false;
        
    }


    public function getReservationsForRoom($room_id,$date){

        $tommorow = date('Y-m-d', strtotime('+1 day', strtotime($date)));

        $reservations = $this->where(["room_id"=>$room_id])
                            ->where(["start_timestamp >"=>$date])
                            ->where(["start_timestamp < "=>$tommorow])
                            ->where(["status"=>"reserved"])
                            ->findAll();

        
        return $reservations;

    }


}

?>