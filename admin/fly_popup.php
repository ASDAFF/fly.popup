<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use \Bitrix\Main\Application,
	Bitrix\Main\Page\Asset,
	Bitrix\Main\Request,
	Bitrix\Main\Localization\Loc,
	Bitrix\Sale\Internals;
	Loc::loadMessages(__FILE__);

$module_id='fly.popup';
\Bitrix\Main\Loader::includeModule('fly.popup');
\Bitrix\Main\Loader::includeModule('iblock');
//head
CJSCore::Init(array("jquery", "ajax", "fx",'drag_drop'));

Asset::getInstance()->addJs('/bitrix/js/'.$module_id.'/script.js');
//include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/default_option.php");

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

function imageBusy($popupArr, $img){
	$impPropArr=array('IMG_1_SRC','IMG_2_SRC','IMG_3_SRC','IMG_4_SRC','IMG_5_SRC');
	foreach($popupArr as $keyPopup=>$valPopup){
		foreach($impPropArr as $nextProp){
			if(
				!empty($valPopup['view']['props'][$nextProp])
				&& (
					$valPopup['view']['props'][$nextProp]==$img['id']
					|| $valPopup['view']['props'][$nextProp]==$img['path']
				)
			){
				return $keyPopup;
			}
		}
	}
	return false;
}
if(!empty($request['action_button'])&&$request['action_button']=='copy'&&!empty($request['ID'])){
	$editableWindow=new popupEdit();
	$newIdPopup=$editableWindow->CopyPopup($request['ID']);
	global $APPLICATION;
	$CURRENT_PAGE = (CMain::IsHTTPS()) ? "https://" : "http://";
	$CURRENT_PAGE .= $_SERVER["HTTP_HOST"];
	header("Location: ".$CURRENT_PAGE."/bitrix/admin/fly_popup.php?id=".$newIdPopup);
}
//ajax operations
if(!empty($request['ajax']) && $request['ajax']=='y'){
	if(!empty($request['command'])){
		if($request['command']=='gettemplate'){
			global $APPLICATION;
			$APPLICATION->IncludeComponent(
				"fly:popup.pro",
				$request['template'],
				Array(
					"MODE" => "TEMPLATE",
					"ID_POPUP" => "NEW"
				)
			);
		}elseif($request['command']=='gettemplatepath'){
			global $APPLICATION;
			$APPLICATION->IncludeComponent(
				"fly:popup.pro",
				$request['template'],
				Array(
					"MODE" => "GET_PATH",
					"ID_POPUP" => "NEW"
				)
			);
		}elseif($request['command']=='get_img'){
			$editableWindow=new popupEdit();
			$allSettings=$editableWindow->getAllPopups();
			$firstQueue=$lastQueue='';
			$res = CFile::GetList(array("ID"=>"desc"), array("MODULE_ID"=>$module_id));
			while($res_arr = $res->GetNext()){
				$keyPopup=imageBusy($allSettings, array('id'=>$res_arr['ID'], 'path'=>'/upload/'.$res_arr['SUBDIR'].'/'.$res_arr['FILE_NAME']));
				$deleteLink='<a href="javascript:void(0);" onclick="delPopupImg(this);" class="del_img" title="'.GetMessage("fly.popup_IMG_BLOCK_DELIMG").'" data-id="'.$res_arr['ID'].'">&nbsp;</a>';
				$existKey='';
				if($keyPopup!==false){
					$deleteLink='';
					$existKey='popup ['.$keyPopup.']';
				}
				$tmpFigure= '<figure>'.
						$deleteLink
						.'<img title="'.GetMessage("fly.popup_IMG_BLOCK_ALTIMG").'" alt="'.GetMessage("fly.popup_IMG_BLOCK_ALTIMG").'" data-id="'.$res_arr['ID'].'" src="/upload/'.$res_arr['SUBDIR'].'/'.$res_arr['FILE_NAME'].'" />
						<figcaption>'.$existKey.'</figcaption>
					</figure>';
				if($keyPopup!==false){
					$firstQueue.=$tmpFigure;
				}else{
					$lastQueue.=$tmpFigure;
				}
			}
			echo $firstQueue.$lastQueue.'<a href="javascript:void(0);" onclick="showHideImgs(\'show_all\')" class="show_all">'.GetMessage("fly.popup_IMG_SHOWALL").'</a><a href="javascript:void(0);" onclick="showHideImgs(\'hide_all\')" class="hide_all">'.GetMessage("fly.popup_IMG_HIDEALL").'</a>';
		}elseif($request['command']=='del_img'){
			CFile::Delete($request['id']);
			echo json_encode('success');
		}elseif($request['command']=='add_custom_colortheme'){
			$editableWindow=new popupEdit();
			$dataStatus=true;
			$requiredVal=array('template', 'color_style', 'name', 'type');
			foreach($requiredVal as $nextVal){
				if(empty($request[$nextVal])){
					$dataStatus=false;
					break;
				}
			}
			if($dataStatus){
				$retArr=$editableWindow->setColorTheme($request['type'], $request['template'], $request['color_style'], $request['name']);
			}else{
				$retArr=array('status'=>'do not data');
			}
			echo json_encode($retArr);
		}elseif($request['command']=='add_custom_template'){
			$editableWindow=new popupEdit();
			$dataStatus=true;
			$requiredVal=array('template', 'name', 'type');
			foreach($requiredVal as $nextVal){
				if(empty($request[$nextVal])){
					$dataStatus=false;
					break;
				}
			}
			if($dataStatus){
				$retArr=$editableWindow->setTemplate($request['type'], $request['template'], $request['name']);
				if($retArr===false){
					$retArr=array('status'=>'error copy');
				}
			}else{
				$retArr=array('status'=>'do not data');
			}
			echo json_encode($retArr);
		}
	}
	die();
}

$editableWindow=new popupEdit();

