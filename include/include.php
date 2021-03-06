<?php
error_reporting(E_ALL); 
ini_set("display_errors", E_ALL); 
ini_set("session.cookie_lifetime",60 * 60 * 24); // expire time 1 day

//TEST를 위해서 다음줄 주석 처리 나중에 다시 주석부분을 풀어야 함 
session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/include/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dbconn.php");
#require "class/dbHelper.php";
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/ErrorHandler.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/tableBuilder.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/DataManager.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/BoardHelper.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/MemberHelper.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/CodeHelper.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/CommentHelper.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/SupportHelper.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/HouseHelper.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/class/HospitalHelper.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/function/global.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/function/converter.php");
#require "function/validator.php";
#require "function/math.php";
#require "function/file.php";
require_once($_SERVER['DOCUMENT_ROOT']."/include/function/script.php");
#require "function/fileuploadComm.php";
#require "function/string.php";
#require "function/debug.php";
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/AttachFile.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/CodeObject.php");
#require "dataFormat/CommentObject.php";
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/MemberObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/MissionObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/MissionaryFamily.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/AccountObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/SupportObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/SupportItemObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/RequestObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/RequestAddInfo.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/RequestItemObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/HouseObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/HospitalObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/RoomObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/ReservationObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/BoardObject.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/dataFormat/BoardGroup.php");

if (isset($_SESSION['userid']) && strlen($_SESSION['userid']) > 0) {
	if (isset($_SESSION['userLv']) && $_SESSION['userLv'] == "") {
		global $mysqli;
		$member = new MemberObject($_SESSION['userid']);
		$_SESSION['userLv'] = $member->userlv;
	}
}

function MoveToPage($page) {
	//header("Location: ".$page);
	echo '<meta http-equiv="Refresh" content="0; url='.$page.'">';
}
?>
