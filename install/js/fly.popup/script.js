var managerPopup={
	type:'action', //selected type popup
	imgType:'IMG_1_SRC', //selected type image for change
	selectedImages:{}, //selected images {type1:id1, ....}
	updateImgBox:function(imgId){
		$.ajax({
			url: '/bitrix/admin/fly_popup.php?ajax=y&command=get_img',
			type: "POST",
			data:{img_type:this.imgType},
			dataType:'html',
			success: function(data){
				$('#popup_img_list').html(data);
				if(imgId && imgId>0){
					$('#popup_img_list').find('a').each(function(){
						if($(this).data('id') && $(this).data('id')==imgId){
							$('#popup_img_list').prepend($(this).closest('figure'));
							return;
						}
					});
				}
			},
			error:function(data){
				console.log(data);
			},
		});
	},
	hint:function(key,hint){
		var keys = key.split('#$%');
		var hints = hint.split('#$%');
		for(var i=0; i<keys.length;i++){
			if(keys[i]!='')
		new BX.CHint({
				parent: BX('hint_'+keys[i]),
				show_timeout: 10,
				hide_timeout: 200,
				dx: 2,
				preventHide: true,
				min_width: 400,
				hint: hints[i]
			});
		}
	},
	updatePreview:function(newSrc){
		var currentTmplt=templatesPopup[this.type];
		for(i=0; i<currentTmplt.length; i++){
			if(currentTmplt[i]['active']){
				managerPopup.detailEditContentBlock.find('input[type=text], input[type=hidden], textarea, select').each(function(){
					if(this.name.indexOf('IMG_')<0){
						var tmpVal=$(this).val()
						if($(this).attr('name')=='CONSENT_LIST' && typeof(agreements) != "undefined"){
							tmpVal=agreements[$(this).val()];
							tmpVal=tmpVal.replace('#BUTTON_TEXT#', currentTmplt[i]['props']['BUTTON_TEXT']);
						}
						currentTmplt[i]['props'][$(this).attr('name')]=tmpVal;
					}
				});
				var tmpTemplate=currentTmplt[i]['templateHTML'];
				if(newSrc){
					templatesPopup[this.type][i]['props'][this.imgType]=newSrc.src;
					tmpTemplate=tmpTemplate.replace('#'+this.imgType+'#', newSrc.src);
					templatesPopup[this.type][i]['props'][this.imgType+'_id']=newSrc.id;
					$('input[name='+this.imgType+']').val(newSrc.id);
				}
				
				var tmpStructureArr={'REQUIRED':{'N':'', 'Y':'required'}, 'SHOW':{'N':'notshow', 'Y':''}};
				for(var nextProp in currentTmplt[i].props){
					var currentStr=currentTmplt[i].props[nextProp];
					if(currentTmplt[i].props[nextProp]=='N' || currentTmplt[i].props[nextProp]=='Y'){
						if(nextProp.indexOf('_REQUIRED')>-1){
							currentStr=tmpStructureArr.REQUIRED[currentTmplt[i].props[nextProp]];
						}else if(nextProp.indexOf('_SHOW')>-1){
							currentStr=tmpStructureArr.SHOW[currentTmplt[i].props[nextProp]];
						}
					}
					var regExp = new RegExp('#'+nextProp+'#','g');
					tmpTemplate=tmpTemplate.replace(regExp, currentStr);
				}
				managerPopup.detailTemplateArea.html(tmpTemplate);
				managerPopup.detailTemplateArea.find('input, button, textarea').prop('disabled', true);
				break;
			}
		}
	if($('.block.roulette').is(":visible")){
		var container=$('.block.roulette tbody');
	var items = container.find('tr');
	var dataset=[];
	var count = items.length;
	var deg = 100/count;
	
	for(var i=0;i<items.length;i++)
		dataset.push({value:deg,color:$(items[i]).find('select.color_selector').val(),text:$(items[i]).find('input').val()});
	
	var maxValue=25;
	function paintRoulett(){
	dataset.reduce(function (prev, curr) {
	  return (function addPart(data, angle) {
	    if (data.value <= maxValue) {
	      return addSector(data, angle, false);
	    }
	    return addPart({
	      value: data.value - maxValue,
	      color: data.color
	    }, addSector({
	      value: maxValue,
	      color: data.color,
	    }, angle, true));
	  })(curr, prev);
	}, 0);
	return dataset.length;
	}
	paintRoulett();
	}
	
	},
	setPreviewBlock:function(activeI){
		var currentTmplt=templatesPopup[this.type][activeI];
		if(!managerPopup.detailTemplateArea){
			managerPopup.detailTemplateArea=$('#detail_template_area');
		}
		var areaHTML=currentTmplt['templateHTML'];
		if(currentTmplt.props){
			
			var tmpStructureArr={'REQUIRED':{'N':'', 'Y':'required'}, 'SHOW':{'N':'notshow', 'Y':''}};
			for(var nextProp in currentTmplt.props){
				var currentStr=currentTmplt.props[nextProp];
				if(currentStr=='N' || currentStr=='Y'){
					if(nextProp.indexOf('_REQUIRED')>-1){
						currentStr=tmpStructureArr.REQUIRED[currentStr];
					}else if(nextProp.indexOf('_SHOW')>-1){
						currentStr=tmpStructureArr.SHOW[currentStr];
					}
				}
				areaHTML=areaHTML.replace('#'+nextProp+'#', currentStr);
			}
		}

		managerPopup.detailTemplateArea.fadeOut(200, function(){
			$('#popup_template_css').remove();
			$('#popup_template_color_css').remove();
			$("head").append('<link rel="stylesheet" id="popup_template_css" href="'+currentTmplt['templateCss']+'/style.css" type="text/css" />');
			if(currentTmplt['color_style']){
				$("head").append('<link rel="stylesheet" id="popup_template_color_css" href="'+currentTmplt['templateCss']+'/themes/'+currentTmplt['color_style']+'.css" type="text/css" />');
			}
			//managerPopup.detailTemplateArea.html(areaHTML);
			managerPopup.detailTemplateArea[0].innerHTML=areaHTML;
			managerPopup.detailTemplateArea.find('input, button, textarea').prop('disabled', true);
			managerPopup.updatePreview();
			managerPopup.detailTemplateArea.fadeIn('slow');

		});

	},
	createTemplateForm:function(type){
		
		this.type=type;
		var currentTmplt=templatesPopup[type],
			currentListTemplate='<select name="template">',
			activeOptionColor='',
			contentBlock='';
		if(!managerPopup.templatesListArea){
			managerPopup.templatesListArea=$('#templates_list');
		}
		if(!managerPopup.detailTemplateHeader){
			managerPopup.detailTemplateHeader=$('.select_block h2');
		}
		var addTemplateName='';
		for(i=0; i<currentTmplt.length; i++){
			var tmpName=currentTmplt[i]['name'],
				activeOptionTemplate='';
			if(currentTmplt[i]['active']){
				addTemplateName=currentTmplt[i].name;
				activeOptionTemplate=' selected="selected"';
				activeOptionColor=currentTmplt[i]['color_style'];
				managerPopup.detailTemplateHeader.html(tmpName);

				if(templatesType[type]['color_style'] && currentTmplt[i]['color_styles']){
					var activeOptionColors=currentTmplt[i]['color_styles'];
				}

				if(!currentTmplt[i]['templateHTML']){
					var activeI=i;
					$.ajax({
						url: '/bitrix/admin/fly_popup.php?ajax=y&command=gettemplate',
						type: "POST",
						data:{template:type+'_'+currentTmplt[activeI]['template']},
						dataType:'html',
						success: function(data){
							currentTmplt[activeI]['templateHTML']=data;
							$.ajax({
								url: '/bitrix/admin/fly_popup.php?ajax=y&command=gettemplatepath',
								type: "POST",
								data:{template:type+'_'+currentTmplt[activeI]['template']},
								dataType:'html',
								success: function(data){
									currentTmplt[activeI]['templateCss']=data;
									managerPopup.setPreviewBlock(activeI);
								},
								error:function(data){
									console.log(data);
								},
							});

						},
						error:function(data){
							console.log(data);
						},
					});
				}else{
					this.setPreviewBlock(i);
				}

				//content block
				var tmpProp=currentTmplt[i]['props'],
					currentTmpHeader='',hints='',hints_text='';

				for(nextProp in tmpProp){
					if((nextProp=='USE_CONSENT_SHOW' || nextProp=='CONSENT_LIST') && typeof(agreements) == "undefined"){
						continue;
					}
					if(templatesType[type]['props'][nextProp] && templatesType[type]['props'][nextProp]['type']!==currentTmpHeader){
						currentTmpHeader=templatesType[type]['props'][nextProp]['type'];
						contentBlock+='<h4>'+popupMessages['titleSet'+currentTmpHeader]+'</h4>';
					}
					if(templatesType[type]['props'][nextProp]){
						if(nextProp.indexOf('IMG_')>-1){
							var tmpImgVal=(tmpProp[nextProp+'_id'])?tmpProp[nextProp+'_id']:'';
							contentBlock+='<input type="hidden" name="'+nextProp+'" value="'+tmpImgVal+'" /><label><span>'+templatesType[type]['props'][nextProp]['name']+'</span> <a href="javascript:void(0);" class="upload" data-idupload="'+nextProp+'">'+popupMessages.selectImg+'</a></label>';
						}else{
							var inputArea='';
							
							if(templatesType[type]['props'][nextProp]['tag'] && templatesType[type]['props'][nextProp]['tag']=='select'){
								inputArea='<select name="'+nextProp+'">';
								for(nextSel in templatesType[type]['props'][nextProp]['list']){
									var selectOption=(nextSel==tmpProp[nextProp])?' selected="selected"':'';
									inputArea+='<option value="'+nextSel+'"'+selectOption+'>'+templatesType[type]['props'][nextProp]['list'][nextSel]+'</option>';
								}
								inputArea+='</select>';
								if(nextProp=='RULE_ID'){
									inputArea+='<a href="/bitrix/admin/sale_discount_edit.php?ID='+tmpProp[nextProp]+'" target="_blank">'+rule_info+'</a>';
								}
								if(nextProp=='EMAIL_TEMPLATE'||nextProp=='MAIL_TEMPLATE'){
									inputArea+='<a href="/bitrix/admin/message_edit.php?lang=ru&ID='+tmpProp[nextProp]+'" target="_blank">'+rule_info+'</a>';
								}
							}else if(templatesType[type]['props'][nextProp]['tag'] && templatesType[type]['props'][nextProp]['tag']=='textarea'){
								inputArea='<textarea rows="5"  name="'+nextProp+'">'+tmpProp[nextProp]+'</textarea>';
							}else if(templatesType[type]['props'][nextProp]['tag'] && templatesType[type]['props'][nextProp]['tag']=='checkbox'){
								tmpChecked=(tmpProp[nextProp]=='Y')?' checked="checked"':'';
								tmpHiddenVal=(tmpProp[nextProp]=='Y')?'Y':'N';
								var showparam='';
								if(typeof(templatesType[type]['props'][nextProp]['block'])=="string"){
										if(templatesType[type]['props'][nextProp]['block']=='start'){
												showparam='start';
										}
								}
								if(showparam=='start'){
									var message='';
									if(tmpHiddenVal=='Y'){
										message=popupMessages.hideBlock;
									}else{
										message=popupMessages.ShowBlock;
									}
									if(nextProp=='EMAIL_SHOW') if(type=='roulette'||type=='discount') message='';
									inputArea='<a href="javascript:void(0)" class="toggle">'+ message +'</a><input type="hidden" name="'+nextProp+'" value="'+tmpHiddenVal+'" class="'+showparam+'"/>';
								}else{
									inputArea='<input type="checkbox" value="Y"'+tmpChecked+' /><input type="hidden" name="'+nextProp+'" value="'+tmpHiddenVal+'" class="'+showparam+'"/>';
								}
								
								showparam='';
							}else{
								inputArea='<input type="text" name="'+nextProp+'" value="'+tmpProp[nextProp]+'" />';
							}

							var hint = '';
							if(templatesType[type]['props'][nextProp]['hint']){
								hint = '<span class="flp-item-hint" id="hint_'+nextProp+'">?</span>';
								hints+=nextProp+'#$%';
								hints_text+=templatesType[type]['props'][nextProp]['hint']+'#$%';
							}
							var startblock='',endblock='';
							if(typeof(templatesType[type]['props'][nextProp]['block'])=="string"){
								if(templatesType[type]['props'][nextProp]['block']=='start'){
									startblock = '<div class="block '+tmpHiddenVal+'">';
								}
								if(templatesType[type]['props'][nextProp]['block']=='end'){
									endblock = '</div>';
								}
							}
							if(!(type=='discount'&&(nextProp=='EMAIL_ADD2BASE'||nextProp=='EMAIL_NOT_NEW'/*||nextProp==''||nextProp==''*/)))
							contentBlock+=startblock+'<label><span>'+templatesType[type]['props'][nextProp]['name']+hint+'</span> '+inputArea+'</label>'+endblock;
						}
					}
				}

				if(!managerPopup.detailEditContentBlock){
					managerPopup.detailEditContentBlock=$('#edit_content');
					managerPopup.detailEditContentBlock.on('change', 'input[type=checkbox]', function(){
						var newVal=(this.checked)?'Y':'N';
						if($(this).closest('div.block').length>0){
							if($(this).next().hasClass('start')){
								$(this).closest('div.block').removeClass('Y').removeClass('N');
								$(this).closest('div.block').addClass(newVal);
							}
						}
						$(this).next().val(newVal);
						var tmpName=$(this).next().attr('name');
						if(tmpName=='POSITION_LEFT' || tmpName=='POSITION_RIGHT' || tmpName=='POSITION_TOP' || tmpName=='POSITION_BOTTOM'){
							var uncheckO={
								'POSITION_LEFT':'POSITION_RIGHT',
								'POSITION_RIGHT':'POSITION_LEFT',
								'POSITION_TOP':'POSITION_BOTTOM',
								'POSITION_BOTTOM':'POSITION_TOP',
							};
							var linkInput=managerPopup.detailEditContentBlock.find('input[name='+uncheckO[tmpName]+']');
							linkInput.parent().find('input[type=checkbox]').prop('checked', false);
							linkInput.val('N');
						}

						managerPopup.updatePreview();
					});
					////////////////////////////// 30.05
					managerPopup.detailEditContentBlock.on('click', 'a.toggle', function(){
						var newVal = ($(this).next().val() == 'Y') ? 'N' : 'Y'
						$(this).text( newVal == 'Y' ? popupMessages.hideBlock : popupMessages.ShowBlock );
						if($(this).closest('div.block').length>0){
							if($(this).next().hasClass('start')){
								$(this).closest('div.block').removeClass('Y').removeClass('N');
								$(this).closest('div.block').addClass(newVal);
							}

						}
						$(this).next().val(newVal);
						managerPopup.updatePreview();
					});
					
					////////////////////////////// 30.05
				}
				managerPopup.detailEditContentBlock.html(contentBlock);
				managerPopup.detailEditContentBlock.find('input[type=text], textarea, select').on('keyup click change', function(){managerPopup.updatePreview();});
				managerPopup.hint(hints,hints_text);
			}
			currentListTemplate+='<option'+activeOptionTemplate+' value="'+currentTmplt[i]['template']+'">'+tmpName+'</option>';
		}
		currentListTemplate+='</select> <a href="javascript:void(0);" class="addCustomTemplate">'+popupMessages.addColorTemplate+'  "<span>'+addTemplateName+'</span>"</a>';

		//colors themes
		if(templatesType[type]['color_style']){
			var currentListColors='<select name="color_style">';
			var tmpColors=templatesType[type]['color_style'];
			if(activeOptionColors){
				tmpColors=activeOptionColors;
			}
			var addcustomColorName='',
				optIsGroup=false,
				optGroupEnd='';
			for(var nextColor in tmpColors){
				activeOptionTemplate='';
				optGroupStart='';
				if(nextColor.indexOf('custom_')>-1 && !optIsGroup){

					optGroupStart='<optgroup label="'+popupMessages.additional+'">';
					optGroupEnd='</optgroup>';
					optIsGroup=true;
				}
				if(nextColor==activeOptionColor){
					activeOptionTemplate=' selected="selected"';
					addcustomColorName=tmpColors[nextColor];
				}
				currentListColors+=optGroupStart+'<option'+activeOptionTemplate+' value="'+nextColor+'">'+tmpColors[nextColor]+'</option>';
			}
			currentListColors+=optGroupEnd+'</select> <a href="javascript:void(0);" class="addCustomColorTheme">'+popupMessages.addColorTheme+' "<span>'+addcustomColorName+'</span>"</a>';
			currentListColors=$(currentListColors);

			if(!managerPopup.detailEditViewBlock){
				managerPopup.detailEditViewBlock=$('#edit_view');
			}
			managerPopup.detailEditViewBlock.html(currentListColors);
			currentListColors.change(function(){
				var currentId=$(this).val();
				for(i=0; i<templatesPopup[type].length; i++){
					if(templatesPopup[type][i]['active']){
						templatesPopup[type][i]['color_style']=currentId;
						break;
					}
				}
				managerPopup.createTemplateForm(type);
			});
		}else{
			$('#edit_view').html('');
		}

		managerPopup.templatesListArea.html(currentListTemplate);
		managerPopup.templatesListArea.find('select').change(function(){
			var currentId=$(this).val();
			for(i=0; i<templatesPopup[type].length; i++){

				if(currentId==templatesPopup[type][i]['template']){
					templatesPopup[type][i]['active']=true;
				}else{
					templatesPopup[type][i]['active']=false;
				}
			}
			managerPopup.createTemplateForm(type);
		});
		/* managerPopup.updatePreview(); */
	}
}

