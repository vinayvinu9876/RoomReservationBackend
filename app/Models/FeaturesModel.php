<?php

namespace App\Models;
use CodeIgniter\Model;

class FeaturesModel extends Model{
    protected $table = "features";
    protected $primaryKey = "id";
    protected $allowedFields = ["feature_name","feature_desc","total_available","updated_at","status"];

    public function update_feature($data){

        $queryBuilder = $this->db->table($this->table);

        $queryBuilder = $queryBuilder->where('id',$data["id"]);
        $result       = $queryBuilder->update($data);
        
        return $result;
    }

    public function read($pageNo,$limit,$offset,$data){

        $queryBuilder = $this->db->table($this->table);
        $countQueryBuilder = $this->db->table($this->table);

        $countQueryBuilder = $countQueryBuilder->select("count(*) as count");

        if($data["status"]){
            $countQueryBuilder = $countQueryBuilder->where(["status"=>$data["status"]]);
            $queryBuilder = $queryBuilder->where(["status"=>$data["status"]]);
        }

        if($data["search"]){
            $countQueryBuilder = $countQueryBuilder->like(["feature_name"=>$data["search"]]);
            $queryBuilder = $queryBuilder->like(["feature_name"=>$data["search"]]);
        }
        
        
        $queryBuilder = $queryBuilder->orderBy("updated_at","desc");
        $queryBuilder = $queryBuilder->limit($limit,$offset);

        $result = $queryBuilder->get()->getResultArray();
        $totalCount  = $countQueryBuilder->get()->getRow()->count;

        return [
            "start" => $offset + 1,
            "end" => $offset + count($result),
            "currentPageNo" => $pageNo,
            "totalResults" => $totalCount,
            "totalPages" => ceil($totalCount/$limit),
            "data" => $result,
        ];  
        

    }
}

?>