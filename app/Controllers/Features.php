<?php

namespace App\Controllers;
use App\Models\FeaturesModel;
use \Hermawan\DataTables\DataTable;

class Features extends BaseController{

    public function index(){
        echo "Hello from features";
        return;
    }

    public function ajaxDataTables(){
        $db = db_connect();
        $builder = $db->table('features')->select('feature_name,total_available,updated_at,status');
          
        return DataTable::of($builder)
               ->addNumbering() //it will return data output with numbering on first column
               ->add('action', function($row){
                    return '<button type="button" class="btn btn-gradient-danger btn-rounded btn-icon">
                                <i class="mdi mdi-eye"></i>
                            </button>';
                }, 'last')
               ->toJson();
    }

    public function create(){

        if(!($this->request->getMethod()==='post')){
            $responseData = ["status"=>"failure","message"=>"Request method must be post"];
            echo json_encode($responseData);
            return;
        }

        $featureModel = new FeaturesModel();

        $fields = ["feature_name","feature_desc","total_available","status"];

        $data = getRequestData($fields,$this->request);

        $validationRes = validateFields($data,"features_create");

        if( $validationRes["success"]){
            $featureModel->save($data);
            $responseData = ["status"=>"success"];
            echo json_encode($responseData);
            return;
        }   
        else{   
            $responseData = ["status"=>"failure","message"=>array_pop($validationRes["errors"])];
            echo json_encode($responseData);
            return;
        }
    }

    public function update($id=null){

        $featureModel = new FeaturesModel();

        $fields = ["feature_name","feature_desc","total_available","status"];

        $data = getRequestData($fields,$this->request);

        $data["id"] =  $id;
        $data["updated_at"] = date('Y-m-d H:i:s');
        

        $validationRes = validateFields($data,"features_update");

        if(!$validationRes["success"]){
            echo json_encode([
                "status" => "failure",
                "message" => $validationRes["errors"][0],
            ]);
            return;
        }

        try{
            $result = $featureModel->update_feature($data);
            echo json_encode(
                [
                    "status" => "success",
                    "updateStatus" => $result,
                    "data" => $data
                ]
            );
        }
        catch(Exception $e){
            echo json_encode(
                [
                    "status" => "failure",
                    "message" => $e->message()
                ]
            );  
        }
    }

    public function read($pageNo=1){

        $limit  = 15; 
        $offset = ($pageNo-1)*$limit;
        
        $featuresModel = new FeaturesModel();

        $data["search"] = $this->request->getVar('search');
        $data["status"] = $this->request->getVar('status');

        $result = $featuresModel->read($pageNo,$limit,$offset,$data);

        echo json_encode(["status"=>"success","data"=>$result]);
    }
}
?>