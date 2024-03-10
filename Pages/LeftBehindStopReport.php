<?php
    require '../Database/connect.php';
    require_once('../ulogin/config/all.inc.php');
    require_once('../ulogin/main.inc.php');
    
    if (!sses_running())
        sses_start();
    
    function isAppLoggedIn(){
        return isset($_SESSION['uid']) && isset($_SESSION['username']) && isset($_SESSION['loggedIn']) && ($_SESSION['loggedIn']===true);
    }
    
    if (!isAppLoggedIn()) {
        header("Location: ../index.php"); /* Redirect browser */
       exit();
    } 

     $hourly = array();
    $entries = array();
    $input = "";
    // $loopDropdown = array();
    $stop ="";
    $stopArray = array();
    $allLeft = array();
    $entryDate = date("m-d-Y");

    //if Filter By Hour is clicked
    if(isset($_POST['HourlyButton'])){
        $dateInputHourly = $_POST['dateInputHourly'];
        if($dateInputHourly != '') {

        $newDate = date("Y-m-d", strtotime($dateInputHourly));
        $entryDate = date("m-d-Y", strtotime($dateInputHourly));

        populateStops($stopArray, $con, $newDate);

        populateTableArray($allLeft, $con, $newDate, $stopArray);

        }
        // header('Location: Entries.php');
  
    }

    function populateHourly( $con, $date, $stop){
        $new_hourly = array();
        for($hour=0; $hour<24; $hour++){
            $new_hourly[$hour] = 0;
        }

        $sql = sprintf("SELECT DISTINCT `boarded`,`stop`,`t_stamp`,`loop`,`driver`,`left_behind`,`bus_identifier` FROM `entries` where `stop` = '$stop' and `t_stamp` LIKE '$date%%'");

        if($big_result =  mysqli_query($con,$sql)) {
            while($row = mysqli_fetch_assoc($big_result)) {
                $hourOfTheEntry = explode(":",explode(" ",$row['t_stamp'])[1])[0];
                $left_behind = $row['left_behind'];
                $new_hourly[intval($hourOfTheEntry)]+=intval($left_behind);
            }
        } else {
            http_response_code(404);
        }

        return $new_hourly;
    }

    //----------------------------

