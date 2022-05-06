<?php

namespace App\Controllers;


class Dashboard extends BaseController{

    public function view(){
        helper('url');
        echo view("dashboard/index.php");
    }

    public function room(){
        helper("url");
        $breadcrumbs = [    
            [
                "type" => "linked",
                "name" => "Dashboard",
                "url" => base_url()."/dashboard/view/"
            ],
            [
                "type" => "no_link",
                "name" => "Rooms"
            ],
        ];
        echo view("dashboard/pages/rooms/index.php",["title"=>"Rooms","breadcrumbs"=> $breadcrumbs ]);
    }

    public function addRoom(){
        helper("url");
        $breadcrumbs = [    
            [
                "type" => "linked",
                "name" => "Dashboard",
                "url" => base_url()."/dashboard/view/"
            ],
            [
                "type" => "linked",
                "name" => "Rooms",
                "url" => base_url()."/dashboard/room"
            ],
            [
                "type" => "no_link",
                "name" => "Add New Room"
            ],
        ];
        echo view("dashboard/pages/rooms/addRoom.php",["title"=>"Add New Room","breadcrumbs"=>$breadcrumbs]);
    }

    public function features(){
        helper("url");
        $breadcrumbs = [    
            [
                "type" => "linked",
                "name" => "Dashboard",
                "url" => base_url()."/dashboard/view/"
            ],
            [
                "type" => "no_link",
                "name" => "Features",
            ]
        ];
        echo view("dashboard/pages/features/index.php",["title"=>"Features","breadcrumbs"=>$breadcrumbs]);
    }

    public function addFeature(){
        helper("url");
        $breadcrumbs = [    
            [
                "type" => "linked",
                "name" => "Dashboard",
                "url" => base_url()."/dashboard/view/"
            ],
            [
                "type" => "linked",
                "name" => "Features",
                "url" => base_url()."/dashboard/features/"
            ],
            [
                "type" => "no_link",
                "name" => "Add New Feature"
            ]
        ];
        echo view("dashboard/pages/features/addFeature.php",["title"=>"Features","breadcrumbs"=>$breadcrumbs]);
    }


    public function priority(){
        helper("url");
        $breadcrumbs = [    
            [
                "type" => "linked",
                "name" => "Dashboard",
                "url" => base_url()."/dashboard/view/"
            ],
            [
                "type" => "no_link",
                "name" => "Priority"
            ]
        ];
        echo view("dashboard/pages/priority/index.php",["title"=>"Priority","breadcrumbs"=>$breadcrumbs]);
    }

    public function addPriority(){
        helper("url");
        $breadcrumbs = [
            [
                "type" => "linked",
                "name" => "Dashboard",
                "url" => base_url()."/dashboard/view/"
            ],
            [
                "type" => "linked",
                "name" => "Priority",
                "url"  =>  base_url()."/dashboard/priority",
            ],
            [
                "type" => "no_link",
                "name" => "Add Priority"
            ]
        ];

        echo view("dashboard/pages/priority/addPriority.php",["title"=>"Priority","breadcrumbs"=>$breadcrumbs]);
    }

}

?>