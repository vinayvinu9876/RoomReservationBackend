<?php

namespace App\Models;
use CodeIgniter\Model;


class RoomFeaturesModel extends Model{
    protected $table = "room_features";
    protected $primaryKey = 'id';

    protected $allowedFields = ['feature_id','room_id','total_available',"updated_at",'status'];
}

?>