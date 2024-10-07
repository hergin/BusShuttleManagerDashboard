<?php
//require_once('../ulogin/config/all.inc.php');
//require_once('../ulogin/main.inc.php');
//
//if (!sses_running())
//	sses_start();

//function isAppLoggedIn(){
//	return isset($_SESSION['uid']) && isset($_SESSION['username']) && isset($_SESSION['loggedIn']) && ($_SESSION['loggedIn']===true);
//}



//if (!isAppLoggedIn()) {
//    header("Location: ../index.php"); /* Redirect browser */
//   exit();
//} 

session_start();
if (!isset($_SESSION['authenticated'])) {
    header('Location: ../index.php');
    exit;
}


require '../Database/connect.php';

require_once(dirname(__FILE__) . '/../DataLink/AccessLayer.php');

$_SESSION["Title"] = "Inspection Reports";


$inspection_report;
$input = "";
$loopDropdown = array();
$loop = "";
$buttonState = "disabled";


$AccessLayer = new AccessLayer();
$loopDropdown = $AccessLayer->get_loops();


// If Submit is Clicked
if (isset($_POST['SubmitButton'])) {
    $input = $_POST['loop'];
    $dateInput = $_POST['dateInput'];
    if ($input != '' && $dateInput != '') {

        $newDate = date("Y-m-d", strtotime($dateInput));
        makeList($inspection_report, $newDate, $input);
    }
    // header('Location: Entries.php');

}

function makeList(&$inspection_report, $date, $loopID)
{
    $AccessLayer = new AccessLayer();
    $inspection_report = $AccessLayer->get_inspection_reports_by_date_and_loopID($date, $loopID);
}

function parse_Inspection_Items($s){
    $AccessLayer = new AccessLayer();
    $str_arr =  explode(",", $s);  
    $new_arr=[];
    
    foreach($str_arr as $value){
        $name = $AccessLayer->get_inspection_items_name($value);
        array_push($new_arr, $name);
    }
    $List = implode(', ', $new_arr);
    
    return $List;
}

?>
<?php
require '../themepart/resources.php';
require '../themepart/sidebar.php';
require '../themepart/pageContentHolder.php';
?>


<HTML LANG="EN">

<HEAD>


</HEAD>

<form method=post>

</form>




<body>
<div class="d-flex justify-content-center">
    <form action="" method="post">
        <div class="form-row align-items-center">
            <div class="col-auto">
                <input class="form-control mb-2" input="text" name="dateInput" id="datepicker" width="276" placeholder="Click to Select Date" required />
            </div>
            <div class="col-auto">
                <select class="form-control mb-2" name="loop" id="loop">
                    <option selected="selected">Select a Loop</option>
                    <?php
                    foreach ($loopDropdown as $loop) { ?>
                        <option name="loop" value="<?= $loop->id ?>"><?= $loop->loops ?>
                        </option>
                    <?php
                    } ?>
                </select>
            </div>
            <div id='submitElement' class="col-auto">
                <button type="submit" id='submitButton' name="SubmitButton" class="btn btn-dark mb-2" <?php echo $buttonState; ?> >Search</button>
                <a href="#" id="xx" class="btn btn-dark mb-2">Export</a>
                <span id="loadingMessage"> loading...</span>
            </div>
        </div>
    </form>
</div>
    <table id="editable_table"  class="table table-bordered table-striped">
        <thead>
            <tr>
            <th>Driver Name</th>
            <th>Loop</th>
            <th>Bus</th>
            <th>Time</th>
            <th>Date</th>
            <th>Beginning Hours</th>
            <th>Ending Hours</th>
            <th>Starting Mileage</th>
            <th>Ending Mileage</th>
            <th>Pre Inspection</th>
            <th>Post Inspection</th>
            <th>Pre Inspection Comments</th>
            <th>Post Inspection Comments</th> 
            </tr>
        </thead>
        <tbody id="loadingTable" style="display:none" class="row_position">
            <?php if(isset($inspection_report)){ foreach ($inspection_report as $report) :?>
                <tr id="<?php echo $report->id ?>">
                    <td><?php echo $AccessLayer->get_user_name($report->driver)[0]->firstname . " " . $AccessLayer->get_user_name($report->driver)[0]->lastname ; ?></td>
                    <td><?php echo $AccessLayer->get_loop_name($report->loop)[0]->loops; ?></td>
                    <td><?php echo $AccessLayer->get_bus_name($report->bus_identifier)[0]->busIdentifier; ?></td>
                    <td><?php echo $report->t_stamp; ?></td>
                    <td><?php echo $report->date_added; ?></td>
                    <td><?php echo $report->beginning_hours; ?></td>
                    <td><?php echo $report->ending_hours; ?></td>
                    <td><?php echo $report->starting_mileage; ?></td>
                    <td><?php echo $report->ending_mileage; ?></td>
                    <td><?php echo parse_Inspection_Items($report->pre_trip_inspection); ?></td>
                    <td><?php echo parse_Inspection_Items($report->post_trip_inspection); ?></td>
                    <td><?php echo $report->pre_comment; ?></td>
                    <td><?php echo $report->post_comment; ?></td>
                    <td style="display:none;"><?php echo $report->id; ?></td>
                    
                </tr>
            <?php endforeach ;} ?>
        </tbody>
    </table>
    
    <script>
        $('#datepickerHourly').datepicker();
    </script>



</body>
<script>
    $('#datepicker').datepicker();
</script>
<script>
    $(document).ready(function() {
        
        /*
        $('#editable_table').Tabledit({
            url: '../Actions/actionEntries.php',
            hideIdentifier: true,
            deleteButton: false,
            editButton: false,
            columns: {
                identifier: [8, 'id'],
                editable: [
                    [0, 'boarded'],
                    [1, 'leftBehind']
                ]
            }
        });
        */
        $('#loadingTable').removeAttr("style");
        $('#loadingMessage').hide();
        $('#submitButton').prop('disabled', false)
    });
</script>

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
            window.navigator.msSaveOrOpenBlob(new Blob([csv], {type: "text/plain;charset=utf-8;"}), "csvname.csv")
        } 
        else {
            $(this).attr({ 'download': filename, 'href': csvData, 'target': '_blank' }); 
        }
}

// This must be a hyperlink
$("#xx").on('click', function (event) {
    
    exportTableToCSV.apply(this, [$('#editable_table'), "InspectionReport_<?php echo strval($_POST['dateInput']) ?>" + ".csv"]);
    
    // We actually need this to be a typical hyperlink
});

});
</script>


</HTML>

<?php require '../themepart/footer.php'; ?>