function sliderWorks(){
	$('.slide_type .wrapper').on('click', 'a', function(){
		$(this).parent().find('a').removeClass('active');
		$(this).addClass('active');
		$('input[name=type]').val($(this).data('id'));
		managerPopup.createTemplateForm($(this).data('id'));
		selectContactTab();
	})
	$('.slide_type .wrapper a').each(function(){
		if($(this).hasClass('active')){
			managerPopup.createTemplateForm($(this).data('id'));
			return;
		}
	})
}
function resort(){
	var container=$('.block.roulette tbody');
	var items = container.find('tr');
	for(var i=0;i<items.length;i++){
		$(items[i]).find('td:nth-child(2)')[0].innerHTML=(i+1);
		$(items[i]).find('select.color_selector').attr('name','roulette_'+(i+1)+'_color');
		$(items[i]).find('select.rule_selector').attr('name','roulette_'+(i+1)+'_rule');
		$(items[i]).find('input').attr('name','roulette_'+(i+1)+'_text');
	}
}
function color_selector(selector){
	var color=selector.val();
	selector.css('background',color).css('color',color);
}
var addSector = function(data, startAngle, collapse) {
  var sectorDeg = 3.6 * data.value;
  var skewDeg = 90 + sectorDeg;
  var rotateDeg = startAngle;
  if (collapse) {
    skewDeg++;
  }
  var container = document.querySelector('#fly_roulette .container');
  var sector = document.createElement('div');
  sector.style.background=data.color;
  sector.className ='sector';
  sector.style.transform='rotate(' + rotateDeg + 'deg) skewY(' + skewDeg + 'deg)';
  container.appendChild(sector);
  var container_text=document.querySelector('#fly_roulette .text');
  var text = document.createElement('div');
  text.style.color='white';
  text.dataset.rule=data.rule;
  text.innerHTML=data.text;
  text.style.transform='rotate('+((rotateDeg-(90-sectorDeg/2)))+'deg) translate(0px,-50%)';
  container_text.appendChild(text);
  return startAngle + sectorDeg;
};
function remove_roulette_row(row){
	if($(row).closest('tbody').find('tr').length>4){
		count=$(row).closest('tbody').find('tr').length-1;
		$(row).closest('tr').remove();
		$('input[name="roulette_element_count"]').val(count);
	}else{
		alert(minimum_message);
	}
}
function row_rule_url(){
	var elements = $('div.roulette table tbody.drag_container tr.draggable');
	for(var i=0;i<elements.length;i++){
		var rule=$(elements[i]).find('select.rule_selector').val();
		$(elements[i]).find('select.rule_selector').closest('td').find('a').remove();
		if(rule>0){
			var url="/bitrix/admin/sale_discount_edit.php?ID="+(rule)+"&lang=ru";
			$(elements[i]).find('select.rule_selector').closest('td').append('<a href="'+url+'" target="_blank">'+rule_info+'</a>');
		}
	}
	
}
var drag;
$(document).ready(function(){
	$('#edit_content').on('change','select[name="RULE_ID"]',function(){
		$(this).val();
		$(this).closest('label').find('a').attr('href','/bitrix/admin/sale_discount_edit.php?ID='+$(this).val());
	});
	$('#edit_content').on('change','select[name="EMAIL_TEMPLATE"]',function(){
		$(this).val();
		$(this).closest('label').find('a').attr('href','/bitrix/admin/message_edit.php?lang=ru&ID='+$(this).val());
	});
	$('#edit_content').on('change','select[name="MAIL_TEMPLATE"]',function(){
		$(this).val();
		$(this).closest('label').find('a').attr('href','/bitrix/admin/message_edit.php?lang=ru&ID='+$(this).val());
	});
	$('.block.timer').on('change','input[type="checkbox"]',function(){
		var newVal=(this.checked)?'Y':'N';
		var cont = $(this.closest('label')).find('input[type="hidden"]');
		cont.val(newVal);
		var tmpName=cont.attr('name');
		if(tmpName=='timer_left' || tmpName=='timer_right' || tmpName=='timer_top' || tmpName=='timer_bottom'){
			var uncheckO={
				'timer_left':'timer_right',
				'timer_right':'timer_left',
				'timer_top':'timer_bottom',
				'timer_bottom':'timer_top',
			};
			var linkInput=cont.closest('.block').find('input[name='+uncheckO[tmpName]+']');
			linkInput.parent().find('input[type=checkbox]').prop('checked', false);
			linkInput.val('N');
		}
	});
	for(var i=0;i<$('.block.roulette tbody').find('select.color_selector').length;i++){
		color_selector($($('.block.roulette tbody').find('select.color_selector')[i]));
		row_rule_url();
	}
	$('.block.roulette').on('change','select.rule_selector',function(){
		row_rule_url();
	});
	$('.block.roulette').on('click','a.add-roulette-row',function(){
		
		count=$('.block.roulette tbody').find('tr').length;
		var append_row='<tr class="adm-list-table-row draggable">';
			append_row+='<td class="adm-list-table-cell">';
				append_row+='<div class="adm-list-table-popup drag_key" draggable="true"></div>';
			append_row+='</td>';
			append_row+='<td class="adm-list-table-cell">';
				append_row+=count+1;
			append_row+='</td>';
			append_row+='<td class="adm-list-table-cell">';
				append_row+='<input type="text" size=50 name="roulette_'+(count+1)+'_text">';
			append_row+='</td>';
			append_row+='<td class="adm-list-table-cell">';
				append_row+='<select class="color_selector" name="roulette_'+(count+1)+'_color">';
				for(var i in colors_for_roulette)
					append_row+='<option value="'+i+'" style="background:'+i+';color:'+i+'">'+colors_for_roulette[i]+'</option>';
				append_row+='</select>';
			append_row+='</td>';
			append_row+='<td class="adm-list-table-cell">';
				append_row+='<select name="roulette_'+(count+1)+'_rule">';
				for(var i in basket_rule_for_roulette){
					if(i=='nothing') append_row+='<optgroup label="'+basket_rule_basic+'">';
					if(i==tmpFirstBasketRule) append_row+='<optgroup label="'+basket_rule_rules+'">';
					append_row+='<option value="'+i+'">'+basket_rule_for_roulette[i]+'</option>';
					if(i==tmpLastBasketRule) append_row+='</optgroup>';
					if(i=='win') append_row+='</optgroup>';
				}
				append_row+='</select>';
			append_row+='</td>';
			append_row+='<td class="adm-list-table-cell">';
				append_row+='<a href="javascript:;" onclick="remove_roulette_row(this);"><img width="20px" height="25px" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeD0iMHB4IiB5PSIwcHgiIHZpZXdCb3g9IjAgMCAzNzguMzAzIDM3OC4zMDMiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM3OC4zMDMgMzc4LjMwMzsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSI1MTJweCIgaGVpZ2h0PSI1MTJweCI+Cjxwb2x5Z29uIHN0eWxlPSJmaWxsOiNGRjM1MDE7IiBwb2ludHM9IjM3OC4zMDMsMjguMjg1IDM1MC4wMTgsMCAxODkuMTUxLDE2MC44NjcgMjguMjg1LDAgMCwyOC4yODUgMTYwLjg2NywxODkuMTUxIDAsMzUwLjAxOCAgIDI4LjI4NSwzNzguMzAyIDE4OS4xNTEsMjE3LjQzNiAzNTAuMDE4LDM3OC4zMDIgMzc4LjMwMywzNTAuMDE4IDIxNy40MzYsMTg5LjE1MSAiLz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg=="></a>';
			append_row+='</td>';
		append_row+='</tr>';
		$('.block.roulette tbody').append(append_row);
		for(var i=0;i<$('.block.roulette tbody').find('select.color_selector').length;i++){
			color_selector($($('.block.roulette tbody').find('select.color_selector')[i]));
		}
		
		drag = BX.DragDrop.create({
			'dragItemControlClassName':'drag_key',
			'dragItemClassName':'draggable',
			'dragActiveClass':'sorting',
			'dropZoneList':document.querySelector('tbody.drag_container'),
			'dragEnd':resort,
			'sortable':{
				'rootElem':document.querySelector('tbody.drag_container'),
				'gagClass':'sort',
			}
		});
		$('input[name="roulette_element_count"]').val((count+1));
		row_rule_url();
	});
	$('.block.roulette tbody').on('change','select.color_selector',function(){
		color_selector($(this));
	});
	drag = BX.DragDrop.create({
		'dragItemControlClassName':'drag_key',
		'dragItemClassName':'draggable',
		'dragActiveClass':'sorting',
		'dropZoneList':document.querySelector('tbody.drag_container'),
		'dragEnd':resort,
		'sortable':{
			'rootElem':document.querySelector('tbody.drag_container'),
			'gagClass':'sort',
		}
	});
	
	
	var slideRoot=$('.slide_type');
	if(managerPopup.type){
		slideRoot.find('a').each(function(){
			if($(this).hasClass('active')){
				return;
			}
			slideRoot.append($(this));
		});
	}
	slideRoot.flpSlider();
	sliderWorks();
	selectContactTab();

	$('.popup_detail').on('click', 'a.upload', function(){
		managerPopup.imgType=$(this).data('idupload');
		var popup=new BX.CDialog({
			'title':popupMessages.titlePopupImgBlock,
			'content':BX('popup_manager_files'),
			'width':800,
			'height':500
		});

		BX.addCustomEvent(popup, 'onWindowRegister',function(){
			$('#popup_manager_files').css('display','block');
			managerPopup.updateImgBox();
		});

		BX.addCustomEvent(popup, 'onWindowClose',function(){
			$('#popup_manager_files').css('display','none');
			managerPopup.updateImgBox();
		});
		popup.Show();
	});

	//fix file uploader
	$('#popup_manager_files').find('a.file-selectdialog-switcher').trigger('click');

	$('#popup_img_list').on('click', 'img', function(){
		managerPopup.updatePreview({'src':this.src, 'id':$(this).data('id')});
	});

	$('input[name=cancel]').click(function(){
		location.reload();
	});

	$('.add_product_field').click(function(){
		var tmpFields=$(this).closest('td').find('.button_add');
		$('<div class="button_add"><input name="saleIDProdInBasket[]" id="saleIDProdInBasket_'+tmpFields.length+'" value="" size="5" type="text"> <input type="button" value="..." onclick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang=ru&amp;n=saleIDProdInBasket_'+tmpFields.length+'&amp;k=n&amp;\', 900, 700);"> <span id="sp_saleIDProdInBasket_'+tmpFields.length+'"></span></div>').insertBefore($(this));
	});

	$('.select_block').on('change', 'select[name=contact_iblock]', function(){
		var checked=($(this).val()!='')?true:false;
		$(this).closest('div').find('input[name=contact_save_to_iblock]').prop('checked', checked);
	});

	$('.select_block').on('click', '.addCustomTemplate', function(){
		var ajaxData={
			type:managerPopup.type,
			template:managerPopup.templatesListArea.find('select').val()
		};
		$(this).parent().append('<div class="setnewtemplate"> <span>'+popupMessages.enterName+':</span> <input type="text" placeholder="'+popupMessages.nameIsRequired+'" name="template_name" value="'+$(this).find('span').text()+'_2" /> <a href="javascript:void(0);" class="adm-btn adm-btn-save">'+popupMessages.create+'</a></div>');
		$(this).remove();
		$('.select_block').on('click', '.setnewtemplate a', function(){
			var inputName=$(this).parent().find('input'),
				replaceBlock=$(this).parent();
			if(inputName.prop('disabled')==false && inputName.val()!==''){
				ajaxData.name=inputName.val();
				inputName.prop('disabled', true);
				$.ajax({
					url: '/bitrix/admin/fly_popup.php?ajax=y&command=add_custom_template',
					type: "POST",
					data:ajaxData,
					dataType:'json',
					//dataType:'html',
					success: function(data){
						var successBlock='<span class="successBlock">'+popupMessages.customTemplateCreateSuccess+': <a href="/bitrix/admin/fileman_file_edit.php?path='+data.newPath+'" target="_blank">'+popupMessages.edit+'</a></span>';
						replaceBlock.replaceWith(successBlock);
						templatesPopup[ajaxData.type].push(data.popup);
						managerPopup.templatesListArea.find('select[name=template]').append('<option value="'+data.code+'">'+ajaxData.name+'['+data.id+']'+'</option>');
						managerPopup.templatesListArea.find('select[name=template] option').prop('selected', false);
						managerPopup.templatesListArea.find('select[name=template] option').last().prop('selected', true);
						//managerPopup.templatesListArea.find('select[name=template]').trigger('change');
					},
					error:function(data){
						console.log(data);
					}
				});
			}
		});
	});

	$('.select_block').on('click', '.addCustomColorTheme', function(){
		var ajaxData={
			type:managerPopup.type,
			template:managerPopup.templatesListArea.find('select').val(),
			color_style:managerPopup.detailEditViewBlock.find('select[name=color_style]').val()
		};
		$(this).parent().append('<div class="setnewcolor"> <span>'+popupMessages.enterNameColor+':</span> <input type="text" placeholder="'+popupMessages.nameIsRequired+'" name="color_style_name" value="'+$(this).find('span').text()+'_2" /> <a href="javascript:void(0);" class="adm-btn adm-btn-save">'+popupMessages.create+'</a></div>');
		$(this).remove();
		$('.select_block').on('click', '.setnewcolor a', function(){
			var inputName=$(this).parent().find('input'),
				replaceBlock=$(this).parent();
			if(inputName.prop('disabled')==false && inputName.val()!==''){
				ajaxData.name=inputName.val();
				inputName.prop('disabled', true);
				$.ajax({
					url: '/bitrix/admin/fly_popup.php?ajax=y&command=add_custom_colortheme',
					type: "POST",
					data:ajaxData,
					dataType:'json',
					success: function(data){
						var successBlock='<span class="successBlock">'+popupMessages.colorThemeCreateSuccess+': <a href="/bitrix/admin/fileman_file_edit.php?path='+data.newPath+'" target="_blank">'+popupMessages.edit+'</a></span>';
						replaceBlock.replaceWith(successBlock);
						for(var i=0; i<templatesPopup[ajaxData.type].length; i++){
							if(templatesPopup[ajaxData.type][i]['template']==ajaxData.template){
								templatesPopup[ajaxData.type][i]['color_styles'][data.code]=ajaxData.name+'['+data.id+']';
								break;
							}
						}
						managerPopup.detailEditViewBlock.find('select[name=color_style]').append('<option value="'+data.code+'">'+ajaxData.name+'['+data.id+']'+'</option>');
						managerPopup.detailEditViewBlock.find('select[name=color_style] option').prop('selected', false);
						managerPopup.detailEditViewBlock.find('select[name=color_style] option').last().prop('selected', true);
					},
					error:function(data){
						console.log(data);
					}
				});
			}
		});
	});

});

