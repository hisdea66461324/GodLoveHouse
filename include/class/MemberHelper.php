<?php 
class MemberHelper {
	protected $record = array();
	//protected $eHandler = new ErrorHandler();
	
	public function __set($name,$value) { 
		switch($name) {
			case "PAGE_UNIT" :
												$this->record['pageUnit'] = $value;
												break;
			case "PAGE_COUNT" :		
												$this->record['pageCount'] = $value;						
												break;
			default : 
						$this->record[$name] = $value;
						break;
		}
	}
	
	public function __get($name) { 
		switch ($name) {
			case "PAGE_UNIT" :
												return $this->record['pageUnit'];
			case "PAGE_COUNT" : 
												return $this->record['pageCount'];
			
			default : 
				return $this->record[$name];
		}
	}
	
	public function __isset($name) {
		return isset($this->record[$name]); 
  }
  
  
  function __construct($idx = -1) {
		$this->record['pageCount'] = 5;
		$this->record['pageUnit'] = 10;
		$this->record['strConditionQuery'] = "";
		$this->record['strOrderQuery'] = "";
	}
	
	function getMemberByUserId($userId) {
		$member = new MemberObject();

		if ($member->Open($userId)==false) {
			//$this->eHandler->ignoreError("Member Not Found.");
			echo "Member Not Found";
		} 
		return $member;
	} 
	
	function getMemberByUserNick($nick) {
		$member = new MemberObject();

		if ($member->OpenByNick($nick) == false) {
			//$this->m_eHandler->ignoreError("Member Not Found.");
			echo "Member Not Found.";
		} 
		return $member;
	} 
	
	function getMissionInfoByUserId($userId) {
		$mission = new MissionObject();

		if ($mission->Open($userId) == false) {
			//$this->m_eHandler->ignoreError("Member Not Found.");
			echo "Member Not Found";
		} 


		return $mission;
	}
	
	
	function getAccountInfoByUserId($userId) {
		$account = new AccountObject();

		if ($account->Open($userId) == false) {
			//$m_eHandler->ignoreError("Account Not Found.");
			echo "Account Not Found.";
		} 
		return $account;
	} 

	function getSupportByUserId($userId) {
		$support = new SupportObject();

		if ($support->Open($userId) == false) {
			//$m_eHandler->ignoreError("Supporter Not Found.");
			echo "Supporter Not Found.";
		} 


		return $support;
	} 	

	function getFamilyType($missionId,$userId) {
		
		global $mysqli;
		$familyType = "";
		if (strlen($userId) > 0) {
			
			$stmt = $mysqli->prepare("SELECT `familyType` FROM family WHERE `userId` = ? AND `followUserId` = ?");
			$stmt->bind_param("ii", $userId, $missionId);
			$stmt->execute();
			$stmt->bind_result($familyType);
			$stmt->close();
		
			if(strlen($familyType) > 0) {
				return $familyType;
			}
			else 
				return false;
		}
	}
	
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
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT CNT(*) AS recordCount from users".$this->record['strConditionQuery']);
		$stmt->execute();
		$stmt->bind_result($total);
		$stmt->close();
		
		return makePagingN($curPage, $this->record['pageCount'], $this->record['pageUnit'], $total);
	} 
	
	function getMemberListWithPageing($curPage) {
		global $mysqli;
		
		$topNum =  $this->record['pageCount'] * ($curPage - 1);
		$query = "SELECT * FROM users ".$this->record['strConditionQuery'].$this->record['strOrderQuery']." LIMIT $topNum, ".$this->record['pageCount'];
		$stmt = $mysqli->prepare($query);
		$stmt->execute();
		$stmt->close();

		return $mysqli->prepare($query);
	} 
	
	function setMissionListCondition($field,$keyword) {
		$strWhere = "";
		if (strlen($field) > 0 && strlen($keyword) > 0) {
			$strWhere = $strWhere." AND ".$field." LIKE '%".$keyword."%'";
		} 

		return "WHERE approval = 1 ".$strWhere;
	} 
	
	function makePagingMissionList($curPage) {
		global $mysqli;
		$recordCount = 0;
		
		$query = "SELECT COUNT(*) AS `recordCount` FROM missionary ".$this->record['strConditionQuery'];
		$stmt = $mysqli->prepare($query);
		$stmt->execute();
		$stmt->bind_result($recordCount);
		$stmt->close();
		return makePagingN($curPage, $this->record['pageCount'], $this->record['pageUnit'], $recordCount);
	} 
	
	function getMissionListWithPageing($curPage) {
		global $mysqli;
		$userId = -1;
		
		$topNum =  $this->record['pageCount'] * ($curPage - 1);
		$query = "SELECT `userid` FROM missionary ".$this->record['strConditionQuery']." ORDER BY `missionName` LIMIT $topNum, ".$this->record['pageCount'];;
		$stmt = $mysqli->prepare($query);
		$stmt->execute();
		$stmt->bind_result($userId);
		$stmt->close();
		
		
		if($userId > 0) {
			$mission = new MissionObject();
			$mission->Open($userId);
		}
		else {
			$mission = null;
		}
		
		return $mission;
	} 
	
	function getMissionList($query) {
		global $mysqli;
		$userId = -1;
		$stmt = $mysqli->prepare($query);
		$stmt->execute();
		$stmt->bind_result($userId);
		$stmt->close();
		
		
		if($useId > 0) {
			$mission = new MissionObject();
			$mission->Open($useId);
		}
		
		return $mission;
	}
	
	
	function getMemberListByPrayer($key) {
		global $mysqli;
		$userId = -1;
		$query = "SELECT `userId` FROM family WHERE `familyType` = 'F0002' AND `followUserId` = ? ";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("i", $key);
		$stmt->execute();
		$stmt->bind_result($userId);
		$stmt->close();
	
		if($useId > 0) {
			$mission = new MissionObject();
			$mission->Open($useId);
		}
		
		return $mission;
	} 

	function getMemberListByRegular($key) {
		global $mysqli;
		$userId = -1;
		$query = "SELECT `userId` FROM family WHERE `familyType` = 'F0001' AND `followUserId` = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("i", $key);
		$stmt->execute();
		$stmt->bind_result($userId);
		$stmt->close();
	
		if($useId > 0) {
			$mission = new MissionObject();
			$mission->Open($useId);
		}
		
		return $mission;
	} 
}

?>
