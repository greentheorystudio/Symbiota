<?php
/*
 * * ****  Accepts  ********************************************
 *
 * POST requests
 *
 * ****  Input Variables  ********************************************
 *
 * job_id: ID for batch processing job.
 * url: URL of results.
 *
 * * ****  Output  ********************************************
 *
 * ERROR or SUCCESS messages.
 *
 */

include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/FieldGuideManager.php');

$jobId = array_key_exists('job_id',$_POST)?$_POST['job_id']:'';
$resultUrl = array_key_exists('url',$_POST)?$_POST['url']:'';
$jobArr = explode('_',$jobId,2);
if($jobArr){
    $collid = $jobArr[0];
    $token = $jobArr[1];
    $jobID = $collid.'_'.$token;

    $rHandler = new FieldGuideManager();
    if($jobId && $resultUrl && $rHandler->validateFGResults($collid, $jobId)) {
        $rHandler->logFGResults($collid,$token,$resultUrl);
    }
}
