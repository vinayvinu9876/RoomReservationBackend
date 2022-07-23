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
                                "meeting_title",
                                "headed_by",
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

    public function geLast100Reservations($room_id){
        if(!$room_id){
            return [];
        }
        $reservations = $this->where(["room_id"=>$room_id])
                             ->where(["status"=>"reserved"])
                             ->orderBy("start_timestamp","desc")
                             ->limit(100)
                             ->findAll();

        return $reservations;
    }

    public function get_meeting_list_pagination_admin($limit,$offset,$data){

        $countQueryBuilder = $this->db->table("$this->table rres");
        $resultQueryBuilder = $this->db->table("$this->table rres");

        $countQueryBuilder = $countQueryBuilder->select("count(*) as count");
        $resultQueryBuilder = $resultQueryBuilder->select("rres.*,r.room_name");

        $resultQueryBuilder = $resultQueryBuilder->join("rooms r","rres.room_id=r.room_id");
        $countQueryBuilder = $countQueryBuilder->join("rooms r","rres.room_id=r.room_id");

        /*
        $query = "SELECT 
                  rres.*,r.room_name 
                  FROM 
                  room_reservation rres , rooms r 
                  WHERE 
                  rres.room_id=r.room_id 
                  ORDER BY start_timestamp desc
                  LIMIT $limit OFFSET $offset; ";

        $countQuery = "SELECT COUNT(*) as count from room_reservation";
        */

        if($data["start"]){
            $data["start"] = date('Y-m-d 00:00:00',$data["start"]);

            $resultQueryBuilder = $resultQueryBuilder->where("rres.start_timestamp >=",$data["start"]);
            $countQueryBuilder  = $countQueryBuilder->where("rres.start_timestamp >=",$data["start"]);
        }

        if($data["end"]){
            
            $data["end"] = date('Y-m-d 23:59:59',$data["end"]);

            $resultQueryBuilder = $resultQueryBuilder->where("rres.end_timestamp <=",$data["end"]);
            $countQueryBuilder  = $countQueryBuilder->where("rres.end_timestamp <=",$data["end"]);
        }

        if($data["room_id"]){
            $resultQueryBuilder = $resultQueryBuilder->where("rres.room_id",$data["room_id"]);
            $countQueryBuilder  = $countQueryBuilder->where("rres.room_id",$data["room_id"]);
        }

        if($data["searchText"]){
            $resultQueryBuilder = $resultQueryBuilder->like("r.room_name",$data["searchText"]);
            $countQueryBuilder = $countQueryBuilder->like("r.room_name",$data["searchText"]);

            $resultQueryBuilder = $resultQueryBuilder->orLike("rres.meeting_title",$data["searchText"]);
            $countQueryBuilder = $countQueryBuilder->orLike("rres.meeting_title",$data["searchText"]);

            $resultQueryBuilder = $resultQueryBuilder->orLike("rres.headed_by",$data["searchText"]);
            $countQueryBuilder = $countQueryBuilder->orLike("rres.headed_by",$data["searchText"]);
        }
        
        


        switch ($data["sort"]) {
            case 1:
                $resultQueryBuilder->orderBy("rres.start_timestamp","asc");
                break;
            case 2:
                $resultQueryBuilder->orderBy("rres.start_timestamp","desc");
                break;
            default:
                $data["sort"] = 2;
                $resultQueryBuilder->orderBy("rres.start_timestamp","desc");
                break;
        }
        
        $resultQueryBuilder = $resultQueryBuilder->limit($limit,$offset);

        $countResult = $countQueryBuilder->get()->getRow();
        $result = $resultQueryBuilder->get()->getResult("array");

        return [
            "count" => intval($countResult->count),
            "startIndex" => $offset+1,
            "endIndex" => $offset+(count($result)),
            "totalPages" => ceil(intval($countResult->count)/$limit),
            "pageNo" => (($offset/$limit)+1),
            "data" => $result,

            //search data
            "searchText" => $data["searchText"],
            "start" => $data["start"],
            "end" => $data["end"],
            "room_id" => $data["room_id"],
            "sort" => $data["sort"]
        ];

    }

    public function cancel_reservation($reservation_id){

        $getReservationQueryBuilder = $this->db->table("$this->table");
        $getReservationQueryBuilder = $getReservationQueryBuilder->where("reservation_id",$reservation_id);

        $reservationData = $getReservationQueryBuilder->get()->getRow();

        $today = date("Y-m-d H:i:s");
        $meetingDate = $reservationData->start_timestamp; //from database

        if(strtotime($today) > strtotime($meetingDate)){
            throw new \Exception("Meeting data has been completed already");
        }

        if($reservationData->status==="cancelled"){
            throw new \Exception("Meeting has been cancelled already");
        }


        $updateQuery = "UPDATE $this->table set status='cancelled' WHERE reservation_id = $reservation_id ";

        $result = $this->db->query($updateQuery);

        return $result;

    }


}

?>