<?php 
function alert($msg) {
?>
<html><head><title>Alert</title></head><body>
	<script language="javascript">
		alert("<?php echo $msg;?>");
	</script>
</body></html>
<?php 
	exit();
} 

function alertBack($msg) {
?>
<html><head><title>Alert & History Back</title></head><body>
	<script language="javascript">
		alert("<?php echo $msg;?>");
		history.back();
	</script>
</body></html>
<?php 
	exit();
} 

function alertCloseReload($msg) {
?>
<html><head><title>Alert & Close & Reload</title></head><body>
	<script language="javascript">
		opener.document.location.reload();
		alert("<?php echo $msg;?>");
		window.close();
	</script>
</body></html>
<?php 
	exit();
} 

function alertClose($msg) {
?>
<html><head><title>Alert & Close</title></head><body>
	<script language="javascript">
		alert("<?php echo $msg;?>");
		window.close();
	</script>
</body></html>
<?php 
	exit();

} 

function OpenerOfParentReload() {
?>
<html><head><title>Parent Reload</title></head><body>
	<script language="javascript">
		opener.parent.document.location.reload();
	</script>
</body></html>
<?php 
} 

function OpenerReload() {
?>
<html><head><title>Opener Reload</title></head><body>
	<script language="javascript">
		opener.document.location.reload();
	</script>
</body></html>
<?php 
} 

function closeAndReload() {
?>
<html><head><title>Close & Reload</title></head><body>
	<script language="javascript">
		opener.document.location.reload();
		window.close();
	</script>
</body></html>
<?php 
	exit();
} 

function alertGoPage($msg, $url) {
?>
<html><head><title>Alert & Go Page</title></head><body>
	<script language="javascript">
		alert("<?php echo $msg;?>");
		document.location.href="<?php echo $url;?>";
	</script>
</body></html>
<?php 
	exit();
} 

function alertGoTopPage($msg,$url) {
?>
<html><head><title>Alert & Go Top Page</title></head><body>
	<script language="javascript">
		alert("<?php echo $msg;?>");
		top.location.href="<?php echo $url;?>";
	</script>
</body></html>
<?php 
	exit();
} 

function goPage($url) {
?>
<html><head><title>Go Page</title></head><body>
	<script language="javascript">
		document.location.href="<?php echo $url;?>";
	</script>
</body></html>
<?php 
	exit();
} 

function invalidAccess() {
	$error_message[500];
} 

function sendSMSMessage($from_number, $to_number, $message, $return_page="http://godlovehouse.net/living/smsResult.php", $confirm=false, $confirm_msg="문자 메세지를 보내시겠습니까") {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title>SMS Message</title>
<script type="text/javascript">
//<![CDATA[
	function formSubmit() {
		<? if ($confirm) { ?>
			if (!confirm("<?=$confirm_msg?>")) {
				location.href = "<?=$return_page?>";
				return;
			}
		<? } ?>
		document.charset='utf-8';
		document.getElementById("smssend").submit();
	}
//]]>
</script>
</head>
<body onLoad="formSubmit()">
	<form id="smssend" name="smssend" action="http://www.pongdang.net/client/sendsms.aspx" method="post">
		<input type="hidden" name="returnURL" value="<?=$return_page?>" />	<!-- 결과회신페이지-->
		<input type="hidden" name="FaildURL" value="http://godlovehouse.net/living/smsResult.php" />		<!-- 실패회신페이지-->
		<input type="hidden" name="P_ID" value="npngjjh" />												<!-- 고객아이디--> 
		<input type="hidden" name="P_CODE" value="dad504907bdf14747cb40bf2b8b81553" />					<!-- 퐁당넷제공고객코드-->
		<input type="hidden" name="P_SENDTEL" value="<?=$to_number?>" />									<!-- 수신번호--> 
		<input type="hidden" name="P_RETURNTEL" value="<?=$from_number?>" />								<!-- 회신번호-->
		<input type="hidden" name="P_MSG" value="<?=$message?>" />										<!-- 메세지내용-->
		<input type="hidden" name="P_TYPE" value="N" />													<!-- 발송타입-->
		<input type="hidden" name="P_TIME" value="" />
		<!--input type="submit" value="전송" />													<!-- 발송시간-->
	</form>
</body>
</html>
<?
} 
?>
