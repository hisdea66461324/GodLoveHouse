<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/include.php");
needUserLv(1);

$chkCenter = isset($_REQUEST["chkCenter"]) ? $_REQUEST["chkCenter"] : array();
if (count($chkCenter) == 0) {
	alertBack("선택된 항목이 없습니다.");
} 

$s_Helper = new SupportHelper();

$supporter = $s_Helper->getCenterSupportByuserid($_SESSION["userid"]);
$requests = $s_Helper->getCenterListWithCond(implode(',', $chkCenter));

showHeader("HOME > 선교사후원 > 센터사역후원","sponsor","tit_0302.gif");
body();
showFooter();

function body() {
	global $requests, $supporter;
?>
	<!-- //content -->
	<div id="content">

		<!-- //list -->
		<div class="bg_list">

		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="board_list">
		<form action="inputUserInfo.php" name="dataFrm" id="dataFrm" method="post" />
		<input type="hidden" name="idList" id="idList" value="" />
			<col width="20%" />
			<col />
			<tr>
				<th>서비스</th>
				<th class="th01">내용</th>
			</tr>
<? 
	if (count($requests) == 0) {
?>
			<tr>
				<td colspan="2">리스트가 없습니다</td>
			</tr>
<? 
	} else {
		foreach ($requests as $key=>$requestInfo) {
?>
			<tr>
				<td>
					<p class="b">[<?=$requestInfo->Title?>]</p>
					<img src="<?=$requestInfo->Image?>" width="120" height="75" class="img">
				</td>
				<td class="ltd"><?=textFormat($requestInfo->Explain, 1)?></td>
			</tr>
			<tr>
				<td class="total">&nbsp;</td>
				<td class="total3">
					<img src="../images/board/btn_10000.gif" border="0" align="absmiddle" class="m5" onclick="plus(10000, '<?=$key?>')" />
					<img src="../images/board/btn_50000.gif" border="0" align="absmiddle" class="m5" onclick="plus(50000, '<?=$key?>')" /> 매월
<?
			if (!$supporter->IsNew) {
				$price = $supporter->getItemCost($requestInfo->RequestID);
			} else {
				$price = 0;
			} 
?>
					<input type="text" maxlength="10" name="price<?=$key?>" id="price<?=$key?>" value="<?=$price?>" style="text-align:right;" onKeyUp="sum()" onKeyPress="CheckNumber(event);" style="ime-mode:disabled" />
					<input type="hidden" id="reqId<?=$key?>" name="reqId<?=$key?>" value="<?=$requestInfo->RequestID?>"/> 원
				</td>
			</tr>
<? 
		}
	} 

?>
			<tr>
				<td colspan="2">
					합계 : 후원자님이 센터사역에 후원하실 금액의 합계는
					<label id="lbPrice" name="lbPrice" style="text-align:right;" class="total2">0</label>
					<input type="hidden" id="sumPrice" name="sumPrice" value="0"> 원 입니다.
				</td>
			</tr>
		</form>
		</table>

		</div>
		<!-- list// -->

		<p class="btn_right"><img src="../images/board/btn_ok.gif" border="0" onclick="frmSubmit()" style="cursor:pointer;" /></p>
		<!--p class="btn_right">곧 지원될 예정입니다.</p-->
	</div>
	<!-- content// -->

<script type="text/javascript">
//<![CDATA[
	sum();
	
	function plus(price, idx) {
		var obj = document.getElementById('price'+idx);
		obj.value = parseInt(obj.value) + price;
		
		sum();
	}
	
	function sum() {
		var obj;
		var price = 0;
		
		for (var i = 0; i < <?=count($requests)?>; i++) { 
			obj = document.getElementById('price'+i).value;
			if (obj == '') {
				document.getElementById('price'+i).value = 0;
				obj = 0;
			}
			price += parseInt(obj);
		}
		
		document.getElementById('sumPrice').value = price;
		document.getElementById('lbPrice').innerHTML = priceFormat(price.toString());
	}
	
	function frmSubmit() {
		var idList = '';
		for(var i = 0; i < <?=count($requests)?>; i++) {
			obj = document.getElementById('price' + i).value;
			if ( parseInt(obj) > 0) {
				idList = idList + document.getElementById('reqId' + i).value + ",";
			}
		}
		
		document.getElementById('idList').value = idList.substr(0, idList.length - 1);
		document.getElementById('dataFrm').submit();
	}

	function priceFormat(nStr) {
		var strPrice = '';
		var length = nStr.length;
		for (var i = 0; i < length; i += 3) {
			if (nStr.length <= 3) {
				strPrice = nStr + strPrice;
			} else {
				strPrice = ',' + nStr.substring(nStr.length-3, nStr.length) + strPrice;
				nStr = nStr.substring(0, nStr.length-3);
			}
		}
		return strPrice;
	}
//]]>
</script>

<? 
} 
?>

