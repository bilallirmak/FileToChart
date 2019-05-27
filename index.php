<?php
require("templates\base.php")
?>

<div class = "tempForm" id = "tempForm">
  <form action="dropzone.php" class="dropzone" id="my-awesome-dropzone" method = "POST" enctype="multipart/form-data">
    <div class="fallback">
      <input name="file" type="file" multiple />
    </div>
  </form>

</div>


<div id = "liveDiv" class = "row"></div>


<script type="text/javascript">
$("#liveDiv").hide();



Dropzone.options.myAwesomeDropzone = {
  paramName: "file",
  url: "dropzone.php",
  acceptedFiles: '.csv',
  dictInvalidFileType: 'Desteklenmeyen dosya türü',
  dictDefaultMessage: 'bir CSV dosyası sürükle',
  uploadMultiple: false,
  parallelUploads: 1,
  maxFilesize: 10,
  maxFiles: 1,

  accept: function(file, done) {
    
    if (file.name) {
          var form_data = new FormData();                  
          form_data.append('filem',file);
          done(" ");
        $.ajax({
          dataType: 'text', 
          cache: false,
          contentType: false,
          processData: false,
          type: "POST",
          url: "http://localhost/plots.php",
          data: form_data,
        }).done(function(result){
              
              $("#tempForm").hide("slow");
              document.getElementById('liveDiv').innerHTML = result;
              $("#liveDiv").slideDown("slow");
              $("#pic-div").hide();
              $("#title-main").hide();
              $("#title").hide();
              $("#change").hide();
          $("#title-button").click(function(){
              var title = document.getElementById('title-input')
              $("#title-text").hide();
              $("#title").show();
              $("#change").show();
              document.getElementById('title').innerHTML = title.value;
          });
          $("#change").click(function(){
              $("#title").hide();
              $("#title-text").show();
              $("#change").hide();

          });


                var data = new FormData();
                $("#columns").change(function(){
                  data.append('column', this.value);
                  let chartid = document.getElementById('chartid');
                  let tablename = document.getElementById('tablename');
                  data.append('tablename', tablename.value);

                });

                $("#sql-type").change(function(){


                  data.append('sql', this.value);
        
                  $.ajax({
                    dataType: 'text', 
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: "POST",
                    url: "http://localhost/ajx.php",
                    data: data,

                  }).done(function(columnResult){
                    
                    drawBasic(columnResult);

                  });
                  $("#pic-div").show();
                  $("#title-main").show();

                  });

                google.charts.load('current', {packages: ['corechart', 'bar']});

                function drawBasic(columnResult) {
                  
               
                document.getElementById("chartid").setAttribute("style","width: 100%; height: 500px; display: block;");
                let dataArr = [];
                
                dataArr.push(['','Değer']);
                let arr = columnResult.split(",");
                arr.pop();
                for (let i = 0; i < arr.length; i+= 2) {
                let loopArr = [arr[i], parseInt(arr[i+1])];
                dataArr.push(loopArr);
                }


                var data = new google.visualization.DataTable();

                var data = google.visualization.arrayToDataTable(dataArr);

                var options = {
                title: "",
                backgroundColor: 'transparent',
                margin: "0",
                colors: ['#00bfff', '#bfff00'],
                };

                var chart = new google.visualization.ColumnChart(
                document.getElementById('chartid'));

                chart.draw(data, options);
                $("#chart-type").change(function(){
               
                if(this.value == 'Pie'){
                var chart = new google.visualization.PieChart(
                document.getElementById('chartid'));

                        var options = {
                        title: "",
                        backgroundColor: 'transparent',
                        margin: "0",
                        colors: ['#00bfff', '#bfff00', "#ff9999", "#ff00ff", "#ff33cc", "#cc6600", "#B49B95", "#F33D11", "#747BF6", "#2BF2EE", "#2BF23F", "#98A43E"],
                        };

                }else if(this.value == 'Bar'){
                var chart = new google.visualization.ColumnChart(
                document.getElementById('chartid'));
                var options = {
                        title: "",
                        backgroundColor: 'transparent',
                        margin: "0",
                        colors: ['#00bfff', '#bfff00'],
                        };

                

                }
                else{
                var chart = new google.visualization.LineChart(
                document.getElementById('chartid'));
                var options = {
                        title: "",
                        backgroundColor: 'transparent',
                        margin: "0",
                        colors: ['#00bfff', '#bfff00'],
                        };

                }
                chart.draw(data, options);
                

                });

                }

                $("#pic").click(function(){

                html2canvas(document.querySelector("#picContainer")).then(canvas => {

                  var a = document.createElement('a');
                  a.href = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                  a.download = 'myChart.png';
                  a.click();

                });

                });
       
        });

     

    }

    else { done(); }
  }
};

</script>



