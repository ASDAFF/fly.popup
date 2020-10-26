<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
\Bitrix\Main\Loader::IncludeModule("skyweb24.popuppro");
$popup=new popuppro;
$res='';
$email = '';
if(isset($_REQUEST['email']))
	$email=$_REQUEST['email'];
if(!isset($_REQUEST['addtotable'])){
	$res = $popup->getCoupon($_REQUEST['id'],$_REQUEST['avaliable'],$email,$_REQUEST['idPopup']);
	echo $res;
	die();
}
if(isset($_REQUEST['addtotable'])&&$_REQUEST['addtotable']!='Y'){
	$res = $popup->getCoupon($_REQUEST['id'],$_REQUEST['avaliable'],$email,$_REQUEST['idPopup']);
	echo $res;
	die();
}
if(isset($_REQUEST['email'])&&isset($_REQUEST['idPopup'])&&$_REQUEST['addtotable']=='Y'){
	if($_REQUEST['unique']=='Y'){
		if($popup->searchinMailList($email)){
			$res = $popup->getCoupon($_REQUEST['id'],$_REQUEST['avaliable'],$email,$_REQUEST['idPopup']);
			$popup->insertToMailList($email,'',$_REQUEST['idPopup']);
			echo $res;
		}else{
			echo 'not_unique';
		}
	}else{
		$res = $popup->getCoupon($_REQUEST['id'],$_REQUEST['avaliable'],$email,$_REQUEST['idPopup']);
		$popup->insertToMailList($email,'',$_REQUEST['idPopup']);
		echo $res;
	}
}
die();
