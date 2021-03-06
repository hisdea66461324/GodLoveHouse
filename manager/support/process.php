﻿<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/include.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/manageMenu.php");

$mode = (isset($_REQUEST["mode"])) ? trim($_REQUEST["mode"]) : "";


switch (($mode)) {
	case "editRequest":
		editRequest();
		break;
	case "deleteRequest":
		deleteRequest();
		break;
	case "editRequestDetail":
		editRequestDetail();
		break;
	case "deleteRequestDetail":
		deleteRequestDetail();
		break;
	case "statusChange":
		statusChange();
		break;
} 

function editRequest() {
	$requestObj = new RequestObject();

	$supportType = (isset($_REQUEST["supportType"])) ? trim($_REQUEST["supportType"]) : "";
	$requestObj->reqId = (isset($_REQUEST["reqId"])) ? trim($_REQUEST["reqId"]) : -1;
	$requestObj->supportType = $supportType;
	$requestObj->title = (isset($_REQUEST["title"])) ? trim($_REQUEST["title"]) : "";
	$requestObj->explain = (isset($_REQUEST["explain"])) ? trim($_REQUEST["explain"]) : "";
	$requestObj->imageId = (isset($_REQUEST["idImageFile"])) ? trim($_REQUEST["idImageFile"]) : -1;
	$requestObj->Update();
	
	/*
	echo "<br>";
	echo $supportType;
	echo "<br>";
	echo $requestObj->reqId;
	echo "<br>";
	echo $requestObj->title;
	echo "<br>";
	echo $requestObj->explain;
	echo "<br>";
	echo $requestObj->imageId;
	echo "<br>";
	exit();
	*/

	switch (($supportType)) {
		case "03001":
			$requestAdd = new RequestAddInfo();
			$requestAdd->reqId = $requestObj->reqId;
			$requestAdd->userid = (isset($_REQUEST["userid"])) ? trim($_REQUEST["userid"]) : -1;
			$requestAdd->dueDate = (isset($_REQUEST["dueDate"])) ? trim($_REQUEST["dueDate"]) : -1;
			$requestAdd->nationCode = (isset($_REQUEST["nationCode"])) ? trim($_REQUEST["nationCode"]) : "";
			$requestAdd->Update();
			$retURL="index.php";
			break;
		case "03002":
			$retURL="center.php";
			break;
		case "03003":
			$retURL="service.php";
			break;
		default:
			print "에러";
			exit();

			break;
	} 

	$requestObj = null;

	$requestAdd = null;

	header("Location: ".$retURL);
} 

function deleteRequest() {
	$supportType = (isset($_REQUEST["supportType"])) ? trim($_REQUEST["supportType"]) : "";
	
	switch (($supportType)) {
		case "03001":
			$requestAdd = new RequestAddInfo();
			$reqId = (isset($_REQUEST["reqId"])) ? trim($_REQUEST["reqId"]) : -1;
			$requestAdd->Open($reqId);
			$requestAdd->Delete();
			$retURL="index.php";
			break;
		case "03002":
			$retURL="center.php";
			break;
		case "03003":
			$retURL="service.php";
			break;
		default:
			print "Error";
			exit();

			break;
	} 

	$requestObj = new RequestObject();
	$reqId = (isset($_REQUEST["reqId"])) ? trim($_REQUEST["reqId"]) : -1;
	$requestObj->Open($reqId);
	$requestObj->Delete();

	$requestObj = null;

	$requestAdd = null;

	header("Location: ".$retURL);
} 

function editRequestDetail() {
	$requestItemObj = new RequestItemObject();
	
	
	$requestItemObj->reqItemId = (isset($_REQUEST["reqItemId"])) ? trim($_REQUEST["reqItemId"]) : -1;
	$requestItemObj->reqId = (isset($_REQUEST["reqItemId"])) ? trim($_REQUEST["reqItemId"]) : -1;
	$requestItemObj->item = (isset($_REQUEST["item"])) ? trim($_REQUEST["item"]) : "";
	$requestItemObj->descript = (isset($_REQUEST["descript"])) ? trim($_REQUEST["descript"]) : "";
	$requestItemObj->cost = (isset($_REQUEST["cost"])) ? trim($_REQUEST["cost"]) : 0;
	$requestItemObj->status = (isset($_REQUEST["status"])) ? trim($_REQUEST["status"]) : "";
	$requestItemObj->Update();

	header("Location: "."subRequest.php?reqId=".$requestItemObj->RequestID);
	$requestItemObj = null;

} 

function deleteRequestDetail() {
	$requestItemObj = new RequestItemObject();
	$idx = (isset($_REQUEST["id"])) ? trim($_REQUEST["id"]) : -1;
	$requestItemObj->Open($idx);
	$requestItemObj->Delete();

	header("Location: "."subRequest.php?reqId=".$requestItemObj->reqItemId);
} 

function statusChange() {
	$supId = (isset($_REQUEST["supId"])) ? trim($_REQUEST["supId"]) : -1;
	$wait = (isset($_REQUEST["wait"])) ? trim($_REQUEST["wait"]) : 0;
	
	$support = new SupportObject();	
	$support->OpenWithSupId($supId);
	$support->ChangeStatus();
	header("Location: "."supportList.php?wait=".$wait);
} 
?>
