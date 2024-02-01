<?php

# this file is the starting point to your application
# the code below is just to verify your connection works and you can receive events.
# you can replace this file completely
# alternatively hook your methods in here

include("../migrations/migrations.php");
include("../models/Events.php");

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);


switch ($_SERVER['REQUEST_URI']) {
    case '/setup':
        //create event table 
        migrate_datebase();
        //Seeding to events table
        $jsonData = file_get_contents('../migrations/events.json');
        $data = json_decode($jsonData, true);
        foreach ($data['requests'] as $input) {
            $keyToCheck = 'events';
                if (array_key_exists($keyToCheck, $input)) {
                    $eventModel = new Events();
                    // Check if $input is an array
                    if (is_array($input)) {
                        // Insert events
                        $eventModel->insertEvents($input['events']);
                    } else {
                        echo "The parameter 'events' is not an array.";
                    }                    
                } else {
                    error_reponse(400,"The key '$keyToCheck' does not exist in the array.");
                }
        }

        break;
    case '/setup-rollback':
        //Rollback all database setup
        migration_rollback();
        break;    
    case '/receive':
        // Check if the key exists in the array
        $keyToCheck = 'events';
        
        if (array_key_exists($keyToCheck, $input)) {
            $eventModel = new Events();
            // Check if $input is an array
            if (is_array($input)) {
                // Insert events
                $eventModel->insertEvents($input['events']);                
            } else {
                echo "The parameter 'my_param' is not an array.";
                // Handle non-array value
            }
           
            // Don't forget to handle errors, and consider using prepared statements for better security.
            receive($input);
        } else {
            error_reponse(400,"The key '$keyToCheck' does not exist in the array.");
        }
        break;
    case '/longest-activity':
        //Implemented an endpoint for finding which activity takes the longest overall time
        $eventModel = new Events();
        $response = $eventModel->getLongestActivity();
        echo json_encode($response);
        break;
    case '/activity-by-user':
        //Implemented an endpoint that returns how long each student took for each question in an activity.
        $eventModel = new Events();
        $response = $eventModel->getLongestActivityByEachStudent($input);
        echo json_encode($response);
        break;
    default:
        echo '{"status":"404", "message": "Not Found"}';
}

// remove me
function receive($input,$data)
{
    $response = [
        "status" => "200",
        "request_method" => $_SERVER['REQUEST_METHOD'],
        "message"=> "Successfully saved."
    ];

    echo json_encode($response);
}
// error response
function error_reponse($status,$message)
{
    $response = [
        "status" => $status,
        "request_method" => $_SERVER['REQUEST_METHOD'],
        "error" => $message
    ];

    echo json_encode($response);
}
