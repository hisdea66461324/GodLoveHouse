<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/include.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/manageMenu.php");

$mode = isset($_REQUEST["mode"]) ? trim($_REQUEST["mode"]) : "";
checkUserLogin();

if ($mode=="addSupport") {
	addSupport();
} else if ($mode=="addCenterSupport") {
	addCenterSupport();
} else if ($mode=="addServiceSupport") {
	addServiceSupport();
} 


function addSupport() {
	$c_Helper = new CodeHelper();

	# 후원 정보	
	$support = new SupportObject();
	$support->Open($_SESSION["userid"], $c_Helper->getSupportCode(1));
	$support->Name = $_REQUEST["name"];
	$support->Jumin = $_REQUEST["nid1"].$_REQUEST["nid2"];
	$support->Phone = $_REQUEST["tel1"]."-".$_REQUEST["tel2"]."-".$_REQUEST["tel2"];
	$support->Mobile = $_REQUEST["hp1"]."-".$_REQUEST["hp2"]."-".$_REQUEST["hp3"];
	$support->Email = $_REQUEST["email1"]."@".$_REQUEST["email2"];
	$support->Zipcode = $_REQUEST["post1"].$_REQUEST["post2"];
	$support->Address1 = $_REQUEST["addr1"];
	$support->Address2 = $_REQUEST["addr2"];
	$support->SupportType = $c_Helper->getSupportCode(1);
	$support->Update();

	//후원 항목 예약	
	$detailIDList = explode(",", trim($_REQUEST["detailId"]));
	$s_Helper = new SupportHelper();
	$reqItemList = $s_Helper->getReqItemListByReqId($_REQUEST["reqId"]);
	
	for ($i = 0; $i <= count($reqItemList) - 1; $i++) {
		$checkValue=false;
		foreach ($detailIDList as $detailId) {
			if (($reqItemList[$i]->RequestItemID==intval(trim($detailId)))) {
				//해당 항목 예약
				$reqItemList[$i]->SendUser = $_SESSION["userid"];
				$reqItemList[$i]->Update();
				$checkValue=true;
			} 
		}
		if (!$checkValue && strlen($reqItemList[$i]->SendUser) > 0 && $reqItemList[$i]->SendUser == $_SESSION["userid"]) {
			//해당 항목 예약 삭제
			$reqItemList[$i]->SendUser = "";
			$reqItemList[$i]->Update();
		} 
	}

	# 후원 상세 정보	$supportItem = new SupportItemObject();
	$supportItem->OpenWithIndex($support->SupportID, $_REQUEST["reqId"]);
	$supportItem->Cost = $_REQUEST["sumPrice"];
	$supportItem->update();

	alertGoPage("신청되었습니다", "http://".$_SERVER['HTTP_HOST']."/sponsor/special.php");
} 

function addCenterSupport() {
	//입금 정보	
	$m_Helper = new MemberHelper();
	$account = $m_Helper->getAccountInfoByuserid($_SESSION["userid"]);
	$member = $m_Helper->getMemberByuserid($_SESSION["userid"]);
	$account->Method = $_REQUEST["method"];
	$account->Bank = $_REQUEST["bank"];
	$account->Number = $_REQUEST["number"];
	$account->Name = $_REQUEST["accName"];
	$account->Jumin = $_REQUEST["jumin1"].$_REQUEST["jumin2"];
	$account->SendDate = $_REQUEST["sendDate"];
	$account->ExpectDate = $_REQUEST["expectDate"];
	$account->Update();

	// 후원 정보	
	$c_Helper = new CodeHelper();
	$support = new SupportObject();
	$support->Open($_SESSION["userid"], $c_Helper->getSupportCode(2));
	$support->Name = $_REQUEST["supName"];
	$support->Jumin = $_REQUEST["supNID"];
	$support->Phone = $_REQUEST["phone"];
	$support->Mobile = $_REQUEST["mobile"];
	$support->Email = $_REQUEST["email"];
	$support->Zipcode = $_REQUEST["zipcode"];
	$support->Address1 = $_REQUEST["address1"];
	$support->Address2 = $_REQUEST["address2"];
	$support->SupportType = $c_Helper->getSupportCode(2);
	$support->Update();

	//후원 상세 정보 삭제	
	$s_Helper = new SupportHelper();
	$s_Helper->delSupItemListBySupId($support->SupportID);

	//후원 상세 정보 등록	
	$idList=explode(" ",trim($_REQUEST["idList"]));
	$priceList=explode(" ",trim($_REQUEST["priceList"]));
	for ($i=0; $i<=count($idList); $i = $i+1) {
		$m_items = new SupportItemObject();
		$m_items->OpenWithIndex($support->SupportID, $idList[$i]);
		$m_items->Cost = $priceList[$i];
		$m_items->Update();
	}


	alertGoPage("신청되었습니다", "http://".$_SERVER['HTTP_HOST']."/sponsor/center.php");
} 

function addServiceSupport() {
	$check = trim($_REQUEST["check"]);
	if (strlen(trim($_REQUEST["check"])) == 0) {
		alertBack("선택된 항목이 없습니다.");
	} 


	$m_Helper = new MemberHelper();
	$member = $m_Helper->getMemberByuserid($_SESSION["userid"]);
	$c_Helper = new CodeHelper();

	// 후원 정보	
	$support = new SupportObject();
	$support->Open($_SESSION["userid"], $c_Helper->getSupportCode(3));
	$support->Name = $member->Name;
	$support->Jumin = implode('-', $member->Jumin);
	$support->Phone = implode('-', $member->Phone);
	$support->Mobile = implode('-', $member->Mobile);
	$support->Email = implode('@', $member->Email);
	$support->Zipcode = implode('-', $member->Zipcode);
	$support->Address1 = $member->Address1;
	$support->Address2 = $member->Address2;
	$support->SupportType = $c_Helper->getSupportCode(3);
	$support->Update();

	//후원 상세 정보 삭제	
	$s_Helper = new SupportHelper();
	$s_Helper->delSupItemListBySupId($support->SupportID);

	//후원 상세 정보 등록	
	$idList = explode(",", trim($_REQUEST["check"]));
	foreach ($idList as $support_item_id) {
		$m_items = new SupportItemObject();
		$m_items->OpenWithIndex($support->SupportID, $support_item_id);
		$m_items->Update();
	}

	alertGoPage("신청되었습니다", "http://".$_SERVER['HTTP_HOST']."/sponsor/service.php");
} 
?>
