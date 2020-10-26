<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="skyweb24_popup_action">
	<?if($arResult['TIMER']=='Y'){
		$APPLICATION->IncludeComponent('skyweb24:popup.pro.timer','',array(
			'TITLE'=>$arResult['TIMER_TEXT'],
			'TIME'=>$arResult['TIMER_DATE'],
			'LEFT'=>$arResult['TIMER_LEFT'],
			'RIGHT'=>$arResult['TIMER_RIGHT'],
			'TOP'=>$arResult['TIMER_TOP'],
			'BOTTOM'=>$arResult['TIMER_BOTTOM'],
		),$component);
	}?>
	<img id="img_going_s1" src="<?=$arResult['IMG_1_SRC']?>">
	<div class="text">
		<h2><?=$arResult['TITLE']?></h2>
		<div class="info"><?=$arResult['CONTENT']?></div><br>
		<h3><?=$arResult['SUBTITLE']?></h3>
		<a onclick="<?=$arResult['BUTTON_METRIC']?>" target="<?=$arResult['HREF_TARGET']?>" href="<?=$arResult['LINK_HREF']?>" class="sw24TargetAction going_link"><?=$arResult['LINK_TEXT']?></a>
	</div>
</div>