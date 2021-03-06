<?php 
# ************************************************************
#  name : HouseHelper Class
#  description : 
#  		help to use HouseObject & RoomObject
# 		create a HouseObject & RoomObject
# 
#  editor : Sookbun Lee 
#  last update date : 2009/12/30
# ************************************************************
class HouseHelper {
	public $m_eHandler;

	public $m_pageCount;
	public $m_pageUnit;
	public $m_StrConditionQuery;

	public function __construct() {
		$this->m_eHandler = new ErrorHandler();
	} 

	public function __destruct() {
	} 

	#  property
	# ***********************************************
	public function __get($name) {
		switch ($name) {
			case "PAGE_UNIT":
				return $this->m_pageUnit;
			case "PAGE_COUNT":
				return $this->m_pageCount;
			default: 
				return null;
		}
	}
	
	public function __set($name, $value) {
 		switch ($name) {
			case "PAGE_UNIT" :
				$this->m_pageUnit = $value;
				break;
			case "PAGE_COUNT" :
				$this->m_pageCount = $value;
				break;
		}
		 
	}

	#  method : return one Object
	# ***********************************************
	function getHouseInfoById($houseId) {
		if ($house = new HouseObject($houseId)) {
			return $house;
		} 

		$this->m_eHandler->ignoreError("House Not Found.");
		return null;
	} 

	function getRoomInfoById($roomId) {
		$room = new RoomObject();

		if ($room->Open($roomId) == false) {
			$this->m_eHandler->ignoreError("Room Not Found.");
		} 

		return $room;
	} 

	#  method : Return Object List
	# ************************************************************
	function setCondition($houseId, $regionCode, $fromDate, $toDate) {
		$fromDate = explode("-", $fromDate);
		if (count($fromDate) == 3) {
			$fromDate = mktime(0, 0, 0, $fromDate[1], $fromDate[2], $fromDate[0]);
		} else {
			$fromDate = "";
		}

		$toDate = explode("-", $toDate);
		if (count($toDate) == 3) {
			$toDate = mktime(0, 0, 0, $toDate[1], $toDate[2], $toDate[0]);
		} else {
			$toDate = "";
		}
		
		$strWhere = " WHERE B.status = 'S2002' AND A.houseId = B.houseId AND A.hide = 0 ";
		if (strlen($houseId) > 0) {
			$strWhere = $strWhere." AND A.houseId = '{$houseId}'";
		} 
		if (strlen($regionCode) > 0) {
			$strWhere = $strWhere." AND B.regionCode = '{$regionCode}'";
		} 
		if (strlen($fromDate) > 0 && strlen($toDate) > 0) {
			$strWhere = $strWhere." AND A.roomId NOT IN (SELECT DISTINCT roomId FROM reservation WHERE reservStatus != 'S0004' AND startDate <= '{$toDate}' AND endDate >= '{$fromDate}' )";
		} elseif (strlen($fromDate) > 0) {
			$strWhere = $strWhere." AND A.roomId NOT IN (SELECT DISTINCT roomId FROM reservation WHERE reservStatus != 'S0004' AND endDate >= '{$fromDate}')";
		} elseif (strlen($toDate) > 0) {
			$strWhere = $strWhere." AND A.roomId NOT IN (SELECT DISTINCT roomId FROM reservation WHERE reservStatus != 'S0004' AND startDate <= '{$toDate}')";
		} 

		$this->m_StrConditionQuery = $strWhere;
	} 

	function setEtcCondition($regionCode) {
		$strWhere=" WHERE B.status = 'S2001' AND A.houseId = B.houseId AND A.hide = 0 ";
		if (strlen($regionCode) > 0) {
			$strWhere = $strWhere." AND B.regionCode = '{$regionCode}'";
		} 
		$this->m_StrConditionQuery = $strWhere;
	} 

	function makePagingHTML($curPage) {
		global $mysqli;
		$query = "SELECT COUNT(*) AS recordCount FROM room A, house B ".$this->m_StrConditionQuery;

		if ($result = $mysqli->query($query)) {
			while ($row = $result->fetch_array()) {
				$total = $row["recordCount"];
			}
		}

		return makePagingN($curPage, $this->m_pageCount, $this->m_pageUnit, $total);
	} 

