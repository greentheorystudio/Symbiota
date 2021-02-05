<?php
include_once(__DIR__ . '/ChecklistVoucherAdmin.php');
include_once(__DIR__ . '/DwcArchiverCore.php');
require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ChecklistVoucherPensoft extends ChecklistVoucherAdmin {

	public function __construct() {
		parent::__construct();
	}

	public function downloadPensoftXlsx(): void
	{
        $taxaSheet = null;
        $materialsSheet = null;
	    $spreadsheet = new Spreadsheet();
        try {
            $taxaSheet = $spreadsheet->getActiveSheet()->setTitle('Taxa');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}
        $penArr = $this->getPensoftArr();
		$headerArr = $penArr['header'];
		$taxaArr = $penArr['taxa'];

		$letters = range('A', 'Z');
		$columnCnt = 0;
		foreach($headerArr as $headerValue){
			$colLet = $letters[$columnCnt%26].'1';
			if($columnCnt > 26) {
				$colLet .= $letters[floor($columnCnt / 26)];
			}
			$taxaSheet->setCellValue($colLet, $headerValue);
			$columnCnt++;
		}

		$rowCnt = 2;
		foreach($taxaArr as $tid => $recArr){
			$columnCnt = 0;
			foreach($headerArr as $headerKey => $v){
				$colLet = $letters[$columnCnt%26].$rowCnt;
				if($columnCnt > 26) {
					$colLet .= $letters[floor($columnCnt / 26)];
				}
				$cellValue = ($recArr[$headerKey] ?? '');
				$taxaSheet->setCellValue($colLet, $cellValue);
				$columnCnt++;
			}
			$rowCnt++;
		}

        try {
            $materialsSheet = $spreadsheet->createSheet(1)->setTitle('Materials');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}

        $dwcaHandler = new DwcArchiverCore();
		$dwcaHandler->setVerboseMode(0);
		$dwcaHandler->setCharSetOut('ISO-8859-1');
		$dwcaHandler->setSchemaType('pensoft');
		$dwcaHandler->setExtended(false);
		$dwcaHandler->setRedactLocalities(1);
		$dwcaHandler->addCondition('clid','EQUALS',$_REQUEST['clid']);
		$dwcArr = $dwcaHandler->getDwcArray();

		if($dwcArr){
			$headerArr = array_keys($dwcArr[0]);
			$columnCnt = 0;
			foreach($headerArr as $headerValue){
				$colLet = $letters[$columnCnt%26];
				if($columnCnt > 25) {
					$colLet = $letters[floor(($columnCnt / 26) - 1)] . $colLet;
				}
				$materialsSheet->setCellValue($colLet.'1', $headerValue);
				$columnCnt++;
			}
			foreach($dwcArr as $cnt => $rowArr){
				$rowCnt = $cnt+2;
				$columnCnt = 0;
				foreach($rowArr as $colKey => $cellValue){
					$colLet = $letters[$columnCnt%26];
					if($columnCnt > 25) {
						$colLet = $letters[floor(($columnCnt / 26) - 1)] . $colLet;
					}
					$materialsSheet->setCellValue($colLet.$rowCnt, $cellValue);
					$columnCnt++;
				}
			}
		}

        try {
            $spreadsheet->createSheet(2)->setTitle('ExternalLinks');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}

        $file = $this->getExportFileName().'.xlsx';
		header('Content-Description: Checklist Pensoft Export');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		$writer = new Xlsx($spreadsheet);
        try {
            $writer->save('php://output');
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {}
    }

	protected function getPensoftArr(): array
	{
		$clidStr = $this->clid;
		if($this->childClidArr){
			$clidStr .= ','.implode(',',$this->childClidArr);
		}

		$clArr = array();
		$kingdomArr = array();
		$sql = 'SELECT t.tid, t.kingdomId, t.sciname, t.author, t.unitname1, t.unitname2, t.unitind3, t.unitname3, t.rankid, c.familyoverride '.
			'FROM fmchklsttaxalink c INNER JOIN taxa t ON c.tid = t.tid '.
			'INNER JOIN taxstatus ts ON c.tid = ts.tid '.
			'WHERE (ts.taxauthid = 1) AND (c.clid IN('.$clidStr.')) '.
			'ORDER BY IFNULL(c.familyoverride, ts.family), t.sciname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->kingdomId){
                if(isset($kingdomArr[$r->kingdomId])) {
                    ++$kingdomArr[$r->kingdomId];
                }
                else {
                    $kingdomArr[$r->kingdomId] = 0;
                }
            }
			$clArr[$r->tid]['tid'] = $r->tid;
			$clArr[$r->tid]['author'] = $this->encodeStr($r->author);
			if($r->familyoverride) {
				$clArr[$r->tid][140] = $r->familyoverride;
			}
			if($r->rankid < 180){
				$clArr[$r->tid][$r->rankid] = $r->unitname1;
			}
			else{
				$clArr[$r->tid][180] = $r->unitname1;
				if($r->unitname2) {
					$clArr[$r->tid]['epithet'] = $r->unitname2;
				}
				if($r->unitname3){
					if($r->rankid === 230){
						$clArr[$r->tid]['subsp'] = $r->unitname3;
					}
					elseif($r->rankid === 240){
						$clArr[$r->tid]['var'] = $r->unitname3;
					}
					elseif($r->rankid === 260){
						$clArr[$r->tid]['f'] = $r->unitname3;
					}
					else{
						$clArr[$r->tid]['infra'] = $r->unitname3;
					}
				}
			}
		}
		$rs->free();

		$rankArr = array();
		$sql = 'SELECT t.tid, t2.sciname as parentstr, t2.rankid '.
			'FROM fmchklsttaxalink c INNER JOIN taxa t ON c.tid = t.tid '.
			'INNER JOIN taxaenumtree e ON c.tid = e.tid '.
			'INNER JOIN taxa t2 ON e.parenttid = t2.tid '.
			'WHERE (e.taxauthid = 1) AND (c.clid IN('.$clidStr.'))';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$clArr[$r->tid][$r->rankid] = $this->encodeStr($r->parentstr);
			$rankArr[$r->rankid] = $r->rankid;
		}
		$rs->free();

		$outArr = array();
		if($clArr){
			$outArr['taxa'] = $clArr;
			asort($kingdomArr);
			end($kingdomArr);
			$sql = 'SELECT rankid, rankname FROM taxonunits WHERE rankid IN('.implode(',',$rankArr).') AND kingdomid IN('.implode(',',$kingdomArr).') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$rankArr[$r->rankid] = $r->rankname;
			}
			$rs->free();

			$headerArr = array('tid'=>'Taxon_Local_ID');
			ksort($rankArr);
			foreach($rankArr as $id => $name){
				if($id > 180 && !isset($headerArr[180])) {
					$headerArr[180] = 'Genus';
				}
				if($id >= 220) {
					break;
				}
				$headerArr[$id] = $name;
			}
			if(!isset($headerArr[180])) {
				$headerArr[180] = 'Genus';
			}
			$headerArr['epithet'] = 'Species';
			$headerArr['subsp'] = 'Subspecies';
			$headerArr['var'] = 'Variety';
			$headerArr['f'] = 'Form';
			$headerArr['author'] = 'Authorship';
			$headerArr['notes'] = 'Notes';
			$headerArr['habitat'] = 'Habitat';
			$headerArr['abundance'] = 'Abundance';
			$headerArr['source'] = 'Source';

			foreach($headerArr as $k => $v){
				if(is_numeric($v)) {
					$headerArr[$k] = 'Unranked Node';
				}
			}
			$outArr['header'] = $headerArr;
		}
		return $outArr;
	}
}
