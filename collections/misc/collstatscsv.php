<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceCollectionProfile.php');
include_once(__DIR__ . '/../../classes/OccurrenceManager.php');
ini_set('max_execution_time', 1200);

$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$collId = array_key_exists('collids',$_REQUEST)?$_REQUEST['collids']:'';
$cPartentTaxon = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']:'';
$cCountry = array_key_exists('country',$_REQUEST)?$_REQUEST['country']:'';
$years = array_key_exists('years',$_REQUEST)?(int)$_REQUEST['years']:1;

$famArr = $_SESSION['statsFamilyArr'] ?? Array();
$geoArr = $_SESSION['statsCountryArr'] ?? Array();
$ordArr = $_SESSION['statsOrderArr'] ?? Array();

$days = 365 * $years;
$months = 12 * $years;

$collManager = new OccurrenceCollectionProfile();

$fileName = '';
$outputArr = array();
$header = array();
$dataArr = array();
$headerArr = array();
$collIdArr = array();

if(is_numeric($collId)){
    $collIdArr[] = (int)$collId;
}
elseif(strpos($collId, ',') !== false){
    $collIdArr = explode(',',$collId);
}

if($action === 'Download Family Dist' || $action === 'Download Geo Dist' || $action === 'Download Order Dist'){
	$header = array('Names','SpecimenCount','GeorefCount','IDCount','IDGeorefCount','GeorefPercent','IDPercent','IDGeorefPercent');
	if($action === 'Download Family Dist'){
		$fileName = 'stats_family.csv';
		if($famArr){
			foreach($famArr as $name => $data){
				$specCnt = $data['SpecimensPerFamily'];
				$geoRefCnt = $data['GeorefSpecimensPerFamily'];
				$IDCnt = $data['IDSpecimensPerFamily'];
				$IDGeoRefCnt = $data['IDGeorefSpecimensPerFamily'];
				$geoRefPerc = ($data['GeorefSpecimensPerFamily']?round(100*($data['GeorefSpecimensPerFamily']/$data['SpecimensPerFamily'])):0);
				$IDPerc = ($data['IDSpecimensPerFamily']?round(100*($data['IDSpecimensPerFamily']/$data['SpecimensPerFamily'])):0);
				$IDgeoRefPerc = ($data['IDGeorefSpecimensPerFamily']?round(100*($data['IDGeorefSpecimensPerFamily']/$data['SpecimensPerFamily'])):0);
				$outputArr[] = array($name, $specCnt, $geoRefCnt, $IDCnt, $IDGeoRefCnt, $geoRefPerc, $IDPerc, $IDgeoRefPerc);
			}
		}
	}
    if($action === 'Download Order Dist'){
        $fileName = 'stats_order.csv';
        if($ordArr){
            foreach($ordArr as $name => $data){
                $specCnt = $data['SpecimensPerOrder'];
                $geoRefCnt = $data['GeorefSpecimensPerOrder'];
                $IDCnt = $data['IDSpecimensPerOrder'];
                $IDGeoRefCnt = $data['IDGeorefSpecimensPerOrder'];
                $geoRefPerc = ($data['GeorefSpecimensPerOrder']?round(100*($data['GeorefSpecimensPerOrder']/$data['SpecimensPerOrder'])):0);
                $IDPerc = ($data['IDSpecimensPerOrder']?round(100*($data['IDSpecimensPerOrder']/$data['SpecimensPerOrder'])):0);
                $IDgeoRefPerc = ($data['IDGeorefSpecimensPerOrder']?round(100*($data['IDGeorefSpecimensPerOrder']/$data['SpecimensPerOrder'])):0);
                $outputArr[] = array($name, $specCnt, $geoRefCnt, $IDCnt, $IDGeoRefCnt, $geoRefPerc, $IDPerc, $IDgeoRefPerc);
            }
        }
    }
	if($action === 'Download Geo Dist'){
		$fileName = 'stats_country.csv';
		if($geoArr){
			foreach($geoArr as $name => $data){
				$specCnt = $data['CountryCount'];
				$geoRefCnt = $data['GeorefSpecimensPerCountry'];
				$IDCnt = $data['IDSpecimensPerCountry'];
				$IDGeoRefCnt = $data['IDGeorefSpecimensPerCountry'];
				$geoRefPerc = ($data['GeorefSpecimensPerCountry']?round(100*($data['GeorefSpecimensPerCountry']/$data['CountryCount'])):0);
				$IDPerc = ($data['IDSpecimensPerCountry']?round(100*($data['IDSpecimensPerCountry']/$data['CountryCount'])):0);
				$IDgeoRefPerc = ($data['IDGeorefSpecimensPerCountry']?round(100*($data['IDGeorefSpecimensPerCountry']/$data['CountryCount'])):0);
				$outputArr[] = array($name, $specCnt, $geoRefCnt, $IDCnt, $IDGeoRefCnt, $geoRefPerc, $IDPerc, $IDgeoRefPerc);
			}
		}
	}
}