	function getRoomListWithPaging($curPage, $sort="A.roomId") {
		global $mysqli;
		$rooms = array();
		
		$topNum = $this->m_pageCount * ($curPage - 1);
		$query = "SELECT A.roomId, B.houseId FROM room A, house B {$this->m_StrConditionQuery} ORDER BY {$sort} ASC LIMIT {$topNum}, {$this->m_pageCount}";

		if ($result = $mysqli->query($query)) {
			while ($row = $result->fetch_array()) {
				$rooms[] = new RoomObject($row["roomId"]);
			}
		}
		
		return $rooms;
	} 

	function getHouseList($query) {
		global $mysqli;

		$houses = array();
		if ($result = $mysqli->query($query)) {
			while ($row = $result->fetch_array()) {
				$houses[] = new HouseObject($row["houseId"]);
			}
		}

		return $houses;
	} 

	function getHouseListByEtc() {
		$query = "SELECT houseId FROM house WHERE (status = 'S2001' OR roomCount = 0)";
		return $this->getHouseList($query);
	} 

	function getHouseListByRegion($regionCode) {
		if (strlen($regionCode) == 0) {
			$query = "SELECT houseId FROM house WHERE houseId IN (SELECT distinct houseId FROM room)";
		} else {
			$query = "SELECT houseId FROM house WHERE houseId IN (SELECT distinct houseId FROM room) AND regionCode = '{$regionCode}'";
		} 

		return $this->getHouseList($query);
	} 

	function getHouseListByuserid($userid, $houseType) {
		if ($userid == "lovehouse") {
			if ($houseType == 1) {
				$query = "SELECT houseId FROM house WHERE status = 'S2002'";
			} else if ($houseType == 2) {
				$query = "SELECT houseId FROM house WHERE status = 'S2004'";
			} else {
				$query = "SELECT houseId FROM house WHERE status = 'S2001'";
			} 
		} else {
			if ($houseType == 1) {
				$query = "SELECT houseId FROM house WHERE userid = '$userid' AND status = 'S2002'";
			} else if ($houseType == 2) {
				$query = "SELECT houseId FROM house WHERE userid = '$userid' AND status = 'S2004'";
			} else {
				$query = "SELECT houseId FROM house WHERE userid = '$userid' AND status = 'S2001'";
			}
		}

		return $this->getHouseList($query);
	} 

	function setReservationListConditionWithHouse($search, $houseId, $fromDate = 0, $toDate = 0) {
		switch ($search) {
			case "1":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0001' AND A.houseId = '".$houseId."'";
				break;
			case "2":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0002' AND A.houseId = '".$houseId."'";
				break;
			case "3":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0003' AND A.houseId = '".$houseId."'";
				break;
			case "4":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0004' AND A.houseId = '".$houseId."'";
				break;
			default:
				$this->m_StrConditionQuery = " AND C.reservStatus <> 'S0004' AND A.houseId = '".$houseId."'";
				break;
		} 

		if ($fromDate > 9) {
			$this->m_StrConditionQuery.= " AND C.startDate <= {$toDate} AND C.endDate >= {$fromDate}";
		}
	} 

	function setReservationListConditionWithRoom($search, $houseId, $roomId, $fromDate = 0, $toDate = 0) {
		if ($roomId == "") {
			$this->setReservationListConditionWithHouse($search, $houseId, $fromDate, $toDate);
			return;
		}

		switch ($search) {
			case "1":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0001' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
			case "2":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0002' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
			case "3":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0003' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
			case "4":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0004' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
			default:
				$this->m_StrConditionQuery = " AND C.reservStatus <> 'S0004' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
		} 

		if ($fromDate > 9) {
			$this->m_StrConditionQuery.= " AND C.startDate <= {$toDate} AND C.endDate >= {$fromDate}";
		}
	} 

	function setReservationListCondition_n($search, $houseId, $roomId) {
		switch ($search) {
			case "1":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0001' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
			case "2":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0002' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
			case "3":
				$this->m_StrConditionQuery=" AND C.reservStatus = 'S0003' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
			case "4":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0004' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
			default:
				$this->m_StrConditionQuery = " AND C.reservStatus <> 'S0004' AND A.houseId = '".$houseId."' AND B.roomId = '".$roomId."'";
				break;
		} 
	} 

