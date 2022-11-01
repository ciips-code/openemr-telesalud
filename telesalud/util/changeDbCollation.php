<?php

function MysqlError($mysqli)
{
    if (mysqli_errno($mysqli)) {
        echo "<b>Mysql Error: " . mysqli_error($mysqli) . "</b>\n";
    }
}

// $username = "openemr";
// $password = "openemr";
// $db = "openemr";
// $host = "localhost";
$host = 'telesalud-openemr-mysql';
$port = '3306';
$username = 'openemr';
$password = 'openemr';
$db = 'openemr';
$db_encoding = 'utf8';

$target_charset = "utf8";
$target_collate = "utf8_general_ci";

echo "<pre>";
try {
    
    $mysqli = mysqli_connect($host, $username, $password);
    mysqli_select_db($mysqli, $db);
    
    $tabs = array();
    $res = mysqli_query($mysqli, "SHOW TABLES");
    
    // //
    while (($row = mysqli_fetch_row($res)) != null) {
        
        $tabs[] = $row[0];
    }
    
    // set database charset
    mysqli_query($mysqli, "ALTER DATABASE {$db} DEFAULT CHARACTER SET {$target_charset} COLLATE {$target_collate}");
    
    // now, fix tables
    foreach ($tabs as $tab) {
        echo "<br> $tab";
        $res = mysqli_query($mysqli, "show index from {$tab}");
        //
        $indicies = array();
        
        while (($row = mysqli_fetch_array($res)) != null) {
            if ($row[2] != "PRIMARY") {
                $indicies[] = array(
                    "name" => $row[2],
                    "unique" => ! ($row[1] == "1"),
                    "col" => $row[4]
                );
                mysqli_query($mysqli, "ALTER TABLE {$tab} DROP INDEX {$row[2]}");
                //
                echo "Dropped index {$row[2]}. Unique: {$row[1]}\n";
            }
        }
        
        $res = mysqli_query($mysqli, "DESCRIBE {$tab}");
        //
        while (($row = mysqli_fetch_array($res)) != null) {
            $name = $row[0];
            $type = $row[1];
            $set = false;
            if (preg_match("/^varchar\((\d+)\)$/i", $type, $mat)) {
                $size = $mat[1];
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} VARBINARY({$size})");
                //
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} VARCHAR({$size}) CHARACTER SET {$target_charset}");
                //
                $set = true;
                
                echo "Altered field {$name} on {$tab} from type {$type}\n";
            } else if (! strcasecmp($type, "CHAR")) {
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} BINARY(1)");
                //
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} VARCHAR(1) CHARACTER SET {$target_charset}");
                //
                $set = true;
                
                echo "Altered field {$name} on {$tab} from type {$type}\n";
            } else if (! strcasecmp($type, "TINYTEXT")) {
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} TINYBLOB");
                //
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} TINYTEXT CHARACTER SET {$target_charset}");
                //
                $set = true;
                
                echo "Altered field {$name} on {$tab} from type {$type}\n";
            } else if (! strcasecmp($type, "MEDIUMTEXT")) {
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} MEDIUMBLOB");
                //
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} MEDIUMTEXT CHARACTER SET {$target_charset}");
                //
                $set = true;
                
                echo "Altered field {$name} on {$tab} from type {$type}\n";
            } else if (! strcasecmp($type, "LONGTEXT")) {
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} LONGBLOB");
                //
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} LONGTEXT CHARACTER SET {$target_charset}");
                //
                $set = true;
                
                echo "Altered field {$name} on {$tab} from type {$type}\n";
            } else if (! strcasecmp($type, "TEXT")) {
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} BLOB");
                //
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} TEXT CHARACTER SET {$target_charset}");
                //
                $set = true;
                
                echo "Altered field {$name} on {$tab} from type {$type}\n";
            }
            
            if ($set)
                mysqli_query($mysqli, "ALTER TABLE {$tab} MODIFY {$name} COLLATE {$target_collate}");
        }
        
        // re-build indicies..
        foreach ($indicies as $index) {
            if ($index["unique"]) {
                mysqli_query($mysqli, "CREATE UNIQUE INDEX {$index["name"]} ON {$tab} ({$index["col"]})");
                //
            } else {
                mysqli_query($mysqli, "CREATE INDEX {$index["name"]} ON {$tab} ({$index["col"]})");
                //
            }
            
            echo "Created index {$index["name"]} on {$tab}. Unique: {$index["unique"]}\n";
        }
        
        // set default collate
        mysqli_query($mysqli, "ALTER TABLE {$tab}  DEFAULT CHARACTER SET {$target_charset} COLLATE {$target_collate}");
    }
    
    mysqli_close($mysqli);
    echo "</pre>";
} catch (Exception $e) {
    MysqlError($mysqli);
    echo $e->getMessage();
}
