BX.ready(function(){
	BX.ajax({
		url: '/bitrix/components/fly/popup.pro/ajax.php',
		data: {
			'type':'getPopups',
			'pageUrl':encodeURIComponent(location.href),
			'site':flyPopups.site,
			'dateUser':Math.round(+new Date()/1000),
			
		},
		method: 'POST',
		dataType: 'json',
		timeout:300,
		async: true,
		/* scriptsRunFirst:true, */
		onsuccess: function(data){
			if(Object.keys(data).length>0){
				flyPopups.popups=data;
				flyPopups.tmpParams={};
				for(key in data){
					flyPopups.tmpParams[key]={};
				}
				startConditions();
			}
		},
		onfailure: function(data){
			console.log(data);
		}
	});
})

function startConditions(){
	if(flyPopups.basket){
		BX.addCustomEvent('onAjaxSuccess', function(a,b){
			if(b && b.url && b.url.indexOf('basket')>-1){
				BX.ajax({
					url: '/bitrix/components/fly/popup.pro/ajax.php',
					data: {
						'type':'getBasket'
					},
					method: 'POST',
					dataType: 'json',
					timeout:300,
					async: true,
					/* scriptsRunFirst:true, */
					onsuccess: function(data){
						flyPopups.basket=data;
						basketCondition();
					},
					onfailure: function(data){
						console.log(data);
					}
				});
			}
		});
		basketCondition();
	}
	for(key in flyPopups.popups){
		var nextPopup=flyPopups.popups[key];
		var tmpAnchor=(nextPopup.anchorVisible)?document.querySelectorAll('a[name="'+nextPopup.anchorVisible+'"]'):0;
		var tmpClassLink=(nextPopup.onClickClassLink)?document.querySelectorAll('.'+nextPopup.onClickClassLink):0;
		if(nextPopup.repeatTime_type){
			if(!nextPopup.repeatTime){
				delete nextPopup.repeatTime_type;
			}
		}
		//console.log(nextPopup);
		if(BX.getCookie('flp_popups_'+key) && nextPopup.repeatTime){
			delete flyPopups.popups[key];
		}else if(tmpClassLink==0 && nextPopup.onClickClassLink){
			delete flyPopups.popups[key];
		}else if(tmpAnchor==0 && nextPopup.anchorVisible){
			delete flyPopups.popups[key];
		}else{
			if(nextPopup.afterTimeSecond){
				if(nextPopup.afterTimeSecond>0){
					setTimeout(checkConditions, nextPopup.afterTimeSecond*1000, key, 'afterTimeSecond');
				}else{
					checkConditions(key, 'afterTimeSecond');
				}
			}else{
				//if afterTimeSecond==''
				checkConditions(key, 'afterTimeSecond');
			}
			
			if(!BX.getCookie('flp_popups_'+key) && nextPopup.repeatTime){
				checkConditions(key, 'repeatTime');
			}
			if(nextPopup.timeInterval){
				var tmpInterval=timeIntervalStatus(nextPopup.timeInterval);
				if(tmpInterval.inInterval){
					checkConditions(key, 'timeInterval');
				}else{
					setTimeout(checkConditions, tmpInterval.beforeShow*1000, key, 'timeInterval');
				}
			}
			if(tmpAnchor.length>0){
				for(var i=0; i<tmpAnchor.length; i++){
					tmpAnchor[i].popupId=key;
					showAnchor.call(tmpAnchor[i]);
					BX.bind(BX(document), 'scroll', BX.delegate(showAnchor, tmpAnchor[i]));
				}
			}
			if(nextPopup.alreadygoing){
				BX.bind(BX(document), 'mousemove', BX.delegate(showAlreadyGoing, key));
			}
			if(nextPopup.onClickClassLink){
				for(var i=0; i<tmpClassLink.length; i++){
					tmpClassLink[i].popupId=key;
					BX.bind(BX(tmpClassLink[i]), 'click', function(e){
						e.preventDefault();
						openByClick(this.popupId);
						return false;
					})
				}
			}
		}
	}
	if(Object.keys(flyPopups.popups).length>0){
		uploadPopups();
	}
	
}

function uploadPopups(){
	
	var tmpKey=[]
	for(key in flyPopups.popups){
		tmpKey.push(key);
	}
	if(tmpKey.length>0){
		BX.ajax({
			url: '/bitrix/components/fly/popup.pro/ajax.php',
			data: {
				'type':'getTemplatePath',
				'popupIds':tmpKey
			},
			method: 'POST',
			dataType: 'json',
			timeout:300,
			async: true,
			/* scriptsRunFirst:true, */
			onsuccess: function(data){
				flyPopups.popupdata=data;
				//uploadPopupsHTML();
				setTimeout(uploadPopupsHTML, 2);
			},
			onfailure: function(data){
				console.log(data);
			}
		});
	}
}

