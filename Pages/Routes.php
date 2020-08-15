<?php
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

require_once(dirname(__FILE__) . '/../DataLink/AccessLayer.php');

$_SESSION["Title"] = "Routes";

$stopNames;
$input = "";
$loopDropdown;
$stopDropdown;
$loopStopDropdown;
$loopName;
$results;
$afterStop;

$AccessLayer = new AccessLayer();
$loopDropdown = $AccessLayer->get_loops();
$stopDropdown = $AccessLayer->get_stops();


if (isset($_SESSION['loopInput'])) {
    $stopNames = array();
    makeList($stopNames, $_SESSION['loopInput']);
    $loopStopDropdown = $AccessLayer->get_stop_id_and_displayOrder_by_displayOrder($_SESSION['loopInput']);
}
if (isset($_POST['loop'])) {
    $_SESSION['loopInput'] = $_POST['loop'];
    if ($_SESSION['loopInput'] != '') {
        $_SESSION['savedLoopValue'] = '';
        $loopName = $AccessLayer->get_distinct_loops_in_stoploop_and_loops($_SESSION['loopInput']);
        $loopStopDropdown = $AccessLayer->get_stop_id_and_displayOrder_by_displayOrder($_SESSION['loopInput']);
        makeList($stopNames, $_SESSION['loopInput']);
    }
}

// If post occurs
if (isset($_POST['SubmitButton'])) {
    $input = $_POST['stopToAdd'];
    $afterStop = $_POST['afterStop'];
    //echo $afterStop;
    if ($input != '' && $_SESSION['loopInput'] != '') {
        addRoute($input, $_SESSION['loopInput'], $afterStop);
        $_SESSION['savedLoopValue'] = $_SESSION['loopInput'];
    }
    header('Location: Routes.php');
}


function makeList(&$stopNames, $input)
{
    $AccessLayer = new AccessLayer();
    $stopNames = $AccessLayer->get_stop_id_and_displayOrder_by_displayOrder($input);
}

function addRoute($stopID, $loopID, $afterStop)
{
    $AccessLayer = new AccessLayer();
    $AccessLayer->add_route($stopID, $loopID, $afterStop);
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

<body>
    <div align="center">

        <div class="d-flex justify-content-center">
            <div class="form-group">
                <form class="needs-validation" novalidate action="" method="post">
                    <div class="form-row align-items-center">
                        <div class="col-auto">
                            <label class="sr-only" for="inlineFormInput">Stop Name</label>

                        </div>
                    </div>
                </form>
                <div class="d-flex justify-content-center">

                </div>
                <br>
                <div class="d-flex justify-content-center">
                    <div class="form-group">
                        <form action="" method="post">
                            <div class="form-row align-items-center">
                                <div class="col-auto">

                                </div>
                                <div class="col-auto">
                                    
                                        <h3>Display by <select onchange="this.form.submit()" class="mb-2" name="loop" id="loop">
                                                <option selected="selected">Select a Loop</option>
                                                <?php
                                                foreach ($loopDropdown as $name) { ?>
                                                    <option name="loop" value="<?= $name->id ?>"><?= $name->loops ?>
                                                    </option>
                                                <?php
                                                } ?>
                                            </select>
                                            <h3>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center">


            <form action="" method="post">
                
            <h3 style="display:none;" id="assignmentOptions">Assign <select class="" name="stopToAdd" id="stopToAdd" required>
                    <option selected="selected">Select a Stop</option>
                    <?php
                    foreach ($stopDropdown as $name) { ?>
                        <option name="stopToAdd" value="<?= $name->id ?>"><?= $name->stops ?>
                        </option>
                    <?php
                    } ?>
                </select>
                 <select class="" name="afterStop" id="afterStop" required>
                    <option selected value="none">Before All</option>
                    <?php
                    foreach ($loopStopDropdown as $name) { ?>
                        <option name="afterStop" value="<?= $name->displayOrder ?>">after <?= $name->stops ?>
                        </option>
                    <?php
                    } ?>
                </select>
                <button type="submit" name="SubmitButton" class="btn btn-dark">Assign</button>
            </h3>

                
        </div>
        </form>
        

    </div>

    <table id="editable_table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th><?php if (isset($loopName[0])) {
                        echo $loopName[0]->loops;
                        $_SESSION['loopString'] = $loopName[0]->loops;
                    } else {
                        if (isset($_SESSION['loopString'])){echo $_SESSION['loopString'];}
                    } ?></th>
                <th style="display:none;"></th>
                <th>Stop</th>
            </tr>
        </thead>
        <tbody class="row_position">
            <?php if (isset($stopNames)) {
                foreach ($stopNames as $log) : ?>
                    <tr class="" id="<?php echo $log->id; ?>">
                        <td width="20%">
                        <a href="#!" class="up btn btn-dark">Move Up</a>
                        <a href="#!" class="down btn btn-dark">Move Down</a>
                        </td>
                        <td width="80%" style="display:none;"><?php if ($log->displayOrder == "0") {
                                                        echo "Click and Drag To Set Position";
                                                    } else {
                                                        echo $log->displayOrder;
                                                    } ?></td>
                        <td><?php echo $log->stops; ?></td>
                        <td style="display:none;"><?php echo $log->id; ?></td>
                        <td style="display:none;"><?php echo $log->route_id; ?></td>
                        <td style="display:none;"><?php echo $log->loop; ?></td>
                        <td style="display:none;"><?php echo $log->route_id; ?></td>
                        <td style="display:none;"><?php echo $log->stopDeletion; ?></td>

                    </tr>
                <?php endforeach;
            } ?>
        </tbody>
    </table>
    </div>
