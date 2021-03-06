<?php
require_once($_SERVER['DOCUMENT_ROOT']."/include/include.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/manageMenu.php");

$field = (isset($_REQUEST["field"])) ? trim($_REQUEST["field"]) : "";
$keyword = (isset($_REQUEST["keyword"])) ? trim($_REQUEST["keyword"]) : "";
$userLv = (isset($_REQUEST["userLv"])) ? trim($_REQUEST["userLv"]) : 0;
$gotoPage = (isset($_REQUEST["gotoPage"])) ? trim($_REQUEST["gotoPage"]) : "";
$uid = (isset($_REQUEST["userid"])) ? trim($_REQUEST["userid"]) : "";

$m_Helper = new MemberHelper();
$member = $m_Helper->getMemberByuserid($uid);
$mission = $m_Helper->getMissionInfoByuserid($uid);

$c_Helper = new CodeHelper();
$codes = $c_Helper->getNationCodeList();

if ($member->userLv != "3") {
	$display="none";
	$btnValue="선교사 정보 추가입력";
	$churchContact = array("","","");
	$managerContact = array("","","");
	$memo="간단한 소개 :".chr(13).chr(13).chr(13)."사역소개 :".chr(13).chr(13).chr(13);
	$prayList="1.".chr(13).chr(13)."2.".chr(13).chr(13)."3.".chr(13).chr(13);
	$familyList = array();
} else {
	$display="";
	$btnValue="선교사 정보 입력취소";
	$churchContact = explode("-", $mission->churchContact);
	$managerContact = explode("-", $mission->managerContact);
	$memo = $mission->memo;
	$prayList = $mission->prayList;
	$familyList = $mission->family;
} 

checkAuth();
showAdminHeader("관리툴 - 회원등록","","","");
body();
showAdminFooter();

