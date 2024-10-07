<?php
function appLogout()
{
    // When a user explicitly logs out you'll definetely want to disable
    // autologin for the same user. For demonstration purposes,
    // we don't do that here so that the autologin function remains
    // easy to test.
    //$ulogin->SetAutologin($_SESSION['username'], false);

    unset($_SESSION['uid']);
    unset($_SESSION['username']);
    unset($_SESSION['loggedIn']);
}
?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->



    <link rel="stylesheet" href="style.css">
    <script src="jquery.tabledit.min.js"></script>
    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar Holder -->


        <nav id="sidebar">
            <div class="sidebar-header">
                <h3 style="text-align: center">Management Dashboard</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="active">
                    <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Manage Data</a>
                    <ul class="collapse list-unstyled" id="homeSubmenu">
                        <li>
                            <a class="disabled" href="../Pages/Entries.php">Entries</a>
                        </li>
                        <li>
                            <a class="disabled" href="../Pages/Users.php">Drivers</a>
                        </li>
                        <li>
                            <a href="../Pages/Loops.php">Loops</a>
                        </li>
                        <li>
                            <a href="../Pages/Stops.php">Stops</a>
                        </li>
                        <li>
                            <a href="../Pages/Routes.php">Routes</a>
                        </li>
                        <li>
                            <a href="../Pages/Buses.php">Buses</a>
                        </li>
                        <!--<li>
                            <a href="../Pages/Inspection_items.php">Inspection Items</a>
                        </li>-->
                    </ul>
                    <a href="#reportSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">View Reports</a>
                    <ul class="collapse list-unstyled" id="reportSubmenu">
                        <li>
                            <a href="../Pages/EntryReports.php">Loop Reports</a>
                        </li>
                        <li>
                            <a href="../Pages/StopReports.php">Stop Reports</a>
                        </li>
                        <li>
                            <a href="../Pages/BusReports.php">Bus Reports</a>
                        </li>

                        <li>
                            <a href="../Pages/LeftBehindStopReport.php">Left Behind Stop Reports</a>
                        </li>
                        <li>
                            <a href="../Pages/LeftBehindEntryReports.php">Left Behind Loop Reports</a>
                        </li>
                        <li>
                            <a href="../Pages/LeftBehindBusReports.php">Left Behind Bus Reports</a>
                        </li>
                        <!--
                        <li>
                            <a href="../Pages/InspectionReports.php">Inspection Reports</a>
                        </li>-->
                    </ul>
                </li>
            </ul>

            <ul class="list-unstyled CTAs">
                <li>
                    <a class="btn btn-block btn-dark"  href="../logout.php">Log out</a>
                </li>
            </ul>
        </nav>

        <div style="min-height: 100%;">
            <button style="background:#BA0C2F;display: inline;" type="button" id="sidebarCollapse" class="navbar-btn" title="Toggle Sidebar">

                <span style="background:white;"></span>
                <span style="background:white;"></span>
                <span style="background:white;"></span>
            </button>

        </div>
