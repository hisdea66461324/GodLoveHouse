﻿<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/include.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/manageMenu.php");

$mode = trim($_REQUEST["mode"]);
if ($mode=="add") {
	addCode();
} else if ($mode=="delete") {
	deleteCode();
} 


function addCode() {
	$objCode = new CodeObject();
	$objCode->Code = $_REQUEST["newCode"];
	$objCode->Name = $_REQUEST["newName"];
	$objCode->CodeType = $_REQUEST["newType"];
	$objCode->Update();

	header("Location: "."index.php");
} 

function deleteCode() {
	$objCode = new CodeObject();
	$objCode->OpenById($_REQUEST["id"]);
	$objCode->Delete();

	header("Location: "."index.php");
} 
?>

