<?php

namespace App\Models;
use CodeIgniter\Model;

class RoomDownTimeModel extends Model{
    protected $table = "room_down_time";
    protected $primaryKey = "id";

    protected $allowedFields = ["room_id","desc","start","end","day","updated_at","status"];


    public function updateRoomDowntime($data){
        if($this->checkifUpdateTimeOverlaps($data["id"],$data)){
            throw new \Exception("time overlaps other records");
        }
        $data["updated_at"] = date('Y-m-d H:i:s');
        $this->update($data["id"],$data); 
        return true;
    }


    public function getOverlappingRecords($day,$roomId,$start,$end){
        $query = "  SELECT * FROM room_down_time
                    WHERE 
                    (day='".$day."' and  room_id=".$roomId.")
                    AND
                    (
                        (start >= '".$start."' and start <'".$end."')
                        or
                        (start <='".$start."'  and end > '".$start."')
                    );
                ";

      log_message("info"," query = ".$query);
      
      $result = $this->db->query($query)->getResultArray("room_down_time");

     return $result;
    }

    private function getOverlappingRecordsForUpdate($id,$day,$roomId,$start,$end){
        $query = "SELECT * FROM room_down_time
                    WHERE 
                    (day='".$day."' and  room_id=".$roomId." and id<>$id)
                    AND
                    (
                        (start >= '".$start."' and start <='".$end."')
                        or
                        (start <='".$start."'  and end >= '".$start."')
                    );
                ";

      log_message("info"," query = ".$query);
      
      $result = $this->db->query($query)->getResultArray("room_down_time");

       return $result;
    }

    private function checkifUpdateTimeOverlaps($id,$data){
        $downTimeData = $this->find($id);

        if(count($downTimeData)===0){
            throw new \Exception("Record not found");
        }

        $possibleCombinations = [
            [
                "exists"        =>[],
                "not_exists"    =>["start","end","day"]
            ],
            [ 
                "exists"        =>["start","end","day"],
                "not_exists"    =>[]
            ],

            [
                "exists"        => ["start","end"],
                "not_exists"    =>["day"]
            ],
            [
                "exists"        => ["start"],
                "not_exists"    => ["end","day"]
            ],

            [
               "exists"         => ["end","day"],
               "not_exists"     => ["start"], 
            ],
            [
                "exists"        =>  ["end"],
                "not_exists"    =>  ["start","day"],
            ],
            
            [
                "exists"        =>  ["day"], 
                "not_exists"    =>  ["end","start"]
            ],

        ];

        
        $matchingRecords = null;

        foreach($possibleCombinations as $combination){
            if($this->checkIfMatches($combination["exists"],$combination["not_exists"],$data)){
                $existsArray = $combination["exists"];
                $day = array_key_exists("day",$existsArray) ? $data["day"] : $downTimeData["day"];
                $start = array_key_exists("start",$existsArray) ? $data["start"] : $downTimeData["start"];
                $end = array_key_exists("end",$existsArray) ? $data["end"] : $downTimeData["end"]; 
                $roomId = $downTimeData["room_id"];

                $matchingRecords = $this->getOverlappingRecordsForUpdate($data["id"],$day,$roomId,$start,$end);
            }
        }

        if($matchingRecords!==null){
            if(count($matchingRecords)>0){
                return true;
            }
        }

        return false;
    }
    
    private function checkIfMatches($exists,$notExists,$data){

        foreach($exists as $field){
            if(!array_key_exists($field,$data)){
                return false;
            }
        }

        foreach($notExists as $field){
            if(array_key_exists($field,$data)){
                return false;
            }
        }

        return true;

    }
}

?>