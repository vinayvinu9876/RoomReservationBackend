<?php
namespace App\Controllers;
use App\Models\RoomModel;
use App\Models\RoomFeaturesModel;
use App\Models\RoomDownTimeModel;
use App\Models\RoomReservationModel;
use App\Models\MediaModel;
use App\Models\RoomMediaModel;
use \Hermawan\DataTables\DataTable;

class Room extends BaseController{

    public function index(){
        helper('url');
        $fields = ["Room Name","Room Desc","Capacity","Status","Updated on"];
        echo view('dashboard/components/header/index.php',["title"=>"Rooms"]);
        echo view("dashboard/pages/rooms.php",["title"=>"Rooms","fields"=>$fields,"roomData" => []]);
    }

    public function ajaxDataTables(){
        $db = db_connect();
        $builder = $db->table('rooms')->select('room_name,room_capacity,status,updated_at');
          
        return DataTable::of($builder)
               ->addNumbering() //it will return data output with numbering on first column
               ->add('action', function($row){
                    return '<button type="button" class="btn btn-gradient-danger btn-rounded btn-icon">
                                <i class="mdi mdi-eye"></i>
                            </button>';
                }, 'last')
               ->toJson();
    }

    public function view(){
        $roomModel = new RoomModel();

        $roomdata = $roomModel->where(["room_id >"=>0])->findAll();

        $fields = ["Room Name","Room Desc","Capacity","Status","Updated on"];

        echo $roomdata;
    }

    public function read($id=null){    
        $roomModel = new RoomModel();

        if($id!=null){
            $roomQueryResult = $roomModel->where("room_id",$id)->findAll();

            if(count($roomQueryResult)===0){
                echo json_encode(["status"=>"failure","message"=>"Room data with id doesn't exist"]);
                return;                
            }

            $roomData = $roomQueryResult[0];
            
            $roomDetails = $this->getRoomData($id);

            $data["room_data"] = $roomData;
            $data["room_features"] = $roomDetails["room_features"]; 
            $data["room_media"] = $roomDetails["room_media"];
            $data["room_down_time"] = $roomDetails["room_down_time"];
 
            $resultPayload = [
                "status" => "success",
                "data"=> $data
            ];

            echo json_encode($resultPayload);
            return;
        }

        $status=$this->request->getPost('status');
        $search=$this->request->getPost('search');

        log_message("info","search = ".$search." status  =  ".$status);

        if($status===null && $search===null){
            $data["rooms"] = $roomModel->findAll();
        }
        else if($status===null && $search!==null){
            $data["rooms"] = $roomModel->like(["room_name"=>"%".$search."%"])->findAll();
        }
        else if($status!==null && $search===null){
            log_message("info","from status != null and search == null");
            $data["rooms"] = $roomModel->where(["status"=>$status])->findAll();
        }
        else{
            $data["rooms"] = $roomModel->like("room_name","%".$search."%")->where(["status"=>$status])->findAll();
        }

        $allRoomsData = [];

        foreach($data["rooms"] as $room){
            $roomDetails = $this->getRoomData($room["room_id"]);
            $roomDetails["room_data"] = $room;
            array_push($allRoomsData,$roomDetails);
        }

        echo json_encode(["status"=>"success","data"=>$allRoomsData]);
    }

    public function get_admin_rooms($pageNo=1){

        $limit = 20;
        $offset = ($limit*($pageNo-1));

        $fields = ["searchText"];

        $data = getRequestData($fields,$this->request);

        if(!array_key_exists("searchText",$data)){
            $data["searchText"] = "";
        }

        $roomModel = new RoomModel();

        $results = $roomModel->get_admin_rooms($limit,$offset,$data["searchText"]);

        $results["pageNo"] = $pageNo;

        echo json_encode(
            [
                "status" => "success",
                "data" => $results 
            ]
        );

    }

    public function get_user_room_data($pageNo = 1){
        $roomModel = new RoomModel();

        $fields = ["searchText"];

        $data = getRequestData($fields,$this->request);

        if(!array_key_exists("searchText",$data)){
            $data["searchText"] = "";
        }

        $limit = 20;
        $offset = (($pageNo-1)*$limit);

        $results = $roomModel->get_user_rooms($pageNo,$limit,$offset,$data["searchText"]);

        $result = [
            "status" => "success",
            "data" => $results
        ];

        echo json_encode($result);
    }

