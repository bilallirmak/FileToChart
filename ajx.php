<?php

require("connection.php");

if($_POST){

    $column = $_POST["column"];
    $sql_type = $_POST["sql"];

    $tablename = $_POST["tablename"];
    if($sql_type == "Count"){
        $chartData = mysqli_query($connectcsv, "SELECT `".$column."`, COUNT(`".$column."`) as num FROM `".$tablename."`

            GROUP BY `".$column."`");

    }else if($sql_type == "Sum"){

        $chartData = mysqli_query($connectcsv, "SELECT `".$column."`, SUM(`".$column."`) as num FROM `".$tablename."`

        GROUP BY `".$column."`");

    }else if($sql_type == "Average"){

        $chartData = mysqli_query($connectcsv, "SELECT `".$column."`, round(AVG(`".$column."`), 2) as num FROM `".$tablename."`

        GROUP BY `".$column."`");

    }

     while($row = mysqli_fetch_row($chartData)) {
        echo $row[0], "," , $row[1], ",";
        
        }

            
}


?>