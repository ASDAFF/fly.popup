<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
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
<div id="skyweb24_roulette">
    <?$deg=100/$arResult['ELEMENTS_COUNT'];?>
    <h2><?=$arResult['TITLE']?></h2>
    <h3><?=$arResult['SUBTITLE']?></h3>
     <div class="contain_roulette">
      <div class="rotate_shadow">
      <div class="rotate_block">
        <section class="container">
        </section>
        <div class="text">
        </div>
      </div>
      </div>
      <div class="arrow">
        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
<path style="fill:#FD003A;" d="M256,0C156.698,0,76,80.7,76,180c0,33.6,9.302,66.301,27.001,94.501l140.797,230.414
	c2.402,3.9,6.002,6.301,10.203,6.901c5.698,0.899,12.001-1.5,15.3-7.2l141.2-232.516C427.299,244.501,436,212.401,436,180
	C436,80.7,355.302,0,256,0z M256,270c-50.398,0-90-40.8-90-90c0-49.501,40.499-90,90-90s90,40.499,90,90
	C346,228.9,306.999,270,256,270z"/>
<path style="fill:#E50027;" d="M256,0v90c49.501,0,90,40.499,90,90c0,48.9-39.001,90-90,90v241.991
	c5.119,0.119,10.383-2.335,13.3-7.375L410.5,272.1c16.799-27.599,25.5-59.699,25.5-92.1C436,80.7,355.302,0,256,0z"/>
<g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
</svg>
      </div>
    </div>
    <script>
        function validateEmail(elementValue){      
        	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        	return emailPattern.test(elementValue); 
        }
        function getCoupon(id,text){
          var url = "<?=$templateFolder?>/ajax.php?id="+id+"&avaliable=<?=$arResult['TIMING']?>";
          var email = BX('skyweb24_roulette').querySelector('label.input');
          var getContinue=true;
            url+="&email="+email.querySelector('input').value+"&idPopup=<?=$arParams['ID_POPUP']?>&addtotable=<?=$arResult['EMAIL_ADD2BASE']?>&unique=<?=$arResult['EMAIL_NOT_NEW']?>&resultText="+text;
            BX.ajax({
              url:url,
              method:'POST',
              onsuccess: function(data){
                if(data=='not_unique'){
                  BX('skyweb24_roulette').querySelector('p.result_roll_not_unique').style.display='inline-block';
                }else{
                  BX('skyweb24_roulette').querySelector('p.result_roll_not_unique').style.display='none';
                  BX('skyweb24_roulette').querySelector('label.input').remove()//.style.display='none';
                  BX('skyweb24_roulette').querySelector('button.roll_roulette').remove()//.style.display='none';
                  skyweb24PopupTargetAction();
                }
              },
              onfailure: function(data){
                console.log(data);
              }
            });
        }
        var dataset = [
            <?foreach($arResult['ELEMENTS'] as $element){?>
                {
                    value:<?=$deg?>,
                    color:'<?=$element['color']?>',
                    text:'<?=$element['text']?>',
                    rule:'<?=$element['rule']?>'
                },
            <?}?>
];

var maxValue = 25;

function roll_roulette_func(count){
	var email = BX('skyweb24_roulette').querySelector('label.email');
	getContinue=validateEmail(email.querySelector('input').value);
  if(email.querySelector('input').value==''){
    email.className+=" blink";
    setTimeout(function(){
      email.className=email.className.replace('blink','');
    },1000);
  }
  //getContinue=true;
  
	if(getContinue){
		var percent = 100/count;
		var deg = 360/100;
		var center = 90-(percent*3.6/2);
		var rand = Math.floor(Math.random()*(count));
		var res=-(deg*percent*(rand)-center);
		var sector = rand+1; 
		if(sector>count) sector=1; //выигрыш
		document.querySelector('#skyweb24_roulette div.rotate_block').style.transform='rotate('+(res-3600)+'deg)';
    setTimeout(function(){res_roll(sector)},7000);
	}else{
		BX('skyweb24_roulette').querySelector('p.result_roll_not_unique').style.display='none';
        email.className+=" error";
        email.focus();
	}
  
}
function res_roll(sector){
	var text=document.querySelector('#skyweb24_roulette div.rotate_block>.text>div:nth-child('+sector+')').innerHTML;
	var rule=document.querySelector('#skyweb24_roulette div.rotate_block>.text>div:nth-child('+sector+')').dataset.rule;
  var not = document.querySelectorAll('#skyweb24_roulette div.rotate_block>.container>div:not(:nth-child('+sector+'))');
  for(var i=0; i<not.length;i++){
    not[i].className+=' negative';
  }
	
	if(rule!='nothing'){
    document.querySelector('#skyweb24_roulette p.result_roll').style.display='';
    //document.querySelector('#skyweb24_roulette p.result_roll>span').innerHTML=text;
		getCoupon(rule,text);
	}else{
    BX('skyweb24_roulette').querySelector('label.input').remove();
    BX('skyweb24_roulette').querySelector('button.roll_roulette').remove();
    BX('skyweb24_roulette').querySelector('p.result_roll').remove();
    BX('skyweb24_roulette').querySelector('p.result_roll_nothing').style.display='';
  }
	<?=$arResult['BUTTON_METRIC']?>
}
var addSector = function(data, startAngle, collapse) {
  var sectorDeg = 3.6 * data.value;
  var skewDeg = 90 + sectorDeg;
  var rotateDeg = startAngle;
  if (collapse) {
    skewDeg++;
  }
  var container = document.querySelector('#skyweb24_roulette .container');
  var sector = document.createElement('div');
  sector.style.background=data.color;
  sector.className ='sector';
  sector.style.transform='rotate(' + rotateDeg + 'deg) skewY(' + skewDeg + 'deg)';
  container.appendChild(sector);
  var container_text=document.querySelector('#skyweb24_roulette .text');
  var text = document.createElement('div');
  text.style.color='white';
  text.dataset.rule=data.rule;
  text.innerHTML=data.text;
  text.style.transform='rotate('+((rotateDeg-(90-sectorDeg/2)))+'deg) translate(0px,-50%)';
  container_text.appendChild(text);
  return startAngle + sectorDeg;
};


function paintRoulett(){dataset.reduce(function (prev, curr) {
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
    </script>
    <p class="result_roll" style="display:none;"><?=$arResult['RESULT_TEXT']?></p>    
    <p class="result_roll_nothing" style="display:none;"><?=$arResult['NOTHING_TEXT']?></p>    
      <label class="email input">
        <input placeholder="<?=$arResult['EMAIL_PLACEHOLDER']?>" type="email" name="email">
        <p class="result_roll_fail" style="display:none;"><?=GetMessage('skyweb24.roulette_wrong')?><span></span></p>
        <p class="result_roll_not_unique" style="display:none;"><?=$arResult['EMAIL_NOT_NEW_TEXT']?><span></span></p>
      </label>
      <button class="roll_roulette"><?=$arResult['BUTTON_TEXT']?></button>
    
</div>
