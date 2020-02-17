<?php
include_once($SERVER_ROOT.'/classes/ChecklistVoucherPensoft.php');

define('EOL',(PHP_SAPI === 'cli') ? PHP_EOL : '<br />');
require_once $SERVER_ROOT.'/vendor/phpoffice/phpexcel/PHPExcel.php';

class ChecklistVoucherPensoftExcel extends ChecklistVoucherPensoft {

	public function __construct() {
		parent::__construct();
		ini_set('memory_limit','512M');
	}

	public function downloadPensoftXlsx(): void
	{
		$objPHPExcel = new PHPExcel();
		$penArr = $this->getPensoftArr();
		$headerArr = $penArr['header'];
		$taxaArr = $penArr['taxa'];
		$letters = range('A', 'Z');

		$objPHPExcel->getActiveSheet()->setTitle('Taxa');

		$columnCnt = 0;
		foreach($headerArr as $headerValue){
			$colLet = $letters[$columnCnt%26].'1';
			if($columnCnt > 26) {
				$colLet .= $letters[floor($columnCnt / 26)];
			}
			$objPHPExcel->getActiveSheet()->setCellValue($colLet, $headerValue);
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
				$objPHPExcel->getActiveSheet()->setCellValue($colLet, $cellValue);
				$columnCnt++;
			}
			$rowCnt++;
		}

		$objPHPExcel->createSheet(1)->setTitle('Materials');
		$objPHPExcel->setActiveSheetIndex(1);

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
				$objPHPExcel->getActiveSheet()->setCellValue($colLet.'1', $headerValue);
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
					$objPHPExcel->getActiveSheet()->setCellValue($colLet.$rowCnt, $cellValue);
					$columnCnt++;
				}
			}
		}

		$objPHPExcel->createSheet(2)->setTitle('ExternalLinks');

		$objPHPExcel->setActiveSheetIndex(0);

		$file = $this->getExportFileName().'.xlsx';
		header('Content-Description: Checklist Pensoft Export');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
