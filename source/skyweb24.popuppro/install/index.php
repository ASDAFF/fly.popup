<?
use Bitrix\Main;
IncludeModuleLangFile(__FILE__);
Class skyweb24_popuppro extends CModule
{
	const MODULE_ID = 'skyweb24.popuppro';
	var $MODULE_ID = 'skyweb24.popuppro'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct(){
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("skyweb24.popuppro_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("skyweb24.popuppro_MODULE_DESCRIPTION");

		$this->PARTNER_NAME = GetMessage("skyweb24.popuppro_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("skyweb24.popuppro_PARTNER_URI");
	}

	function InstallDB($arParams = array()){
		global $DB;
		$DB->Query('CREATE TABLE IF NOT EXISTS skyweb24_popuppro (
			id INT NOT NULL AUTO_INCREMENT,
			sort INT NOT NULL,
			active VARCHAR(1) DEFAULT NULL,
			name VARCHAR(50) DEFAULT NULL,
			type VARCHAR(30) NOT NULL,
			stat_show INT DEFAULT NULL,
			stat_time INT DEFAULT NULL,
			stat_action INT DEFAULT NULL,
			settings TEXT NOT NULL,
			cont_iblock INT DEFAULT NULL,
			cont_posttemplate INT DEFAULT NULL,
			PRIMARY KEY (id)
		);');
		$DB->Query('CREATE TABLE IF NOT EXISTS skyweb24_popuppro_add_colors (
			id INT NOT NULL AUTO_INCREMENT,
			template VARCHAR(50) NOT NULL,
			name VARCHAR(50) DEFAULT NULL,
			PRIMARY KEY (id));'
		);
		$DB->Query('CREATE TABLE IF NOT EXISTS skyweb24_popuppro_add_templates(
			id INT NOT NULL AUTO_INCREMENT,
			name VARCHAR(50) NOT NULL,
			type VARCHAR(50) NOT NULL,
			template TEXT NOT NULL,
			PRIMARY KEY (id));'
		);
		//RegisterModuleDependences("main","OnEpilog", $this->MODULE_ID, "popuppro","createPopup");
		//RegisterModuleDependences("main","OnEndBufferContent", $this->MODULE_ID, "popuppro","setBufferContent");
		return true;
	}

	function UnInstallDB($arParams = array()){
		global $DB;
		$DB->Query('DROP TABLE IF EXISTS skyweb24_popuppro;');
		$DB->Query('DROP TABLE IF EXISTS skyweb24_popuppro_add_colors;');
		$DB->Query('DROP TABLE IF EXISTS skyweb24_popuppro_add_templates;');
		//UnRegisterModuleDependences("main","OnEpilog", $this->MODULE_ID, "popuppro","createPopup");
		//UnRegisterModuleDependences("main","OnEndBufferContent", $this->MODULE_ID, "popuppro","setBufferContent");
		return true;
	}

	function InstallEvents(){
		RegisterModuleDependences("main","OnBeforeEndBufferContent", $this->MODULE_ID, "popuppro", "insertPopups");
		return true;
	}

	function UnInstallEvents(){
		UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", $this->MODULE_ID, "popuppro", "insertPopups");
		return true;
	}
	function InstallPostEvent(){
		$keyPost='SKYWEB24_POPUPPRO_SEND_COUPON';
		$rsET = CEventType::GetList(array('EVENT_NAME'=>$keyPost));
		if(!$arET = $rsET->Fetch()){
			$obEventType = new CEventType;
			$obEventType->Add(array(
				'LID'=>'ru',
				'EVENT_NAME'=>$keyPost,
				"NAME"=>GetMessage("skyweb24.popuppro_EVENT_NAME"),
				"DESCRIPTION"=>str_ireplace("\t", '',GetMessage("skyweb24.popuppro_EVENT_DESCRIPTION"))
			));
		}
		$keyPost='SKYWEB24_POPUPPRO_ROULETTE_SEND';
		$rsET = CEventType::GetList(array('EVENT_NAME'=>$keyPost));
		if(!$arET = $rsET->Fetch()){
			$obEventType = new CEventType;
			$obEventType->Add(array(
				'LID'=>'ru',
				'EVENT_NAME'=>$keyPost,
				"NAME"=>GetMessage("skyweb24.popuppro_EVENT_NAME_ROULETTE"),
				"DESCRIPTION"=>str_ireplace("\t", '',GetMessage("skyweb24.popuppro_EVENT_DESCRIPTION_ROULETTE"))
			));
		}	
	}
	function UnInstallPostEvent(){
		$keyPost='SKYWEB24_POPUPPRO_SEND_COUPON';
		$rsET = CEventType::GetList(array('EVENT_NAME'=>$keyPost));
		if($arET = $rsET->Fetch()){
			$et = new CEventType;
			$et->Delete($keyPost);
		}
		$keyPost='SKYWEB24_POPUPPRO_ROULETTE_SEND';
		$rsET = CEventType::GetList(array('EVENT_NAME'=>$keyPost));
		if($arET = $rsET->Fetch()){
			$et = new CEventType;
			$et->Delete($keyPost);
		}
	}
	function InstallPostTemplate(){
		$obTemplate = new CEventMessage;
		$template=array();
		$template['LID']=array();
		$sites=Bitrix\Main\SiteTable::getList();
		while($s=$sites->Fetch())
			$template['LID'][]=$s['LID'];
		$template['EVENT_NAME']="SKYWEB24_POPUPPRO_SEND_COUPON";
		$template['ACTIVE']='Y';
		$template['EMAIL_FROM']='#DEFAULT_EMAIL_FROM#';
		$template['EMAIL_TO']='#EMAIL#';
		$template['SUBJECT']=GetMessage("skyweb24.popuppro_TEMPLATE_SUBJECT");
		$template['BODY_TYPE']='html';
		$template['MESSAGE']=str_ireplace("\t", '',GetMessage("skyweb24.popuppro_TEMPLATE_MESSAGE"));
		$obTemplate->Add($template);
		$template['EVENT_NAME']="SKYWEB24_POPUPPRO_ROULETTE_SEND";
		$template['ACTIVE']='Y';
		$template['EMAIL_FROM']='#DEFAULT_EMAIL_FROM#';
		$template['EMAIL_TO']='#EMAIL#';
		$template['SUBJECT']=GetMessage("skyweb24.popuppro_TEMPLATE_SUBJECT_ROULETTE");
		$template['BODY_TYPE']='html';
		$template['MESSAGE']=str_ireplace("\t", '',GetMessage("skyweb24.popuppro_TEMPLATE_MESSAGE_ROULETTE"));
		$obTemplate->Add($template);
	}
	function UnInstallPostTemplate(){
		$templates = CEventMessage::GetList($by="site_id",$order="desc",array('TYPE_ID'=>'SKYWEB24_POPUPPRO_SEND_COUPON'));
		$emessage = new CEventMessage;
		while($template=$templates->GetNext()){
			$emessage->Delete((int)$template['ID']);
		}
		$templates = CEventMessage::GetList($by="site_id",$order="desc",array('TYPE_ID'=>'SKYWEB24_POPUPPRO_ROULETTE_SEND'));
		$emessage = new CEventMessage;
		while($template=$templates->GetNext()){
			$emessage->Delete((int)$template['ID']);
		}
	}
	function InstallFiles(){
		
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components')){
			if ($dir = opendir($p)){
				while (false !== $item = readdir($dir)){
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
		
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		return true;
	}

	function UnInstallFiles(){
		
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components')){
			if ($dir = opendir($p)){
				while (false !== $item = readdir($dir)){
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0)){
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		
		DeleteDirFilesEx("/upload/".$this->MODULE_ID);
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		DeleteDirFilesEx("/bitrix/themes/.default/icons/".$this->MODULE_ID);
		DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID);
		return true;
	}

	function DoInstall(){
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
		RegisterModule(self::MODULE_ID);
		$this->InstallEvents();
		$this->InstallPostEvent();
		$this->InstallPostTemplate();
	}

	function DoUninstall(){
		global $APPLICATION;
		$this->UnInstallEvents();
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallPostTemplate();
		$this->UnInstallPostEvent();
	}
}
?>
