<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="fly_share_default">
	<div class="bg">		
		<h2><?=$arResult['TITLE']?></h2>
		<h3><?=$arResult['SUBTITLE']?></h3>
		<div class="socialButtons">
			<a class="flTargetAction <?=$arResult['SOC_VK']?>" href="https://vk.com/share.php?url=<?=$_SERVER["HTTP_REFERER"];?>" target="<?=$arResult['HREF_TARGET']?>" title="<?=GetMessage("fly_referralsales_SHARE_IN_VK")?>"><img src="<?=$templateFolder?>/img/vk.jpg"></a>
			<a class="flTargetAction <?=$arResult['SOC_OD']?>" href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl=<?=$_SERVER["HTTP_REFERER"];?>" target="<?=$arResult['HREF_TARGET']?>" title="<?=GetMessage("fly_referralsales_SHARE_IN_OK")?>"><img src="<?=$templateFolder?>/img/odnoklassniki.jpg"></a>
			<a class="flTargetAction <?=$arResult['SOC_FB']?>" href="http://www.facebook.com/sharer/sharer.php?u=<?=$_SERVER["HTTP_REFERER"];?>" target="<?=$arResult['HREF_TARGET']?>" title="<?=GetMessage("fly_referralsales_SHARE_IN_FB")?>"><img src="<?=$templateFolder?>/img/faceb.jpg"></a>
			<a class="flTargetAction <?=$arResult['SOC_TW']?>" href="http://twitter.com/share?url=<?=$_SERVER["HTTP_REFERER"];?>" target="<?=$arResult['HREF_TARGET']?>" title="<?=GetMessage("fly_referralsales_SHARE_IN_TWITTER")?>"><img src="<?=$templateFolder?>/img/tw.jpg"></a>
			<a class="flTargetAction <?=$arResult['SOC_GP']?>" href="http://plus.google.com/share?url=<?=$_SERVER["HTTP_REFERER"];?>" target="<?=$arResult['HREF_TARGET']?>" title="<?=GetMessage("fly_referralsales_SHARE_IN_GOOGLE")?>"><img src="<?=$templateFolder?>/img/google.jpg"></a>
			<a class="flTargetAction <?=$arResult['SOC_MR']?>" href="http://connect.mail.ru/share?share_url=<?=$_SERVER["HTTP_REFERER"];?>" target="<?=$arResult['HREF_TARGET']?>" title="<?=GetMessage("fly_referralsales_SHARE_IN_MM")?>"><img src="<?=$templateFolder?>/img/mail.jpg"></a>
		</div>
	</div>
</div>
