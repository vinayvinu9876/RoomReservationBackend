<?php

namespace App\Models;
use CodeIgniter\Model;

class PriorityModel extends Model{
    protected $table = "priority";
    protected $primaryKey = "id";
    protected $allowedFields = ["role_ids","name","desc","updated_on","priority_no","status"];

    public function update_priority($data){
        $queryBuilder = $this->db->table($this->table);

        $queryBuilder = $queryBuilder->where("id",$data["id"]);
        $result       = $queryBuilder->update($data);
        
        return $result;
    }
}

?>