<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main,
	Bitrix\Main\Application,
	Bitrix\Main\Page\Asset,
	Bitrix\Main\Request,
	Bitrix\Main\Localization\Loc;
	
	Loc::loadMessages(__FILE__);
	
if(\Bitrix\Main\Loader::includeModule('skyweb24.popuppro')){
	//request...
	$context = Application::getInstance()->getContext();
	$request = $context->getRequest();
	$popupsO=new popuppro;
	$reqArr=array(
		'type'=>$request->get('type'),
		'pageUrl'=>$request->get('pageUrl'),
		'site'=>$request->get('site'),
		'dateUser'=>$request->get('dateUser'),
		'popupIds'=>$request->get('popupIds'),
		'popupId'=>$request->get('popupId'),
		'popupTime'=>$request->get('popupTime'),
	);
	if(!empty($reqArr['type'])){
		if($reqArr['type']=='getPopups' && !empty($reqArr['pageUrl'])){
			if(empty($_SESSION['skwb24_popuppro_count_pages'])){
				$_SESSION['skwb24_popuppro_count_pages']=1;
			}else{
				$_SESSION['skwb24_popuppro_count_pages']=$_SESSION['skwb24_popuppro_count_pages']+1;
			}
			$retStr=$popupsO->getAvailablePopups(array(
				'site'=>$reqArr['site'],
				'dateUser'=>$reqArr['dateUser'],
				'pageUrl'=>urldecode($reqArr['pageUrl']),
				'countPages'=>$_SESSION['skwb24_popuppro_count_pages']
			));
			$retStr=CUtil::PhpToJSObject($retStr);
			echo str_replace("'", '"', $retStr);
		}elseif($reqArr['type']=='getBasket'){
			$basket=popuppro::GetBasketInfo();
			$retStr=CUtil::PhpToJSObject($basket);
			echo str_replace("'", '"', $retStr);
		}elseif($reqArr['type']=='getTemplatePath' && !empty($reqArr['popupIds'])){
			$paths=$popupsO->getComponentPath($reqArr['popupIds']);
			$retStr=CUtil::PhpToJSObject($paths);
			echo str_replace("'", '"', $retStr);
		}elseif($reqArr['type']=='getHTML' && !empty($reqArr['popupId'])){
			$popupsO->getHTMLByPopup($reqArr['popupId']);
		}elseif($reqArr['type']=='statisticShow' && !empty($reqArr['popupId'])){
			$retStr=$popupsO->setStatistic($reqArr['popupId'], 1, 'stat_show');
			$retStr=CUtil::PhpToJSObject($retStr);
			echo str_replace("'", '"', $retStr);
		}elseif($reqArr['type']=='statisticTime' && intval($reqArr['popupId']) && intval($reqArr['popupTime'])){
			$retStr=$popupsO->setStatistic($reqArr['popupId'], intval($reqArr['popupTime']), 'stat_time');
			$retStr=CUtil::PhpToJSObject($retStr);
			echo str_replace("'", '"', $retStr);
		}elseif($reqArr['type']=='statisticAction' && intval($reqArr['popupId'])>0){
			$retStr=$popupsO->setStatistic($reqArr['popupId'], 1, 'stat_action');
			$retStr=CUtil::PhpToJSObject($retStr);
			echo str_replace("'", '"', $retStr);
		}
	}
}else{
	echo 'module skyweb24.popuppro not included!';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>