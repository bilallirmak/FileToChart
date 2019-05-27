<?php
require("connection.php");

if ($_FILES) {
    if(isset($_FILES["filem"])){
        $uploadDir = "uploads/";
        $temp = explode(".", $_FILES["filem"]["name"]);
        $filename =  reset($temp) . round(microtime(true)) . '.' . end($temp);
        $uploadFile = $uploadDir . $filename;
 
        if(move_uploaded_file($_FILES["filem"]["tmp_name"], $uploadFile)){

            if($connectcsv){
                if (($handle = fopen("$uploadFile", "r")) !== FALSE) {
                    $fileNoDot = str_replace(".", "", $filename);
                    $checkTableName = mysqli_query($connectcsv, "SHOW TABLES FROM csvdb LIKE '%$fileNoDot%'");
                    if(mysqli_num_rows($checkTableName)!==0){
                        $uniqid = uniqid();
                        $fileNoDot = $fileNoDot.$uniqid;
                    }

                    $data = fgetcsv($handle);
                    $num = count($data);
                    $columnsSimple = array();
                    for ($d=0; $d < $num; $d++) {
                        $columnsSimple[$d] = str_replace("#","id", str_replace(".","", str_replace(" ", "", $data[$d])));
                    }
                    
                    $arrayColumnType = array();
                    $data = fgetcsv($handle);

                    for ($d=0; $d < $num; $d++) { 
                        if ((int)$data[$d] != 0) {
        
                            $arrayColumnType[$d] = "INT(11)";
                            
                        } else {
                            $arrayColumnType[$d] = "VARCHAR(255)"; 
                        }
                    }

                    $columns = array();
                    $values1 = array();
                    for ($d=0; $d < $num; $d++) {
                    
                        $columns[$d] = str_replace("#","id",str_replace(".", "",str_replace(" ", "", $columnsSimple[$d]))). "  $arrayColumnType[$d]";
                        $values1[] = "'$data[$d]'";
                    }

                    $columns = implode(", ", $columns);
                    $columnsSimple = implode(", ", $columnsSimple);
                    $values1 = implode(", ", $values1);
                    
                    $createTable = mysqli_query($connectcsv, "CREATE TABLE IF NOT EXISTS `".$fileNoDot."` ($columns)");
                    $insertTable = mysqli_query($connectcsv, "INSERT INTO `".$fileNoDot."` ($columnsSimple) VALUES ($values1)");

                    while (($data = fgetcsv($handle)) !== FALSE) {
                        $values = array();
                    
                        for ($c=0; $c < $num; $c++) {
                            if ((int)$data[$c] != 0) {
                                $data[$c] = (int)$data[$c];
                                $values[] = $data[$c];
                            }else{
                                $values[] = "'$data[$c]'";
                            }
                        }  
        
                        $values = implode(", ", $values);
                        
                        $insertTable = mysqli_query($connectcsv, "INSERT INTO `".$fileNoDot."` ($columnsSimple) VALUES ($values)");
                    }
                    fclose($handle);
                }

                $columnsNames = mysqli_query($connectcsv, "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema='csvdb' AND table_name='$fileNoDot'");

                if (mysqli_num_rows($columnsNames) > 0) {
                    $fields_name = array();
                    while($row = mysqli_fetch_row($columnsNames)){
                        $fields_name[] = $row[0];
                    }

                    $field_count = count($fields_name);
                    
                } 

            }else{

                die("Error");
            }
        }
    }
}
?>

<div class = 'container' >

    <div class = "select1">
    <select class='form-control' id = 'columns' name = 'columns' >"
        <option value="" disabled="disabled" selected="selected">Sütun Seçiniz</option>
        <?php 
        for($f=0; $f < $field_count; $f++){
            echo "<option>$fields_name[$f]</option>";
        }?>
    </select>

    <select class='form-control' id = 'sql-type' name = 'sql-type'>
        <option value="" disabled="disabled" selected="selected">Ne yapmak istiyorsunuz?</option>
        <option>Count</option>
        <option>Sum</option>
        <option>Average</option>

    </select>

    <select class='form-control' id = 'chart-type' name = 'chart-type'>
    
        <option>Bar</option>
        <option>Line</option>
        <option>Pie</option>

    </select>

    <div id = "pic-div" style = "width:40px; height: 50px; float: right;"><button id = "pic" class = "screenshot"><img src="static\images\photo-camera.png" rel = "Resim Al" alt="Resim Al"> </button></div>
    

    </div>

<input type = 'hidden' value = <?php echo $fileNoDot ?> name = 'tablename' id='tablename'>   

</div> 

<div class = "container" id = "picContainer">
    <div id = "title-main">
        <div id = "title-text">
            <input id = "title-input" type="text" placeholder = "Grafik başlığı?">
            <button class ="btn btn-link" id = "title-button">Tamam</button>    
        </div>
        <div id = "title-show">
            <div id = "title"></div> 
            <button class ="btn btn-link" id = "change">Değiştir</button>
        </div>
    </div>
    <div id ="char-out" class="char-out">
            <div class="chart-into"><div id="chartid" style="width: 100%; height: 300px"></div></div>   
    </div>
</div>