function body() {
		global $field, $keyword, $gotoPage;
		global $display, $btnValue, $churchContact, $managerContact, $memo, $prayList, $familyList;
		global $uid, $userLv;
		global $member, $mission, $codes;
?>
	<div class="sub">
	<a href="editForm.php">회원등록</a> | 
	<a href="index.php?userLv=0">전체목록</a> | 
	<a href="index.php?userLv=1">일반회원</a> |
	<a href="index.php?userLv=3">선교사</a> | 
	<a href="index.php?userLv=7">선교관관리자</a>
	</div>
	</div>
	<div id="wrap">
		<div class="lSec">
		<ul>
			<li><img src="/images/manager/lm_0100.gif"></li>
		<li><a href="editForm.php"><img src="/images/manager/lm_0101.gif"></a></li>
		<li><a href="index.php?userLv=0"><img src="/images/manager/lm_0102.gif"></a></li>
		<li><a href="index.php?userLv=1"><img src="/images/manager/lm_0103.gif"></a></li>
		<li><a href="index.php?userLv=3"><img src="/images/manager/lm_0104.gif"></a></li>
		<li><a href="index.php?userLv=7"><img src="/images/manager/lm_0105.gif"></a></li>
		<li><img src="/images/manager/lm_bot.gif"></li>
		</ul>
	</div>
	<div class="rSec">
	
	<!-- div class="main" -->
		<form name="dataForm" id="dataForm" method="post">
		<input type="hidden" id="field" name="field" value="<?=$field?>" />
		<input type="hidden" id="keyword" name="keyword" value="<?=$keyword?>" />
		<input type="hidden" id="gotoPage" name="gotoPage" value="<?=$gotoPage?>" />
		<input type="hidden" id="mode" name="mode" value="editUser" />
		<input type="hidden" id="missionary" name="missionary" value="1" />
		<input type="hidden" id="userLv" name="userLv" value="<?=$userLv?>" />
		<div id="joinForm">
			<dl>
				<dt>
					아이디 
				<dd><? if (strlen($member->userid) > 0) { ?>
						<?=$member->userid?> <input type="hidden" id="userid" name="userid" value="<?=$member->userid?>" />
					<?	 } else { ?>
					<input type="text" id="userid" name="userid" size="20" onclick="checkId(event);" style="ime-mode:disabled;" readonly value="<?=$uid?>" />
				<? } ?><dt>
					닉네임 
				<dd>
					<input type="text" id="nickName" name="nickName" maxlength="30" tabindex="2" onclick="checkName();" readonly value="<?=$member->nick?>" />
					<img src="/images/board/btn_idcheck.gif" border="0" align="absmiddle" class="m2" onclick="checkName()" style="cursor:pointer" /></a>
				<dt>
					이름 
				<dd>
					<input type="text" id="name" name="name" size="20" maxlength="20" value="<?=$member->name?>" />&nbsp;&nbsp;
					(공백없이 한글만 입력 가능)
				<!-- 
				<dt>
					주민등록번호
				<dd>
					<input type="text" id="jumin1" name="jumin1" size="8" onKeyPress="CheckNumber(event);" style="ime-mode:disabled" maxlength=6 value="<?=$member->jumin[0]?>" /> -
	 				<input type="password" id="jumin2" name="jumin2" size="14" onKeyPress="CheckNumber(event);" style="ime-mode:disabled" maxlength=7 value="<?=$member->jumin[1]?>" />
	 			-->
				<dt>
					E-mail
				<dd>
					<input type="text" id="email1" name="email1" size="20" maxlength=20 style="ime-mode:disabled;" value="<?=$member->email[0]?>" />@
					<input type="text" id="email2" name="email2" size="20" style="ime-mode:disabled;" value="<?=$member->email[1]?>" />
				<dt>
					우편번호
				<dd>
					<input type="text" id="post1" name="post1" size="3" readonly onclick="PostPopup();" value="<?=$member->zipcode[0]?>" /> - 
					<input type="text" id="post2" name="post2" size="3" readonly onclick="PostPopup();" value="<?=$member->zipcode[1]?>" />&nbsp;
					<img src="/images/board/btn_zipcode.gif" border=0 align="absmiddle" name="Btn_search" onclick="PostPopup();" style="cursor:pointer;" />
				<dt>
					주소
				<dd>
					<input type="text" id="addr1" name="addr1" size="50" readonly onclick="PostPopup();" value="<?=$member->address1?>" />
					<input type="text" id="addr2" name="addr2" size="20" value="<?=$member->address2?>" />
				<dt>
					전화번호
				<dd>
					<select id="tel1" name="tel1">
						<option value="02" <?php if (($member->phone[0]=="02")) { print "selected"; } ?>>02</option>
						<option value="031" <?php if (($member->phone[0]=="031")) { print "selected"; } ?>>031</option>
						<option value="032" <?php if (($member->phone[0]=="032")) { print "selected"; } ?>>032</option>
						<option value="033" <?php if (($member->phone[0]=="033")) { print "selected"; } ?>>033</option>
						<option value="041" <?php if (($member->phone[0]=="041")) { print "selected"; } ?>>041</option>
						<option value="042" <?php if (($member->phone[0]=="042")) { print "selected"; } ?>>042</option>
						<option value="043" <?php if (($member->phone[0]=="043")) { print "selected"; } ?>>043</option>
						<option value="051" <?php if (($member->phone[0]=="051")) { print "selected"; } ?>>051</option>
						<option value="052" <?php if (($member->phone[0]=="052")) { print "selected"; } ?>>052</option>
						<option value="053" <?php if (($member->phone[0]=="053")) { print "selected"; } ?>>053</option>
						<option value="054" <?php if (($member->phone[0]=="054")) { print "selected"; } ?>>054</option>
						<option value="055" <?php if (($member->phone[0]=="055")) { print "selected"; } ?>>055</option>
						<option value="061" <?php if (($member->phone[0]=="061")) { print "selected"; } ?>>061</option>
						<option value="062" <?php if (($member->phone[0]=="062")) { print "selected"; } ?>>062</option>
						<option value="063" <?php if (($member->phone[0]=="063")) { print "selected"; } ?>>063</option>
						<option value="064" <?php if (($member->phone[0]=="064")) { print "selected"; } ?>>064</option>
						<option value="070" <?php if (($member->phone[0]=="070")) { print "selected"; } ?>>070</option>
					</select> -
					<input type="text" id="tel2" name="tel2" size="4" onKeyPress="CheckNumber(event);" style="ime-mode:disabled" maxlength="4" value="<?=$member->phone[1]?>" /> - 
					<input type="text" id="tel3" name="tel3" size="4" onKeyPress="CheckNumber(event);" style="ime-mode:disabled" maxlength="4" value="<?=$member->phone[2]?>" />
				<dt>
					휴대폰번호
				<dd>
					<select id="hp1" name="hp1">
						<option value="010" <?php if (($member->mobile[0]=="010")) { print "selected"; } ?>>010</option>
						<option value="011" <?php if (($member->mobile[0]=="011")) { print "selected"; } ?>>011</option>
						<option value="016" <?php if (($member->mobile[0]=="016")) { print "selected"; } ?>>016</option>
						<option value="017" <?php if (($member->mobile[0]=="017")) { print "selected"; } ?>>017</option>
						<option value="018" <?php if (($member->mobile[0]=="018")) { print "selected"; } ?>>018</option>
						<option value="019" <?php if (($member->mobile[0]=="019")) { print "selected"; } ?>>019</option>
					 </select> -
					<input type="text" id="hp2" name="hp2" size="4" onKeyPress="CheckNumber(event);" style="ime-mode:disabled" maxlength="4" value="<?=$member->mobile[1]?>" /> - 
					<input type="text" id="hp3" name="hp3" size="4" onKeyPress="CheckNumber(event);" style="ime-mode:disabled" maxlength="4" value="<?=$member->mobile[2]?>" />
				<dt>
					SMS 수신동의
				<dd>
					<input type="checkbox" id="smsOk" name="smsOk" <? if ($member->msgOk == 1) { print "checked"; } ?> />
					<input type="hidden" id="msgOk" name="msgOk" value="<?=$msgOk?>" />
				<dt>
					<strong>패스워드</strong>
				<dd>
					<input type="text" id="password" name="password" size="20" maxlength="20" value="" />&nbsp;&nbsp;
					<font color="red">(* 주의해 주세요. 공백으로 두면 패스워드가 변경되지 않습니다.)</font>

				<dt>
					&nbsp;
				<dd>
					<input type="button" id="btnMission" name="btnMission" value="<?=$btnValue?>" onclick="checkMission()" style="cursor:pointer" />
			</dl>
		</div>

		<!-- //선교사일 경우 -->
		<div id="joinMission" name="joinMission" style="display:<?=$display?>;">
			<dl>
				<dt>
					선교사명
				<dd>
					<input type="text" name="missionName" id="missionName" maxlength="30" tabindex="27" value="<?=$mission->missionName?>" />

				<dt>출생년도
				<dd>
					<select name="birth_year" id="birth_year" tabindex="25">
<?
	for ($i = 0; $i <= 99; $i++) {
		if ($mission->BirthYear == (date('Y') - $i)) {
			print "<option value='".(date('Y') - $i)."' selected>".(date('Y') - $i)." </option>";			
		} else {
			print "<option value='".(date('Y') - $i)."'>".(date('Y') - $i)." </option>";
		}
	}
?>
					</select>
				<dt>
					파송년도
				<dd>
					<select name="sent_year" id="sent_year" tabindex="25">
<?
	for ($i = 0; $i <= 99; $i++) {
		if ($mission->SentYear == (date('Y') - $i)) {
			print "<option value='".(date('Y') - $i)."' selected>".(date('Y') - $i)." </option>";			
		} else {
			print "<option value='".(date('Y') - $i)."'>".(date('Y') - $i)." </option>";
		}
	}
?>
					</select>
				<dt>
					파송기관(교회)
				<dd>
					<input type="text" name="church" id="church" maxlength="20" size="30" value="<?=$mission->church?>" />
				<dt>
					파송기관 연락처
				<dd>
					<select name="churchContact1" id="churchContact1" tabindex="24">
						<option value="02" <?php if (($churchContact[0]=="02")) { print "selected"; } ?>>02</option>
						<option value="031" <?php if (($churchContact[0]=="031")) { print "selected"; } ?>>031</option>
						<option value="032" <?php if (($churchContact[0]=="032")) { print "selected"; } ?>>032</option>
						<option value="033" <?php if (($churchContact[0]=="033")) { print "selected"; } ?>>033</option>
						<option value="041" <?php if (($churchContact[0]=="041")) { print "selected"; } ?>>041</option>
						<option value="042" <?php if (($churchContact[0]=="042")) { print "selected"; } ?>>042</option>
						<option value="043" <?php if (($churchContact[0]=="043")) { print "selected"; } ?>>043</option>
						<option value="051" <?php if (($churchContact[0]=="051")) { print "selected"; } ?>>051</option>
						<option value="052" <?php if (($churchContact[0]=="052")) { print "selected"; } ?>>052</option>
						<option value="053" <?php if (($churchContact[0]=="053")) { print "selected"; } ?>>053</option>
						<option value="054" <?php if (($churchContact[0]=="054")) { print "selected"; } ?>>054</option>
						<option value="055" <?php if (($churchContact[0]=="055")) { print "selected"; } ?>>055</option>
						<option value="061" <?php if (($churchContact[0]=="061")) { print "selected"; } ?>>061</option>
						<option value="062" <?php if (($churchContact[0]=="062")) { print "selected"; } ?>>062</option>
						<option value="063" <?php if (($churchContact[0]=="063")) { print "selected"; } ?>>063</option>
						<option value="064" <?php if (($churchContact[0]=="064")) { print "selected"; } ?>>064</option>
						<option value="070" <?php if (($churchContact[0]=="070")) { print "selected"; } ?>>070</option>
					</select>
					-
					<input type="text" name="churchContact2" id="churchContact2" style="ime-mode:disabled;width:50px" onKeyPress="CheckNumber(event);" maxlength="4" tabindex="25" value="<?=$churchContact[1]?>" />
					-
					<input type="text" name="churchContact3" id="churchContact3" style="ime-mode:disabled;width:50px" onKeyPress="CheckNumber(event);" maxlength="4" tabindex="26" value="<?=$churchContact[2]?>" />
				<dt>
					선교지
				<dd>
					<select name="nation" id="nation" tabindex="32">
					<?php 
	for ($i=0; $i<=count($codes)-1; $i = $i+1) {
		$codeObj = $codes[$i];
		if (($codeObj->code == $mission->nationCode)) {
			print "<option value=\"".$codeObj->code."\" selected>".$codeObj->name."</option>";
		} else {
			print "<option value=\"".$codeObj->code."\">".$codeObj->name."</option>";
		} 


	}

?>
					</select>
				<dt>
					국내 연락처
				<dd>
					<select name="managerContact1" id="managerContact1" tabindex="24">
						<option value="010" <?php if (($managerContact[0]=="010")) { print "selected"; } ?>>010</option>
						<option value="011" <?php if (($managerContact[0]=="011")) { print "selected"; } ?>>011</option>
						<option value="016" <?php if (($managerContact[0]=="016")) { print "selected"; } ?>>016</option>
						<option value="017" <?php if (($managerContact[0]=="017")) { print "selected"; } ?>>017</option>
						<option value="018" <?php if (($managerContact[0]=="018")) { print "selected"; } ?>>018</option>
						<option value="019" <?php if (($managerContact[0]=="019")) { print "selected"; } ?>>019</option>
						<option value="02" <?php if (($managerContact[0]=="02")) { print "selected"; } ?>>02</option>
						<option value="031" <?php if (($managerContact[0]=="031")) { print "selected"; } ?>>031</option>
						<option value="032" <?php if (($managerContact[0]=="032")) { print "selected"; } ?>>032</option>
						<option value="033" <?php if (($managerContact[0]=="033")) { print "selected"; } ?>>033</option>
						<option value="041" <?php if (($managerContact[0]=="041")) { print "selected"; } ?>>041</option>
						<option value="042" <?php if (($managerContact[0]=="042")) { print "selected"; } ?>>042</option>
						<option value="043" <?php if (($managerContact[0]=="043")) { print "selected"; } ?>>043</option>
						<option value="051" <?php if (($managerContact[0]=="051")) { print "selected"; } ?>>051</option>
						<option value="052" <?php if (($managerContact[0]=="052")) { print "selected"; } ?>>052</option>
						<option value="053" <?php if (($managerContact[0]=="053")) { print "selected"; } ?>>053</option>
						<option value="054" <?php if (($managerContact[0]=="054")) { print "selected"; } ?>>054</option>
						<option value="055" <?php if (($managerContact[0]=="055")) { print "selected"; } ?>>055</option>
						<option value="061" <?php if (($managerContact[0]=="061")) { print "selected"; } ?>>061</option>
						<option value="062" <?php if (($managerContact[0]=="062")) { print "selected"; } ?>>062</option>
						<option value="063" <?php if (($managerContact[0]=="063")) { print "selected"; } ?>>063</option>
						<option value="064" <?php if (($managerContact[0]=="064")) { print "selected"; } ?>>064</option>
						<option value="070" <?php if (($managerContact[0]=="070")) { print "selected"; } ?>>070</option>

					</select>
					-
					<input type="text" name="managerContact2" id="managerContact2" style="ime-mode:disabled;width:50px" onKeyPress="CheckNumber(event);" maxlength="4" tabindex="25" value="<?=$managerContact[1]?>" />
					-
					<input type="text" name="managerContact3" id="managerContact3" style="ime-mode:disabled;width:50px" onKeyPress="CheckNumber(event);" maxlength="4" tabindex="26" value="<?=$managerContact[2]?>" />
				<dt>
					후원 계좌번호
				<dd>
					<input type="text" name="accountNo" id="accountNo" maxlength="30" size="30" style="ime-mode:disabled" value="<?=$mission->accountNo?>">
				<dt>
					은행명
				<dd>
					<input type="text" name="bank" id="bank" maxlength="30" size="30" value="<?=$mission->bank?>">
				<dt>
					예금주
				<dd>
					<input type="text" name="accountName" id="accountName" maxlength="20" value="<?=$mission->accountName?>">
				<dt>
					기타메모
				<dd>
					<textarea name="memo" id="memo" class="b10"><?=$memo?></textarea>
				<dt>
					기도제목
				<dd>
					<textarea name="prayList" id="prayList"><?=$prayList?></textarea>
				<!--
				<dt>
					가족사항
				<dd>
					<table id="tblFamily" name="tbmFamily" width="400" align="center" border="1" cellspacing="3" cellpadding="0" class="board_con">
					<tr><th>이름</th><th>나이</th><th>성별</th><th>관계</th><th>삭제</th></tr>
					<?php 
	$year=substr(time(),0,4);
	if ((strlen($mission->userid)>0)) {
		foreach ($familyList as $familyObj) {
?>
						<tr id="trFamily" align="center">
							<input type="hidden" name="familyId" id="familyId" value="<?=$familyObj->familyId?>" />
							<td><input type="text" name="familyName" id="familyName" style="width:150px" value="<?=$familyObj->name?>" /></td>
							<td><select name="familyAge" id="familyAge">
							<?php 
			for ($i=0; $i<=99; $i = $i+1) {
				if (((strftime("%Y",-$i)) == $familyObj->Age)) {
					print "<option value='".(strftime("%Y",-$i))."' selected>".($i+1)."세, ".(strftime("%Y",-$i))." </option>";
				}
					else
				{
					print "<option value='".(strftime("%Y",-$i))."'>".($i+1)."세, ".(strftime("%Y",-$i))." </option>";
				} 


			}

?>
							</select></td>
							<td>
								<select name="familySex" id="familySex">
									<option value="남자" <?php if (($familyObj->sex="남자")) { print "selected"; } ?>>남자</option>
									<option value="여자" <?php if (($familyObj->sex=="여자")) { print "selected"; } ?>>여자</option>
								</select>
							</td>
							<td>
								<select name="familyRelation" id="familyRelation">
									<option value="부모" <?php if (($familyObj->relation=="부모")) { print "selected"; } ?>>부모</option>
									<option value="배우자" <?php if (($familyObj->relation=="배우자")) { print "selected"; } ?>>배우자</option>
									<option value="자녀" <?php if (($familyObj->relation=="자녀")) { print "selected"; } ?>>자녀</option>
									<option value="형제" <?php if (($familyObj->relation=="형제")) { print "selected"; } ?>>형제</option>
									<option value="기타" <?php if (($familyObj->relation=="기타")) { print "selected"; } ?>>기타</option>
								</select>
							</td>
							<td><input type="button" name="btnDelFamily" id="btnDelFamily" value="삭제" style="cursor:pointer;" onclick="deleteFamily(<?=$familyObj->familyId?>)" /></td>
						</tr>
					<?php 

		}

	} else {
?>
						<tr id="trFamily" align="center">
							<input type="hidden" name="familyId" id="familyId" value="-1" />
							<td><input type="text" name="familyName" id="familyName" style="width:150px" /></td>
							<td><select name="familyAge" id="familyAge">
<?php 
	for ($i = 0; $i <= 99; $i++) {
		print "<option value='".(date('Y') - $i)."'>".($i + 1)."세, ".(date('Y') - $i)." </option>";

	}
?>
							</select></td>
							<td><select name="familySex" id="familySex"><option value="남자">남자</option><option value="여자">여자</option></select></td>
							<td>
								<select name="familyRelation" id="familyRelation">
								<option value="부모">부모</option><option value="배우자">배우자</option>
								<option value="자녀">자녀</option><option value="형제">형제</option>
								<option value="기타">기타</option>
								</select>
							</td>
							<td>&nbsp;</td>
						</tr>
					<?php 
	} 

?>
					</table>
					<input type="hidden" id="familyNum" name="familyNum" value="1">
					<p class="left" valign="absmiddle">
						<input type="button" value=" 추가 " border="0" style="cursor:pointer" onclick='addRelation()' onfocus='this.blur();'>
						<input type="button" value=" 삭제 " border="0" style="cursor:pointer" onclick='DelRelation()' onfocus='this.blur();'>
					</p>
				<dt>
					가족사항 공개여부
				<dd>
					<input name="flagFamily" id="flagFamily" type="radio" value="1" class="chk" <?php if (($mission->flagFamily==1)) {
		print "checked";
	} ?>> 공개 &nbsp;&nbsp;&nbsp;&nbsp;
					<input name="flagFamily" id="flagFamily" type="radio" value="0" class="chk" <?php if (($mission->flagFamily==0)) {
		print "checked";
	} ?>> 비공개 -->

				<dt>
					선교사 증명
				<dd>
					<?=$mission->attachfilelink?><br>
					<input type="button" name="fileUpload" id="fileUpload" value=" 파일 업로드 " onclick="uploadFile(event, 'missionFile', 'mission')" style="cursor:pointer" />
					<input type="hidden" name="idmissionFile" id="idmissionFile" value="" />
					<input type="text" name="txtmissionFile" id="txtmissionFile" value="<?=$mission->attachFileName?>" size="80" readonly /> <br />
					* fax(0505-911-0811), 혹은 email(godlovehouse@nate.com)로 보내주셔도 됩니다.
				<!--dt>
					프로필 이미지
				<dd>
					<div id="showimage" style="position:absolute;visibility:hidden;border:1px solid black"></div>
					<input type="button" name="imgUpload" id="imgUpload" value="이미지 업로드" onclick="uploadImage(event, 'Profile', 'mission')" style="cursor:pointer" /> (190 x 120)<br />
					<input type="hidden" name="idProfile" id="idProfile" value="<?=$mission->ImageID?>" />
					<img src="<?=$mission->fileImage?>" id="imgProfile" width="190" height="120" onclick="showImage(this, event)" alt="크게보기" style="cursor:pointer" /-->
				<dt>
					선교사 승인
				<dd>
					<select name="approval" id="approval">
						<option value="0" <? if (!$mission->approval) { print "selected"; } ?>>미승인</option>
						<option value="1" <? if ($mission->approval) { print "selected"; } ?>>승인</option>
					</select>
			</dl>
		</div>
		<!-- 선교사일경우// -->
		
		<div id="joinFormButton">
			<dl>
				<dt>
					&nbsp;
				<dd>
					<img src="/images/board/btn_ok.gif" border="0" onclick="check();" style="cursor:pointer;">&nbsp;
					<img src="/images/board/btn_cancel.gif" border="0" onclick="history.back(-1);" style="cursor:pointer"></a>
			</dl>
		</div>
		</form>
	</div>
<?php 
} 
?>