if($collIdArr){
    if($action === 'Download CSV'){
        $fileName = 'year_stats.csv';
        $headerArr = $collManager->getYearStatsHeaderArr($months);
        $dataArr = $collManager->getYearStatsDataArr(implode(',',$collIdArr),$days);
    }
    if($action === 'Download Stats per Coll' && (!$cPartentTaxon && !$cCountry)){
        $header = array('Collection','Specimens','Georeferenced','Imaged','Species ID','Families','Genera','Species','Total Taxa','Types');
        $fileName = 'stats_per_coll.csv';
        $resultsTemp = $collManager->runStatistics(implode(',',$collIdArr));
        if($resultsTemp){
            unset($resultsTemp['familycnt'], $resultsTemp['genuscnt'], $resultsTemp['speciescnt'], $resultsTemp['TotalTaxaCount'], $resultsTemp['TotalImageCount']);
            ksort($resultsTemp);
            $i = 0;
            foreach($resultsTemp as $k => $collArr){
                $dynPropTempArr = array();
                $outputArr[$i]['CollectionName'] = $collArr['CollectionName'];
                $outputArr[$i]['recordcnt'] = $collArr['recordcnt'];
                $outputArr[$i]['georefcnt'] = $collArr['georefcnt'];
                $outputArr[$i]['OccurrenceImageCount'] = $collArr['OccurrenceImageCount'];
                if($collArr['dynamicProperties']){
                    $dynPropTempArr = json_decode($collArr['dynamicProperties'],true);
                    if(is_array($dynPropTempArr)){
                        $outputArr[$i]['SpecimensCountID'] = $dynPropTempArr['SpecimensCountID'];
                    }
                }
                $outputArr[$i]['familycnt'] = $collArr['familycnt'];
                $outputArr[$i]['genuscnt'] = $collArr['genuscnt'];
                $outputArr[$i]['speciescnt'] = $collArr['speciescnt'];
                $outputArr[$i]['TotalTaxaCount'] = $collArr['TotalTaxaCount'];
                if($collArr['dynamicProperties'] && is_array($dynPropTempArr)) {
                    $outputArr[$i]['TypeCount'] = $dynPropTempArr['TypeCount'];
                }
                $i++;
            }
        }
    }
    if($action === 'Download Stats per Coll' && ($cPartentTaxon || $cCountry)){
        $header = array('Collection','Specimens','Georeferenced','Imaged','Species ID','Families','Genera','Species','Total Taxa','Types');
        $fileName = 'stats_per_coll.csv';
        $resultsTemp = $collManager->runStatisticsQuery(implode(',',$collIdArr),$cPartentTaxon,$cCountry);
        if($resultsTemp){
            unset($resultsTemp['families'], $resultsTemp['countries']);
            ksort($resultsTemp);
            $i = 0;
            foreach($resultsTemp as $k => $collArr){
                $dynPropTempArr = array();
                $outputArr[$i]['CollectionName'] = $collArr['CollectionName'];
                $outputArr[$i]['recordcnt'] = $collArr['recordcnt'];
                $outputArr[$i]['georefcnt'] = $collArr['georefcnt'];
                $outputArr[$i]['OccurrenceImageCount'] = $collArr['OccurrenceImageCount'];
                $outputArr[$i]['SpecimensCountID'] = $collArr['speciesID'];
                $outputArr[$i]['familycnt'] = $collArr['familycnt'];
                $outputArr[$i]['genuscnt'] = $collArr['genuscnt'];
                $outputArr[$i]['speciescnt'] = $collArr['speciescnt'];
                $outputArr[$i]['TotalTaxaCount'] = $collArr['TotalTaxaCount'];
                $outputArr[$i]['TypeCount'] = $collArr['TypeCount'];
                $i++;
            }
        }
    }
}

