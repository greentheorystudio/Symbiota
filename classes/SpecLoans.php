<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class SpecLoans{

	private $conn;
	private $collId = 0;
	private $loanId = 0;
	private $exchangeId;

	public function __construct() {
		$connection = new DbService();
		$this->conn = $connection->getConnection();
	}
	
	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}

	public function getLoanOutList($searchTerm,$displayAll): array
	{
		$retArr = array();
		$sql = 'SELECT l.loanid, l.datesent, l.loanidentifierown, i.institutioncode, l.forwhom, l.dateclosed '.
			'FROM omoccurloans l LEFT JOIN institutions i ON l.iidborrower = i.iid '.
			'WHERE l.collidown = '.$this->collId.' ';
		if($searchTerm){
			$sql .= 'AND l.loanidentifierown LIKE "%'.$searchTerm.'%" ';
		}
		if(!$displayAll){
			$sql .= 'AND ISNULL(l.dateclosed) ';
		}
		$sql .= 'ORDER BY l.loanidentifierown + 1 DESC';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->loanid]['loanidentifierown'] = $r->loanidentifierown;
				$retArr[$r->loanid]['institutioncode'] = $r->institutioncode;
				$retArr[$r->loanid]['forwhom'] = $r->forwhom;
				$retArr[$r->loanid]['dateclosed'] = $r->dateclosed;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getTransInstList(): array
	{
		$iidArr = array();
		$sql = 'SELECT DISTINCT e.iid, i.institutioncode '.
			'FROM omoccurexchange AS e INNER JOIN institutions AS i ON e.iid = i.iid '.
			'WHERE e.collid = '.$this->collId.' AND e.iid IS NOT NULL '.
			'ORDER BY i.institutioncode';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$iidArr[$r->iid]['institutioncode'] = $r->institutioncode;
			}
		}
		$sql = 'SELECT rt.iid, e.invoicebalance FROM omoccurexchange AS e '.
			'INNER JOIN (SELECT iid, MAX(exchangeid) AS exchangeid FROM omoccurexchange '.
			'GROUP BY iid,collid HAVING (collid = '.$this->collId.')) AS rt ON e.exchangeid = rt.exchangeid ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
					$iidArr[$r->iid]['invoicebalance'] = $r->invoicebalance;
			}
			$rs->close();
		}
		return $iidArr;
	}
	
	public function getTransactions($collId,$iid): array
	{
		$retArr = array();
		$sql = 'SELECT exchangeid, identifier, transactiontype, in_out, datesent, datereceived, '.
			'totalexmounted, totalexunmounted, totalgift, totalgiftdet, adjustment, invoicebalance '.
			'FROM omoccurexchange '.
			'WHERE collid = '.$collId.' AND iid = '.$iid.' '.
			'ORDER BY exchangeid DESC';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->exchangeid]['identifier'] = $r->identifier;
				$retArr[$r->exchangeid]['transactiontype'] = $r->transactiontype;
				$retArr[$r->exchangeid]['in_out'] = $r->in_out;
				$retArr[$r->exchangeid]['datesent'] = $r->datesent;
				$retArr[$r->exchangeid]['datereceived'] = $r->datereceived;
				$retArr[$r->exchangeid]['totalexmounted'] = $r->totalexmounted;
				$retArr[$r->exchangeid]['totalexunmounted'] = $r->totalexunmounted;
				$retArr[$r->exchangeid]['totalgift'] = $r->totalgift;
				$retArr[$r->exchangeid]['totalgiftdet'] = $r->totalgiftdet;
				$retArr[$r->exchangeid]['adjustment'] = $r->adjustment;
				$retArr[$r->exchangeid]['invoicebalance'] = $r->invoicebalance;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getLoanOnWayList(): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT o.loanid, o.loanidentifierown, c.collectionname '.
			'FROM omoccurloans AS o LEFT OUTER JOIN omcollections AS c ON o.iidBorrower = c.iid '.
			'WHERE c.CollID = '.$this->collId.' AND ISNULL(o.collidBorr) AND ISNULL(o.dateClosed)' ;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->loanid]['loanidentifierown'] = $r->loanidentifierown;
				$retArr[$r->loanid]['collectionname'] = $r->collectionname;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getLoanInList($searchTerm,$displayAll): array
	{
		$retArr = array();
		$sql = 'SELECT l.loanid, l.datereceivedborr, l.loanidentifierborr, l.datedue, l.dateclosed, i.institutioncode, l.forwhom '.
			'FROM omoccurloans l LEFT JOIN institutions i ON l.iidowner = i.iid '.
			'WHERE collidborr = '.$this->collId.' ';
		if($searchTerm){
			$sql .= 'AND l.loanidentifierborr LIKE "%'.$searchTerm.'%" ';
		}
		if(!$displayAll){
			$sql .= 'AND ISNULL(l.dateclosed) ';
		}
		$sql .= 'ORDER BY l.loanidentifierborr + 1';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->loanid]['loanidentifierborr'] = $r->loanidentifierborr;
				$retArr[$r->loanid]['institutioncode'] = $r->institutioncode;
				$retArr[$r->loanid]['forwhom'] = $r->forwhom;
				$retArr[$r->loanid]['dateclosed'] = $r->dateclosed;
			}
			$rs->close();
		}
		return $retArr;
	} 
	
	public function getLoanOutDetails($loanId): array
	{
		$retArr = array();
		$sql = 'SELECT loanid, loanidentifierown, iidborrower, datesent, totalboxes, '.
			'shippingmethod, datedue, datereceivedown, dateclosed, forwhom, description, '.
			'notes, createdbyown, processedbyown, processedbyreturnown, invoicemessageown '.
			'FROM omoccurloans '.
			'WHERE loanid = '.$loanId;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['loanidentifierown'] = $r->loanidentifierown;
				$retArr['iidborrower'] = $r->iidborrower;
				$retArr['datesent'] = $r->datesent;
				$retArr['totalboxes'] = $r->totalboxes;
				$retArr['shippingmethod'] = $r->shippingmethod;
				$retArr['datedue'] = $r->datedue;
				$retArr['datereceivedown'] = $r->datereceivedown;
				$retArr['dateclosed'] = $r->dateclosed;
				$retArr['forwhom'] = $r->forwhom;
				$retArr['description'] = $r->description;
				$retArr['notes'] = $r->notes;
				$retArr['createdbyown'] = $r->createdbyown;
				$retArr['processedbyown'] = $r->processedbyown;
				$retArr['processedbyreturnown'] = $r->processedbyreturnown;
				$retArr['invoicemessageown'] = $r->invoicemessageown;
			}
			$rs->close();
		}
		return $retArr;
	} 
	
	public function getLoanInDetails($loanId): array
	{
		$retArr = array();
		$sql = 'SELECT loanid, loanidentifierown, loanidentifierborr, collidown, iidowner, datesentreturn, totalboxesreturned, '.
			'shippingmethodreturn, datedue, datereceivedborr, dateclosed, forwhom, description, numspecimens, '.
			'notes, createdbyborr, processedbyborr, processedbyreturnborr, invoicemessageborr '.
			'FROM omoccurloans '.
			'WHERE loanid = '.$loanId;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['loanidentifierown'] = $r->loanidentifierown;
				$retArr['loanidentifierborr'] = $r->loanidentifierborr;
				$retArr['collidown'] = $r->collidown;
				$retArr['iidowner'] = $r->iidowner;
				$retArr['datesentreturn'] = $r->datesentreturn;
				$retArr['totalboxesreturned'] = $r->totalboxesreturned;
				$retArr['shippingmethodreturn'] = $r->shippingmethodreturn;
				$retArr['datedue'] = $r->datedue;
				$retArr['datereceivedborr'] = $r->datereceivedborr;
				$retArr['dateclosed'] = $r->dateclosed;
				$retArr['forwhom'] = $r->forwhom;
				$retArr['description'] = $r->description;
				$retArr['numspecimens'] = $r->numspecimens;
				$retArr['notes'] = $r->notes;
				$retArr['createdbyborr'] = $r->createdbyborr;
				$retArr['processedbyborr'] = $r->processedbyborr;
				$retArr['processedbyreturnborr'] = $r->processedbyreturnborr;
				$retArr['invoicemessageborr'] = $r->invoicemessageborr;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getExchangeDetails($exchangeId): array
	{
		$retArr = array();
		$sql = 'SELECT exchangeid, identifier, collid, iid, transactiontype, in_out, datesent, datereceived, '.
			'totalboxes, shippingmethod, totalexmounted, totalexunmounted, totalgift, totalgiftdet, adjustment, '.
			'invoicebalance, invoicemessage, description, notes, createdby '.
			'FROM omoccurexchange '.
			'WHERE exchangeid = '.$exchangeId;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['identifier'] = $r->identifier;
				$retArr['collid'] = $r->collid;
				$retArr['iid'] = $r->iid;
				$retArr['transactiontype'] = $r->transactiontype;
				$retArr['in_out'] = $r->in_out;
				$retArr['datesent'] = $r->datesent;
				$retArr['datereceived'] = $r->datereceived;
				$retArr['totalboxes'] = $r->totalboxes;
				$retArr['shippingmethod'] = $r->shippingmethod;
				$retArr['totalexmounted'] = $r->totalexmounted;
				$retArr['totalexunmounted'] = $r->totalexunmounted;
				$retArr['totalgift'] = $r->totalgift;
				$retArr['totalgiftdet'] = $r->totalgiftdet;
				$retArr['adjustment'] = $r->adjustment;
				$retArr['invoicebalance'] = $r->invoicebalance;
				$retArr['invoicemessage'] = $r->invoicemessage;
				$retArr['description'] = $r->description;
				$retArr['notes'] = $r->notes;
				$retArr['createdby'] = $r->createdby;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getExchangeValue($exchangeId){
		$exchangeValue = 0;
		$sql = 'SELECT totalexmounted, totalexunmounted FROM omoccurexchange WHERE exchangeid = '.$exchangeId;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$exchangeValue = (($r->totalexmounted)*2) + ($r->totalexunmounted);
			}
			$rs->close();
		}
		return $exchangeValue;
	}
	
	public function getExchangeTotal($exchangeId){
		$exchangeTotal = 0;
		$sql = 'SELECT totalexmounted, totalexunmounted, totalgift, totalgiftdet FROM omoccurexchange WHERE exchangeid = '.$exchangeId;
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$exchangeTotal = ($r->totalexmounted) + ($r->totalexunmounted) + ($r->totalgift) + ($r->totalgiftdet);
			}
			$rs->close();
		}
		return $exchangeTotal;
	}

	public function editLoanOut($pArr): string
	{
		$statusStr = '';
		$loanId = $pArr['loanid'];
		if(is_numeric($loanId)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k !== 'formsubmit' && $k !== 'loanid' && $k !== 'collid'){
					$sql .= ','.$k.'='.($v?'"'.SanitizerService::cleanInStr($this->conn,$v).'"':'NULL');
				}
			}
			$sql = 'UPDATE omoccurloans SET '.substr($sql,1).' WHERE (loanid = '.$loanId.')';
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of loan failed.';
			}
		}
		return $statusStr;
	}
	
	public function deleteLoan($loanId): int
	{
		$status = 0;
		if(is_numeric($loanId)){
			$sql = 'DELETE FROM omoccurloans WHERE (loanid = '.$loanId.')';
			if($this->conn->query($sql)){
				$status = 1;
			}
		}
		return $status;
	}
	
	public function deleteExchange($exchangeId): int
	{
		$status = 0;
		if(is_numeric($exchangeId)){
			$sql = 'DELETE FROM omoccurexchange WHERE (exchangeid = '.$exchangeId.')';
			if($this->conn->query($sql)){
				$status = 1;
			}
		}
		return $status;
	}
	
	public function editLoanIn($pArr): string
	{
		$statusStr = '';
		$loanId = $pArr['loanid'];
		if(is_numeric($loanId)){
			$sql = '';
			foreach($pArr as $k => $v){
				if($k !== 'formsubmit' && $k !== 'loanid' && $k !== 'collid'){
					$sql .= ','.$k.'='.($v?'"'.SanitizerService::cleanInStr($this->conn,$v).'"':'NULL');
				}
			}
			$sql = 'UPDATE omoccurloans SET '.substr($sql,1).' WHERE (loanid = '.$loanId.')';
			if($this->conn->query($sql)){
				$statusStr = 'SUCCESS: information saved';
			}
			else{
				$statusStr = 'ERROR: Editing of loan failed.';
			}
		}
		return $statusStr;
	}

    public function editExchange($pArr): string
    {
        $statusStr = '';
        $retArr = array();
        $exchangeId = $pArr['exchangeid'];
        $collId = $pArr['collid'];
        $Iid = $pArr['iid'];
        if(is_numeric($exchangeId)){
            $sql = '';
            foreach($pArr as $k => $v){
                if($k !== 'formsubmit' && $k !== 'exchangeid' && $k !== 'collid'){
                    $sql .= ','.$k.'='.($v?'"'.SanitizerService::cleanInStr($this->conn,$v).'"':'NULL');
                }
            }
            $sql = 'UPDATE omoccurexchange SET '.substr($sql,1).' WHERE (exchangeid = '.$exchangeId.')';
            if($this->conn->query($sql)){
                $statusStr = 'SUCCESS: information saved';
            }
            else{
                $statusStr = 'ERROR: Editing of exchange failed.';
            }

            $sql = 'SELECT invoicebalance FROM omoccurexchange '.
                'WHERE exchangeid =  (SELECT MAX(exchangeid) FROM omoccurexchange '.
                'WHERE (exchangeid < '.$exchangeId.') AND (collid = '.$collId.') AND (iid = '.$Iid.'))';
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $retArr['invoicebalance'] = $r->invoicebalance;
                }
                $rs->close();
            }
            if(!array_key_exists('invoicebalance',$retArr) || !$retArr['invoicebalance']){
                $prevBalance = 0;
            }
            else{
                $prevBalance = $retArr['invoicebalance'];
            }
            $currentBalance = 0;
            if($pArr['transactiontype'] === 'Shipment'){

                if($pArr['in_out'] === 'In'){
                    $currentBalance = ((int)$prevBalance - ((int)$pArr['totalexmounted'] * 2 + (int)$pArr['totalexunmounted']));
                }
                elseif($pArr['in_out'] === 'Out'){
                    $currentBalance = ((int)$prevBalance + ((int)$pArr['totalexmounted'] * 2 + (int)$pArr['totalexunmounted']));
                }

            }
            elseif($pArr['transactiontype'] === 'Adjustment'){
                $currentBalance = (int)$prevBalance + (int)$pArr['adjustment'];
            }
            $sql3 = 'UPDATE omoccurexchange SET invoicebalance = '.$currentBalance.' WHERE (exchangeid = '.$exchangeId.')';
            if($this->conn->query($sql3)){
                $statusStr .= ' and balance updated.';
            }
        }
        return $statusStr;
    }
	
	public function createNewLoanOut($pArr): string
	{
		$statusStr = '';
		$sql = 'INSERT INTO omoccurloans(collidown,loanidentifierown,iidowner,iidborrower,createdbyown) '.
			'VALUES('.$this->collId.',"'.SanitizerService::cleanInStr($this->conn,$pArr['loanidentifierown']).'",(SELECT iid FROM omcollections WHERE collid = '.$this->collId.'), '.
			'"'.SanitizerService::cleanInStr($this->conn,$pArr['reqinstitution']).'","'.SanitizerService::cleanInStr($this->conn,$pArr['createdbyown']).'") ';
		//echo $sql;
		if($this->conn->query($sql)){
			$this->loanId = $this->conn->insert_id;
		}
		else{
			$statusStr = 'ERROR: Creation of new loan failed.';
		}
		return $statusStr;
	}
	
	public function createNewLoanIn($pArr): string
	{
		$statusStr = '';
		$sql = 'INSERT INTO omoccurloans(collidborr,loanidentifierown,loanidentifierborr,iidowner,createdbyborr) '.
			'VALUES('.$this->collId.',"","'.SanitizerService::cleanInStr($this->conn,$pArr['loanidentifierborr']).'","'.SanitizerService::cleanInStr($this->conn,$pArr['iidowner']).'",'.
			'"'.SanitizerService::cleanInStr($this->conn,$pArr['createdbyborr']).'")';
		//echo $sql;
		if($this->conn->query($sql)){
			$this->loanId = $this->conn->insert_id;
		}
		else{
			$statusStr = 'ERROR: Creation of new loan failed.';
		}
		return $statusStr;
	}
	
	public function createNewExchange($pArr): string
	{
		$statusStr = '';
		$sql = 'INSERT INTO omoccurexchange(identifier,collid,iid,transactiontype,createdby) '.
			'VALUES("'.SanitizerService::cleanInStr($this->conn,$pArr['identifier']).'",'.$this->collId.',"'.SanitizerService::cleanInStr($this->conn,$pArr['iid']).'",'.
			'"'.SanitizerService::cleanInStr($this->conn,$pArr['transactiontype']).'","'.SanitizerService::cleanInStr($this->conn,$pArr['createdby']).'")';
		//echo $sql;
		if($this->conn->query($sql)){
			$this->exchangeId = $this->conn->insert_id;
		}
		else{
			$statusStr = 'ERROR: Creation of new exchange failed.';
		}
		return $statusStr;
	}
	
	public function getSpecTotal($loanId): array
	{
		$retArr = array();
		$sql = 'SELECT loanid, COUNT(loanid) AS speccount '.
			'FROM omoccurloanslink '.
			'WHERE loanid = '.$loanId.' '.
			'GROUP BY loanid';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['speccount'] = $r->speccount;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getSpecList($loanId): array
	{
		$retArr = array();
		$sql = 'SELECT l.loanid, l.occid, IFNULL(o.catalognumber,o.othercatalognumbers) AS catalognumber, '.
			'o.sciname, CONCAT_WS(" ",o.recordedby,IFNULL(o.recordnumber,o.eventdate)) AS collector, '.
			'CONCAT_WS(", ",stateprovince,county,locality) AS locality, l.returndate '.
			'FROM omoccurloanslink AS l LEFT OUTER JOIN omoccurrences AS o ON l.occid = o.occid '.
			'WHERE l.loanid = '.$loanId.' '.
			'ORDER BY o.catalognumber desc,o.catalognumber+1 desc';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->occid]['occid'] = $r->occid;
				$retArr[$r->occid]['catalognumber'] = $r->catalognumber;
				$retArr[$r->occid]['sciname'] = $r->sciname;
				$retArr[$r->occid]['collector'] = $r->collector;
				$retArr[$r->occid]['locality'] = $r->locality;
				$retArr[$r->occid]['returndate'] = $r->returndate;
			}
			$rs->close();
		}
		return $retArr;
	} 

	public function addSpecimen($loanId,$collId,$catNum): ?int
	{
		$retVal = 0;
	    $occArr = array();
		if(is_numeric($collId) && is_numeric($loanId)){
			$sql = 'SELECT occid FROM omoccurrences WHERE (collid = '.$collId.') AND (catalognumber = "'.trim($catNum).'") ';
			//echo $sql;
			$result = $this->conn->query($sql);
			while($row = $result->fetch_object()) {
				$occArr[] = $row->occid;
			}
			if(count($occArr) === 0){
				$sql = 'SELECT occid FROM omoccurrences WHERE (collid = '.$collId.') AND (othercatalognumbers = "'.trim($catNum).'")';
				$result = $this->conn->query($sql);
				while($row = $result->fetch_object()) {
					$occArr[] = $row->occid;
				}
			}
			if(count($occArr) !== 0) {
                if(count($occArr) > 1) {
                    $retVal = 2;
                }
                else{
                    $sql = 'INSERT INTO omoccurloanslink(loanid,occid) '.
                        'VALUES ('.$loanId.','.$occArr[0].') ';
                    //echo $sql;
                    if($this->conn->query($sql)){
                        $retVal = 1;
                    }
                    else{
                        $retVal = 3;
                    }
                }
            }
		}
		return $retVal;
	}
	
	public function editSpecimen($reqArr): void
	{
		if(!array_key_exists('occid',$reqArr)) {
			return;
		}
		$occidArr = $reqArr['occid'];
		$applyTask = $reqArr['applytask'];
		$loanId = $reqArr['loanid'];
		if($occidArr){
			if($applyTask === 'delete'){
				$sql = 'DELETE FROM omoccurloanslink WHERE loanid = '.$loanId.' AND (occid IN('.implode(',',$occidArr).')) ';
				$this->conn->query($sql);
			}
			else{
				$sql = 'UPDATE omoccurloanslink SET returndate = "'.date('Y-m-d H:i:s').'" WHERE loanid = '.$loanId.' AND (occid IN('.implode(',',$occidArr).')) ';
				$this->conn->query($sql);
			}
		}
	}
	
	public function getInvoiceInfo($identifier,$loanType): array
	{
		$retArr = array();
		if($loanType === 'exchange'){
			$sql = 'SELECT e.exchangeid, e.identifier, e.iid, '.
			'e.totalboxes, e.shippingmethod, e.totalexmounted, e.totalexunmounted, e.totalgift, e.totalgiftdet, '.
			'e.invoicebalance, e.invoicemessage, e.description, i.contact, i.institutionname, i.institutionname2, '.
			'i.institutioncode, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country '.
			'FROM omoccurexchange AS e LEFT OUTER JOIN institutions AS i ON e.iid = i.iid '.
			'WHERE exchangeid = '.$identifier;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['exchangeid'] = $r->exchangeid;
					$retArr['identifier'] = $r->identifier;
					$retArr['iid'] = $r->iid;
					$retArr['totalboxes'] = $r->totalboxes;
					$retArr['shippingmethod'] = $r->shippingmethod;
					$retArr['totalexmounted'] = $r->totalexmounted;
					$retArr['totalexunmounted'] = $r->totalexunmounted;
					$retArr['totalgift'] = $r->totalgift;
					$retArr['totalgiftdet'] = $r->totalgiftdet;
					$retArr['invoicebalance'] = $r->invoicebalance;
					$retArr['invoicemessage'] = $r->invoicemessage;
					$retArr['description'] = $r->description;
					$retArr['contact'] = $r->contact;
					$retArr['institutionname'] = $r->institutionname;
					$retArr['institutionname2'] = $r->institutionname2;
					$retArr['institutioncode'] = $r->institutioncode;
					$retArr['address1'] = $r->address1;
					$retArr['address2'] = $r->address2;
					$retArr['city'] = $r->city;
					$retArr['stateprovince'] = $r->stateprovince;
					$retArr['postalcode'] = $r->postalcode;
					$retArr['country'] = $r->country;
				}
			}
		}
		else{
			$sql = 'SELECT e.loanid, e.loanidentifierown, e.loanidentifierborr, e.datesent, e.totalboxes, e.totalboxesreturned, '.
				'e.numspecimens, e.shippingmethod, e.shippingmethodreturn, e.datedue, e.datereceivedborr, e.forwhom, '.
				'e.description, e.invoicemessageown, e.invoicemessageborr, i.contact, i.institutionname, i.institutionname2, '.
				'i.institutioncode, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country ';
			if($loanType === 'out'){
				$sql .= 'FROM omoccurloans AS e LEFT OUTER JOIN institutions AS i ON e.iidborrower = i.iid ';
			}
			elseif($loanType === 'in'){
				$sql .= 'FROM omoccurloans AS e LEFT OUTER JOIN institutions AS i ON e.iidowner = i.iid ';
			}
			$sql .= 'WHERE loanid = '.$identifier;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['loanid'] = $r->loanid;
					$retArr['loanidentifierown'] = $r->loanidentifierown;
					$retArr['loanidentifierborr'] = $r->loanidentifierborr;
					$retArr['datesent'] = $r->datesent;
					$retArr['totalboxes'] = $r->totalboxes;
					$retArr['totalboxesreturned'] = $r->totalboxesreturned;
					$retArr['numspecimens'] = $r->numspecimens;
					$retArr['shippingmethod'] = $r->shippingmethod;
					$retArr['shippingmethodreturn'] = $r->shippingmethodreturn;
					$retArr['datedue'] = $r->datedue;
					$retArr['datereceivedborr'] = $r->datereceivedborr;
					$retArr['forwhom'] = $r->forwhom;
					$retArr['description'] = $r->description;
					$retArr['invoicemessageown'] = $r->invoicemessageown;
					$retArr['invoicemessageborr'] = $r->invoicemessageborr;
					$retArr['contact'] = $r->contact;
					$retArr['institutionname'] = $r->institutionname;
					$retArr['institutionname2'] = $r->institutionname2;
					$retArr['institutioncode'] = $r->institutioncode;
					$retArr['address1'] = $r->address1;
					$retArr['address2'] = $r->address2;
					$retArr['city'] = $r->city;
					$retArr['stateprovince'] = $r->stateprovince;
					$retArr['postalcode'] = $r->postalcode;
					$retArr['country'] = $r->country;
				}
			}
		}
		return $retArr;
	}
	
	public function getFromAddress($collId): array
	{
		$retArr = array();
		$sql = 'SELECT i.institutionname, i.institutionname2, i.phone, '.
			'i.institutioncode, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country '.
			'FROM omcollections AS o LEFT OUTER JOIN institutions AS i ON o.iid = i.iid '.
			'WHERE o.collid = '.$collId.' ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['institutionname'] = $r->institutionname;
				$retArr['institutionname2'] = $r->institutionname2;
				$retArr['phone'] = $r->phone;
				$retArr['institutioncode'] = $r->institutioncode;
				$retArr['address1'] = $r->address1;
				$retArr['address2'] = $r->address2;
				$retArr['city'] = $r->city;
				$retArr['stateprovince'] = $r->stateprovince;
				$retArr['postalcode'] = $r->postalcode;
				$retArr['country'] = $r->country;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getToAddress($institution): array
	{
		$retArr = array();
		$sql = 'SELECT i.contact, i.institutionname, i.institutionname2, i.phone, '.
			'i.institutioncode, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country '.
			'FROM institutions AS i '.
			'WHERE i.iid = '.$institution.' ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr['contact'] = $r->contact;
				$retArr['institutionname'] = $r->institutionname;
				$retArr['institutionname2'] = $r->institutionname2;
				$retArr['phone'] = $r->phone;
				$retArr['institutioncode'] = $r->institutioncode;
				$retArr['address1'] = $r->address1;
				$retArr['address2'] = $r->address2;
				$retArr['city'] = $r->city;
				$retArr['stateprovince'] = $r->stateprovince;
				$retArr['postalcode'] = $r->postalcode;
				$retArr['country'] = $r->country;
			}
			$rs->close();
		}
		return $retArr;
	}
	
	public function getInstitutionArr(): array
	{
		$retArr = array();
		$sql = 'SELECT i.iid, IFNULL(c.institutioncode,i.institutioncode) as institutioncode, '. 
			'i.institutionname '. 
			'FROM institutions i LEFT JOIN (SELECT iid, institutioncode, collectioncode, collectionname '. 
			'FROM omcollections WHERE colltype = "PreservedSpecimen") c ON i.iid = c.iid '.
			'ORDER BY i.institutioncode,c.institutioncode,c.collectionname,i.institutionname';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[$r->iid] = $r->institutioncode.' - '.$r->institutionname;
			}
		}
		return $retArr;
	} 
	
	public function setCollId($c): void
	{
		$this->collId = $c;
	}
	
	public function getLoanId(): int
	{
		return $this->loanId;
	}
	
	public function getExchangeId(){
		return $this->exchangeId;
	}
}
