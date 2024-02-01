<?php

class Events
{
    private $conn;

    // Database connection details
    private $servername = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $dbname = DB_NAME;
    // Table name
    private $tableName = "events";
    private $data_array = ['name','timestamp','user_id','activity_id'];

    // Constructor to establish a database connection
    public function __construct()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Destructor to close the database connection
    public function __destruct()
    {
        $this->conn->close();
    }

    // Insert a new Event into the database
    public function insertEvents($dataArray)
    {
        // Extract values from objects
        $values = array_map(function ($data)  {
            $name = $data['name'];
            $timestamp = date('Y-m-d H:i:s', $data['timestamp']);
            $user_id = $data['user_id'];
            $activity_id = $data['activity_id'];          
            return "('$name', '$timestamp', '$user_id','$activity_id')";
        }, $dataArray);
        $sql = "INSERT INTO $this->tableName (".implode(', ', $this->data_array).") VALUES " . implode(', ', $values);

        if ($this->conn->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    // Get returns how long each student took for each question in an activity.
    public function getLongestActivityByEachStudent($input)
    {   
        $values = array_map(function ($data){return "'$data'";},$input['user_ids']);    
        $events = array();
        $sql = "SELECT id,user_id,activity_id,timestamp, 
            count(timestamp) as duplicates,
            LAG(timestamp) OVER (PARTITION BY user_id,activity_id ORDER BY timestamp) AS previous_timestamp,
            TIMEDIFF(timestamp, LAG(timestamp) OVER (PARTITION BY user_id,activity_id ORDER BY timestamp)) AS time_spent_on_question
         from $this->tableName
            where activity_id = '".$input["activity_id"]."' 
            AND user_id IN (".implode(', ', $values).") 
            group by user_id,timestamp;
        ";
        // echo $sql; exit;
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
        }

        return $events;
    }

    // Implement an endpoint for finding which activity takes the longest overall time
    public function getLongestActivity()
    {   
        $events = array();
        $sql = "SELECT
        activity_id,
        AVG(time_difference) AS average_time
            FROM (
                SELECT
                activity_id,
                timestamp,
                LAG(timestamp) OVER (PARTITION BY activity_id ORDER BY timestamp) AS previous_timestamp,
                TIMESTAMPDIFF(SECOND, LAG(timestamp) OVER (PARTITION BY activity_id ORDER BY timestamp), timestamp) AS time_difference
                FROM
                $this->tableName
            ) AS subquery
            WHERE
                time_difference IS NOT NULL
            GROUP BY
                activity_id
            ORDER BY
                average_time DESC
            LIMIT 1;
        ";
        // echo $sql; exit;
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $events = $row;
            }
        }
        return $events;
    }
    
}

?>
