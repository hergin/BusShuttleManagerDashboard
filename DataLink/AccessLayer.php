<?php

require_once(dirname(__FILE__) . '/../config.php');

include_once('../Model/User.php');

class AccessLayer
{
  public $drivers = [];
  public $stops = [];
  public $loops = [];
  public $buses = [];
  public $Inspection_items = [];

  public function __construct()
  { }

  public function dbconnect()
  {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DBNAME)
      or die("<br/>Coule not connect to MYSQL Server");

    // mysqli_select_db($conn, DB_DBNAME)
    //     or die("<br/>Could not select the indicated database");

    return $conn;
  }

  // CUSTOM METHODS HERE

  // Users
  public function get_user_as_User_Object($id)
  {
    // Reuse existing query
    $sql = "SELECT * FROM users WHERE id='$id'";
    $results = $this->query($sql);

    // check for results
    if (!$results) {
      return $results;
    } else {
      // array to hold User objects
      $object_results = array();
      // cycle through and convert to User objects
      foreach ($results as $result) {
        $object_results[] = new User($result);
      }
      // return array of User objects
      return $object_results;
    }
  }

  public function get_all_users_as_User_Objects()
  {
    // Reuse existing query
    $sql = "SELECT * FROM users WHERE is_deleted='0' ORDER BY lastname ASC";
    $results = $this->query($sql);

    // check for results
    if (!$results) {
      return $results;
    } else {
      // array to hold User objects
      $object_results = array();
      // cycle through and convert to User objects
      foreach ($results as $result) {
        $object_results[] = new User($result);
      }
      // return array of User objects
      return $object_results;
    }
  }

  public function update_user($userID, $firstname, $lastname)
  {
    $sql = "UPDATE users SET firstname='$firstname', lastname='$lastname' WHERE id='$userID'";
    $results = $this->query($sql);
  }

  public function remove_user($userID)
  {
    $sql = "UPDATE users SET is_deleted=1 WHERE id='$userID'";
    $results = $this->query($sql);
  }

  public function get_user_name($userID){
    if(array_key_exists($userID,$this->drivers)) {
      return $this->drivers[$userID];
    }
    $sql = sprintf("SELECT firstname,lastname FROM users WHERE id=$userID");
    $this->drivers[$userID] = $this->query($sql);
    return $this->drivers[$userID];
  }

  // Loops
  public function get_loops()
  {
    $sql = sprintf("SELECT * FROM loops WHERE is_deleted='0' ORDER BY loops ASC");
    return $this->query($sql);
  }

  public function add_loop($loopName)
  {
    $sql = sprintf("INSERT INTO `loops`(`loops`) VALUES ( '$loopName' )");
    $results = $this->query($sql);
  }

  public function remove_loop($loopID)
  {
    $sql = sprintf("UPDATE loops SET is_deleted=1 WHERE id='$loopID'");
    $this->query($sql);
    $sql = sprintf("UPDATE stop_loop SET is_deleted=1 WHERE `loop`='$loopID'");
    $this->query($sql);
  }

  public function restore_loop($loopID)
  {
    $sql = sprintf("UPDATE loops SET is_deleted=0 WHERE id='$loopID'");
    $this->query($sql);
    $sql = sprintf("UPDATE stop_loop SET is_deleted=0 WHERE `loop`='$loopID'");
    $this->query($sql);
  }

  public function update_loop($loopID, $loopName)
  {
    $sql = sprintf("UPDATE loops SET loops='$loopName'WHERE id='$loopID'");
    $results = $this->query($sql);
  }

  public function get_loop_name($loopID){
    if(array_key_exists($loopID,$this->loops)) {
      return $this->loops[$loopID];
    }
    $sql = sprintf("SELECT loops FROM loops WHERE is_deleted='0' AND id=$loopID");
    $this->loops[$loopID] = $this->query($sql);
    return $this->loops[$loopID];
  }

  // Stops
  public function get_stops()
  {
    $sql = sprintf("SELECT * FROM stops WHERE is_deleted='0' ORDER BY stops ASC");
    return $this->query($sql);
  }

  public function get_stops_by_loop($loopID){
    $sql = sprintf("SELECT DISTINCT `stops`.`stops`, `stop_loop`.`displayOrder`, `stop_loop`.`loop` FROM `stops` 
    INNER JOIN `stop_loop` ON `stop_loop`.`stop` = `stops`.`id` AND `stop_loop`.`loop` = '$loopID'");
    return $this->query($sql);
  }

  public function add_stop($stopName)
  {
    $sql = sprintf("INSERT INTO `stops`(`stops`) VALUES ( '$stopName' )");
    $this->query($sql);
  }

  public function remove_stop($stopID)
  {
    $sql = sprintf("UPDATE stops SET is_deleted=1 WHERE id='$stopID'");
    $this->query($sql);
    $sql = sprintf("UPDATE stop_loop SET is_deleted=1 WHERE `stop`='$stopID'");
    $this->query($sql);
  }


  public function restore_stop($stopID)
  {
    $sql = sprintf("UPDATE stops SET is_deleted=0 WHERE id='$stopID'");
    $this->query($sql);
    $sql = sprintf("UPDATE stop_loop SET is_deleted=0 WHERE `stop`='$stopID'");
    $this->query($sql);
  }

  public function update_stop($stopID, $stopName)
  {
    $sql = sprintf("UPDATE stops SET stops='$stopName'WHERE id='$stopID'");
    $this->query($sql);
  }

  public function get_stop_name($stopID) {
    if(array_key_exists($stopID,$this->stops)) {
      return $this->stops[$stopID];
    }
    $sql = sprintf("SELECT stops FROM stops WHERE is_deleted='0' AND id=$stopID");
    $this->stops[$stopID] = $this->query($sql);
    return $this->stops[$stopID];
  }

  // Buses
  public function get_buses()
  {
    $sql = sprintf("SELECT * FROM buses WHERE is_deleted='0' ORDER BY busIdentifier ASC");
    return $this->query($sql);
  }

  public function add_bus($busName)
  {
    $sql = sprintf("INSERT INTO `buses`(`busIdentifier`) VALUES ( '$busName' )");
    $results = $this->query($sql);
  }

  public function remove_bus($busID)
  {
    $sql = sprintf("UPDATE buses SET is_deleted=1 WHERE id='$busID'");
    $results = $this->query($sql);
  }

  public function update_bus($busID, $busName)
  {
    $sql = sprintf("UPDATE buses SET busIdentifier='$busName'WHERE id='$busID'");
    $results = $this->query($sql);
  }

  public function get_bus_name($busID) {
    if(array_key_exists($busID,$this->buses)) {
      return $this->buses[$busID];
    }
    $sql = sprintf("SELECT busIdentifier FROM buses WHERE is_deleted='0' AND id=$busID");
    $this->buses[$busID] = $this->query($sql);
    return $this->buses[$busID];
  }

  //inspection_items_list
  public function get_inspection_items()
  {
    $sql = sprintf("SELECT * FROM inspection_items_list WHERE is_deleted='0' ORDER BY inspection_item_name ASC");
    return $this->query($sql);
  }

  public function add_inspection_items($InspectionItemsName, $Pre, $Post)
  {
    $sql = sprintf("INSERT INTO `inspection_items_list`(`inspection_item_name`, `pre_trip_inspection`, `post_trip_inspection`) VALUES ( '$InspectionItemsName', $Pre, $Post)");
    $results = $this->query($sql);
  }

  public function remove_inspection_items($InspectionItemID)
  {
    $sql = sprintf("UPDATE inspection_items_list SET is_deleted=1 WHERE id='$InspectionItemID'");
    $results = $this->query($sql);
  }

  public function update_inspection_items($InspectionItemID, $InspectionItemsName)
  {
    $sql = sprintf("UPDATE inspection_items_list SET inspection_item_name='$InspectionItemsName' WHERE id='$InspectionItemID'");
    $results = $this->query($sql);
  }

// there is bug in MySQL Bug #80933, bit value of 0 does not update unless used the method below.
  public function update_pre_checkbox ($InspectionItemID, $pre_item)
  {
    $sql = sprintf("UPDATE inspection_items_list SET pre_trip_inspection = $pre_item WHERE id='$InspectionItemID'");    
    $results = $this->query($sql); 
  }

  public function update_post_checkbox ($InspectionItemID, $post_item)
  {
    $sql = sprintf("UPDATE inspection_items_list SET post_trip_inspection = $post_item WHERE id='$InspectionItemID'");
    $results = $this->query($sql); 
  }

  
  


  public function get_inspection_items_name($InspectionItemID) {
    if(array_key_exists($InspectionItemID,$this->Inspection_items)) {    
      return $this->Inspection_items[$InspectionItemID];
    }

    $sql = sprintf("SELECT inspection_item_name FROM inspection_items_list WHERE is_deleted='0' AND id=$InspectionItemID");
    $result = $this->query($sql);
    $this->Inspection_items[$InspectionItemID] = $result[0]->inspection_item_name;
    return $this->Inspection_items[$InspectionItemID];
  }

  // inspection reports
  public function get_inspection_reports_by_date_and_loopID($dateAdded, $loopID) {
    $sql = sprintf("SELECT * FROM `inspection_report` WHERE `date_added`='$dateAdded' AND `loop`= '$loopID'  ORDER BY `t_stamp` DESC");
    $aux_result = $this->query($sql);
    $result = $this->remove_duplicate_inspection_reports($aux_result); // ALTERNATIVE in PHP code
    return $result;
  }

  public function remove_duplicate_inspection_reports($inspection_report) {
    $result = array();

    foreach($inspection_report as $report) {
      $found = false;
      foreach($result as $new_report) {
        if($report->driver==$new_report->driver
          && $report->pre_trip_inspection==$new_report->pre_trip_inspection
          && $report->post_trip_inspection==$new_report->post_trip_inspection
          && $report->beginning_hours==$new_report->beginning_hours
          && $report->ending_hours==$new_report->ending_hours
          && $report->starting_mileage==$new_report->starting_mileage
          && $report->ending_mileage==$new_report->ending_mileage
          && $report->t_stamp==$new_report->t_stamp
          && $report->date_added==$new_report->date_added
          && $report->loop==$new_report->loop
          && $report->bus_identifier==$new_report->bus_identifier) {
            $found = true;
          }
      }
      if($found==false) {
        $result[] = $report;
      }
    }

    return $result;
  }

  // Entries
  public function get_entries_by_date_and_loopID($dateAdded, $loopID) {
    $sql = sprintf("SELECT * FROM `entries` WHERE `date_added`='$dateAdded' AND `loop`= '$loopID'  ORDER BY `t_stamp` DESC");
    $aux_result = $this->query($sql);
    $result = $this->remove_duplicate_entries($aux_result); // ALTERNATIVE in PHP code
    return $result;
  }

  public function get_entries_by_date($dateAdded) {
    $sql = sprintf("SELECT * FROM `entries` WHERE `date_added`='$dateAdded' ORDER BY `t_stamp` DESC");
    $aux_result = $this->query($sql);
    $result = $this->remove_duplicate_entries($aux_result); // ALTERNATIVE in PHP code
    return $result;
  }

  // ALTERNATIVE eliminate duplicate entries here - TEMPORARY SOLUTION FOR ISSUE #18 in driver app
  // duplicate entry looks like everything is same except the IDs
  public function remove_duplicate_entries($entries) {
    $result = array();

    foreach($entries as $entry) {
      $found = false;
      foreach($result as $new_entry) {
        if($entry->boarded==$new_entry->boarded
          && $entry->left_behind==$new_entry->left_behind
          && $entry->stop==$new_entry->stop
          && $entry->t_stamp==$new_entry->t_stamp
          && $entry->date_added==$new_entry->date_added
          && $entry->loop==$new_entry->loop
          && $entry->driver==$new_entry->driver
          && $entry->bus_identifier==$new_entry->bus_identifier) {
            $found = true;
          }
      }
      if($found==false) {
        $result[] = $entry;
      }
    }

    return $result;
  }

  public function update_entries_boarded_and_leftbehind($boarded, $leftBehind, $entryID) {
    $sql = sprintf("UPDATE entries SET boarded='$boarded', left_behind='$leftBehind' WHERE id='$entryID'");
    $this->query($sql);
  } 

  // Will likely be wanted eventually so here it is. 
  public function remove_entry($entryID) {
    $sql = sprintf("UPDATE entries SET is_deleted='1' WHERE id='$entryID'");
    $this->query($sql);
  } 

  // Routes
  public function get_distinct_loops_in_stoploop_and_loops($id) {
    $sql = sprintf("SELECT DISTINCT `loops`.`loops`, `stop_loop`.`loop`
    FROM `loops` 
        LEFT JOIN `stop_loop` ON `stop_loop`.`loop` = `loops`.`id` AND stop_loop.is_deleted='0'
    WHERE `stop_loop`.`loop` = '$id' ");

    return $this->query($sql);
  }

  public function get_stop_id_and_displayOrder_by_displayOrder($loopID){
    $sql = sprintf("SELECT stops.stops, stops.id, stops.is_deleted as stopDeletion, stop_loop.displayOrder, stop_loop.loop, stop_loop.id as route_id
      FROM stops 
        inner JOIN stop_loop ON stop_loop.loop='$loopID' AND stop_loop.is_deleted='0'
      AND stop_loop.stop=stops.id AND stops.is_deleted=0 ORDER BY displayOrder");

    return $this->query($sql);
  }

  public function remove_route($routeID){
    $sql = sprintf("UPDATE stop_loop SET is_deleted=1 WHERE `id`='$routeID'");
    $this->query($sql);
  }

  public function restore_route($routeID){
    $sql = sprintf("UPDATE stop_loop SET is_deleted=0 WHERE `id`='$routeID'");
    $this->query($sql);
  }

  public function add_route($stopID, $loopID, $afterStop){
    if($afterStop === "none") {
      $sql = sprintf("INSERT INTO `stop_loop`(`stop`, `loop`, `displayOrder`) VALUES ( '$stopID','$loopID', 0 )");
      $this->query($sql);
    } else {
      $sql = sprintf("INSERT INTO `stop_loop`(`stop`, `loop`, `displayOrder`) VALUES ( '$stopID','$loopID', $afterStop )");
      $this->query($sql);
    }
    
  }


  // END CUSTOM METHODS


  // Turns each returned field name into a property of the QueryResult object. 
  private function query($sql)
  {

    $this->dbconnect();

    $conn = $this->dbconnect();

    $res = mysqli_query($conn, $sql);

    if ($res) {
      if (strpos($sql, 'SELECT') === false) {
        return true;
      }
    } else {
      if (strpos($sql, 'SELECT') === false) {
        return false;
      } else {
        return null;
      }
    }

    $results = array();

    while ($row = mysqli_fetch_array($res)) {

      $result = new QueryResult();

      foreach ($row as $k => $v) {
        $result->$k = $v;
      }

      $results[] = $result;
    }
    return $results;
  }
}

class QueryResult
{

  private $_results = array();

  public function __construct()
  { }

  public function __set($var, $val)
  {
    $this->_results[$var] = $val;
  }

  public function __get($var)
  {
    if (isset($this->_results[$var])) {
      return $this->_results[$var];
    } else {
      return null;
    }
  }
}
