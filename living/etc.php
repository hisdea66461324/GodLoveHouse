<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/include.php");

$regionCode = (isset($_REQUEST["region"])) ? trim($_REQUEST["region"]) : "";
$page = (isset($_REQUEST["page"])) ? trim($_REQUEST["page"]) : 1;

$c_Helper = new CodeHelper();
$h_Helper = new HouseHelper();
$h_Helper->PAGE_UNIT = 10; //하단 페이징 단위 
$h_Helper->PAGE_COUNT = 7; //한페이지에 보여줄 리스트 갯수 
$h_Helper->setEtcCondition($regionCode); // 조건문 작성
$strPage = $h_Helper->makePagingHTML($page);
$codes = $c_Helper->getLocalCodeList();
$houses = $h_Helper->getHouseListByRegion($regionCode);
$rooms = $h_Helper->getRoomListWithPaging($page);

showHeader("HOME > 선교관 >	기타 선교관 안내","living","tit_0201.gif");
body();
showFooter();

function body() {
	global $page, $strPage;
	global $codes, $rooms, $regionCode;
	global $h_Helper, $c_Helper;
?>
		<!-- //content -->
		<div id="content">
			<!-- //search -->
		<form name="findFrm" id="findFrm" action="etc.php" method="get">
			<div id="search"> <img src="../images/board/img_search.gif" class="r10" align="absmiddle">
				<select name="region" id="region" onchange="selectRegion()">
					<option value=''>-- 지역선택 --</option>
<?php
	foreach ($codes as $codeObj) {
		if ($regionCode == $codeObj->Code) {
			print "<option value='".$codeObj->Code."' selected>".$codeObj->Name."</option>";
		} else {
			print "<option value='".$codeObj->Code."'>".$codeObj->Name."</option>";
		} 
	}

?>
				</select>
		 <img src="../images/board/btn_search.gif" border="0" align="absmiddle" style="cursor:pointer;" onclick="frmSubmit()">
		</div>
		 <input type="hidden" name="page" id="page" value="<?=$page?>" />
		</form>
			<!-- search// -->
			<!-- //list -->
			<div class="bg_list">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="board_list">
					<col width="7%" />
					<col width="20%" />
					<col width="20%" />
					<col />
					<tr>
						<th>No</th>
						<th>이미지</th>
						<th class="th01">선교관(방이름)</th>
						<th class="th01">내용</th>
					</tr>
<?php
	if (count($rooms) == 0) {
?>
					<tr>
						<td colspan="4">
							리스트가 없습니다
						</td>
					</tr>
<?php
	} else {
		$i = -1;
		foreach ($rooms as $roomObj) {
			$i++;
			$houseObj = $h_Helper->getHouseInfoById($roomObj->HouseID);
?>
			<tr>
				<td><?=(($page - 1) * $h_Helper->PAGE_COUNT) + ($i + 1)?></td>
				<td>
					<a href="reservationDetail.php?houseId=<?=$roomObj->HouseID?>&roomId=<?=$roomObj->RoomID?>">
					<img src="<?=$roomObj->Image1?>" width="120" height="75" border="0" class="img"></a></td>
							<td>
					<a href="reservationDetail.php?houseId=<?=$roomObj->HouseID?>&roomId=<?=$roomObj->RoomID?>">
					<?=$houseObj->HouseName?><br />(<?=$roomObj->RoomName?>)
					</a>
				</td>
				<td class="ltd">
					<ul class="intro">
						<li><b>운영</b> : <?=$houseObj->AssocName?></li>
						<li><b>주소</b> : <a href="#" Onclick="javascript:window.open('../navermaps/a5.php?Naddr=<?=rawurlencode($houseObj->Address1.$houseObj->Address2)?>','win','top=0, left=500,width=550,height=450')"><?=$houseObj->Address1?></a></li>
						<li><b>담당자</b> : <?=$houseObj->Manager1?></li>
						<li><b>요금</b> : <?=$roomObj->showFee()?></li>
					</ul>
				</td>
				<td class="ltd" width="100">
					<span class="btn1g"><a href="reservationDetail.php?houseId=<?=$roomObj->HouseID?>&roomId=<?=$roomObj->RoomID?>">자세히 보기</a></span>
				</td>
			</tr>
<?php 
		}
	}
?>
				</table>
			</div>
			<!-- list// -->
			<!-- //page -->
			<?=$strPage?>
			<!-- page// -->
		</div>
	<!-- content// -->
<?php 
} 
?>

<script type="text/javascript">
//<![CDATA[
	calendar_init();
	
	function selectRegion() {
		var region = document.getElementById("region").value;
		if (region.length == 0) {
			location.href = "etc.php";
		}
		
		location.href = "etc.php?region=" + region;
	}
	
	function selectHouse() {
		var house = document.getElementById("houseId").value;
		if (house.length == 0) {
			return;
		}
		location.href = "etc.php?region=<?=$regionCode?>&houseId=" + house;
	}
	
	function frmSubmit() {
		var endDate = document.getElementById("toDate").value;
		var startDate = document.getElementById("fromDate").value;

		if (startDate.length == 0 || endDate.length == 0) {
			alert('숙박 기간을 정확히 입력해 주세요');
			return;
		}

		if (startDate.replace(/-/g,'') >= endDate.replace(/-/g,'')) {
			alert('기간이 잘못되었습니다.');
			return;
		}

		var findFrm = document.getElementById("findFrm");
		findFrm.submit();
	}
//]]>
</script>
