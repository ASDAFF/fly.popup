<?
/**
 * Copyright (c) 27/10/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main\Application,
	Bitrix\Main\Page\Asset,
	Bitrix\Main\Request,
	Bitrix\Sale\Internals,
	Bitrix\Main\Localization\Loc;
	
Loc::loadMessages(__FILE__);

$module_id="fly.popup";

//head
\Bitrix\Main\Loader::IncludeModule($module_id);
$APPLICATION->SetTitle(GetMessage("fly.popup_STAT_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
//$APPLICATION->IncludeFile("/bitrix/modules/".$module_id."/include/headerInfo.php", Array());

echo GetMessage("fly.popup_STAT_TITLE");
?>

popupPro STAT
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>