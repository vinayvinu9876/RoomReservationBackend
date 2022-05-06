<?php

namespace Config;

use CodeIgniter\Validation\CreditCardRules;
use CodeIgniter\Validation\FileRules;
use CodeIgniter\Validation\FormatRules;
use CodeIgniter\Validation\Rules;

class Validation
{
    //--------------------------------------------------------------------
    // Setup
    //--------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    //--------------------------------------------------------------------
    // Rules
    //--------------------------------------------------------------------

    public $room_create = [    
        
        'room_name' => [        
           'rules' => 'required|min_length[3]|max_length[300]|is_unique[rooms.room_name]',
           'errors' => [
               'required' => 'Room name is required',
               'min_length' => 'Room name must atleast have 3 characters',
               'max_length' => 'Room name cannot exceed 300 characters',
               'is_unique' => 'Room with this name already exists'
           ]
        ],
        'room_desc' => [
           'rules' => 'required|min_length[50]|max_length[1000]',
           'errors' => [
               'required' => 'Room description is required',
               'min_length'=> 'Description should atleast contain 50 characters',
               'max_length' => 'Description can have maximum of 1000 characters'
           ]
        ],
        'room_capacity' => [
            'rules' => 'required|numeric|less_than_equal_to[1500]|greater_than[0]',
            'errors' => [
                'required' => "Room capacity is required",
                'numeric' => "Room capacity must be a number",
                'less_than_equal_to' => "Capacity of a room cannot exceed 1500",
                'greater_than' => 'Capacity must be greater than 0'
            ]
        ],
        'status' => [
            'rules' => 'required|in_list[active,inactive,under_maintenance,cleaning,temporarily_unavailable,permanently_unavailable]',
            'errors' => [
                'required' => "Status of the room is required",
                'in_list' => 'Room status is not valid'
            ]
        ]

    ];


    public $room_update = [   
        'room_id' => [        
            'rules' => 'required|numeric|is_not_unique[rooms.room_id]',
            'errors' => [
                'required' => 'Room id is required',
                'numeric' => 'Room id must a number',
                'is_not_unique' => 'Room id doesnt exist'
            ]
         ], 
        'room_name' => [        
           'rules' => 'min_length[3]|max_length[300]|is_unique[rooms.room_name,room_name,{room_name}]|if_exist',
           'errors' => [
               'min_length' => 'Room name must atleast have 3 characters',
               'max_length' => 'Room name cannot exceed 300 characters',
               'is_unique' => 'Room with this name already exists'
           ]
        ],
        'room_desc' => [
           'rules' => 'min_length[50]|max_length[1000]|if_exist',
           'errors' => [
               'min_length'=> 'Description should atleast contain 50 characters',
               'max_length' => 'Description can have maximum of 1000 characters'
           ]
        ],
        'room_capacity' => [
            'rules' => 'numeric|less_than_equal_to[1500]|greater_than[0]|if_exist',
            'errors' => [
                'numeric' => "Room capacity must be a number",
                'less_than_equal_to' => "Capacity of a room cannot exceed 1500",
                'greater_than' => 'Capacity must be greater than 0'
            ]
        ],
        'status' => [
            'rules' => 'in_list[active,inactive,under_maintenance,cleaning,temporarily_unavailable,permanently_unavailable]|if_exist',
            'errors' => [
                'in_list' => 'Room status is not valid'
            ]
        ]

    ];

    public $features_create = [
        'feature_name' => [
            "rules" => 'required|min_length[2]|max_length[300]|is_unique[features.feature_name]',
            'errors' => [
                'required' => "Feature name is required",
                "min_length"=> "Feaure name must have atleast 2 characters",
                "max_length" => "Feature name cannot exceed 300 characters",
                "is_unique" => "Feature with this name already exists"
            ]
        ],
        'feature_desc' => [
            'rules' => 'required|min_length[30]|max_length[1000]',
            'errors' => [
                'required' => "Feature description is required",
                "min_length" => "Feature description must have minimum of 30 characters",
                "max_length" => "Feature description can have maximum of `100 characters"
            ]
        ],
        'total_available' => [
            'rules' => 'required|numeric',
            'errors' => [
                'required' => "Please add the total available no. of features",
                'numeric' => "Total Available must be a number"
            ]
        ],
        'status' => [
            'rules' => 'required|in_list[active,maintenance,permanently_unavailable]',
            'errors' => [
                'required' => "Status is required",
                'in_list' => 'Feature status is not valid'
            ]
        ]   
    ];

