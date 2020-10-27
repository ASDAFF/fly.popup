<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Application,
	Bitrix\Main\Request,
	Bitrix\Main\Localization\Loc;
	Loc::loadMessages(__FILE__);


$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$title = GetMessage("fly.popup_MENU_MAIN_LIST_TITLE");
$text = GetMessage("fly.popup_MENU_MAIN_LIST");
if(!empty($request['id'])){
	$title = GetMessage("fly.popup_MENU_MAIN_DETAIL_TITLE");
	$text = GetMessage("fly.popup_MENU_MAIN_DETAIL");
}

//IncludeModuleLangFile(__FILE__, LANGUAGE_ID);

  $aMenu = array(
    "parent_menu" => "global_menu_marketing", // поместим в раздел "Маркетинг"
    "sort"        => 100,                    // вес пункта меню
    "url"         => "",  // ссылка на пункте меню
    "text"        => GetMessage("fly.popup_MENU_MAIN"),       // текст пункта меню
    "title"       => GetMessage("fly.popup_MENU_MAIN_TITLE"), // текст всплывающей подсказки
    "icon"        => "flp_popup_menu_icon", // малая иконка
   // "page_icon"   => "flp_refsales_page_icon", // большая иконка
    "items_id"    => "fly_popup",  // идентификатор ветви
    "items"       => array(// остальные уровни меню сформируем ниже.
		array(
			"url"         => "fly_popup.php?lang=".LANGUAGE_ID,  // ссылка на пункте меню
			"title"       => $title, // текст всплывающей подсказки
			 "text"        => $text,       // текст пункта меню
		)/*,
		array(
			"url"         => "fly_popup_stat.php?lang=".LANGUAGE_ID,  // ссылка на пункте меню
			"title"       => GetMessage("fly.popup_MENU_MAIN_STAT_TITLE"), // текст всплывающей подсказки
			 "text"        => GetMessage("fly.popup_MENU_MAIN_STAT"),       // текст пункта меню
		)*/
	)   
  );

return $aMenu;
?>