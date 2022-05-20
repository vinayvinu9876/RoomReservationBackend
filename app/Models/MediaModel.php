<?php

namespace App\Models;
use CodeIgniter\Model;

class MediaModel extends Model{
    protected $table = "media";
    protected $primaryKey = "id";
    protected $allowedFields = ["filename","url","updated_on"];
}

?>