<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
require_once __DIR__ . "/class/getjson.class.php";
$objGetJSON = new GetJSON();
if (!file_exists(_G_TEMP_DIR)) {
	mkdir(_G_TEMP_DIR);
}
$v_action = $_REQUEST['action'];

if($v_action == "receivePost") {

    $objJsonContent = json_decode(file_get_contents('php://input'), true);
    $objExtract = $objGetJSON -> doExtract($objJsonContent, _G_MY_API, $g_dictParameterStepAPI);
    if($objExtract[0]) {
        echo json_encode($objExtract[1]);
    } else {
        echo "<div style='color:red;'>" . $objExtract[1] . "</div>";
    }

} else if ($v_action == "getResultAPI") {
    //http://localhost/TransCa98277/webservice.php?action=getResultAPI&api=2&recur=n
    $v_api = $_REQUEST['api'];
    $arrFilter = json_decode($_REQUEST['filter'], TRUE);
    if(!in_array($v_api, unserialize(_G_API_NO))) {
        echo "<div style='color:red;'>API number incorrect ! API number should be " . implode(" or ", unserialize(_G_API_NO)) . ".</div>";
        die;
    }
    $doRecursive = TRUE;
    if(strtolower($_REQUEST['recur']) != "y") {
        $doRecursive = FALSE;
    }
    
    $objResultAPI = $objGetJSON -> getResultAPI($v_api, $doRecursive, $arrFilter);
    if(!$objResultAPI[0]) {
        echo "<div style='color:red;'>" . $objResultAPI[2] . "</div>";
        die;
    }
    echo json_encode($objResultAPI[1]);

} else if ($v_action == "getRecallSummary") {
    
    $recallNumber = $_REQUEST['recallNumber'];
    $resRecallSummary = $objGetJSON -> getRecallSummary($recallNumber);
    echo $resRecallSummary;

} else {
    if(empty($v_action)) {
        echo "<div style='color:red;'>Action is NULL !</div>";
    } else {
        echo "<div style='color:red;'>Action error : " . $v_action . " !</div>";
    }
    die;
}
?>