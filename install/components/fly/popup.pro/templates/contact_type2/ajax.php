<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Application;
$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$temlateName=$request->get("template_name");
$idPopup=$request->get("id_popup");
if(!empty($temlateName) && !empty($idPopup)){
	$APPLICATION->IncludeComponent("fly:popup.pro", $temlateName, array(
		"ID_POPUP" => $idPopup
	),false);
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php"); ?>