function getPrepolader(){
	if(!flyPopups.preloadBlock){
		var preloadBlock=document.createElement('div');
			preloadBlock.style.position='absolute';
			preloadBlock.style.left='-1000000px';
			preloadBlock.style.top='-1000000px';
			preloadBlock.className='flyPreloadBlock';
		document.body.appendChild(preloadBlock);
		flyPopups.preloadBlock=preloadBlock;
	}
}

function uploadPopupsHTML(){
	for(key in flyPopups.popups){
		(function(){
			var currentKey=key;
			BX.ajax({
				url: '/bitrix/components/fly/popup.pro/ajax.php',
				data: {
					'type':'getHTML',
					'popupId':key
				},
				method: 'POST',
				dataType: 'html',
				timeout:300,
				scriptsRunFirst:true,
				async: true,
				onsuccess: function(data){
					flyPopups.popupdata[currentKey].DATA=data;
					getPrepolader();
					var tmpNode=document.createElement('div');
					tmpNode.innerHTML=data;
					var tmpImgs=tmpNode.querySelectorAll('img');
					if(tmpImgs.length>0){
						for(var i=0; i<tmpImgs.length; i++){
							flyPopups.preloadBlock.appendChild(tmpImgs[i]);
						}
					}
				},
				onfailure: function(data){
					console.log(data);
				}
			});
		})();
	}
}

function showPopup(popupId){
	popupId=popupId.toString();
	if(flyPopups.popupdata && flyPopups.popupdata[popupId] && flyPopups.popupdata[popupId].DATA){
		BX.remove(BX('fly_popup_style'));
		BX.remove(BX('fly_popup_color'));
		var head = document.getElementsByTagName('head')[0];
		var s_tepmlate = BX.create('link', {'attrs':{
			'id':'fly_popup_style',
			'type':'text/css',
			'rel':'stylesheet',
			'href':flyPopups.popupdata[popupId].STYLE
		}});
		head.appendChild(s_tepmlate);
		
		if(flyPopups.popupdata[popupId].THEME){
			var s_color = BX.create('link', {'attrs':{
				'id':'fly_popup_color',
				'type':'text/css',
				'rel':'stylesheet',
				'href':flyPopups.popupdata[popupId].THEME
			}});
			head.appendChild(s_color);
		}
		var popup = new BX.PopupWindow("popup-message", null, {
			content: "---",
			autoHide : true,
			zIndex: 0,
			offsetTop : 1,
			offsetLeft : 0,
			className: 'flPopup',
			lightShadow : true,
			closeIcon : true,
			closeByEsc : true,
			onPopupClose: function(){
				//console.log('close');
			},
			overlay:{
				backgroundColor:'#000'
			},
			events:{
				onAfterPopupShow: function(){
					positionBanner();
					if(flyPopups.popupdata[popupId].VIDEO_AUTOPLAY){
						flyPopupTargetAction();
					}
				}
			}
		});
		popup.setContent(flyPopups.popupdata[popupId].DATA);
		
		if(flyPopups.currentPopup){
			flyPopups.currentPopup.close();
		}
		flyPopups.currentPopup=popup;
		flyPopups.currentPopupId=popupId;
		flyPopups.currentPopupStartTime=Math.floor(Date.now()/1000);
		popup.show();

		var targetsAction=popup.contentContainer.querySelectorAll(".flTargetAction");
		if(targetsAction.length>0){
			for(var i=0; i<targetsAction.length; i++){
				targetsAction[i].onclick=flyPopupTargetAction;
			}
		}
		
		//statistic open
		BX.ajax({
			url: '/bitrix/components/fly/popup.pro/ajax.php',
			data: {
				'type':'statisticShow',
				'popupId':popupId
			},
			method: 'POST',
			dataType: 'html',
			timeout:300,
			async: true,
			/* scriptsRunFirst:true, */
			onsuccess: function(data){
				//console.log(data);
			},
			onfailure: function(data){
				console.log(data);
			}
		});
		
		if(popup.contentContainer.querySelector('.fly_popup_pro_timer .timer')!=null){
			function startTimer(){
				var timer=popup.contentContainer.querySelectorAll('.fly_popup_pro_timer .timer .clock>span:not(.sep)');
				var d,h,m,s;
				d=timer.item(0).innerHTML;
				h=timer.item(1).innerHTML;
				m=timer.item(2).innerHTML;
				s=timer.item(3).innerHTML;
				if(s==0){
					if(m==0){
						if(h==0){
							if(d==0){
								popup.close();
							}
							d--;
							h=24;
							if(d<10) d='0'+d;
						}
						h--;
						m=60;
						if(h<10) h='0'+h;
					}
					m--;
					if(m<10) m='0'+m;
					s=59;
				}
				else s--;
				if(s<10) s="0"+s;
				popup.contentContainer.querySelector('.fly_popup_pro_timer .timer .clock').innerHTML="<span>"+d+"</span><span class='sep'>:</span><span>"+h+"</span><span class='sep'>:</span><span>"+m+"</span><span class='sep'>:</span><span>"+s+"</span>";
				setTimeout(startTimer,1000);
			}
			startTimer();
		}
		if(popup.contentContainer.querySelector('section.container')!=null){
			var count = paintRoulett();
			
			BX.bind(BX(popup.contentContainer.querySelector('button.roll_roulette')),'click',function(){roll_roulette_func(count);});
		}
	}else{
		setTimeout(showPopup, 50, popupId);
	}
}

