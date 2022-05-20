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

        $data = [
            "id" => $id,
            "updated_at"=> date('Y-m-d H:i:s')
        ];

        $validationRes = validateFields($data,"features_update");

        if(!$validationRes["success"]){
            echo view("util/error_messages.php",['errors' => $validationRes["errors"]]);
            return;
        }

        try{
            $featureModel->update($data["id"],$data);
            echo view("util/success.php",["message"=>"Feature updated succesfully"]);
        }
        catch(Exception $e){
            echo view("util/error_messages.php",["errors"=>[$e.message()]]);
        }
    }

    public function read($status=null){

        $featuresModel = new FeaturesModel();

        $search=$this->request->getPost('search');

        log_message("info","search = ".$search." status  =  ".$status);

        if($status===null && $search===null){
            $data["features"] = $featuresModel->findAll();
        }
        else if($status===null && $search!==null){
            $data["features"] = $featuresModel->like(["feature_name"=>"%".$search."%"])->findAll();
        }
        else if($status!==null && $search===null){
            log_message("info","from status != null and srach == null");
            $data["features"] = $featuresModel->where(["status"=>$status])->findAll();
        }
        else{
            $data["features"] = $featuresModel->like("feature_name","%".$search."%")->where(["status"=>$status])->findAll();
        }

        echo json_encode(["status"=>"success","data"=>$data["features"]]);
    }
}
?>