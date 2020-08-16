<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
/**
 * Returns a list of Inspection Items for populating the list on our driver interface.
 */
//require '../Database/connect.php';
require_once(dirname(__FILE__) . '/../DataLink/AccessLayer.php');

//$input = filter_input_array(INPUT_POST);
$checkbox = json_decode(file_get_contents("php://input"), true);


function updateCheckbox ($input)
{

$InspectionItemID = $input['id'];
$InspectionItemType = $input['type'];
$InspectionItemCheck = intval($input['checked']);

$AccessLayer = new AccessLayer();

if($InspectionItemType === 'pre') {
    $AccessLayer->update_pre_checkbox($InspectionItemID,$InspectionItemCheck);
    return true;
}
if($InspectionItemType === 'post') {
    $AccessLayer->update_post_checkbox($InspectionItemID,$InspectionItemCheck);
    return true;
}
return false;
}

if(updateCheckbox($checkbox)){
    http_response_code(200);
    echo json_encode(array("message" => "Success! Inspection Item has been added."));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Inspection Item has been removed."));
}

?>