    public $features_update = [
        'id' => [        
            'rules' => 'required|numeric|is_not_unique[features.id]',
            'errors' => [
                'is_not_unique' => "Feature with this id doesnt exist",
                'required' => 'Feature id is required',
                'numeric' => 'Feature id must a number'
            ]
         ], 
        'feature_name' => [
            "rules" => 'if_exist|min_length[2]|max_length[300]|is_unique[features.feature_name,feature_name,{feature_name}]',
            'errors' => [
                "min_length"=> "Feaure name must have atleast 2 characters",
                "max_length" => "Feature name cannot exceed 300 characters",
                "is_unique" => "Feature with this name already exists"
            ]
        ],
        'feature_desc' => [
            'rules' => 'if_exist|min_length[30]|max_length[1000]',
            'errors' => [
                "min_length" => "Feature description must have minimum of 30 characters",
                "max_length" => "Feature description can have maximum of `100 characters"
            ]
        ],
        'total_available' => [
            'rules' => 'if_exist|numeric',
            'errors' => [
                'numeric' => "Total Available must be a number"
            ]
        ],
        'status' => [
            'rules' => 'if_exist|in_list[active,maintenance,permanently_unavailable]',
            'errors' => [
                'in_list' => 'Feature status is not valid'
            ]
        ]   
    ];

    public $room_features_create = [
        "feature_id" =>[
            "rules"=> "required|numeric|is_not_unique[features.id]",
            "errors"=>[
                "is_not_unique" => "Feature id doesn't exist",
                "required" => "Feature id is required",
                "numeric" => "Feature id must be number"
            ]
        ],
        "room_id" => [
            "rules" => "required|numeric|is_not_unique[rooms.room_id]",
            "errors" => [
                "required" => "Room id is required",
                "numeric" => "Room id must be a number",
                "is_not_unique" => "Room id doesn't exist"
            ]
        ],
        "total_available" => [
            "rules" => "required|numeric",
            "errors" => [
                "required" => "Total available is required",
                "numeric" => "Total available must be a number"
            ]
        ],
        "status" => [
            "rules" => "required|in_list[active,inactive,maintenance,permanently_moved]",
            "errors" => [
                "required" => "Status is required",
                "in_list" => "Invalid status value"
            ]
        ]
    ];

    public $room_features_update = [
        "id" => [
            "rules" => "required|numeric|is_not_unique[room_features.id]",
            "errors" => [
                "required" => "Room feature id is required",
                "numeric" => "Room feature must be a number",
                "is_not_unique" => "Room feature id doesn't exist"
            ]
        ],
        "status" => [
            "rules" => "in_list[active,inactive,maintenance,permanently_moved]|if_exist",
            "errors" => [
                "required" => "Status is required",
                "in_list" => "Invalid status value"
            ]
        ],
        "total_available" => [
            "rules" => "numeric|if_exist",
            "errors" => [
                "required" => "Total available is required",
                "numeric" => "Total available must be a number"
            ]
        ],
    ];  


    public $room_down_time_create = [
        "room_id" => [
            "rules" => "required|numeric",
            "errors" => [
                "required" => "Room id is required",
                "numeric" => "Room id must be number",
            ]
        ],
        "desc" => [
            "rules" => "required|min_length[10]|max_length[1000]",
            "errors" => [
                "required" => "Description is required",
                "min_length" => "Description must have minimum length of 10",
                "max_length" => "Description can have maximum length of 1000"
            ]
        ],
        "start" => [
            "rules" => "required|regex_match[^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$]",
            "errors" => [
                "required" => "Start time is required"
            ]
        ],
        "end" => [
            "rules" => "required|regex_match[^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$]",
            "errors" => [
                "required" => "End time is required"
            ]
        ],
        "day" => [
            "rules" => "required|in_list[sun,mon,tue,wed,thu,fri,sat,sun]",
            "errors" => [
                "required" => "Day is required",
                "in_list" => "Invalid day value"
            ]
        ],
        "status" => [
            "rules" => "required|in_list[active,inactive]",
            "errors" => [
                "required" => 'status is required',
                "in_list" => "Invalid status value"
            ]
        ]
    ];

    public $room_down_time_update = [
        "id" => [
            "rules" => "required|numeric|is_not_unique[room_down_time.id]",
            "errors" => [
                "required"=>"Id is required",
                "numeric" => 'Id must be a number',
                "is_not_unique"=>"Id doesn't exist"
            ]
        ],
        "room_id" => [
            "rules" => "if_exist|numeric|is_not_unique[rooms.room_id]",
            "errors" => [
                "required" => "Room id is required",
                "numeric" => "Room id must be number",
                "is_not_unique"=>"Room id doesn't exist"
            ]
        ],
        "desc" => [
            "rules" => "if_exist|min_length[10]|max_length[1000]",
            "errors" => [
                "required" => "Description is required",
                "min_length" => "Description must have minimum length of 10",
                "max_length" => "Description can have maximum length of 1000"
            ]
        ],
        "start" => [
            "rules" => "if_exist|regex_match[^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$]",
            "errors" => [
                "regex_match" => "Start time is not valid"
            ]
        ],
        "end" => [
            "rules" => "if_exist|regex_match[^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$]",
            "errors" => [
                "regex_match" => "End time is not valid"
            ]
        ],
        "day" => [
            "rules" => "if_exist|in_list[sun,mon,tue,wed,thu,fri,sat,sun]",
            "errors" => [
                "required" => "Day is required",
                "in_list" => "Invalid day value"
            ]
        ],
        "status" => [
            "rules" => "if_exist|in_list[active,inactive]",
            "errors" => [
                "required" => 'status is required',
                "in_list" => "Invalid status value"
            ]
        ]
    ];

