<?php

namespace App\Models;
use CodeIgniter\Model;

class PriorityModel extends Model{
    protected $table = "priority";
    protected $primaryKey = "id";
    protected $allowedFields = ["name","desc","updated_on","priority_no","status"];
}

?>