function showHideImgs(direct){
	if(direct=='show_all'){
		$('#popup_img_list').find('figure').css('display', 'inline-block');
		$('#popup_img_list').find('.hide_all').css('display', 'block');
		$('#popup_img_list').find('.show_all').css('display', 'none');
	}else{
		$('#popup_img_list').find('figure').css('display', 'none');
		$('#popup_img_list').find('figure').slice(0,4).css('display', 'inline-block');
		$('#popup_img_list').find('.show_all').css('display', 'block');
		$('#popup_img_list').find('.hide_all').css('display', 'none');
	}
}

function delPopupImg(o){
	var _this=$(o);
	$.ajax({
		url: '/bitrix/admin/fly_popup.php?ajax=y&command=del_img',
		type: "POST",
		data:{id:_this.data('id')},
		dataType:'json',
		success: function(data){
			_this.closest('figure').remove();
		},
		error:function(data){
			console.log(data);
		}
	});
}

BX.addCustomEvent('uploadFinish', function(result){
	var uploadImgId=0;
	if(result.element_id){
		uploadImgId=result.element_id;
	}
	managerPopup.updateImgBox(uploadImgId);
});
BX.addCustomEvent('stopUpload', function(result){
	setTimeout(function(){managerPopup.updateImgBox();}, 100);
});