// should be done --------------------

    function populateStops(&$stopArray, $con, $date){
        $sql = sprintf("SELECT distinct `stops`.*, `stop_loop`.`stop`, `entries`.`date_added`
        FROM `stops` 
            LEFT JOIN `stop_loop` ON `stop_loop`.`stop` = `stops`.`id` 
            LEFT JOIN `entries` ON `entries`.`stop` = `stops`.`id`
        WHERE `entries`.`date_added` ='$date'");
        
        if($result = mysqli_query($con,$sql)) {
            while($row = mysqli_fetch_assoc($result)) {
                array_push($stopArray, $row);
            }
            } else {
            http_response_code(404);
            }
        
    }

    // -------------------------------

//----------------------

    function populateTableArray(&$allLeft, $con, $date, $stopArray){

        $hourly = array();

        $counter = 0;
        foreach($stopArray as $instance){
           
            $allLeft[$counter] = array();
            $hourly = populateHourly( $con, $date, $instance['stop']);
            
            $allLeft[$counter] = $hourly;
            $counter = $counter + 1;

        }
        $counter = 0;

    }

    //-------------------------------------------

?>



<?php
        require '../themepart/resources.php';
        require '../themepart/sidebar.php';
        require '../themepart/pageContentHolder.php';
        $_SESSION["Title"]="Students Left Behind by Stop";
    ?>


<HTML LANG="EN">

<HEAD>


</HEAD>

<form method=post>

</form>

   



    <script>
        $('#datepicker').datepicker();
    </script>

<body>



    

<!-- Controls the selections for the hourly filter -->
<div class="d-flex justify-content-center">
        <form action="" method="post">
         <div class="form-row align-items-center">
          <div class="col-auto">
                 <input class="form-control mb-2" input="text" name="dateInputHourly" id="datepickerHourly" width="276" placeholder="Click to Select Date" required  />
               </div>

        <div class="col-auto">
          
          <button type="submit" name="HourlyButton" class="btn btn-dark mb-2">Filter By Hour</button>
          <a href="#" id="xx" class="btn btn-dark mb-2">Export</a>

        </div>
        </div>
    </form>
    </div>
    <!-- ends hourly selections control -->

    

    <!-- Creates table for hourly -->
    <table id="editable_table" class="table table-bordered table-striped">
        <thead>
        <tr><th colspan="18"><?php echo $entryDate?></th></tr>
            <tr>
                <th>Stops</th>
                <?php 
                $time = 0; 
                $AMOrPM = 'AM';
                
                for($i = 0; $i<24; $i=$i+1){ ?>
                    <td><?php echo "$time:00 - $time:59 $AMOrPM" ; ?></td>
                    <?php 
                        if($time == 11){
                            if($AMOrPM == 'AM'){
                                $AMOrPM ='PM';
                            }elseif($AMOrPM == 'PM'){
                                $AMorPM = 'AM';
                        }
                    }
                        if($time == 12){
                            $time = 1;

                            
                        }else{
                            $time = $time + 1;
                        }
                    
                    ?>
                <?php }  ?>          
                
                
            </tr>
        </thead>
        <!-- ends table for hourly -->


    <!-- This adds the sql info the hourly display -->
        <?php $time = 7; ?>
        <tbody id="tbodyid" class="row_position">
            <?php                
               $counter = 0;
               foreach($stopArray as $stop){ ?>
                    <td> <?php echo $stop['stops']; ?>
                    <?php    
                    for($i=7;$i<24;$i=$i+1){ ?>
                        <td> <?php echo 0 + $allLeft[$counter][$i] ?> </td>
                               
                            <?php 
                        }
                        if($time == 12){
                            $time = 1;
                        }else{
                            $time = $time + 1;
                        }
                        
                        $counter = $counter+1;
                        
                        ?>


                    <!-- <td style="display:none;"><?php //echo $log['id']; ?></td> -->
                </tr>
                        <?php 
                 
                
             } 

             ?>
        </tbody>
    </table>
    <!-- ends sql info -->

    <script>

        $('#datepickerHourly').datepicker();

    </script>



</body>

<script>
    $(document).ready(function () {

function exportTableToCSV($table, filename) {

    var $rows = $table.find('tr:has(td),tr:has(th)'),

        // Temporary delimiter characters unlikely to be typed by keyboard
        // This is to avoid accidentally splitting the actual contents
        tmpColDelim = String.fromCharCode(11), // vertical tab character
        tmpRowDelim = String.fromCharCode(0), // null character

        // actual delimiter characters for CSV format
        colDelim = '","',
        rowDelim = '"\r\n"',

        // Grab text from table into CSV formatted string
        csv = '"' + $rows.map(function (i, row) {
            var $row = $(row), $cols = $row.find('td,th');

            return $cols.map(function (j, col) {
                var $col = $(col), text = $col.text();

                return text.replace(/"/g, '""'); // escape double quotes

            }).get().join(tmpColDelim);

        }).get().join(tmpRowDelim)
            .split(tmpRowDelim).join(rowDelim)
            .split(tmpColDelim).join(colDelim) + '"',

        

        // Data URI
        csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);
        
        console.log(csv);
        
        if (window.navigator.msSaveBlob) { // IE 10+
            //alert('IE' + csv);
            window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "export.csv")
        } 
        else {
            $(this).attr({ 'download': filename, 'href': csvData, 'target': '_blank' }); 
        }
}

// This must be a hyperlink
$("#xx").on('click', function (event) {
    
    exportTableToCSV.apply(this, [$('#editable_table'), "<?php echo strval($entryDate) ?>" + ".csv"]);
    
    // IF CSV, don't do event.preventDefault() or return false
    // We actually need this to be a typical hyperlink
});

});
</script>



</HTML>

<?php require '../themepart/footer.php'; ?>