function checkConditions(keyDelete, propDelete){
	for(key in flyPopups.popups){
		if(keyDelete==key){
			if(propDelete=="repeatTime"){
				flyPopups.tmpParams[keyDelete].repeatTime=flyPopups.popups[keyDelete].repeatTime;
				flyPopups.tmpParams[keyDelete].repeatTime_type=flyPopups.popups[keyDelete].repeatTime_type;
				delete flyPopups.popups[key]['repeatTime_type'];
			}
			delete flyPopups.popups[key][propDelete];
			break;
		}
	}
	if(Object.keys(flyPopups.popups[keyDelete]).length==0){
		if(flyPopups.tmpParams[keyDelete].repeatTime && flyPopups.tmpParams[keyDelete].repeatTime>0){
			var type={
				hour:3600,
				day:86400,
				week:604800,
				month:2419200,
				year:31536000,
				};
			var tmp_time=type[flyPopups.tmpParams[keyDelete].repeatTime_type]*flyPopups.tmpParams[keyDelete].repeatTime;
			BX.setCookie('flp_popups_'+keyDelete, 'Y', {expires: tmp_time, path:'/'});
		}
		showPopup(keyDelete);
	}
}

function basketCondition(){
	for(key in flyPopups.popups){
		var nextPopup=flyPopups.popups[key];
		if(nextPopup.saleCountProduct && nextPopup.saleCountProduct<=flyPopups.basket.products.length){
			checkConditions(key, 'saleCountProduct');
		}
		if(nextPopup.saleSummBasket && nextPopup.saleSummBasket<=flyPopups.basket.summ){
			checkConditions(key, 'saleSummBasket');
		}
		if(nextPopup.saleIDProdInBasket){
			var tmpProdId=true;
			for(var i=0; i<nextPopup.saleIDProdInBasket.length; i++){
				if(!BX.util.in_array(nextPopup.saleIDProdInBasket[i], flyPopups.basket.products)){
					tmpProdId=false;
					break;
				}
			}
			if(tmpProdId){
				checkConditions(key, 'saleIDProdInBasket')
			}
		}
	}
}

function openByClick(popupId){
	//console.log(popupId);
	showPopup(popupId);
}

function showAlreadyGoing(e){
	if(flyPopups.popups && flyPopups.popups[this].alreadygoing){
		if(e.clientY<50){
			if(Object.keys(flyPopups.popups[this]).length==1){
				checkConditions(this, 'alreadygoing');
			}
		}
	}
}

function showAnchor(){
	if(!this.popupId){
		return;
	}
	var portHeight=window.innerHeight,
	targetRect=this.getBoundingClientRect();
	if(targetRect.bottom>0 && targetRect.top<portHeight && this.popupId){
		showPopup(this.popupId);
		delete this.popupId;
	}
}

//This function defines whether the client gets to the set range and if isn't present - that how many remained to him
function timeIntervalStatus(interval){
	interval=interval.split('#');
	
	var tmpD=new Date,
		currentDate=tmpD.getHours()*3600+tmpD.getMinutes()*60,
		retArr={inInterval:false};
	
	for(var i=0; i<2; i++){
		if(interval[i]!=''){
			var tmpTime=interval[i].split(':');
			interval[i]=tmpTime[0]*3600+tmpTime[1]*60;
		}else{
			interval[i]=0;
		}
	}
	if(interval[1]==0){
		interval[1]=86400;
	}
	if(interval[1]>interval[0]){
		if(currentDate>=interval[0] && currentDate<=interval[1]){
			retArr.inInterval=true;
		}else{
			retArr.beforeShow=interval[0]-currentDate;
			if(interval[1]<currentDate){
				retArr.beforeShow+=86400;
			}
		}
	}else{
		if((currentDate>=interval[0] && currentDate>=interval[1]) || (currentDate<=interval[0] && currentDate<=interval[1])){
			retArr.inInterval=true;
		}else{
			retArr.beforeShow=(interval[0]-currentDate)*1000;
		}
	}
	return retArr;
}

