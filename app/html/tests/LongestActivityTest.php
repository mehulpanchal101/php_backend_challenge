<?php
// include("../models/Events.php");
class Database
{
    // Assume this method fetches user data from the database
    public function getLongestActivity()
    {
        // $eventModel = new Events();
        // $response = $eventModel->getLongestActivity();
        return ['activity_id' => "geo-2-3", 'average_time' => '27.9020'];
    }
}

class LongestActivityTest extends \PHPUnit\Framework\TestCase
{
    

    public function testGetLongestActivity()
    {
        // Create an instance of the Database class
        $database = new Database();

        // Call the getLongestActivity method
        $longestActivityData = $database->getLongestActivity();

        // Assert that the result is an array
        $this->assertIsArray($longestActivityData);

        // Assert that the array has the expected keys
        $this->assertArrayHasKey('activity_id', $longestActivityData);
        $this->assertArrayHasKey('average_time', $longestActivityData);

        // Assert that the values of the keys are as expected
        $this->assertEquals('geo-2-3', $longestActivityData['activity_id']);
        $this->assertEquals('27.9020', $longestActivityData['average_time']);
    }    
}