if(!empty($request['id'])){
	$idPopup=$request['id'];
	$APPLICATION->SetTitle(GetMessage("fly.popup_MAIN_TITLE"));
}else{
	$APPLICATION->SetTitle(GetMessage("fly.popup_LIST_TITLE"));

	$sTableID = $editableWindow->getTableSetting();
	$oSort = new CAdminSorting($sTableID, "ID", "desc");
	$lAdmin = new CAdminList($sTableID, $oSort);

	if($lAdmin->EditAction()){
		foreach($FIELDS as $ID=>$arFields){
			if(!$lAdmin->IsUpdated($ID))
				continue;
			$editableWindow->editFromTableList($ID, $arFields);
        }
	}

	if($arID = $lAdmin->GroupAction()){
		if($_REQUEST['action_target']=='selected'){
			$rsData = $DB->Query('SELECT * FROM '.$sTableID.';', false, $err_mess.__LINE__);
			while($arRes = $rsData->Fetch()){
				$arID[] = $arRes['id'];
			}
		}
		foreach($arID as $ID){
			if(strlen($ID)<=0)
				continue;
			$ID = IntVal($ID);
			switch($_REQUEST['action']){
				case "delete":
					$DB->Query('DELETE FROM '.$sTableID.' WHERE id='.$ID.';', false, $err_mess.__LINE__);
					break;
				case "activate":
				case "deactivate":
					$cData = $DB->Query('SELECT * FROM '.$sTableID.' WHERE id='.$ID.';', false, $err_mess.__LINE__);
					if($arFields = $cData->Fetch()){
						$arFields["active"]=($_REQUEST['action']=="activate"?"Y":"N");
						$tmpSetting=unserialize($arFields['settings']);
						$tmpSetting['condition']['active']=$arFields['active'];
						$DB->Query('UPDATE '.$sTableID.' SET active="'.$arFields["active"].'", settings=\''.serialize($tmpSetting).'\' WHERE id='.$ID.';', false, $err_mess.__LINE__);
					}else
						$lAdmin->AddGroupError(GetMessage("fly.popup_SAVE_ERROR")." ".GetMessage("fly.popup_POPUP_EMPTY"), $ID);
					break;
			}
		}
	}


	$rsData = $DB->Query('SELECT * FROM '.$sTableID.' order by '.$by.' '.$order.';', false, $err_mess.__LINE__);
	$rsData->NavStart(CAdminResult::GetNavSize());
	$rsData = new CAdminResult($rsData, $sTableID);
	$lAdmin->NavText($rsData->GetNavPrint(GetMessage("fly.popup_TABLELIST_PAGINATOR")));
	$lAdmin->AddHeaders(array(
	  array("id"    =>"ID",
		"content"  =>"ID",
		"sort"     =>"id",
		"default"  =>true,
	  ),
	  array("id"    =>"SORT",
		"content"  =>GetMessage("fly.popup_TABLELIST_SORT"),
		"sort"     =>"sort",
		"default"  =>true,
	  ),
	  array("id"    =>"active",
		"content"  =>GetMessage("fly.popup_TABLELIST_ACTIVE"),
		"sort"     =>"active",
		"default"  =>true,
	  ),
	  array("id"    =>"NAME",
		"content"  =>GetMessage("fly.popup_TABLELIST_NAME"),
		"sort"     =>"name",
		"default"  =>true,
	  ),
	  array("id"    =>"TYPE",
		"content"  =>GetMessage("fly.popup_TABLELIST_TYPE"),
		"sort"     =>"type",
		"default"  =>true,
	  ),
	  array("id"    =>"STAT_SHOW",
		"content"  =>GetMessage("fly.popup_TABLELIST_STAT_SHOW"),
		"sort"     =>"stat_show",
		"default"  =>true,
	  ),
	  array("id"    =>"STAT_TIME",
		"content"  =>GetMessage("fly.popup_TABLELIST_STAT_TIME"),
		"sort"     =>"stat_time",
		"default"  =>true,
	  ),
	  array("id"    =>"STAT_ACTION",
		"content"  =>GetMessage("fly.popup_TABLELIST_STAT_ACTION"),
		"sort"     =>"stat_action",
		"default"  =>true,
	  ),
	  array("id"    =>"SITES",
		"content"  =>GetMessage("fly.popup_TABLELIST_SITES"),
		"default"  =>false,
	  ),
	  array("id"    =>"MASK_IN",
		"content"  =>GetMessage("fly.popup_TABLELIST_MASK_IN"),
		"default"  =>false,
	  ),
	  array("id"    =>"MASK_OUT",
		"content"  =>GetMessage("fly.popup_TABLELIST_MASK_OUT"),
		"default"  =>false,
	  )
	));

	$typesArr=$editableWindow->getTypes();
	while($arRes = $rsData->NavNext(true, "f_")){
		$row =$lAdmin->AddRow($f_id, $arRes);
		$row->AddViewField("ID", '<a href="./fly_popup.php?lang='.SITE_ID.'&id='.$f_id.'">'.$f_id.'</a>');
		$row->AddViewField("SORT", $f_sort);
		$row->AddInputField("SORT", array("size"=>10, 'value'=>$f_sort));

		$row->AddCheckField("active");

		$row->AddInputField("NAME", array("size"=>15, 'value'=>$f_name));
		$row->AddViewField("NAME", '<a href="./fly_popup.php?lang='.SITE_ID.'&id='.$f_id.'">'.$f_name.'</a>');
		$f_type=(empty($typesArr[$f_type]))?$f_type:$typesArr[$f_type]['name'];
		$row->AddViewField("TYPE", $f_type);
		$row->AddViewField("STAT_SHOW", $f_stat_show);
		$row->AddViewField("STAT_TIME", popup::convertTimeFromSecond($f_stat_time));
		$row->AddViewField("STAT_ACTION", $f_stat_action);

		$f_settings=unserialize($arRes['settings']);

		$row->AddViewField("MASK_IN", $f_settings['condition']['showOnlyPath']);
		$row->AddViewField("MASK_OUT", $f_settings['condition']['hideOnlyPath']);
		$tmpSites=implode(',',$f_settings['condition']['sites']);
		$tmpSites=str_ireplace('all', '', $tmpSites);
		$tmpSites=str_ireplace(',,', ',', $tmpSites);
		$row->AddViewField("SITES", $tmpSites);


		$arActions = Array();
		$arActions[] = array(
			"ICON"=>"edit",
			"DEFAULT"=>true,
			"TEXT"=>GetMessage("fly.popup_TABLE_EDIT"),
			"ACTION"=>$lAdmin->ActionRedirect("./fly_popup.php?id=".$f_id)
		);
		$arActions[]=array(
			"ICON"=>"copy",
			"TEXT"=>GetMessage("fly.popup_TABLE_COPY"),
			"ACTION"=>"if(confirm('".GetMessage('fly.popup_TABLE_COPY_CONFIRM')."')) ".$lAdmin->ActionRedirect("fly_popup.php?action_button=copy&ID=".$f_id)
		);
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("fly.popup_TABLE_DELETE"),
			"ACTION"=>"if(confirm('".GetMessage('fly.popup_TABLE_DELETE_CONFIRM')."')) ".$lAdmin->ActionDoGroup($f_id, "delete")
		);

		$row->AddActions($arActions);
	}

	$lAdmin->AddFooter(
	  array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()), // кол-во элементов
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), // счетчик выбранных элементов
	  )
	);
	$lAdmin->AddGroupActionTable(Array(
		"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate"=>GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate"=>GetMessage("MAIN_ADMIN_LIST_DEACTIVATE")
	));

	$aContext = array(
		array(
			"TEXT"=>GetMessage("fly.popup_LIST_CREATE_NEW_POPUP"),
			"LINK"=>"fly_popup.php?lang=".LANG."&id=new",
			"TITLE"=>GetMessage("fly.popup_LIST_CREATE_NEW_POPUP"),
			"ICON"=>"btn_new",
		)
	);
	$lAdmin->AddAdminContextMenu($aContext);

	$lAdmin->CheckListMode();
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->IncludeFile("/bitrix/modules/".$module_id."/include/headerInfo.php", Array());
//create or edit popup
$editFlag = $request->getPost("id_popup");
if(!empty($editFlag)){
	$editableWindow->setPopupId($editFlag);
	$upd=$editableWindow->editPopup($request);
	if($upd['status']=='error'){
		CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE"=>GetMessage("fly.popup_ERROR_SAVE").':'.$upd['data']));
	}else{
		if($idPopup=='new'){
			$tmpMess=GetMessage("fly.popup_SUCCESS_ADD", array('ID'=>$upd['data']));
			$idPopup=$upd['data'];
		}else{
			$tmpMess=GetMessage("fly.popup_SUCCESS_UPDATE", array('ID'=>$upd['data']));
		}
		CAdminMessage::ShowMessage(Array("TYPE"=>"OK", "MESSAGE"=>$tmpMess));
	}
}

//consent out Message
$consentOutMessage = new CAdminMessage(array(
		'TYPE'=>'ERROR',
		'MESSAGE'=>GetMessage("fly.popup_CONSENT_OUT"),
		"HTML"=>true
	));

