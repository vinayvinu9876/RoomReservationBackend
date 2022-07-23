<?php

namespace App\Models;
use CodeIgniter\Model;


class RoomFeaturesModel extends Model{
    protected $table = "room_features";
    protected $primaryKey = 'id';

    protected $allowedFields = ['feature_id','room_id','total_available',"updated_at",'status'];

    public function add_or_remove_feature($room_feature_id,$value){

        if(!$room_feature_id){
            throw new \Exception("Feature id is not available");
        }

        if(!$value){
            throw new \Exception("Value to add or remove is not found");
        }

        $sql = "UPDATE $this->table set total_available=(total_available + $value) where feature_id=$room_feature_id";

        $this->db->query($sql); 
    }
    
}

?>