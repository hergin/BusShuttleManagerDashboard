<?php
//require_once('../ulogin/config/all.inc.php');
//require_once('../ulogin/main.inc.php');

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


require_once(dirname(__FILE__) . '/../DataLink/AccessLayer.php');

$_SESSION["Title"]="Stops";

$input = "";
$results;

makeList($results);


// If post occurs
if (isset($_POST['SubmitButton'])) {
    $input = $_POST['inputText'];
    if ($input != '') {
        postLoop($input);
    }
    header('Location: Stops.php');
}

function makeList(&$results)
{
    $AccessLayer = new AccessLayer();
    $results = $AccessLayer->get_stops(); 
}

function postLoop($stopName)
{
    $AccessLayer = new AccessLayer();
    $AccessLayer->add_stop($stopName);
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
            <p>
                <h3>Create a New Stop<h3>
            </p>
        </div>
        <br>
        <div class="d-flex justify-content-center">
            <div class="form-group">
                <form class="needs-validation" novalidate action="" method="post">
                    <div class="form-row align-items-center">
                        <div class="col-auto">
                            <label class="sr-only" for="inlineFormInput">Stop Name</label>
                            <input type="text" input="text" class="form-control mb-2" name='inputText' id="inlineFormInput" placeholder="enter stop name" required>
                        </div>
                        <div class="col-auto">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="SubmitButton" class="btn btn-dark form-control mb-2">Create</button>
                        </div>
                    </div>
                </form>



                <div class="d-flex justify-content-center">
                </div>
                <br>
                <div class="d-flex justify-content-center">
                </div>
            </div>
        </div>
    </div>

    <table id="editable_table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Stop</th>
            </tr>
        </thead>
        <tbody class="row_position">
            <?php foreach ($results as $stop) : ?>
                <tr id="<?php echo $stop->id ?>">
                    <td><?php echo $stop->stops; ?></td>
                    <td style="display:none;"><?php echo $stop->id; ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    </div>
</body>

<script>
    $(document).ready(function() {
        $('#editable_table').Tabledit({
            url: '../Actions/actionStops.php',
            hideIdentifier: true,
            buttons: {
        confirm: {
            class: 'btn btn-lg btn-danger',
            html: 'This stop will be removed from </br>  all routes after deletion. </br> Click here to proceed'
        }
    },
            columns: {
                identifier: [1, 'id'],
                editable: [
                    [0, 'stop']
                ]
            }
        });

    });

</script>


</HTML>
<?php require '../themepart/footer.php'; ?>