function flyPopupClose(){
	//statistic close
	if(flyPopups && flyPopups.currentPopupId && flyPopups.currentPopupStartTime){
		BX.ajax({
			url: '/bitrix/components/fly/popup.pro/ajax.php',
			data: {
				'type':'statisticTime',
				'popupId':flyPopups.currentPopupId.toString(),
				'popupTime':(Math.floor(Date.now()/1000) - flyPopups.currentPopupStartTime)
			},
			method: 'POST',
			dataType: 'html',
			timeout:300,
			/* scriptsRunFirst:true, */
			async: true,
			onsuccess: function(data){
				delete flyPopups.currentPopupId;
				delete flyPopups.currentPopupStartTime;
			},
			onfailure: function(data){
				console.log(data);
			}
		});
	}
}

function flyPopupTargetAction(){
	//statistic close
	if(flyPopups && flyPopups.currentPopupId){
		BX.ajax({
			url: '/bitrix/components/fly/popup.pro/ajax.php',
			data: {
				'type':'statisticAction',
				'popupId':flyPopups.currentPopupId.toString()
			},
			method: 'POST',
			dataType: 'html',
			timeout:300,
			async: true,
			/* scriptsRunFirst:true, */
			onsuccess: function(data){
				//console.log(data);
			},
			onfailure: function(data){
				console.log(data);
			}
		});
	}
}

BX.addCustomEvent('onPopupClose', function(){
	flyPopupClose();
});

BX.addCustomEvent('onPopupShow', function(){
 	if(this.params.className=='flPopup'){
		this.contentContainer.parentNode.style.opacity=0;
		
	}
});

function getPosition(posObj){
	var retData=false;
	var positions={};
	if(posObj.POSITION_LEFT && posObj.POSITION_LEFT=='Y'){
		retData=true;
		positions.POSITION_LEFT=true;
	}
	if(posObj.POSITION_RIGHT && posObj.POSITION_RIGHT=='Y'){
		retData=true;
		positions.POSITION_RIGHT=true;
	}
	if(posObj.POSITION_TOP && posObj.POSITION_TOP=='Y'){
		retData=true;
		positions.POSITION_TOP=true;
	}
	if(posObj.POSITION_BOTTOM && posObj.POSITION_BOTTOM=='Y'){
		retData=true;
		positions.POSITION_BOTTOM=true;
	}
	if(retData){
		return positions;
	}
	return false;
}
function setPosition(o, pos){
	setTimeout(function(){
		flyPopups.currentPopup.adjustPosition();
		if(pos.POSITION_BOTTOM){
			var tmpStyle=getComputedStyle(o);
			
			o.style.bottom='';
			o.style.top=(document.documentElement.scrollTop+document.documentElement.clientHeight-parseInt(tmpStyle.height))+'px';
		}else if(pos.POSITION_TOP){
			o.style.bottom='';
			o.style.top=document.documentElement.scrollTop+'px';
		}
		
		if(pos.POSITION_LEFT){
			o.style.right='';
			o.style.left='0';
		}else if(pos.POSITION_RIGHT){
			o.style.left='';
			o.style.right='0';
		}
		//o.style.position='fixed';
		flyPopups.currentPopup.contentContainer.parentNode.style.opacity=1;
		
	}, 90);
}

function positionBanner(){
	var _this=flyPopups.currentPopup;
	if(_this.params.className=='flPopup'){
		var currentPos=getPosition(flyPopups.popupdata[flyPopups.currentPopupId]);
		var img = flyPopups.currentPopup.contentContainer.querySelectorAll('img');
		if(img.length==0){
			if(currentPos===false){
				setTimeout(function(){
					flyPopups.currentPopup.adjustPosition();
					_this.contentContainer.parentNode.style.opacity=1;
				}, 90);
			}else{
				setPosition(_this.contentContainer.parentNode, currentPos);
			}
		}else{
			if(currentPos===false){
				var container=_this.contentContainer
				var innerImgs=img.length;
				for(var i = 0; i<img.length;i++){
					img[i].addEventListener('load',function(){
						innerImgs--;
						flyPopups.currentPopup.adjustPosition();
						if(innerImgs==0){
							setTimeout(function(){
								flyPopups.currentPopup.adjustPosition();
								container.parentNode.style.opacity=1;
							}, 50);
						}
					});
				}
				flyPopups.currentPopup.adjustPosition();
			}else{
				setPosition(_this.contentContainer.parentNode, currentPos);
			}
			
		}
	}
}

window.onbeforeunload = function(){
	flyPopupClose();
};
