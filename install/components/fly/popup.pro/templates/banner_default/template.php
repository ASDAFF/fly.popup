<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="fly_banner_default">
	<?if($arResult['TIMER']=='Y'){
		$APPLICATION->IncludeComponent('fly:popup.pro.timer','',array(
			'TITLE'=>$arResult['TIMER_TEXT'],
			'TIME'=>$arResult['TIMER_DATE'],
			'LEFT'=>$arResult['TIMER_LEFT'],
			'RIGHT'=>$arResult['TIMER_RIGHT'],
			'TOP'=>$arResult['TIMER_TOP'],
			'BOTTOM'=>$arResult['TIMER_BOTTOM'],
		),$component);
	}?>
	<a class="flTargetAction" href="<?=$arResult['LINK_HREF']?>" target="<?=$arResult['HREF_TARGET']?>">
		<img src="<?=$arResult['IMG_1_SRC']?>">
	</a>
</div>