    public $room_down_time_read = [
        "room_id" => [
            "rules" => "required|numeric|is_not_unique[rooms.room_id]",
            "errors" => [
                "required" => "Room id is required",
                "numeric" => "Room id must be number",
                "is_not_unique"=>"Room id doesn't exist"
            ]
        ],
        "day" => [
            "rules" => "if_exist|in_list[sun,mon,tue,wed,thu,fri,sat,sun]",
            "errors" => [
                "required" => "Day is required",
                "in_list" => "Invalid day value"
            ]
        ],
        "status" => [
            "rules" => "if_exist|in_list[active,inactive]",
            "errors" => [
                "required" => 'status is required',
                "in_list" => "Invalid status value"
            ]
        ]
    ];
    
    public $room_reservation_create = [
        "room_id" =>[
            "rules" => "required|is_not_unique[rooms.room_id]",
            "errors" => [
                "required" => "Room id is required",
                "is_not_unique" => "Room id doesn't exist"
            ]
        ],
        "start_timestamp"=>[
            "rules"=>"required|numeric|greater_than[1650892015126]",
            "errors" => [
                "required" => "Start timestamp is required",
                "numeric" => "Start timestamp must be number",
                "greater_than" => "Start timestamp must be from future"
            ]
        ],
        "end_timestamp" =>[
            "rules" => "required|numeric|greater_than[1650892015126]",
            "errors" => [
                "required" => "End timestamp is required",
                "numeric" => "End timestamp must be number",
                "greater_than" => "End timestamp must be from future"
            ]
        ],
        "reservation_description" => [
            "rules" => "required|min_length[30]|max_length[1000]",
            "errors" => [
                "required" => "Description is required",
                "min_length" => "Description must contain atleast 30 characters",
                "max_length" => "Description can have maximum of 1000 characters"
            ]
        ],
        "reservation_requirements" => [
            "rules" => "required|min_length[30]|max_length[1000]",
            "errors" => [
                "required" => "Reservation requirements are required",
                "min_length" => "Reservation requirement must have atleast 30 characters",
                "max_length" => "Reservation requirement can have maximum of 1000 characters"
            ]
        ],
        "reserved_by" => [
            "rules" => "required|min_length[3]|max_length[100]",
            "errors" => [
                "required" => "Reserved by is required",
                "min_length" => "Reserved by must atleast have 3 characters",
                "max_length" => "Reserved by can have maximum of 100 chatacters"
            ]
        ],
        "status" => [
            "rules" => "required|in_list[booked,cancelled]",
            "errors"=>[
                "required" => "Status is required",
                "in_list" => "Status value is not valid"
            ]
        ]
    ];

    public $priority_create = [
        "name" => [
            "rules" => "required|min_length[3]|max_length[100]|is_unique[priority.name]",
            "errors" => [
                "required" => "Priority name is required",
                "min_length" => "Priority name must have minimum of 3 characters",
                "max_length" => "Priority name can have maximum of 100 characters",
                "is_unique" => "Priority with this name already exists"
            ]
        ],
        "desc" => [
            "rules" => 'required|min_length[3]|max_length[1000]',
            "errors" => [
                "required" => "Description is required",
                "min_length" => "Description must have atleast 3 characters",
                "max_length" => "Description can have maximum of 1000 characters"
            ]
        ],
        "priority_no" => [
            "rules" => "required|numeric|greater_than[0]|less_than[100]|is_unique[priority.priority_no]",
            "errors" => [
                "required" => "Priority no. is required",
                "numeric" => "Priority no. must be a number",
                "less_than" => "Priority no cannot be greater than 100"
            ]
        ],
        "status" => [
            "rules" => "required|in_list[active,inactive]",
            "errors" => [
                "required" => "Status is required",
                "in_list" => "Status value is not valid"
            ]
        ]
    ];

    public $priority_update = [
        "name" => [
            "rules" => "if_exist|min_length[3]|max_length[100]|is_unique[priority.name]",
            "errors" => [
                "required" => "Priority name is required",
                "min_length" => "Priority name must have minimum of 3 characters",
                "max_length" => "Priority name can have maximum of 100 characters",
                "is_unique" => "Priority with this name already exists"
            ]
        ],
        "desc" => [
            "rules" => 'if_exist|min_length[3]|max_length[1000]',
            "errors" => [
                "required" => "Description is required",
                "min_length" => "Description must have atleast 3 characters",
                "max_length" => "Description can have maximum of 1000 characters"
            ]
        ],
        "priority_no" => [
            "rules" => "if_exist|numeric|greater_than[0]|less_than[100]|is_unique[priority.priority_no,priority_no,{priority_no}]",
            "errors" => [
                "required" => "Priority no. is required",
                "numeric" => "Priority no. must be a number",
                "greater_than" => "Pririty no. cannot be less than 1",
                "less_than" => "Priority no cannot be greater than 100",
                "is_unique" => "Priority no. must be unique"
            ]
        ],
        "status" => [
            "rules" => "if_exist|in_list[active,inactive]",
            "errors" => [
                "required" => "Status is required",
                "in_list" => "Status value is not valid"
            ]
        ]
    ];
    
}
