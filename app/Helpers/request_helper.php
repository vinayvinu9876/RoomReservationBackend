<?php 
namespace App\Controllers;

if(!function_exists("getRequestData")){
    function getRequestData($fields,$request){
        $data = [];
        foreach ($fields as $param) {
            if($request->getVar($param)){
                $data[$param] = $request->getVar($param);
            }
        }
        return $data;
    }

    function validateFields($data,$validatorRuleName){
        $validation =  \Config\Services::validation();
        $validation->reset();
        $validation->setRuleGroup($validatorRuleName);
        $res = [
            "success" => false,
            "errors" => []
        ];
        if($validation->run($data)){
            $res["success"] = true ;
            return $res;
        }
        $res["success"] = false;
        $res["errors"] = $validation->getErrors();
        return $res;
    }
    
}



?>