	function setReservationListCondition($search) {
		switch ($search) {
			case "1":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0001' ";
				break;
			case "2":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0002' ";
				break;
			case "3":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0003' ";
				break;
			case "4":
				$this->m_StrConditionQuery = " AND C.reservStatus = 'S0004' ";
				break;
			default:
				$this->m_StrConditionQuery = " AND C.reservStatus <> 'S0004' ";
				break;
		} 
	} 

	function makeReservationListPagingHTML($curPage) {
		global $mysqli;
		
		$query = "SELECT COUNT(*) AS recordCount FROM house A, room B, reservation C ";
		if ($_SESSION['userid'] == "lovehouse") {
			$query = $query." WHERE A.houseId = B.houseId AND B.roomId = C.roomId ".$this->m_StrConditionQuery;
		} else {
			$query = $query." WHERE A.houseId = B.houseId AND B.roomId = C.roomId AND A.userid = '".$_SESSION['userid']."' ".$this->m_StrConditionQuery;
		} 

		if ($result = $mysqli->query($query)) {
			while ($row = $result->fetch_array()) {
				$total = $row["recordCount"];
			}
		}

		return makePagingN($curPage, $this->m_pageCount, $this->m_pageUnit, $total);
	} 

	function getReservationListWithPaging($curPage, $debug = false) {
		global $mysqli;

		$query = "SELECT C.reservationNo FROM house A, room B, reservation C ";
		if ($_SESSION['userid'] == "lovehouse") {
			$query = $query." WHERE A.houseId = B.houseId AND B.roomId = C.roomId ".$this->m_StrConditionQuery;
		} else {
			$query = $query." WHERE A.houseId = B.houseId AND B.roomId = C.roomId AND A.userid = '".$mysqli->real_escape_string($_SESSION['userid'])."' ".$this->m_StrConditionQuery;
		} 

		$start = $this->m_pageCount * ($curPage - 1);
		$query = $query." ORDER BY C.reservationNo DESC LIMIT $start, {$this->m_pageCount}";

		if ($debug) {
			echo $query;
		}

		$reserveInfo = array();
		if ($result = $mysqli->query($query)) {
			while ($row = $result->fetch_assoc()) {
				$reserveInfo[] = new ReservationObject($row["reservationNo"]);
			}
			$result->close();
		}

		return $reserveInfo;
	} 

	function getReservationList($query) {
		global $mysqli;
		
		$reserveInfo = array();
		if ($result = $mysqli->query($query)) {
			while ($row = $result->fetch_array()) {
				array_push($reserveInfo, new ReservationObject($row["reservationNo"]));
			}
		}

		return $reserveInfo;
	} 

	function getReservationListByManager($curPage) {
		global $mysqli;

		$query = "SELECT C.reservationNo FROM house A, room B, reservation C ";
		if ($_SESSION['userid'] == "lovehouse") {
			$query = $query."WHERE A.houseId = B.houseId AND B.roomId = C.roomId";
		} else {
			$query = $query."WHERE A.houseId = B.houseId AND B.roomId = C.roomId AND A.userid = '".$mysqli->real_escape_string($_SESSION['userid'])."'";
		} 

		return $this->getReservationList($query);
	} 

	function getReservationListByUser($curPage, $status = "0") {
		global $mysqli;

		$query = "SELECT C.reservationNo FROM house A, room B, reservation C ";
		$query = $query."WHERE A.houseId = B.houseId AND B.roomId = C.roomId AND C.userid = '".$mysqli->real_escape_string($_SESSION['userid'])."' ";
		if ($status == "1") {
			$query .= " AND C.reservStatus = 'S0001'";
		} else if ($status == "2") {
			$query .= " AND C.reservStatus = 'S0002'";
		} else if ($status == "3") {
			$query .= " AND C.reservStatus = 'S0003'";
		} else if ($status == "4") {
			$query .= " AND C.reservStatus = 'S0004'";
		}
		$query = $query."ORDER BY C.regDate DESC";
		return $this->getReservationList($query);
	} 
} 
?>