</body>

<script>
    $(document).ready(function() {
        $('#editable_table').Tabledit({
            url: '../Actions/actionRoutes.php',
            hideIdentifier: true,
            editButton: false,
            columns: {
                identifier: [4, 'id'],
                editable: []
            }
        });

        var selectedData = new Array();
        $('.row_position>tr').each(function() {
            var id = $(this).find("td").eq(3).html();
            var loopID = $(this).find("td").eq(5).html();
            var routeID = $(this).find("td").eq(6).html();
            var isDeleted = $(this).find("td").eq(7).html();
            selectedData.push([$.trim(id), $.trim(loopID), $.trim(routeID), $.trim(isDeleted)]);
        });
        console.log(selectedData);

            $('#assignmentOptions').show();

        updateOrder(selectedData);

        $(".up,.down").click(function() {
            var row = $(this).parents("tr:first");
            if ($(this).is(".up")) {
                row.insertBefore(row.prev()).hide().show('slow');
                row.attr('class', 'table-success');
                var selectedData = new Array();
                $('.row_position>tr').each(function() {
                    var id = $(this).find("td").eq(3).html();
                    var loopID = $(this).find("td").eq(5).html();
                    var routeID = $(this).find("td").eq(6).html();
                    var isDeleted = $(this).find("td").eq(7).html();
                    selectedData.push([$.trim(id), $.trim(loopID), $.trim(routeID), $.trim(isDeleted)]);
                });
                console.log(selectedData);
                updateOrder(selectedData);
            } else {
                row.insertAfter(row.next()).hide().show('slow');
                row.attr('class', 'table-success');
                var selectedData = new Array();
                $('.row_position>tr').each(function() {
                    var id = $(this).find("td").eq(3).html();
                    var loopID = $(this).find("td").eq(5).html();
                    var routeID = $(this).find("td").eq(6).html();
                    var isDeleted = $(this).find("td").eq(7).html();
                    selectedData.push([$.trim(id), $.trim(loopID), $.trim(routeID), $.trim(isDeleted)]);
                });
                console.log(selectedData);
                updateOrder(selectedData);
            }
        });
    });

    function updateOrder(data) {

        $.ajax({
            url: "../Actions/reOrderStops.php",
            type: 'post',
            data: {
                position: data
            },
            success: function() {
                //location.reload()
            }
        })
    }
</script>


</HTML>
<?php require '../themepart/footer.php'; ?>