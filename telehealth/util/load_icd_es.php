<?php

function MysqlError($mysqli)
{
    if (mysqli_errno($mysqli)) {
        echo "<b>Mysql Error: " . mysqli_error($mysqli) . "</b>\n";
    }
}

$host           = '192.168.68.50';
$port           = '3306';
$username       = 'root';
$password       = 'root';
$db             = 'openemr';

try {

    $mysqli = new mysqli($host, $username, $password, $db);

    // Desactivamos la lista que tiene por defecto
    $sql = "UPDATE list_options SET activity = 0 WHERE list_id = 'medical_problem_issue_list'";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();

    // Eliminamos si hay referencias 
    $sql = "DELETE FROM list_options WHERE list_id = 'medical_problem_issue_list' AND codes LIKE 'ICD11:%'";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();

    ini_set('auto_detect_line_endings', true);
    $handle = fopen('cie11_es.csv', 'r');

    while (($data = fgetcsv($handle, 0, ';')) !== false ) {
        $listId    = 'medical_problem_issue_list';
        $optionId  = trim($data[0]);
        $title      = trim($data[1]);
        $code       = 'ICD11:' . $data[0];
        $activity   = 1;

        $sql = "SELECT * FROM list_options WHERE list_id = 'medical_problem_issue_list' AND option_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('s', $optionId);
        $stmt->execute();

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            print_r($result);
        } else {
            echo "Se incluirá el código $code \n";
            $sql = "INSERT INTO list_options (list_id, option_id, title, codes, activity) VALUES(?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('ssssd', $listId, $optionId, $title, $code, $activity);
            $result = $stmt->execute();
        }   
        
    }
    ini_set('auto_detect_line_endings', false);

} catch (Exception $e) {
    MysqlError($mysqli);
    echo $e->getMessage();
}