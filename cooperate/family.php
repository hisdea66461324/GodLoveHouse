<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/include.php");

$field = (isset($_REQUEST["field"])) ? trim($_REQUEST["field"]) : "";
$keyword = (isset($_REQUEST["keyword"])) ? trim($_REQUEST["keyword"]) : "";
$page = (isset($_REQUEST["page"])) ? trim($_REQUEST["page"]) : 1;

// 선교사 정보
$m_Helper = new MemberHelper();
$m_Helper->setMissionListCondition($field, $keyword);
$missions = $m_Helper->getMissionListWithPageing($page);
$strPage = $m_Helper->makePagingMissionList($page);

showHeader("HOME > 후원 > 선교사 가족과 함께","sponsor","tit_0401.gif");
body();
showFooter();

function body() {
	global $field, $keyword, $page, $strPage;
	global $missions;
?>
	<!-- //content -->
	<div id="content">
		<!-- //search -->
		<form name="findFrm" id="findFrm" action="family.php" method="get">
		<div id="search">
			<img src="../images/board/img_search.gif" class="r10" align="absmiddle">
			<select name="field" id="field">
				<option value="B.name"<?php if (($field=="B.name")) { ?> selected<?php } ?>>지역</option>
				<option value="A.church"<?php if (($field=="A.church")) { ?> selected<?php } ?>>파송기관</option>
			</select>
			<input type="text" name="keyword" id="keyword" style="width:150px" class="input" value="<?=$keyword?>">
			<img src="../images/board/btn_search.gif" border="0" align="absmiddle" onclick="frmSubmit();" style="cursor:pointer;">
		</div>
		</form>
		<!-- search// -->

		<!-- //list -->
		<div class="bg_list">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="board_list">
				<col width="7%" />
				<col width="20%" />
				<col width="20%" />
				<col />
				<col width="15%" />
				<tr>
					<th>ID</th>
					<th>사진</th>
					<th class="th01">지역</th>
					<th class="th01">이름</th>
					<th class="th01">파송단체</th>
				</tr>
<?php 
	if ((count($missions)==0)) {
?>
				<tr>
					<td colspan="5">리스트가 없습니다</td>
				</tr>
<?php 
	} else {
		for ($i=0; $i<=count($missions)-1; $i = $i+1) {
			$mission = $missions[$i];
?>
				<tr>
					<td><?=($i+1)?></td>
					<td><a href="familyDetail.php?userid=<?=$mission->userid?>"><img src="<?=$mission->fileImage?>" width="120" height="75" border="0" class="img"></a></td>
					<td><?=$mission->nation?></td>
					<td class="ltd">
						<p class="b"><?=$mission->missionName?></p>
						<p>
<?php 
			if (strlen($mission->memo) < 150) {
				print textFormat($mission->memo, 1);
			} else {
				print substr(textFormat($mission->memo, 1),0,150)."...";
			} 

?>
						</p>
					</td>
					<td><?=$mission->church?></td>
				</tr>
				<tr>
					<td colspan="5" class="total">
						<?php $count = $mission->familyCount?>
						가족참여 회원수 : <?=$count[0]?> 명 / 정기후원 회원수 : <?=$count[1]?> 명
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
<?php } ?>

<script type="text/javascript">
//<![CDATA[
	function frmSubmit() {
		var findFrm = document.getElementById("findFrm");
		findFrm.submit();
	}
//]]>
</script>