$tmpConsent=$editableWindow->getConsentList();
if(empty($idPopup)){
	$lAdmin->DisplayList();
	if(count($tmpConsent)==0){
		echo $consentOutMessage->Show();
	}
}else{
	$editableWindow->setPopupId($idPopup);
	if(count($tmpConsent)==0){
		echo $consentOutMessage->Show();
	}
	?>
	<script>
		(window.BX||top.BX).message({'JSADM_FILES':'<?=GetMessage("fly.popup_JSADM_FILES")?>'});
	</script>
	<section class="popup_detail"><form action="" enctype="multipart/form-data" method="post" name="detail_popup">
		<input type="hidden" name="id_popup" value="<?=$idPopup?>" /><?
	$aTabs = array(
		array("DIV" => "fl_popup_settings".$nextPopup["id"], "TAB" => GetMessage("fly.popup_TAB_SETTING_NAME"), "TITLE" => GetMessage("fly.popup_TAB_SETTING_DESC"), "ONSELECT"=>'selectPreviewTab()'),
		array("DIV" => "fl_popup_condition".$nextPopup["id"], "TAB" => GetMessage("fly.popup_TAB_CONDITION_NAME"), "TITLE" => GetMessage("fly.popup_TAB_CONDITION_DESC")),
		/*array("DIV" => "fl_popup_contact".$nextPopup["id"], "TAB" => GetMessage("fly.popup_TAB_CONDITION_CONTACT"), "TITLE" => GetMessage("fly.popup_TAB_CONTACT_DESC"), "ONSELECT"=>'selectContactTab()')*/
	);
	$tabControl = new CAdminTabControl("tabControl".$nextPopup["id"], $aTabs);
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	$types=$editableWindow->getTypes();
	$templates=$editableWindow->getTemplates();
	?>
	<tr>
		<td>
			<h3><?=GetMessage("fly.popup_VIEWS_STEP_1")?></h3>
			<div class="slide_type">
			<?
			$formType='';
			foreach($types as $nextType){
				$activeClass='';
				if(!empty($nextType['active']) && $nextType['active']==true){
					$activeClass=' active';
					$formType=$nextType['code'];
				}?>

				<a href="javascript:void(0);" data-id="<?=$nextType['code']?>" class="slide<?=$activeClass?>" title="<?=$nextType['name']?>">
					<h4><?=$nextType['name']?></h4>
					<img src="/bitrix/themes/.default/<?=$module_id?>/types/<?=$nextType['code']?>.jpg" alt="<?=$nextType['name']?>" />
				</a>
			<?}?>
			</div>
			<input type="hidden" name="type" value="<?=$formType?>" />
			<h3><?=GetMessage("fly.popup_VIEWS_STEP_2")?></h3>
			<div class="select_block">
				<header>
					<div id="templates_list"></div>
					<div id="edit_view"></div>
				</header>
				<h3><?=GetMessage("fly.popup_VIEWS_STEP_DEMO")?></h3>
				<section class="preview">
					<div id="detail_template_area"></div>
				</section>
				<h3><?=GetMessage("fly.popup_VIEWS_STEP_3")?></h3>
				<section>

					<div id="edit_content"></div>
	<?
		$db_iblock_type = CIBlockType::GetList();
		$iblockTypes=array();
		while($ar_iblock_type = $db_iblock_type->Fetch()){
			$arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG);
			$iblockTypes[$ar_iblock_type['ID']]=$arIBType['NAME'];
		}
		$res = CIBlock::GetList(Array('TYPE'=>'ASC', 'NAME'=>'ASC'), Array(), false);
		$avIblocks=array();
		$tmpType='';
		while($ar_res = $res->Fetch()){
			if($tmpType!=$ar_res['IBLOCK_TYPE_ID']){
				$tmpType=$ar_res['IBLOCK_TYPE_ID'];
			}
			$avIblocks[$tmpType][$ar_res['ID']]=$ar_res['NAME'];
		}

		$editArr=$editableWindow->getConditions();
		$checkSaveToList=(!empty($editArr['contact']['emailList']))?' checked="checked"':'';
		$checkRegister=(!empty($editArr['contact']['register']))?' checked="checked"':'';
		$checkSaveToIblock=(!empty($editArr['contact']['iblock']))?' checked="checked"':'';
		$checkSendToManager=$templateLink='';
		if(!empty($editArr['contact']['posttemplate'])){
			$checkSendToManager=' checked="checked"';
			$templateLink=' &nbsp; <a href="/bitrix/admin/message_edit.php?ID='.$editArr['contact']['posttemplate'].'" target="_blank">'.GetMessage("fly.popup_CONTACT_EMAIL_TEMPLATE").' #'.$editArr['contact']['posttemplate'].'</a>';
		}
	?>
	<div class="block contacts ">
		<div class="info"><?=GetMessage("fly.popup_CONTACT_NAME")?></div>
			<label>
			    <span><?=GetMessage("fly.popup_CONTACT_SEND_MAIL")?></span>
				<span class="skwb24-item-hint" id="hint_contact_send_mail">?</span>
				<script>
				new top.BX.CHint({
					parent: top.BX("hint_contact_send_mail"),
					show_timeout: 10,
					hide_timeout: 200,
					dx: 2,
					preventHide: true,
					min_width: 400,
					hint: '<?=GetMessage("fly.popup_CONTACT_SEND_MAIL_HINT")?>'
				});
				</script>
			    <input type="checkbox" name="contact_send_mail" value="Y"<?=$checkSendToManager?> /><?=$templateLink?>
			</label>
			<label class="pref">
			    <span><?=GetMessage("fly.popup_CONTACT_SAVE_TO_IBLOCK")?></span>
				<span class="skwb24-item-hint" id="hint_contact_save_to_iblock">?</span>
				<script>
				new top.BX.CHint({
					parent: top.BX("hint_contact_save_to_iblock"),
					show_timeout: 10,
					hide_timeout: 200,
					dx: 2,
					preventHide: true,
					min_width: 400,
					hint: '<?=GetMessage("fly.popup_CONTACT_SAVE_TO_IBLOCK_HINT")?>'
				});
				</script>
				<input type="checkbox" name="contact_save_to_iblock" value="Y"<?=$checkSaveToIblock?> />
			</label>

			<?if(count($avIblocks)>0){?>
				<label class="pref">
				    <span><?=GetMessage("fly.popup_CONTACT_SAVE_LIST_IBLOCK")?></span>

					<select name="contact_iblock"><option value="">...</option><?
					$list='';
					foreach($avIblocks as $keyType=>$nextType){?>
						<optgroup label="<?=$iblockTypes[$keyType]?>"><?
						foreach($nextType as $keyBlock=>$valBlock){
							$selected=(!empty($editArr['contact']['iblock']) && $editArr['contact']['iblock']==$keyBlock)?' selected="selected"':'';
							?>
							<option value="<?=$keyBlock?>"<?=$selected?>><?=$valBlock?> [<?=$keyBlock?>]</option>
						<?}?></optgroup>
					<?}?></select>
				</label>
			<?}
			if (\Bitrix\Main\ModuleManager::isModuleInstalled('sender')){?>
			<label class="pref">
			    <span><?=GetMessage("fly.popup_CONTACT_SAVE_TO_LIST")?></span>
				<span class="skwb24-item-hint" id="hint_contact_save_to_list">?</span>
				<script>
				new top.BX.CHint({
					parent: top.BX("hint_contact_save_to_list"),
					show_timeout: 10,
					hide_timeout: 200,
					dx: 2,
					preventHide: true,
					min_width: 400,
					hint: '<?=GetMessage("fly.popup_CONTACT_SAVE_TO_LIST_HINT")?>'
				});
				</script>
			    <input type="checkbox" name="contact_save_to_list" value="Y"<?=$checkSaveToList?> />
			</label>
			<?}?>
			<label class="pref">
				<span><?=GetMessage("fly.popup_CONTACT_REGISTER")?></span>
				<span class="skwb24-item-hint" id="hint_contact_register">?</span>
				<script>
					new top.BX.CHint({
					parent: top.BX("hint_contact_register"),
					show_timeout: 10,
					hide_timeout: 200,
					dx: 2,
					preventHide: true,
					min_width: 400,
					hint: '<?=GetMessage("fly.popup_CONTACT_REGISTER_HINT")?>'
				});
				</script>
				<input type="checkbox" name="contact_register" value="Y" <?=$checkRegister?>/>
			</label>
	</div>
	<div class="block timer">
	
		<div class="info"><?=GetMessage("fly.popup_TIMER_NAME")?></div>
		<label class="pref">
			<span><?=GetMessage("fly.popup_TIMER_ENABLE")?></span>
			<span class="skwb24-item-hint" id="hint_timer_enable">?</span>
			<script>
				new top.BX.CHint({
					parent: top.BX("hint_timer_enable"),
					show_timeout:10,
					hide_timeout:200,
					dx:2,
					preventHide:true,
					min_width:400,
					hint:'<?=GetMessage("fly.popup_TIMER_ENABLE_HINT")?>'
				});
			</script>
			<?$checkTimer=($editArr['timer']['enabled']=='Y')?'checked="checked"':'';?>
			<input type="checkbox" name="timer_enable" value="Y" <?=$checkTimer?>>
		</label>
		<label class="pref">
			<span><?=GetMessage("fly.popup_TIMER_TIME")?></span>
			<span class="skwb24-item-hint" id="hint_time_hint">?</span>
			<script>
				new top.BX.CHint({
					parent: top.BX("hint_time_hint"),
					show_timeout:10,
					hide_timeout:200,
					dx:2,
					preventHide:true,
					min_width:400,
					hint:'<?=GetMessage("fly.popup_TIMER_TIME_HINT")?>'
				});
			</script>
			<div class="adm-input-wrap adm-input-wrap-calendar">
				<input class="adm-input adm-input-calendar" type="text" name="timer_date" value="<?=$editArr['timer']['date']?>">
				<span class="adm-calendar-icon" title="<?=GetMessage("fly.popup_TIMER_TIME_TITLE")?>" onclick="BX.calendar({node:this,field:'timer_date',form:'',bTime:true,bHideTime:false})"></span>
			</div>
		</label>
		<label class="pref">
			<?//var_dump($_POST["timer_text"]);?>
			<span><?=GetMessage("fly.popup_TIMER_TEXT")?></span>
			<span class="skwb24-item-hint" id="hint_timer_text">?</span>
			<script>
				new top.BX.CHint({
					parent: top.BX("hint_timer_text"),
					show_timeout:10,
					hide_timeout:200,
					dx:2,
					preventHide:true,
					min_width:400,
					hint:'<?=GetMessage("fly.popup_TIMER_TEXT_HINT")?>'
				});
			</script>
			<input type="text" name="timer_text" value="<?echo (!empty($editArr['timer']['text']))?$editArr['timer']['text']:GetMessage("fly.popup_TIMER_TEXT_DEFAULT")?>">
		</label>
		<label class="pref">
			<span><?=GetMessage("fly.popup_TIMER_LEFT")?></span>
			<?$checkTimerLeft=($editArr['timer']['left']=='Y'||empty($editArr['timer']['left']))?'checked="checked"':'';?>
			<input type="checkbox" value="Y" <?=$checkTimerLeft?>>

			<input type="hidden" name="timer_left" value="<?=(!empty($editArr['timer']['left']))?$editArr['timer']['left']:'Y'?>">
		</label>
		<label class="pref">
			<span><?=GetMessage("fly.popup_TIMER_RIGHT")?></span>
			<?$checkTimerRight=($editArr['timer']['right']=='Y')?'checked="checked"':'';?>
			<input type="checkbox" value="Y" <?=$checkTimerRight?>>

			<input type="hidden" name="timer_right" value="<?=(!empty($editArr['timer']['right']))?$editArr['timer']['right']:'N'?>">
		</label>
		<label class="pref">
			<span><?=GetMessage("fly.popup_TIMER_TOP")?></span>
			<?$checkTimerTop=($editArr['timer']['top']=='Y'||empty($editArr['timer']['top']))?'checked="checked"':'';?>
			<input type="checkbox" value="Y" <?=$checkTimerTop?>>

			<input type="hidden" name="timer_top" value="<?=(!empty($editArr['timer']['top']))?$editArr['timer']['top']:'Y'?>">
		</label>
		<label class="pref">
			<span><?=GetMessage("fly.popup_TIMER_BOTTOM")?></span>
			<?$checkTimerBottom=($editArr['timer']['bottom']=='Y')?'checked="checked"':'';?>
			<input type="checkbox" value="Y" <?=$checkTimerBottom?>>

			<input type="hidden" name="timer_bottom" value="<?=(!empty($editArr['timer']['bottom']))?$editArr['timer']['bottom']:'N'?>">
		</label>
	</div>
	<div class="block roulette">
		<?
			$colors = array(
				'#ff9ff3'=>'Jigglypuff',
				'#f368e0'=>'Lian Hong lotus pink',

				'#00d2d3'=>'Jade dust',
				'#01a3a4'=>'Aqua velvet',

				'#feca57'=>'Casandora yellow',
				'#ff9f43'=>'Double dragon skin',

				'#54a0ff'=>'Joust blue',
				'#2e86de'=>'Bleu de france',

				'#ff6b6b'=>'Pastel red',
				'#ee5253'=>'Amour',

				'#5f27cd'=>'Nasu purple',
				'#341f97'=>'Bluebell',

				'#48dbfb'=>'Megaman',
				'#0abde3'=>'Cyanite',

				'#1dd1a1'=>'Wild caribbean green',
				'#10ac84'=>'Dark mountain meadow',

				'#576574'=>'Fuel town',
				'#222f3e'=>'Imperial primer',

			);
			$tmpBasketRule=array();
			$tmpBasketRule['nothing']=GetMessage("fly.popup_ROULETTE_NOTHING");
			$tmpBasketRule['win']=GetMessage("fly.popup_ROULETTE_WIN");
			$tmpLastBasketRule=0;
			$tmpFirstBasketRule=0;
			if (\Bitrix\Main\Loader::IncludeModule('sale')){
				$tmpBasketRule_=Fly\Popup\Tools::getBasketRules();
				foreach($tmpBasketRule_ as $key=>$rule){
					if($tmpFirstBasketRule==0)$tmpFirstBasketRule=$key;
					$tmpBasketRule[$key]=$rule;
					$tmpLastBasketRule=$key;
				}
			}
			if(empty($editArr['roulett'][1])){
				$editArr['roulett']=array(
					'1'=>array('text'=>GetMessage("fly.popup_TYPE_ROULETTE_TEXT_1_DEFAULT"),'color'=>GetMessage("fly.popup_TYPE_ROULETTE_COLOR_1_DEFAULT")),
					'2'=>array('text'=>GetMessage("fly.popup_TYPE_ROULETTE_TEXT_2_DEFAULT"),'color'=>GetMessage("fly.popup_TYPE_ROULETTE_COLOR_2_DEFAULT")),
					'3'=>array('text'=>GetMessage("fly.popup_TYPE_ROULETTE_TEXT_3_DEFAULT"),'color'=>GetMessage("fly.popup_TYPE_ROULETTE_COLOR_3_DEFAULT")),
					'4'=>array('text'=>GetMessage("fly.popup_TYPE_ROULETTE_TEXT_4_DEFAULT"),'color'=>GetMessage("fly.popup_TYPE_ROULETTE_COLOR_4_DEFAULT")),
					'5'=>array('text'=>GetMessage("fly.popup_TYPE_ROULETTE_TEXT_5_DEFAULT"),'color'=>GetMessage("fly.popup_TYPE_ROULETTE_COLOR_5_DEFAULT")),
					'6'=>array('text'=>GetMessage("fly.popup_TYPE_ROULETTE_TEXT_6_DEFAULT"),'color'=>GetMessage("fly.popup_TYPE_ROULETTE_COLOR_6_DEFAULT")),
					'7'=>array('text'=>GetMessage("fly.popup_TYPE_ROULETTE_TEXT_7_DEFAULT"),'color'=>GetMessage("fly.popup_TYPE_ROULETTE_COLOR_7_DEFAULT")),
					'8'=>array('text'=>GetMessage("fly.popup_TYPE_ROULETTE_TEXT_8_DEFAULT"),'color'=>GetMessage("fly.popup_TYPE_ROULETTE_COLOR_8_DEFAULT"))
				);
				$editArr['roulett']['count']=8;
			}
		?>
		<script>
			var colors_for_roulette=<?=CUtil::PhpToJSObject($colors)?>;
			var basket_rule_for_roulette=<?=CUtil::PhpToJSObject($tmpBasketRule)?>;
			var tmpLastBasketRule=<?=$tmpLastBasketRule?>;
			var tmpFirstBasketRule=<?=$tmpFirstBasketRule?>;
			var basket_rule_basic='<?=GetMessage("fly.popup_ROULETTE_BASIC")?>';
			var basket_rule_rules='<?=GetMessage("fly.popup_ROULETTE_RULES")?>';
			var minimum_message='<?=GetMessage("fly.popup_ROULETTE_MINIMUM")?>';
			var rule_info='<?=GetMessage("fly.popup_ROULETTE_RULE_INFO")?>';
		</script>
		<table class="adm-list-table">
			<thead>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell"></td>
					<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage("fly.popup_ROULETTE_SORT")?></div></td>
					<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage("fly.popup_ROULETTE_TEXT")?></div></td>
					<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage("fly.popup_ROULETTE_COLOR")?></div></td>
					<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage("fly.popup_ROULETTE_RULE")?>
					<span class="skwb24-item-hint" id="hint_roulette_rule">?</span>
					<script>
						new top.BX.CHint({
							parent: top.BX("hint_roulette_rule"),
							show_timeout:10,
							hide_timeout:200,
							dx:2,
							preventHide:true,
							min_width:400,
							hint:'<?echo GetMessage("fly.popup_ROULETTE_RULE_INFO_BASIC"); echo (\Bitrix\Main\Loader::IncludeModule('sale'))?GetMessage("fly.popup_ROULETTE_RULE_INFO_SALE"):''?>'
						});
					</script>
					</div></td>
					<td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=GetMessage("fly.popup_ROULETTE_CONTROL")?></div></td>
				</tr>
			</thead>
			<tbody class="drag_container">
				<tr class="adm-list-table-row draggable">
					<td class="adm-list-table-cell">
						<div class="adm-list-table-popup drag_key"></div>
					</td>
					<td class="adm-list-table-cell">
						1
					</td>
					<td class="adm-list-table-cell">
						<input type="text" name="roulette_1_text" size="50" value="<?=$editArr['roulett'][1]['text']?>">
					</td>
					<td class="adm-list-table-cell">
						<select name="roulette_1_color" class="color_selector">
							<?foreach($colors as $hex=>$colorname){?>
								<option style="background:<?=$hex?>;color:<?=$hex?>" <?echo ($editArr['roulett'][1]['color']==$hex)?'selected':''?> value="<?=$hex?>"><?=$colorname?></option>
							<?}?>
						</select>
					</td>
					<td class="adm-list-table-cell">
						<select name="roulette_1_rule" class="rule_selector">
						<?
						foreach($tmpBasketRule as $rule=>$name){
							echo ($rule=='nothing')?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_BASIC").'">':'';
							echo ($tmpFirstBasketRule==$rule)?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_RULES").'">':'';
							?>
							<option value="<?=$rule?>" <?echo ($editArr['roulett'][1]['rule']==$rule)?'selected':''?> ><?=$name?></option>
						<?
							echo ($rule=='win')?'</optgroup>':'';
							echo ($tmpLastBasketRule==$rule)?'</optgroup>':'';
						}?>
						</select>
					</td>
					<td class="adm-list-table-cell">
						<a href="javascript:;" onclick="remove_roulette_row(this);">
							<img width="20px" height='25px' src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAzNzguMzAzIDM3OC4zMDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM3OC4zMDMgMzc4LjMwMzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+Cjxwb2x5Z29uIHN0eWxlPSJmaWxsOiNGRjM1MDE7IiBwb2ludHM9IjM3OC4zMDMsMjguMjg1IDM1MC4wMTgsMCAxODkuMTUxLDE2MC44NjcgMjguMjg1LDAgMCwyOC4yODUgMTYwLjg2NywxODkuMTUxIDAsMzUwLjAxOCAgIDI4LjI4NSwzNzguMzAyIDE4OS4xNTEsMjE3LjQzNiAzNTAuMDE4LDM3OC4zMDIgMzc4LjMwMywzNTAuMDE4IDIxNy40MzYsMTg5LjE1MSAiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==">
						</a>
					</td>
				</tr>
				<tr class="adm-list-table-row draggable">
					<td class="adm-list-table-cell">
						<div class="adm-list-table-popup drag_key"></div>
					</td>
					<td class="adm-list-table-cell">
						2
					</td>
					<td class="adm-list-table-cell">
						<input type="text" name="roulette_2_text" size="50"  value="<?=$editArr['roulett'][2]['text']?>">
					</td>
					<td class="adm-list-table-cell">
						<select name="roulette_2_color" class="color_selector">
							<?foreach($colors as $hex=>$colorname){?>
								<option style="background:<?=$hex?>;color:<?=$hex?>" <?echo ($editArr['roulett'][2]['color']==$hex)?'selected':''?> value="<?=$hex?>"><?=$colorname?></option>
							<?}?>
						</select>
					</td>
					<td class="adm-list-table-cell">
						<select name="roulette_2_rule" class="rule_selector">
						<?
						foreach($tmpBasketRule as $rule=>$name){
							echo ($rule=='nothing')?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_BASIC").'">':'';
							echo ($tmpFirstBasketRule==$rule)?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_RULES").'">':'';
							?>
							<option value="<?=$rule?>" <?echo ($editArr['roulett'][2]['rule']==$rule)?'selected':''?> ><?=$name?></option>
						<?
							echo ($rule=='win')?'</optgroup>':'';
							echo ($tmpLastBasketRule==$rule)?'</optgroup>':'';
						}?>
						</select>
					</td>
					<td class="adm-list-table-cell">
						<a href="javascript:;" onclick="remove_roulette_row(this);"><img width="20px" height='25px' src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAzNzguMzAzIDM3OC4zMDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM3OC4zMDMgMzc4LjMwMzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+Cjxwb2x5Z29uIHN0eWxlPSJmaWxsOiNGRjM1MDE7IiBwb2ludHM9IjM3OC4zMDMsMjguMjg1IDM1MC4wMTgsMCAxODkuMTUxLDE2MC44NjcgMjguMjg1LDAgMCwyOC4yODUgMTYwLjg2NywxODkuMTUxIDAsMzUwLjAxOCAgIDI4LjI4NSwzNzguMzAyIDE4OS4xNTEsMjE3LjQzNiAzNTAuMDE4LDM3OC4zMDIgMzc4LjMwMywzNTAuMDE4IDIxNy40MzYsMTg5LjE1MSAiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg=="></a>
					</td>
				</tr>
				<tr class="adm-list-table-row draggable">
					<td class="adm-list-table-cell">
						<div class="adm-list-table-popup drag_key"></div>
					</td>
					<td class="adm-list-table-cell">
						3
					</td>
					<td class="adm-list-table-cell">
						<input type="text" name="roulette_3_text" size="50" value="<?=$editArr['roulett'][3]['text']?>">
					</td>
					<td class="adm-list-table-cell">
						<select name="roulette_3_color"  class="color_selector">
							<?foreach($colors as $hex=>$colorname){?>
								<option style="background:<?=$hex?>;color:<?=$hex?>" <?echo ($editArr['roulett'][3]['color']==$hex)?'selected':''?> value="<?=$hex?>"><?=$colorname?></option>
							<?}?>
						</select>
					</td>
					<td class="adm-list-table-cell draggable">
						<select name="roulette_3_rule" class="rule_selector">
						<?
						foreach($tmpBasketRule as $rule=>$name){
							echo ($rule=='nothing')?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_BASIC").'">':'';
							echo ($tmpFirstBasketRule==$rule)?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_RULES").'">':'';
							?>
							<option value="<?=$rule?>" <?echo ($editArr['roulett'][3]['rule']==$rule)?'selected':''?> ><?=$name?></option>
						<?
							echo ($rule=='win')?'</optgroup>':'';
							echo ($tmpLastBasketRule==$rule)?'</optgroup>':'';
						}?>
						</select>
					</td>
					<td class="adm-list-table-cell">
						<a href="javascript:;" onclick="remove_roulette_row(this);"><img width="20px" height='25px' src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAzNzguMzAzIDM3OC4zMDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM3OC4zMDMgMzc4LjMwMzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+Cjxwb2x5Z29uIHN0eWxlPSJmaWxsOiNGRjM1MDE7IiBwb2ludHM9IjM3OC4zMDMsMjguMjg1IDM1MC4wMTgsMCAxODkuMTUxLDE2MC44NjcgMjguMjg1LDAgMCwyOC4yODUgMTYwLjg2NywxODkuMTUxIDAsMzUwLjAxOCAgIDI4LjI4NSwzNzguMzAyIDE4OS4xNTEsMjE3LjQzNiAzNTAuMDE4LDM3OC4zMDIgMzc4LjMwMywzNTAuMDE4IDIxNy40MzYsMTg5LjE1MSAiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg=="></a>
					</td>
				</tr>
				<tr class="adm-list-table-row draggable">
					<td class="adm-list-table-cell">
						<div class="adm-list-table-popup drag_key"></div>
					</td>
					<td class="adm-list-table-cell">
						4
					</td>
					<td class="adm-list-table-cell">
						<input type="text" name="roulette_4_text" size="50" value="<?=$editArr['roulett'][4]['text']?>">
					</td>
					<td class="adm-list-table-cell">
						<select name="roulette_4_color" class="color_selector">
							<?foreach($colors as $hex=>$colorname){?>
								<option style="background:<?=$hex?>;color:<?=$hex?>" <?echo ($editArr['roulett'][4]['color']==$hex)?'selected':''?> value="<?=$hex?>"><?=$colorname?></option>
							<?}?>
						</select>
					</td>
					<td class="adm-list-table-cell">
						<select name="roulette_4_rule" class="rule_selector">
						<?
						foreach($tmpBasketRule as $rule=>$name){
							echo ($rule=='nothing')?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_BASIC").'">':'';
							echo ($tmpFirstBasketRule==$rule)?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_RULES").'">':'';
							?>
							<option value="<?=$rule?>" <?echo ($editArr['roulett'][4]['rule']==$rule)?'selected':''?> ><?=$name?></option>
						<?
							echo ($rule=='win')?'</optgroup>':'';
							echo ($tmpLastBasketRule==$rule)?'</optgroup>':'';
						}?>
						</select>
					</td>
					<td class="adm-list-table-cell">
						<a href="javascript:;" onclick="remove_roulette_row(this);"><img width="20px" height='25px' src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAzNzguMzAzIDM3OC4zMDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM3OC4zMDMgMzc4LjMwMzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+Cjxwb2x5Z29uIHN0eWxlPSJmaWxsOiNGRjM1MDE7IiBwb2ludHM9IjM3OC4zMDMsMjguMjg1IDM1MC4wMTgsMCAxODkuMTUxLDE2MC44NjcgMjguMjg1LDAgMCwyOC4yODUgMTYwLjg2NywxODkuMTUxIDAsMzUwLjAxOCAgIDI4LjI4NSwzNzguMzAyIDE4OS4xNTEsMjE3LjQzNiAzNTAuMDE4LDM3OC4zMDIgMzc4LjMwMywzNTAuMDE4IDIxNy40MzYsMTg5LjE1MSAiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg=="></a>
					</td>
				</tr>
				<?if(!empty($editArr['roulett']['count'])&&$editArr['roulett']['count']>4){
					for($i=5;$i<=$editArr['roulett']['count'];$i++){?>
							<tr class="adm-list-table-row draggable">
								<td class="adm-list-table-cell"><div class="adm-list-table-popup drag_key"></div></td>
								<td class="adm-list-table-cell"><?=$i?></td>
								<td class="adm-list-table-cell"><input type="text" name="roulette_<?=$i?>_text" size="50" value="<?=$editArr['roulett'][$i]['text']?>"></td>
								<td class="adm-list-table-cell"><select name="roulette_<?=$i?>_color" class="color_selector"><?foreach($colors as $hex=>$colorname){?><option style="background:<?=$hex?>;color:<?=$hex?>" <?echo ($editArr['roulett'][$i]['color']==$hex)?'selected':''?> value="<?=$hex?>"><?=$colorname?></option><?}?></select></td>
								<td class="adm-list-table-cell"><select name="roulette_<?=$i?>_rule" class="rule_selector"><?foreach($tmpBasketRule as $rule=>$name){echo ($rule=='nothing')?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_BASIC").'">':'';echo ($tmpFirstBasketRule==$rule)?'<optgroup label="'.GetMessage("fly.popup_ROULETTE_RULES").'">':'';?><option value="<?=$rule?>" <?=($editArr['roulett'][$i]['rule']==$rule)?'selected':''?> ><?=$name?></option><?echo ($rule=='win')?'</optgroup>':'';echo ($tmpLastBasketRule==$rule)?'</optgroup>':'';}?></select></td>
								<td class="adm-list-table-cell"><a href="javascript:;" onclick="remove_roulette_row(this);"><img width="20px" height='25px' src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAzNzguMzAzIDM3OC4zMDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM3OC4zMDMgMzc4LjMwMzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+Cjxwb2x5Z29uIHN0eWxlPSJmaWxsOiNGRjM1MDE7IiBwb2ludHM9IjM3OC4zMDMsMjguMjg1IDM1MC4wMTgsMCAxODkuMTUxLDE2MC44NjcgMjguMjg1LDAgMCwyOC4yODUgMTYwLjg2NywxODkuMTUxIDAsMzUwLjAxOCAgIDI4LjI4NSwzNzguMzAyIDE4OS4xNTEsMjE3LjQzNiAzNTAuMDE4LDM3OC4zMDIgMzc4LjMwMywzNTAuMDE4IDIxNy40MzYsMTg5LjE1MSAiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg=="></a></td>
							</tr>
					<?}
				}?>
			</tbody>
		</table>
		<input type="hidden" value="<?=(!empty($editArr['roulett']['count'])?$editArr['roulett']['count']:8)?>" name="roulette_element_count">
		<a href="javascript:;" class="adm-btn-save adm-btn-add adm-btn add-roulette-row"><?=GetMessage("fly.popup_ROULETTE_ADD")?></a>
	</div>
				</section>
				<script>
					var templatesType=<?=CUtil::PhpToJSObject($types)?>;
					var templatesPopup=<?=CUtil::PhpToJSObject($templates)?>;
				</script>
			</div>

			<div id="popup_manager_files" style="display:none">
				<div id="popup_img_list"></div>
				<?$APPLICATION->IncludeComponent("bitrix:main.file.input", "drag_n_drop",
				   array(
					  "INPUT_NAME"=>"UPLOAD_IMG_POPUP",
					  "MULTIPLE"=>"N",
					  "MODULE_ID"=>$module_id,
					  //"MODULE_ID"=>'iblock',
					  "MAX_FILE_SIZE"=>"5000000",
					  "ALLOW_UPLOAD"=>"F",
					  //"ALLOW_UPLOAD_EXT"=>array("jpeg", "jpg", "png", "gif")
					  "ALLOW_UPLOAD_EXT"=>"jpeg,jpg,png,gif"
				   ),
				   false
				);?>
			</div>
		</td>
	</tr>


	<?$tabControl->BeginNextTab();?>
	<?
	$editArr=$editableWindow->getConditions();
	$activeCheckBox=($editArr['active'])?' checked="checked"':'';
	$activeAlreadygoing=($editArr['alreadygoing'])?' checked="checked"':'';
	$selectSite='<select multiple="multiple" size="'.min(3, count($editArr['sites'])).'" name="sites[]">';
	$period_from='';
	$period_to='';
	$editArr['dateStart']=(empty($editArr['dateStart']))?'':ConvertTimeStamp($editArr['dateStart'], "SHORT", LANGUAGE_ID);
	$editArr['dateFinish']=(empty($editArr['dateFinish']))?'':ConvertTimeStamp($editArr['dateFinish'], "SHORT", LANGUAGE_ID);
	if(!empty($editArr['timeInterval'])){
		$tmpPeriod=explode('#', $editArr['timeInterval']);
		$period_from=(!empty($tmpPeriod[0]))?$tmpPeriod[0]:'';
		$period_to=(!empty($tmpPeriod[1]))?$tmpPeriod[1]:'';
	}
	foreach($editArr['sites'] as $nextSite){
		$selectOption=($nextSite['active'])?' selected="selected"':'';
		$selectSite.='<option value="'.$nextSite['id'].'"'.$selectOption.'>'.$nextSite['name'].'</option>';
	}
	$selectSite.='</select>';
	/* $selectnextGroups='<select multiple="multiple" size="'.min(4, count($editArr['sites'])).'" name="groups[]">'; */
	$selectnextGroups='<select multiple="multiple" size="4" name="groups[]">';
	foreach($editArr['groups'] as $nextGroup){
		$selectOption=($nextGroup['active'])?' selected="selected"':'';
		$selectnextGroups.='<option value="'.$nextGroup['id'].'"'.$selectOption.'>'.$nextGroup['name'].'</option>';
	}
	$selectnextGroups.='</select>';
	$serviceName=(!empty($editArr['service_name']))?$editArr['service_name']:GetMessage("fly.popup_TABCOND_SERVICE_NAME").'_'.$idPopup;

	//condition
	/*$condPathsPriority_notshow='';
	$condPathsPriority_show=' checked="checked"';
	if(!empty($editArr['maskPriority']) && $editArr['maskPriority']=='NOTSHOW'){
		$condPathsPriority_notshow=' checked="checked"';
		$condPathsPriority_show='';
	}*/
	?>
	<tr><th colspan="2"><?=GetMessage("fly.popup_TABCOND_TITLE_MAIN")?></th></tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_ACTIVE")?>
			<span class="skwb24-item-hint" id="hint_tabcond_title_main">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_tabcond_title_main"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_ACTIVE_HINT")?>'
			});
			</script>
		</td>
		<td><input type="checkbox" name="active" value="Y"<?=$activeCheckBox?> /></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_SERVICE_NAME")?>
			<span class="skwb24-item-hint" id="hint_service_name">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_service_name"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_SERVICE_NAME_HINT")?>'
			});
			</script>
		</td>
		<td><input type="text" name="service_name" value="<?=$serviceName?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_SORT")?>
			<span class="skwb24-item-hint" id="hint_sort">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_sort"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_SORT_HINT")?>'
			});
			</script>
		</td>
		<td><input type="number" min="1" step="1" size="4" name="sort" value="<?=$editArr['sort']?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_SITE")?>
			<span class="skwb24-item-hint" id="hint_site">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_site"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_SITE_HINT")?>'
			});
			</script>
		</td>
		<td><?=$selectSite?></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_ACTIVE_FROM")?>
			<span class="skwb24-item-hint" id="hint_from">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_from"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_ACTIVE_FROM_HINT")?>'
			});
			</script>
		</td>
		<td><?=CalendarDate('dateStart', $editArr['dateStart'], 'detail_popup')?></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_ACTIVE_TO")?>
			<span class="skwb24-item-hint" id="hint_to">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_to"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_ACTIVE_TO_HINT")?>'
			});
			</script>
		</td>
		<td><?=CalendarDate('dateFinish', $editArr['dateFinish'], 'detail_popup')?></td>
	</tr>
	<tr>
		<th colspan="2"><?=GetMessage("fly.popup_TABCOND_TITLE_ADDITIONAL")?></th>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_GROUPS")?>
			<span class="skwb24-item-hint" id="hint_groups">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_groups"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_GROUPS_HINT")?>'
			});
			</script>
		</td>
		<td><?=$selectnextGroups?></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_SHOWONLYPATH")?>
			<span class="skwb24-item-hint" id="hint_maskshow">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_maskshow"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_SHOWONLYPATH_HINT")?>'
			});
			</script>
		</td>
		<td><textarea name="showOnlyPath" cols="50" rows="5" placeholder="<?=GetMessage("fly.popup_TABCOND_SHOWONLYPATH_EXAMPLE")?>"><?=$editArr['showOnlyPath']?></textarea></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_HIDEONLYPATH")?>
			<span class="skwb24-item-hint" id="hint_maskhide">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_maskhide"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_HIDEONLYPATH_HINT")?>'
			});
			</script>
		</td>
		<td><textarea name="hideOnlyPath" cols="50" rows="5" placeholder="<?=GetMessage("fly.popup_TABCOND_HIDEONLYPATH_EXAMPLE")?>"><?=$editArr['hideOnlyPath']?></textarea></td>
	</tr>
	<?/*<tr>
		<td><?=GetMessage("fly.popup_TABCOND_PATHPRIORITY")?>
			<span class="skwb24-item-hint" id="hint_maskpriority">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_maskpriority"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_PATHPRIORITY_HINT")?>'
			});
			</script>
		</td>
		<td>
			<label><input type="radio" name="maskPriority" value="SHOW"<?=$condPathsPriority_show?> /> <span style="display:inline-block; width:200px;"><?=GetMessage("fly.popup_TABCOND_PRIORITY_SHOW")?></span></label><br>
			<label><input type="radio" name="maskPriority" value="NOTSHOW"<?=$condPathsPriority_notshow?> /> <span style="display:inline-block; width:200px;"><?=GetMessage("fly.popup_TABCOND_PRIORITY_NOTSHOW")?></span></label>
		</td>
	</tr>*/?>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_AFTERSHOWCOUNTPAGES")?>
			<span class="skwb24-item-hint" id="hint_aftershowcount">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_aftershowcount"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_AFTERSHOWCOUNTPAGES_HINT")?>'
			});
			</script>
		</td>
		<td><input type="number" min="0" step="1" name="afterShowCountPages" value="<?=$editArr['afterShowCountPages']?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_AFTERTIMESECOND")?>
			<span class="skwb24-item-hint" id="hint_aftertimesec">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_aftertimesec"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_AFTERTIMESECOND_HINT")?>'
			});
			</script>
		</td>
		<td><input type="number" min="0" step="1" name="afterTimeSecond" value="<?=$editArr['afterTimeSecond']?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_TIMEINTERVAL")?>
			<span class="skwb24-item-hint" id="hint_timeinterval">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_timeinterval"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_TIMEINTERVAL_HINT")?>'
			});
			</script>
		</td>
		<td>
			<?=GetMessage("SKWB24_AG_COND_TIME_FROM")?>
			<?$APPLICATION->IncludeComponent("bitrix:main.clock","",Array(
				"INPUT_ID" => "",
				"INPUT_NAME" => "period_from",
				"INPUT_TITLE" => GetMessage("fly.popup_TABCOND_TIMEINTERVAL_FROM"),
				"INIT_TIME" => $period_from,
				"STEP" => "1"
			));?>
			<?=GetMessage("SKWB24_AG_COND_TIME_TO")?>
			<?$APPLICATION->IncludeComponent("bitrix:main.clock","",Array(
				"INPUT_ID" => "",
				"INPUT_NAME" => "period_to",
				"INPUT_TITLE" => GetMessage("fly.popup_TABCOND_TIMEINTERVAL_TO"),
				"INIT_TIME" => $period_to,
				"STEP" => "1"
			));?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_REPEATTIME")?>
			<span class="skwb24-item-hint" id="hint_repeattime">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_repeattime"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_REPEATTIME_HINT")?>'
			});
			</script>
		</td>
		<td><input type="number" min="0" step="1" name="repeatTime" value="<?=$editArr['repeatTime']?>" />
			<?
			$hour=$day=$week=$month=$year='';
			if(empty($editArr['repeatTime_type'])){
				$day="selected";
			}else{
				${$editArr['repeatTime_type']}="selected='selected'";
			}?>
			<select name="repeatTime_type">
				<option <?=$hour?> value="hour"><?=GetMessage("fly.popup_TABCOND_REPEATTYPE_HOUR")?></option>
				<option <?=$day?> value="day"><?=GetMessage("fly.popup_TABCOND_REPEATTYPE_DAY")?></option>
				<option <?=$week?> value="week"><?=GetMessage("fly.popup_TABCOND_REPEATTYPE_WEEK")?></option>
				<option <?=$month?> value="month"><?=GetMessage("fly.popup_TABCOND_REPEATTYPE_MONTH")?></option>
				<option <?=$year?> value="year"><?=GetMessage("fly.popup_TABCOND_REPEATTYPE_YEAR")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_ALREADYGOING")?>
			<span class="skwb24-item-hint" id="hint_alreadygoing">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_alreadygoing"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_ALREADYGOING_HINT")?>'
			});
			</script>
		</td>
		<td><input type="checkbox" name="alreadygoing" value="Y"<?=$activeAlreadygoing?> /></td>
	</tr>
	<tr>
		<th colspan="2"><?=GetMessage("fly.popup_TABCOND_TITLE_EVENTS")?></th>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_ANCHORVISIBLE")?>
			<span class="skwb24-item-hint" id="hint_anchorvisible">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_anchorvisible"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_ANCHORVISIBLE_HINT")?>'
			});
			</script>
		</td>
		<td><input type="text" name="anchorVisible" value="<?=$editArr['anchorVisible']?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_ONCLICKCLASSLINK")?>
			<span class="skwb24-item-hint" id="hint_onclickclasslink">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_onclickclasslink"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_ONCLICKCLASSLINK_HINT")?>'
			});
			</script>
		</td>
		<td><input type="text" name="onClickClassLink" value="<?=$editArr['onClickClassLink']?>" /></td>
	</tr>
	<?if(isset($editArr['saleCountProduct'])){?>
	<tr>
		<th colspan="2"><?=GetMessage("fly.popup_TABCOND_TITLE_SALE")?></th>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_SALECOUNTPRODUCT")?>
			<span class="skwb24-item-hint" id="hint_countproduct">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_countproduct"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_SALECOUNTPRODUCT_HINT")?>'
			});
			</script>
		</td>
		<td><input type="number" min="0" step="1" name="saleCountProduct" value="<?=$editArr['saleCountProduct']?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_SALESUMMBASKET")?>
			<span class="skwb24-item-hint" id="hint_summbasket">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_summbasket"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_SALESUMMBASKET_HINT")?>'
			});
			</script>
		</td>
		<td><input type="number" min="0" step="1" name="saleSummBasket" value="<?=$editArr['saleSummBasket']?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage("fly.popup_TABCOND_SALEIDPRODINBASKET")?>
			<span class="skwb24-item-hint" id="hint_idinbasket">?</span>
			<script>
			new top.BX.CHint({
				parent: top.BX("hint_idinbasket"),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: '<?=GetMessage("fly.popup_TABCOND_SALEIDPRODINBASKET_HINT")?>'
			});
			</script>
		</td>
		<td>
			<?
			$currentBasketProduct=0;
			if(is_array($editArr['saleIDProdInBasket'])){
				$currentBasketProduct=count($editArr['saleIDProdInBasket']);
				$tov_Names=array();
				$tmpNames=CIBlockElement::GetList(array(),array('ID'=>$editArr['saleIDProdInBasket']),false,false,array('ID','NAME'));
				while($tmpName=$tmpNames->Fetch())
					$tov_Names[$tmpName['ID']]=$tmpName['NAME'];
				foreach($editArr['saleIDProdInBasket'] as $key=>$val){?>
					<div class="button_add">
						<input name="saleIDProdInBasket[]" id="saleIDProdInBasket_<?=$key?>" value="<?=$val?>" size="5" type="text">
						<input type="button" value="..." onclick="jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=<?=LANG?>&amp;n=saleIDProdInBasket_<?=$currentBasketProduct?>&amp;k=n&amp;', 900, 700);">
						<span id="sp_'saleIDProdInBasket_<?=$key?>"><?=$tov_Names[$val]?></span>
					</div>
				<?}?>
			<?}?>
			<div class="button_add">
				<input name="saleIDProdInBasket[]" id="saleIDProdInBasket_<?=$currentBasketProduct?>" value="" size="5" type="text">
				<input type="button" value="..." onclick="jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=<?=LANG?>&amp;n=saleIDProdInBasket_<?=$currentBasketProduct?>&amp;k=n&amp;', 900, 700);">
				<span id="sp_saleIDProdInBasket_<?=$currentBasketProduct?>"></span>
			</div>
			<a href="javacript:void(0);" class="add_product_field"><?=GetMessage("fly.popup_TABCOND_SALEADDPRODUCT")?></a>
		</td>
	</tr>
	<?}?>
	<tr>
		<td colspan="2"><?CAdminMessage::ShowMessage(Array("TYPE"=>"MESSAGE", "MESSAGE"=>GetMessage("fly.popup_LOGIC_INFO")));?></td>
	</tr>
	<?$tabControl->Buttons();?>
	<input type="submit" class="adm-btn-save" name="save" value="<?=GetMessage("fly.popup_TAB_BUTTON_SAVE")?>" title="<?=GetMessage("fly.popup_TAB_BUTTON_SAVE")?>" />&nbsp;
	<input type="submit" class="button" name="apply" value="<?=GetMessage("fly.popup_TAB_BUTTON_APPLY")?>" title="<?=GetMessage("fly.popup_TAB_BUTTON_APPLY")?>" />&nbsp;
	<input  type="button" name="cancel" value="<?=GetMessage("fly.popup_TAB_BUTTON_CANCEL")?>" title="<?=GetMessage("fly.popup_TAB_BUTTON_CANCEL")?>" />
	<?$tabControl->End();?>
	<script>
		<?
		$agreements=$editableWindow->getAgreements(array('button_caption'=>'#BUTTON_TEXT#'));
		if(count($agreements)>0){

			?>var agreements=<?=\CUtil::phpToJSObject($agreements);?>;<?
		}
		?>
		var popupMessages={
			'uploadImg':'<?=GetMessage("fly.popup_IMG_BLOCK_UPLOADIMG")?>',
			'titlePopupImgBlock':'<?=GetMessage("fly.popup_POPUP_IMGBLOCKTITLE")?>',
			'titleSetcontent':'<?=GetMessage("fly.popup_SET_CONTENT")?>',
			'titleSetservice':'<?=GetMessage("fly.popup_POPUP_SET_SERVICE")?>',
			'titleSetpositionpopup':'<?=GetMessage("fly.popup_TABCOND_WINDOW_POSITION")?>',
			'errorContactTabSetting':'<?=GetMessage("fly.popup_ERROR_CONTACT_TAB_SETTING")?>',
			'hideBlock':'<?=GetMessage("fly.popup_HIDE_BLOCK")?>',
			'ShowBlock':'<?=GetMessage("fly.popup_SHOW_BLOCK")?>',
			'selectImg':'<?=GetMessage("fly.popup_JS_SELECT_IMG")?>',
			'addColorTheme':'<?=GetMessage("fly.popup_ADD_COLOR_THEME")?>',
			'addColorTemplate':'<?=GetMessage("fly.popup_ADD_TEMPLATE")?>',
			'confirmAddColorTheme':'<?=GetMessage("fly.popup_CONFIRM_ADD_COLOR_THEME")?>',
			'create':'<?=GetMessage("fly.popup_CREATE_BLOCK")?>',
			'enterName':'<?=GetMessage("fly.popup_CONFIRM_ADD_TEMPLATE_ENTERNAME")?>',
			'enterNameColor':'<?=GetMessage("fly.popup_CONFIRM_ADD_COLOR_ENTERNAME")?>',
			'nameIsRequired':'<?=GetMessage("fly.popup_NAMEISREQUIRED_BLOCK")?>',
			'colorThemeCreateSuccess':'<?=GetMessage("fly.popup_COLORTHEME_CREATESUCCESS")?>',
			'customTemplateCreateSuccess':'<?=GetMessage("fly.popup_CUSTOMTEMPLATE_CREATESUCCESS")?>',
			'apply':'<?=GetMessage("fly.popup_APPLY")?>',
			'edit':'<?=GetMessage("fly.popup_TABLE_EDIT")?>',
			'additional':'<?=GetMessage("fly.popup_TABLE_ADDITIONAL")?>'
		};
		//(window.BX||top.BX).message({'JSADM_FILES':'<?=GetMessage("fly.popup_JSADM_FILES")?>'});
	</script>
	</form></section>
<?}?>

<?
if(isset($_REQUEST['save']) && $_REQUEST['save']==GetMessage("fly.popup_TAB_BUTTON_SAVE") && !empty($_REQUEST['id'])){
	$CURRENT_PAGE = (CMain::IsHTTPS()) ? "https://" : "http://";
	$CURRENT_PAGE .= $_SERVER["HTTP_HOST"];
	$CURRENT_PAGE .= $APPLICATION->GetCurPage(true);
	header('Location: '.$CURRENT_PAGE);
	exit;
}
?>

<?//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
