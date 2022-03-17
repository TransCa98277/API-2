<?php
DEFINE("_G_BDRV_URL", "https://data.tc.gc.ca/v1.3/api/eng/vehicle-recall-database/recall-summary/recall-number/[recall-number]?format=json");
DEFINE("_G_TEMP_DIR", __DIR__ . "/../tmp/");
DEFINE("_G_JSON_DIR", __DIR__ . "/../json/");

$g_dictParameterStepAPI = array();
$g_dictParameterStepAPI["1"][] = "MANUFACTURER_RECALL_NO_TXT";
$g_dictParameterStepAPI["2"][] = "CATEGORY_ETXT";
$g_dictParameterStepAPI["2"][] = "CATEGORY_FTXT";
$g_dictParameterStepAPI["3"][] = "SYSTEM_TYPE_ETXT";
$g_dictParameterStepAPI["3"][] = "SYSTEM_TYPE_FTXT";
$g_dictParameterStepAPI["4"][] = "NOTIFICATION_TYPE_ETXT";
$g_dictParameterStepAPI["4"][] = "NOTIFICATION_TYPE_FTXT";

DEFINE("_G_API_NO", serialize(array("0", "1", "2", "3", "4")));
DEFINE("_G_MY_API", "2");
DEFINE("_G_START_FILE_NAME", "CSCompVehicleRecallStart.json");
?>