<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model{
    protected $table = "rooms";
    protected $primaryKey = 'room_id';

    protected $allowedFields = ['room_name','room_desc','room_capacity',"updated_at",'status'];

    function get_user_rooms($pageNo,$limit,$offset,$searchText){

        $queryBuilder = $this->db->table($this->table);
        $countQueryBuilder = $this->db->table($this->table);

        $countQueryBuilder = $countQueryBuilder->select("count(*) as count");

        if($searchText){
            $queryBuilder = $queryBuilder->like("room_name",$searchText);
            $countQueryBuilder = $countQueryBuilder->like("room_name",$searchText);
        }


        $queryBuilder = $queryBuilder->limit($limit,$offset);
        $queryBuilder = $queryBuilder->where("status","active");
        $countQueryBuilder  = $countQueryBuilder->where("status","active");

        $results = $queryBuilder->get()->getResultArray();

        $countResult = $countQueryBuilder->get()->getRow();

        $final_result = [   
            "count" => $countResult->count,
            "pageNo" => $pageNo,
            "start" => $offset+1,
            "end" => $offset + count($results),
            "data" => $results, 
            "total_pages" => ceil( $countResult->count / $limit)
        ];

        return json_encode($final_result);
    }

    function get_admin_rooms($limit,$offset,$searchText){

        $queryBuilder = $this->db->table($this->table);
        $countQueryBuilder = $this->db->table($this->table);
        $countQueryBuilder = $countQueryBuilder->select("count(*) as count");

        if($searchText){
            $queryBuilder = $queryBuilder->like("room_name",$searchText);
            $countQueryBuilder = $countQueryBuilder->like("room_name",$searchText);
        }
        
        $queryBuilder = $queryBuilder->orderBy("created_at","desc");
        $queryBuilder = $queryBuilder->limit($limit,$offset);

        
        $results = $queryBuilder->get()->getResultArray();
        $count = $countQueryBuilder->get()->getRow()->count;

        return [
            "start"     => $offset+1,
            "end"       => $offset + count($results),
            "total"     => $count,
            "pageNo"    => 0 , // will be updated in controller
            "totalPages"=> ceil($count/$limit),
            "data"      => $results
        ];

    }


}

?>