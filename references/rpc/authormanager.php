<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/ReferenceManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$uid = array_key_exists('uid',$_REQUEST)?(int)$_REQUEST['uid']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$refId = array_key_exists('refid',$_REQUEST)?(int)$_REQUEST['refid']:0;
$refAuthId = array_key_exists('refauthid',$_REQUEST)?(int)$_REQUEST['refauthid']:0;
$firstName = array_key_exists('firstname',$_REQUEST)?$_REQUEST['firstname']:'';
$middleName = array_key_exists('midname',$_REQUEST)?$_REQUEST['midname']:'';
$lastName = array_key_exists('lastname',$_REQUEST)?$_REQUEST['lastname']:'';
$table = array_key_exists('table',$_REQUEST)?$_REQUEST['table']:'';
$field = array_key_exists('field',$_REQUEST)?$_REQUEST['field']:'';
$id = array_key_exists('id',$_REQUEST)?(int)$_REQUEST['id']:0;
$type = array_key_exists('type',$_REQUEST)?$_REQUEST['type']:'';

$refManager = new ReferenceManager();
if($action === 'addauthor'){
	$refManager->addAuthor($refId,$refAuthId);
	$authArr = $refManager->getRefAuthArr($refId);
	$listHtml = '';
	if($authArr){
		$listHtml .= '<ul>';
		foreach($authArr as $k => $v){
			$listHtml .= '<li>';
			$listHtml .= '<a href="authoreditor.php?authid='.$k.'" target="_blank">'.$v.'</a>';
			$listHtml .= ' <button style="margin:0;padding:2px;" type="button" onclick="deleteRefAuthor('.$k.');" title="Delete author"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></button>';
			$listHtml .= '</li>';
		}
		$listHtml .= '</ul>';
	}
	else{
		$listHtml .= '<div><b>There are currently no authors connected with this reference.</b></div>';
	}
	echo $listHtml;
}
if($action === 'createauthor'){
	$refManager->createAuthor($firstName,$middleName,$lastName);
	$refAuthId = $refManager->getRefAuthId();
	$refManager->addAuthor($refId,$refAuthId);
	$authArr = $refManager->getRefAuthArr($refId);
	$listHtml = '';
	if($authArr){
		$listHtml .= '<ul>';
		foreach($authArr as $k => $v){
			$listHtml .= '<li>';
			$listHtml .= '<a href="authoreditor.php?authid='.$k.'" target="_blank">'.$v.'</a>';
			$listHtml .= ' <button style="margin:0;padding:2px;" type="button" onclick="deleteRefAuthor('.$k.');" title="Delete author"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></button>';
			$listHtml .= '</li>';
		}
		$listHtml .= '</ul>';
	}
	else{
		$listHtml .= '<div><b>There are currently no authors connected with this reference.</b></div>';
	}
	echo $listHtml;
}
if($action === 'deleterefauthor'){
	$refManager->deleteRefAuthor($refId,$refAuthId);
	$authArr = $refManager->getRefAuthArr($refId);
	$listHtml = '';
	if($authArr){
		$listHtml .= '<ul>';
		foreach($authArr as $k => $v){
			$listHtml .= '<li>';
			$listHtml .= '<a href="authoreditor.php?authid='.$k.'" target="_blank">'.$v.'</a>';
			$listHtml .= ' <button style="margin:0;padding:2px;" type="button" onclick="deleteRefAuthor('.$k.');" title="Delete author"><i style="height:15px;width:15px;" class="far fa-trash-alt"></i></button>';
			$listHtml .= '</li>';
		}
		$listHtml .= '</ul>';
	}
	else{
		$listHtml .= '<div><b>There are currently no authors connected with this reference.</b></div>';
	}
	echo $listHtml;
}
if($action === 'deletereflink'){
	$refManager->deleteRefLink($refId,$table,$field,$id);
	$authArr = $refManager->getRefAuthArr($refId);
	$listHtml = '';
	if($authArr){
		$listHtml .= '<ul>';
		foreach($authArr as $k => $v){
			$onClick = "deleteRefLink('".$table."','".$field."','".$type."',".$k. ');';
			$listHtml .= '<li>';
			$listHtml .= $v;
			$listHtml .= '</li>';
		}
		$listHtml .= '</ul>';
	}
	else{
		$listHtml .= 'There are no '.$type.' linked with this reference';
	}
	echo $listHtml;
}