<script type="text/javascript">
//<![CDATA[
<?php if ($member->userLv!="3") { ?>
	var chkMission = false;
<?php } else { ?>
	var chkMission = true;
<?php } ?>
	
	function check() {
		if (document.getElementById("userid").value == "") {
			alert("아이디를 입력해주세요.");
			document.getElementById("userid").focus();
			return;
		}
		if (document.getElementById("name").value == "") {
			alert("이름을 입력해주세요.");
			document.getElementById("name").focus();
			return;
		}
		// if (document.getElementById("jumin1").value == "" || document.getElementById("jumin1").value.length < 6 || 
			// document.getElementById("jumin2").value == "" || document.getElementById("jumin2").value.length < 7) {
			// alert("주민등록번호를 확인해주세요.");
			// document.getElementById("jumin1").select();
			// return;
		// }
		if (document.getElementById("email1").value == "" || document.getElementById("email2").value == "") {
			alert("이메일 주소를 확인해주세요.");
			document.getElementById("email1").select();
			return;
		}

		if (document.getElementById("post1").value == "" || 
			document.getElementById("post2").value == "" || 
			document.getElementById("addr1").value == "") {
			alert("우편번호 찾기를 이용해주세요.");
			document.getElementById("post1").focus();
			return;
		}
		
		if (document.getElementById("addr2").value == "") {
			alert("상세주소를 입력해주세요.");
			document.getElementById("addr2").focus();
			return;
		}
		
		if (!document.getElementById("smsOk").checked) {
			document.getElementById("msgOk").value = 0;
		} else {
			document.getElementById("msgOk").value = 1;
		}

		if (chkMission) {
			document.getElementById("missionary").value = 1;
		} else {
			document.getElementById("missionary").value = 0;
		}

		document.getElementById("dataForm").action="process.php";
		document.getElementById("dataForm").submit();
	}
	
	function checkMission() {
		if (chkMission) {
			document.getElementById("btnMission").value = "선교사 정보 추가입력";
			document.getElementById("joinMission").style.display = "none";
			chkMission = false;
		} else {
			document.getElementById("btnMission").value = "선교사 정보 입력취소";
			document.getElementById("joinMission").style.display = "";
			chkMission = true;
		}
	}
	
	function addRelation() {
		var tblObj = document.getElementById("tblFamily");
		var index = (tblObj.rows.length - 1)
 
		var indexLimit = 10;
		var limitMessage = "가족은";
		
		if(index >= indexLimit) {
			alert(limitMessage + " 최대 " + indexLimit + "항목입니다.");
			return;
		}
		
		// explorer 에서만 사용되는 코드 tBodies
		var trObj = document.getElementById("trFamily");
		var newTr = trObj.cloneNode(true);
		var objs = newTr.getElementsByTagName("input");
		var obj = objs[0];
		obj.value = '';
		var bodyObj = tblObj.tBodies[0];
		bodyObj.appendChild(newTr);
	}
	
	function ReplaceIndex(obj, index) {
		var objs = obj.getElementsByTagName("input");

		for(var i = objs.length - 1; i >= 0; i--) {
			var obj = objs[i];
			var newObj = document.createElement(obj.outerHTML);
			newObj.value = '';
			obj.replaceNode(newObj);
		}
 
		var objs = obj.getElementsByTagName("textarea");
		for(var i = objs.length - 1; i >= 0; i--) {
			var obj = objs[i];
			var newObj = document.createElement(obj.outerHTML);
			obj.replaceNode(newObj);
		}
 
		var objs = obj.getElementsByTagName("select");
		for(var i = objs.length - 1; i >= 0; i--) {
			var obj = objs[i];
			var newObj = document.createElement(obj.outerHTML);
			var optionLength = parseInt(obj.options.length, 10);
			for(var j = 0; j < optionLength; j++) {
				newObj.appendChild(obj.options[0]);
			}
			newObj.selectedIndex = 0;
			obj.replaceNode(newObj);
		}
	}

	function DelRelation() {
		var tblObj = document.getElementById("tblFamily");
		var deleteLimit = 1;
		
		if((tblObj.rows.length - 1) <= deleteLimit) {
			alert("삭제할 행이 없습니다\r\n기존 항목 삭제는 관계 컬럼의 삭제버튼을 이용해 주세요");
			return;
		}
		
		if(tblObj.rows.length > 1) {
			tblObj.deleteRow(tblObj.rows.length - 1);
		} else {
			alert("삭제할 행이 없습니다");
		}
	}
	
	function deleteFamily(familyId) {
		location.href = "process.php?mode=deleteFamily&userLv=<?=$userLv?>&userid=<?=$uid?>&familyId=" + familyId;
	}
	
	function showImage(obj, e) {
		crossobj = document.getElementById("showimage");
		
		if (crossobj.style.visibility == "hidden") {
			crossobj.style.left = e.clientX;
			crossobj.style.top = e.clientY;
			crossobj.innerHTML = '<img src="' + obj.src + '" style="cursor:pointer" onClick="closepreview()" />';
			crossobj.style.visibility = "visible";
		} else {
			crossobj.style.visibility = "hidden";
		}
	}

	function closepreview(){
		crossobj.style.visibility="hidden"
	}
//]]>
</script>