    private function getRoomData($roomId){
        
        $roomMediaModel = new RoomMediaModel();
        $roomFeatureModel = new RoomFeaturesModel();
        $roomDownTimeModel = new RoomDownTimeModel();
        $mediaModel = new MediaModel();
        $roomModel = new RoomModel();

        $roomFeatureQuery = "SELECT rf.id,ft.feature_name,ft.feature_desc,rf.total_available as no_of_items,ft.total_available FROM room_features rf,features ft where (rf.room_id = $roomId ) and (ft.id = rf.feature_id )";
        $roomFeatures = $roomFeatureModel->query($roomFeatureQuery)->getResultArray("room_features");

        $mediaQuery = "SELECT * FROM room_media rm,media m where ( rm.room_id = $roomId ) and ( rm.media_id = m.id )";
        $mediaData = $roomModel->db->query($mediaQuery)->getResultArray("room_media");

        $roomMediaData = $this->getMediaAsUri($mediaData);

        $roomDownTimeData = $roomDownTimeModel->where("room_id",$roomId)->findAll();

        return [
            "room_features" => $roomFeatures,
            "room_media" => $roomMediaData,
            "room_down_time" => $roomDownTimeData
        ];
    }

    private function getMediaAsUri($mediaData){

        $images = [];

        foreach($mediaData as $media){

            log_message("info"," File name = ".$media["filename"]);

            $filePath = WRITEPATH . 'uploads/' . $media["filename"];
            log_message("info"," File path = ".$filePath);
            $type = pathinfo($filePath, PATHINFO_EXTENSION);
            $data = file_get_contents($filePath);
            $dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);

            array_push($images,[
                                "room_media_id"=>$media["id"],
                                "media_id" => $media["media_id"],
                                "image"=>$dataUri
                            ]);
        }

