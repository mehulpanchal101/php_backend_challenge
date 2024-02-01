<?php
require_once('../config/config.php');
/**
 * Create Database Connection
 */
function connection(){
    // Database connection parameters
    $servername = DB_SERVER;
    $username = DB_USERNAME;
    $password = DB_PASSWORD;
    $dbname = DB_NAME;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
/**
 * Migration Database
 */
function migrate_datebase(){
    
    $conn = connection();
    // SQL query to create a new table
    $sql = "CREATE TABLE IF NOT EXISTS events (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL,
        user_id VARCHAR(30) NOT NULL,
        activity_id VARCHAR(30) NOT NULL,
        timestamp TIMESTAMP
    )";

    // Execute query
    if ($conn->query($sql) === TRUE) {
        echo "Table 'events' created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
/**
 * Migration rollback
 */
function migration_rollback(){
    $conn = connection();
    // Migration Rollback
    $sql = "DROP TABLE events";
    // Execute query
    if ($conn->query($sql) === TRUE) {
        echo "Table Deleted Succesfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
