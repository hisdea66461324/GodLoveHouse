<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/include.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/manageMenu.php");

$mode = trim($_REQUEST["mode"]);

switch ($mode) {
	case "regist":
		registHouse();
		break;
	case "reservation":
		needUserLv(1);
		reservation();
		break;
	default:
		print "잘못된 접근";
		exit();
		break;
} 

function registHouse() {
	$house = new HouseObject();

	$contact[0] = $_REQUEST["contact1"];
	$contact[1] = $_REQUEST["contact2"];
	$contact[2] = $_REQUEST["contact3"];
	$zipcode[0] = $_REQUEST["post1"];
	$zipcode[1] = $_REQUEST["post2"];

	if ((strlen($_REQUEST["houseId"])>0)) {
		$house->HouseID = $_REQUEST["houseId"];
		$house->userid = $_REQUEST["userid"];
	} 

	$house->HouseName = $_REQUEST["houseName"];
	$house->AssocName = $_REQUEST["assocName"];
	$house->Manager1 = $_REQUEST["manager"];
	$house->Contact1 = $contact;
	$house->BuildingType = $_REQUEST["buildType"];
	$house->Zipcode = $zipcode;
	$house->Address1 = $_REQUEST["addr1"];
	$house->Address2 = $_REQUEST["addr2"];
	$house->Price = $_REQUEST["price"];
	$house->PersonLimit = $_REQUEST["personLimit"];
	$house->RoomLimit = $_REQUEST["roomLimit"];
	$house->Explain = $_REQUEST["explain"];
	$house->RegionCode = $_REQUEST["regionCode"];
	$house->Update();
	$house = null;

	alertGoPage("선교관 등록요청이 되었습니다.","registHouse.php");
} 

function reservation() {
	$book = new reservationObject();

	$houseId = $_REQUEST["houseId"];
	$book->StartDate = $_REQUEST["startDate"];
	$book->EndDate = $_REQUEST["endDate"];
	$book->RoomId = $_REQUEST["roomId"];
	$book->userid = $_SESSION['userid'];
	$book->resv_name = $_REQUEST['resv_name'];
	$book->resv_phone = $_REQUEST['resv_phone'];
	$book->resv_nation = $_REQUEST['resv_nation'];
	$book->resv_assoc = $_REQUEST['resv_assoc'];

	if (!$book->checkId()) {
		header("Location: "."reservation.php?houseId=".$houseId);
	} else if (!$book->checkDate()) {
		header("Location: "."reservationDetail.php?roomId=".$book->RoomId);
	} 

	$book->Update();

	alertGoPage("예약요청 되었습니다.","reservation.php");

	# SMS 메세지 보내기	
	$house = new HouseObject($houseId);
	$manager = new MemberObject($house->userid);

	$from_number = "01010041004";
	$message = "선교관 예약 신청이 들어왔습니다."." 선교관 : ".$house->HouseName." 예약날짜 : ".$_REQUEST["startDate"]." ~ ".$_REQUEST["endDate"];

	sendSMSMessage($from_number, $Join["01085916394"][""], $message);
	sendSMSMessage($from_number, $Join[$manager->Mobile][""], $message);
} 
?>