jQuery.fn.flpSlider = function(options){
	var settings = $.extend({
		'orientation' : 'horizontal',//vertical
		'slides':3
	}, options);
	if(this.find('.wrapper').length>0){
		this.html(this.find('.wrapper').html());
	}
	if(settings.orientation=='horizontal'){
		var wrapper=$('<div class="wrapper"></div>');
		var slides=this.find('.slide');
		this.width(this.width());
		slidesWidth=Math.round(this.width()/settings.slides)-25;
		slides.width(slidesWidth);
		wrapper.height(slides.outerHeight()+2).css({'text-align':'center', 'position':'relative'}).append(slides);
		this.append(wrapper);
		this.append('<a href="javascript:void(0);" class="arrow horizontal left"></a><a href="javascript:void(0);" class="arrow horizontal right"></a>');
		settings.wrapper=wrapper;
	}
	this.find('a.arrow').click(function(){
		var block_width = settings.wrapper.find(".slide").outerWidth();
		if($(this).hasClass('left')){
			settings.wrapper.find(".slide").eq(-1).clone().prependTo(settings.wrapper);
			settings.wrapper.css({"left":"-"+block_width+"px"});
			settings.wrapper.find(".slide").eq(-1).remove();
			settings.wrapper.animate({left: "0px"}, 200);
		}else{
			settings.wrapper.animate({left: "-"+ block_width +"px"}, 200, function(){
				settings.wrapper.find(".slide").eq(0).clone().appendTo(settings.wrapper);
				settings.wrapper.find(".slide").eq(0).remove();
				settings.wrapper.css({"left":"0px"});
			});
		}
	})
};

function selectContactTab(){
	if(managerPopup && managerPopup.type=='contact'){
		$('.block.contacts').css('display','block');
	}else{
		//$('.block.contacts').find('.info').html('<h2 class="error">'+popupMessages.errorContactTabSetting+'</h2>');
		$('.block.contacts').css('display','none');
	}
	if(managerPopup && (managerPopup.type=='banner'||managerPopup.type=='video'||managerPopup.type=='action'||managerPopup.type=='contact'||managerPopup.type=='html'||managerPopup.type=='coupon'||managerPopup.type=='roulette'||managerPopup.type=='discount')){
		$('.block.timer').css('display','block');
	}else{
		$('.block.timer').css('display','none');
	}
	if(managerPopup && managerPopup.type=='roulette'){
		$('.block.roulette').css('display','block');
	}else{
		$('.block.roulette').css('display','none');
	}
}

function selectPreviewTab(){
	if($('.slide_type').width()==0){
		$('.slide_type').css('width', '');
		$('.slide_type').flpSlider();
		sliderWorks();
	}
}
