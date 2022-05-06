<?php

namespace App\Models;
use CodeIgniter\Model;

class FeaturesModel extends Model{
    protected $table = "features";
    protected $primaryKey = "id";
    protected $allowedFields = ["feature_name","feature_desc","total_available","updated_at","status"];
}

?>