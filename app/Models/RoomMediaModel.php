<?php

namespace App\Models;
use CodeIgniter\Model;

class RoomMediaModel extends Model{
    protected $table = "room_media";
    protected $primaryKey = "id";
    protected $allowedFields = ["room_id","media_id","status"];
}

?>