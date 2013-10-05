<?php 
class MemberHelper {
	var $m_eHandler;
	var $m_pageCount;
	var $m_pageUnit;
	var $m_StrConditionQuery;
	var $m_StrOrderQuery;

	#  property
	# ***********************************************
	public function __set($name, $value) { 
		switch ($name) {
			case "PAGE_UNIT":
				$this->m_pageUnit = $value;
				break;
			case "PAGE_COUNT":
				$this->m_pageCount = $value;
				break;
		}
	}

	public function __get($name) { 
		switch ($name) {
			case "PAGE_UNIT":
				return $this->m_pageUnit;
			case "PAGE_COUNT":
				return $this->m_pageCount;
			default:
				return "";
		}
	}

	#  creater
	# ***********************************************
	function __construct() {
		$this->m_eHandler = new ErrorHandler();
		$this->m_pageCount=5;
		$this->m_pageUnit=10;
	} 

	#  destoryer
	# ***********************************************
	function __destruct() {
	} 

	#  method
	# ***********************************************
	function getMemberByUserId($userId) {
		$member = new MemberObject($userId);

		if (isset($member->userid)) {
			$this->m_eHandler->ignoreError("Member Not Found.");
		} 

		return $member;
	} 

	function getMemberByUserNick($nick) {
		$member = new MemberObject();

		if ($member->OpenByNick($nick) == false) {
			$this->m_eHandler->ignoreError("Member Not Found.");
		} 


		return $member;
	} 

	function getMissionInfoByUserId($userId) {
		$mission = new MissionObject();

		if ($mission->Open($userId) == false) {
			$this->m_eHandler->ignoreError("Member Not Found.");
		} 


		return $mission;
	} 

	function getAccountInfoByUserId($userId) {
		$account = new AccountObject();

		if ($account->Open($userId) == false) {
			$this->m_eHandler->ignoreError("Account Not Found.");
		} 


		return $account;
	} 

	function getSupportByUserId($userId) {
		$support = new SupportObject();

		if ($support->Open($userId) == false) {
			$this->m_eHandler->ignoreError("Supporter Not Found.");
		} 


		return $support;
	} 

	function getFamilyType($missionId,$userId) {

		if (strlen($userId) > 0) {
			$query = "SELECT familyType FROM family WHERE userId = '".$missionId."' AND followUserId = '".$userId."'";
			$familyRS = $db->Execute($query);
			if (!$familyRS->EOF && !$familyRS->BOF) {
				$retValue = $familyRS["familyType"];
			} else {
				$retValue=false;
			} 

			$familyRS = null;

		} else {
			$retValue=false;
		} 


		return $retValue;
	} 

	#  method list Helper
	# *********************************************************
	function setCondition($userLv,$field,$keyword) {
		if ($userLv > 0) {
			$strWhere=" WHERE userLv = '".$userLv."'";
		} else {
			$strWhere=" WHERE userLv between 0 and 8 ";
		} 

		if (strlen($field) > 0 && strlen($keyword) > 0) {
			$strWhere = $strWhere." AND ".$field." LIKE '%".$keyword."%'";
		} 

		return $strWhere;
	} 

	function setOrder($order) {
		return " ORDER BY ".$order;
	} 

	function makePagingHTML($curPage) {
		$query = "SELECT COUNT(*) AS recordCount from users".$this->m_StrConditionQuery;
		$countRS = $mysqli->Execute($query);
		$total = $countRS["recordCount"];
		$countRS = null;

		return makePagingN($curPage, $this->m_pageCount, $this->m_pageUnit, $total);
	} 

	function getMemberListWithPageing($curPage) {
		$topNum = $this->m_pageCount * $curPage;

		$query = "SELECT TOP ".$topNum." * FROM users ".$this->m_StrConditionQuery.$this->m_StrOrderQuery;
		$listRS = $mysqli->Execute($query);
		if ($listRS->RecordCount > 0) {
			$listRS->PageSize = $this->m_pageCount;
			$listRS->AbsolutePage = $curPage;
		} 


		return $mysqli->Execute($query);
	} 

	function setMissionListCondition($field,$keyword) {
		if (strlen($field) > 0 && strlen($keyword) > 0) {
			$strWhere = $strWhere." AND ".$field." LIKE '%".$keyword."%'";
		} 

		return "WHERE approval = 1".$strWhere;
	} 

	function makePagingMissionList($curPage) {
		$query = "SELECT COUNT(*) AS recordCount from missionary ".$this->m_StrConditionQuery;
		$countRS = $db->Execute($query);
		$total = $countRS["recordCount"];
		$countRS = null;

		return makePagingN($curPage, $this->m_pageCount, $this->m_pageUnit, $total);
	} 

	function getMissionListWithPageing($curPage) {

		$topNum = $this->m_pageCount * $curPage;

		$query = "SELECT top ".$topNum." userid FROM missionary ".$this->m_StrConditionQuery." ORDER BY missionName";
		$missionRS = $mysqli->Execute($query);
		if (($missionRS->RecordCount>0)) {
			$missionRS->PageSize = $this->m_pageCount;
			$missionRS->AbsolutePage = $curPage;
		} 


		if (!$missionRS->Eof && !$missionRS->Bof) {
			while(!($missionRS->EOF || $missionRS->BOF)) {
				$mission = new MissionObject();
				$mission->Open($missionRS["userid"]);
			} 
		} 

		return $mission;
	} 

	function getMissionList($query) {
		global $mysqli;

		$mission_list = array();
		if ($result = $mysqli->query($query)) {
			while ($row = $result->fetch_assoc()) {
				$mission_list[] = new MissionObject($row["userid"]);
			}
		}

		return $mission_list;
	} 

	function getMemberListByPrayer($userId) {
		global $mysqli;
		$query = "SELECT userid FROM family WHERE familytype = 'F0002' AND followuserid = '".$mysqli->real_escape_string($userId)."'";
		return $this->getMissionList($query);
	} 

	function getMemberListByRegular($userId) {
		global $mysqli;
		$query = "SELECT userid FROM family WHERE familytype = 'F0001' AND followuserid = '".$mysqli->real_escape_string($userId)."'";
		return $this->getMissionList($query);
	} 
} 
?>
