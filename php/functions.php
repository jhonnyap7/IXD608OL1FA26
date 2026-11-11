<?php

// =====================================
// GENERAL FUNCTIONS
// =====================================

function print_p($v) {

    echo "<pre>", print_r($v), "</pre>";
}


function file_get_json($filename) {

    $file = file_get_contents($filename);

    return json_decode($file);
}


// =====================================
// SOPHEA — DATABASE CONNECTION
// =====================================

function makeConn() {

    $config = require __DIR__ . '/db_config.php';

    $conn = new mysqli(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database']
    );

    if ($conn->connect_error) {

        throw new RuntimeException(
            'Database connection failed.'
        );
    }

    $conn->set_charset('utf8mb4');

    return $conn;
}


// =====================================
// SOPHEA — DATABASE QUERY
// =====================================

function makeQuery($conn, $query) {

    $result = $conn->query($query);

    if ($result === false) {

        throw new RuntimeException(
            'Database query failed.'
        );
    }

    return $result;
}