header ('Content-Type: text/csv');
header ("Content-Disposition: attachment; filename=\"$fileName\"");
if($action === 'Download Family Dist' || $action === 'Download Geo Dist' || $action === 'Download Order Dist'){
	if($outputArr){
		$outstream = fopen('php://output', 'wb');
		fputcsv($outstream,$header);

		foreach($outputArr as $row){
			fputcsv($outstream,$row);
		}
		fclose($outstream);
	}
	else{
		echo "Recordset is empty.\n";
	}
}
if($action === 'Download Stats per Coll'){
	if($outputArr){
		$outstream = fopen('php://output', 'wb');
		fputcsv($outstream,$header);

		foreach($outputArr as $row){
			fputcsv($outstream,$row);
		}
		fclose($outstream);
	}
	else{
		echo "Recordset is empty.\n";
	}
}
if($action === 'Download CSV'){
	if($dataArr){
		$outputArr = array();
		$i = 0;
		foreach($dataArr as $code => $data){
			$outputArr[$i]['name'] = $data['collectionname'];
			$outputArr[$i]['object'] = 'Specimens';
			$total = 0;
			foreach($headerArr as $h => $month){
				if(array_key_exists($month, $data['stats']) && array_key_exists('speccnt', $data['stats'][$month])) {
                    $total += $data['stats'][$month]['speccnt'];
                    $outputArr[$i][$month] = $data['stats'][$month]['speccnt'];
                }
				else{
					$outputArr[$i][$month] = 0;
				}
			}
			$outputArr[$i]['total'] = $total;
			$i++;
			$outputArr[$i]['name'] = '';
			$outputArr[$i]['object'] = 'Unprocessed';
			$total = 0;
			foreach($headerArr as $h => $month){
				if(array_key_exists($month, $data['stats']) && array_key_exists('unprocessedCount', $data['stats'][$month])) {
                    $total += $data['stats'][$month]['unprocessedCount'];
                    $outputArr[$i][$month] = $data['stats'][$month]['unprocessedCount'];
                }
				else{
					$outputArr[$i][$month] = 0;
				}
			}
			$outputArr[$i]['total'] = $total;
			$i++;
			$outputArr[$i]['name'] = '';
			$outputArr[$i]['object'] = 'Stage 1';
			$total = 0;
			foreach($headerArr as $h => $month){
				if(array_key_exists($month, $data['stats']) && array_key_exists('stage1Count', $data['stats'][$month])) {
                    $total += $data['stats'][$month]['stage1Count'];
                    $outputArr[$i][$month] = $data['stats'][$month]['stage1Count'];
                }
				else{
					$outputArr[$i][$month] = 0;
				}
			}
			$outputArr[$i]['total'] = $total;
			$i++;
			$outputArr[$i]['name'] = '';
			$outputArr[$i]['object'] = 'Stage 2';
			$total = 0;
			foreach($headerArr as $h => $month){
				if(array_key_exists($month, $data['stats']) && array_key_exists('stage2Count', $data['stats'][$month])) {
                    $total += $data['stats'][$month]['stage2Count'];
                    $outputArr[$i][$month] = $data['stats'][$month]['stage2Count'];
                }
				else{
					$outputArr[$i][$month] = 0;
				}
			}
			$outputArr[$i]['total'] = $total;
			$i++;
			$outputArr[$i]['name'] = '';
			$outputArr[$i]['object'] = 'Stage 3';
			$total = 0;
			foreach($headerArr as $h => $month){
				if(array_key_exists($month, $data['stats']) && array_key_exists('stage3Count', $data['stats'][$month])) {
                    $total += $data['stats'][$month]['stage3Count'];
                    $outputArr[$i][$month] = $data['stats'][$month]['stage3Count'];
                }
				else{
					$outputArr[$i][$month] = 0;
				}
			}
			$outputArr[$i]['total'] = $total;
			$i++;
			$outputArr[$i]['name'] = '';
			$outputArr[$i]['object'] = 'Images';
			$total = 0;
			foreach($headerArr as $h => $month){
				if(array_key_exists($month, $data['stats']) && array_key_exists('imgcnt', $data['stats'][$month])) {
                    $total += $data['stats'][$month]['imgcnt'];
                    $outputArr[$i][$month] = $data['stats'][$month]['imgcnt'];
                }
				else{
					$outputArr[$i][$month] = 0;
				}
			}
			$outputArr[$i]['total'] = $total;
			$i++;
			$outputArr[$i]['name'] = '';
			$outputArr[$i]['object'] = 'Georeferenced';
			$total = 0;
			foreach($headerArr as $h => $month){
				if(array_key_exists($month, $data['stats']) && array_key_exists('georcnt', $data['stats'][$month])) {
                    $total += $data['stats'][$month]['georcnt'];
                    $outputArr[$i][$month] = $data['stats'][$month]['georcnt'];
                }
				else{
					$outputArr[$i][$month] = 0;
				}
			}
			$outputArr[$i]['total'] = $total;
			$i++;
		}

		array_unshift($headerArr, 'Institution', 'Object');
		$headerArr[] = 'Total';

		$outstream = fopen('php://output', 'wb');
		fputcsv($outstream,$headerArr);

		foreach($outputArr as $row){
			fputcsv($outstream,$row);
		}
		fclose($outstream);
	}
	else{
		echo "Recordset is empty.\n";
	}
}
