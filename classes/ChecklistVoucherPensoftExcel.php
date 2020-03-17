<?php
include_once('ChecklistVoucherPensoft.php');
require_once(__DIR__ . '/../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ChecklistVoucherPensoftExcel extends ChecklistVoucherPensoft {

	public function __construct() {
		parent::__construct();
		ini_set('memory_limit','512M');
	}

	public function downloadPensoftXlsx(): void
	{
		$objPHPExcel = new Spreadsheet();
		$penArr = $this->getPensoftArr();
		$headerArr = $penArr['header'];
		$taxaArr = $penArr['taxa'];
		$letters = range('A', 'Z');

        try {
            $objPHPExcel->getActiveSheet()->setTitle('Taxa');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}

        $columnCnt = 0;
		foreach($headerArr as $headerValue){
			$colLet = $letters[$columnCnt%26].'1';
			if($columnCnt > 26) {
				$colLet .= $letters[floor($columnCnt / 26)];
			}
            try {
                $objPHPExcel->getActiveSheet()->setCellValue($colLet, $headerValue);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}
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
                try {
                    $objPHPExcel->getActiveSheet()->setCellValue($colLet, $cellValue);
                } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}
                $columnCnt++;
			}
			$rowCnt++;
		}

        try {
            $objPHPExcel->createSheet(1)->setTitle('Materials');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}
        try {
            $objPHPExcel->setActiveSheetIndex(1);
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
                try {
                    $objPHPExcel->getActiveSheet()->setCellValue($colLet . '1', $headerValue);
                } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}
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
                    try {
                        $objPHPExcel->getActiveSheet()->setCellValue($colLet . $rowCnt, $cellValue);
                    } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}
                    $columnCnt++;
				}
			}
		}

        try {
            $objPHPExcel->createSheet(2)->setTitle('ExternalLinks');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}

        try {
            $objPHPExcel->setActiveSheetIndex(0);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {}

        $file = $this->getExportFileName().'.xlsx';
		header('Content-Description: Checklist Pensoft Export');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');

		$objWriter = new Xlsx($objPHPExcel);
        try {
            $objWriter->save('php://output');
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {}
    }
}