        return $images;

    }

    public function addImage($room_id=null){

        if($room_id===null){
            echo json_encode(["status"=>"failure","message"=>"Room id is not valid"]);
            return;
        }

        if($this->request->getMethod()!=='post'){
            echo json_encode(["status"=>"failure","message"=>"Request method must be post"]);
            return;
        }

        $fields = [
            "image"
        ];

        $data = getRequestData($fields,$this->request);

        $validationRes = validateFields($data,"room_image_add");

        if($validationRes["success"]){
            $this->addImages([["image"=>$data["image"]]],$room_id);
            echo json_encode(["status"=>"success"]);
            return;
        }
        else{
            echo json_encode(["status"=>"failure","message"=>array_pop($data["errors"])]);
            return;
        }

    }

    public function create(){
    
        if($this->request->getMethod()!=='post'){
            echo json_encode(["status"=>"failure","message"=>"Request method must be post"]);
            return;
        }

        $roomModel = new RoomModel();
        
        $fields = [
                "room_name",
                "room_desc",
                "room_capacity",
                "status",

                "room_images",
                "room_features",
                "room_down_time"
        ];
        
        $data = getRequestData($fields,$this->request);

        $images         = json_decode($data["room_images"],true);
        $features       = json_decode($data["room_features"],true);
        $roomDownTime   = json_decode($data["room_down_time"],true);
        
        $validationRes = validateFields($data,"room_create");

        $responseData = [];
    
        if($validationRes["success"]){

            $roomData = [
                "room_name"     => $data["room_name"],
                "room_desc"     => $data["room_desc"],
                "room_capacity" => $data["room_capacity"],
                "status"        => $data["status"]
            ];

            $roomModel->save($roomData);
            $room_id = $roomModel->insertID();

            $this->addImages($images,$room_id);
            $this->addRoomFeatures($features,$room_id);
            $this->addRoomDownTime($roomDownTime,$room_id);

            $responseData = ["status"=>"success"];
        }
        else{
            $data["errors"] = $validationRes["errors"];
            $responseData = ["status"=>"failure","message"=>array_pop($data["errors"])];
        }
        
        echo json_encode($responseData);
    }

    private function addRoomDownTime($downTimeData,$room_id){

        $roomDownTimeModel = new RoomDownTimeModel();

        foreach($downTimeData as $downTime){
            $payload = [
                "room_id"   => $room_id,
                "desc"      => $downTime["desc"],
                "start"     => $downTime["start"],
                "end"       => $downTime["end"],
                "day"       => $downTime["day"],
                "status"    => "active"
            ];

            $roomDownTimeModel->save($payload);
        }
    }

    private function addRoomFeatures($featuresArray,$room_id){
        $roomFeatureModel = new RoomFeaturesModel();

        foreach($featuresArray as $roomFeature){
            $payload = [
                "feature_id" => $roomFeature["feature_id"],
                "room_id"   => $room_id,
                "total_available" => $roomFeature["no_of_items"],
                "status" => "active"
            ];

            $roomFeatureModel->save($payload);
        }
    }

    private function addImages($imagesArray,$room_id){

        $mediaModel     = new MediaModel();
        $roomMediaModel = new RoomMediaModel();

        foreach($imagesArray as $image){

            $mediaID = $this->addImageToMediaModel($image["image"]);

            $payload = [ 
                "room_id"  => $room_id,
                "media_id" => $mediaID,
                "status"   => "active"
            ];

            $roomMediaModel->save($payload);
        }
    }


    private function addImageToMediaModel($image){

        $mediaModel = new MediaModel();

        // parse base64 images
        $imgData = str_replace(' ','+',$image);
        $imgData =  substr($imgData,strpos($imgData,",")+1);
        $imgData = base64_decode($imgData);

        #create file path
        $date = new \DateTime();
        $extension = explode("/",mime_content_type($image))[1];
        $fileName = floor(microtime(true) * 1000) . "." . $extension;
        $filepath = WRITEPATH . 'uploads/' . $fileName;

        #add contents to file from imgData
        $file = fopen($filepath, 'w');
        fwrite($file, $imgData);
        fclose($file);

        # add to db
        $data = ['uploaded_fileinfo' => new \CodeIgniter\Files\File($filepath)];
        $payload = [    
            "filename" => $fileName,
            "url"      => $filepath
        ];
        $mediaModel->save($payload);

        # return record id
        return $mediaModel->insertID();
    }

    public function removeImage($roomId=null,$mediaId=null){

        if($roomId === null){
            echo json_encode(["status"=>"failure","message"=>"Please add room id"]);
            return;
        }

        if($mediaId === null){
            echo json_encode(["status"=>"failure","message"=>"Please add media id"]);
            return;
        }

        $roomMediaModel = new RoomMediaModel();

        try{
            $data = $roomMediaModel->where(["room_id"=>$roomId,"media_id"=>$mediaId])->delete();
            echo json_encode(["status"=>"success"]);
        }
        catch(\Exception $e){
            echo json_encode([
                "status"=>"failure",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function update($id=null){

        if($id===null){
            echo json_encode(["status"=>"failure","message"=>"Id cannot be empty"]);
            return;
        }

        $roomModel = new RoomModel();

        $fields = ["room_name","room_desc","room_capacity","status"];

        $data = getRequestData($fields,$this->request);

        $data["room_id"] = $id;
        
        log_message("info","Room id ".$id);

        $validationRes = validateFields($data,"room_update");

        if($validationRes["success"]===false){

            $resultPayload = [
                "status" => "failure",
                "message" => array_pop($validationRes["errors"])
            ];

            echo json_encode($resultPayload);
            return;
        }

        try{
            $data["updated_at"] = date('Y-m-d H:i:s');
            $roomModel->update($data["room_id"],$data);
            $resultPayload = [
                "status" => "success"
            ];

            echo json_encode($resultPayload);
        }
        catch(Exception $e){
            echo json_encode([
                "status" => "failure",
                "message" => $e.message()
            ]);
        }
    }

    public function roomschedule($id=null,$date=null){

        if($id==null || $date==null){
            $response = [
                "status"=>"failure",
                "message" => "Room id or date not valid"
            ];
            echo json_encode($response);
            return;
        }

        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)){
            echo json_encode([
                "status" => "failure",
                "message" => "Invalid date format"
            ]);
            return;
        }

        $roomDownTimeModel      = new RoomDownTimeModel();
        $roomReservationModel   =  new RoomReservationModel();

        $timestamp  = strtotime($date);
        $dayName    = date('D', $timestamp);

        $roomDownTime = $roomDownTimeModel->getRoomDownTime($id,strtolower($dayName));
        $reservationData = $roomReservationModel->getReservationsForRoom($id,$date);
        

        echo json_encode([
            "status" => "success",
            "data" => [
                "downtime" => $roomDownTime,
                "reserved" => $reservationData,
            ]
        ]);
    }

    public function getRoomReservationData($room_id=null){
        if($room_id==null){
            echo json_encode([
                "status" => "failure",
                "message" => "Room id must be provided"
            ]);
            return;
        }

        $roomReservationModal = new RoomReservationModel();

        $reservationData = $roomReservationModal->geLast100Reservations($room_id);

        echo json_encode([
            "status" => "success",
            "data" => $reservationData
        ]);
    }


    public function cancel_meeting($reservation_id=null){
        /*
        $fields = ["reservation_id"];
        $data = getRequestData($fields,$this->request);
        */
        if(!$reservation_id){
            echo json_encode([
                "status" => "failure",
                "message" => "Meeting is not valid"
            ]);
            return;
        }

        $roomReservationModel = new RoomReservationModel();

        try{
            $roomReservationModel->cancel_reservation($reservation_id);

            echo json_encode([
                "status"=>"success"
            ]);
        }
        catch(\Exception $e){
            echo json_encode(
                [
                    "status" => "failure",
                    "message" => $e->getMessage()
                ]
            );
        }


    }

    
}

?>