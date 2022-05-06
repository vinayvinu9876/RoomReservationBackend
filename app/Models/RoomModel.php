<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model{
    protected $table = "rooms";
    protected $primaryKey = 'room_id';

    protected $allowedFields = ['room_name','room_desc','room_capacity',"updated_at",'status'];
}

?>