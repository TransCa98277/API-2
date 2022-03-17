<?php
require_once __DIR__ . "/../config/config.php";

class GetJSON {

    private $BdrvUrl;
    
    function __construct() {
        $this -> BdrvUrl = _G_BDRV_URL;
	}

    function doExtract($objJsonContent, $v_api, $g_dictParameterStepAPI) {
        $this -> rrmdir(_G_TEMP_DIR, FALSE);
        $objJsonContentFormatter = $objJsonContent;
        $dict_recallNumber_recallSummary = array();
        foreach ($objJsonContent as $k_objJsonContent => $v_objJsonContent) {
            $objRecallSummary = $this -> getRecallSummary($v_objJsonContent['recallNumber']);   
            foreach ($objRecallSummary['ResultSet'] as $k_ResultSet => $v_ResultSet) {
                foreach ($v_ResultSet as $k_v_ResultSet => $v_v_ResultSet) {
                    if(in_array($v_v_ResultSet['Name'], $g_dictParameterStepAPI[$v_api])) {
                        $objJsonContentFormatter[$k_objJsonContent][$v_v_ResultSet['Name']] = $v_v_ResultSet['Value']['Literal'];
                    }
                }
            }
        }
        $this -> rrmdir(_G_TEMP_DIR, FALSE);
        $localFileName = "api" . $v_api . ".json";
        $localFilePath = _G_JSON_DIR . $localFileName;
        $file = fopen($localFilePath, "w");
        fwrite($file, json_encode($objJsonContentFormatter));
        fclose($file);
        return array(TRUE, $objJsonContentFormatter);
    }

    function getResultAPI($v_api, $doRecursive = TRUE, $arrFilter = NULL) {
        $localFileName = "api" . $v_api . ".json";
        if($v_api == 0) {
            $localFileName = _G_START_FILE_NAME;
            $localFilePath = _G_JSON_DIR . $localFileName;
            if (!file_exists($localFilePath)) {
                return array(FALSE, NULL, "The file " . $localFilePath . " was not found !");
            }
        }
        $localFilePath = _G_JSON_DIR . $localFileName;
        if (!file_exists($localFilePath) && $v_api > 0) {
            if(!$doRecursive) {
                return array(FALSE, NULL, "The file `" . $localFilePath . "` was not found !");
            }
        }
        while(!file_exists($localFilePath)) {
            $v_api = $v_api - 1;
            if($v_api == 0) {
                $localFileName = _G_START_FILE_NAME;
                $localFilePath = _G_JSON_DIR . $localFileName;
                break;
            }
            $localFileName = "api" . $v_api . ".json";
            $localFilePath = _G_JSON_DIR . $localFileName;
        }
        if(!file_exists($localFilePath)) {
            return array(FALSE, NULL, "The file " . $localFilePath . " was not found !");
        }
        $strJsonContent = file_get_contents($localFilePath);
        $objJsonContent = json_decode($strJsonContent, TRUE);
        //LI 2022-03-13 do filter
        $objJsonContentDoFilter = array();
        foreach ($arrFilter as $k_arrFilter => $v_arrFilter) {
            $arrTmpFilter = explode("|", $v_arrFilter);
            foreach ($objJsonContent as $k_objJsonContent => $v_objJsonContent) {
                foreach ($v_objJsonContent as $k_v_objJsonContent => $v_v_objJsonContent) {
                    if($k_v_objJsonContent == $arrTmpFilter[0]) {
                        if(stripos($v_v_objJsonContent, $arrTmpFilter[1]) !== false) {
                            $objJsonContentDoFilter[] = $v_objJsonContent;
                        }
                    }
                }
            }
        }
        if(!empty($arrFilter)) {
            if(!empty($objJsonContentDoFilter)) {
                return array(TRUE, $objJsonContentDoFilter, "The result of API " . $v_api . " was found. The Filter " . $arrTmpFilter[0] . " like " . $arrTmpFilter[1] . " has been applied but and some records have been found.");
            } else {
                return array(FALSE, NULL, "The result of API " . $v_api . " was found. The Filter " . $arrTmpFilter[0] . " like " . $arrTmpFilter[1] . " has been applied but nothing has been found.");
            }
        } else {
            return array(TRUE, $objJsonContent, "The result of API " . $v_api . " was found.");
        }
    }

    function getRecallSummary($recallNumber) {
        //LI 2022-03-11 demo d'utilisation URL API
        //https://data.tc.gc.ca/v1.3/api/eng/vehicle-recall-database/recall-summary/recall-number/1977043?format=json
        //wget --no-check-certificate -O - https://data.tc.gc.ca/v1.3/api/eng/vehicle-recall-database/recall-summary/recall-number/1977043?format=json >tmp/1977043.json

        $v_url = str_ireplace("[recall-number]", $recallNumber, $this -> BdrvUrl);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $v_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $strObjJson = curl_exec($ch);
        curl_close($ch);
        if(!$strObjJson) {
            return NULL;
        }
        return json_decode($strObjJson, TRUE);
    }

    function strEncode($str, $action) {
        if($action == "w") {
            $str = mb_convert_encoding($str, "ISO-8859-15", "UTF-8");
        }
        if($action == "r") {
            $str = mb_convert_encoding($str, "UTF-8", "ISO-8859-15");
        }
        return $str;
    }

    function rrmdir($dir, $removeSelf = TRUE) {
		if (is_dir($dir)) {
			$objects = scandir($dir); 
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") { 
					if (is_dir($dir . "/" . $object)) {
						rrmdir($dir . "/" . $object);
					}
					else {
						unlink($dir . "/" . $object);
					}
				}
			}
			if($removeSelf) {
				rmdir($dir);
			}
		}
	}
}
?>