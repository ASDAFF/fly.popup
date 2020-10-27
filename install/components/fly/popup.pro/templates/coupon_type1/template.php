<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
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
<?function inducementWord($number, $wordArr){$cases = array (2, 0, 1, 1, 1, 2);return $wordArr[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];}?>
<div id="fly_coupon_coupon">
	<div class="bg" style="background-image:url('<?=$arResult['IMG_1_SRC']?>')">
		<div>
			<h2><?=$arResult['TITLE']?></h2>
			<p><?=$arResult['SUBTITLE']?></p>
			<span class="sale"><?=GetMessage("POPUP_SALE")?> <?=$arResult['PERCENT']?></span><span class="avaliable"><?=GetMessage("POPUP_AVALIABLE")?> <?=$arResult['TIMING']?>
			<?=GetMessage("POPUP_DAYS_".inducementWord($arResult['TIMING'],array(1,2,5)))?>
			</span>
			<?
				if($arResult['EMAIL_SHOW']=='Y'){
					if($arResult['EMAIL_REQUIRED']=='N' || $arResult['EMAIL_REQUIRED']=='Y'){
						$arResult['EMAIL_REQUIRED']=($arResult['EMAIL_REQUIRED']=='N')?'':'required';
					}
				}
				if($arResult['EMAIL_SHOW']=='N' || $arResult['EMAIL_SHOW']=='Y'){
					$arResult['EMAIL_SHOW']=($arResult['EMAIL_SHOW']=='N')?'notshow':'';
					$param_consent[]=$arResult['EMAIL_TITLE'];
				}
				?>
				<label class="<?=$arResult['EMAIL_SHOW']?> input">
					<p><?=GetMessage("POPUP_INFO")?><?=$arResult['EMAIL_PLACEHOLDER']?></p>
					<input type="email" value="<?=$arResult['EMAIL']?>" name="EMAIL" placeholder="<?=$arResult['EMAIL_PLACEHOLDER']?>"/>
					<span class="error"><?=GetMessage("POPUP_WRONG")?></span>
					<span class="not_new"><?=$arResult['EMAIL_NOT_NEW_TEXT']?></span>
				</label>
			<div class="coupon_block"><button onclick="getCoupon();"><?=$arResult['BUTTON_TEXT']?></button>
			<input class="goodCoupon" type="text" disabled>
			<span><?=GetMessage('POPUP_COPYED')?></span>
			</div>
		</div>
	</div>
</div>
<script>
	function validateEmail(elementValue){      
		var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		return emailPattern.test(elementValue); 
	} 
	function getCoupon(){
		var url = "<?=$templateFolder?>/ajax.php?id=<?=$arResult['RULE_ID']?>&avaliable=<?=$arResult['TIMING']?>";
		var email = BX('fly_coupon_coupon').querySelector('label.input');
		var getContinue=true;
		if(!email.classList.contains('notshow')){
			email = email.querySelector('input');
			getContinue=validateEmail(email.value);
			url+="&email="+email.value+"&idPopup=<?=$arParams['ID_POPUP']?>&addtotable=<?=$arResult['EMAIL_ADD2BASE']?>&unique=<?=$arResult['EMAIL_NOT_NEW']?>";
		}
		if(getContinue){
			if(!email.classList.contains('notshow')){
				email.className="";
			}
			BX.ajax({
				url:url,
				method:'POST',
				onsuccess: function(data){
					
					if(data=='not_unique'){
						BX('fly_coupon_coupon').querySelector('span.not_new').style.display='inline-block';
					}else{
						BX('fly_coupon_coupon').querySelector('span.not_new').style.display='none';
						BX('fly_coupon_coupon').querySelector('label.input').remove()//.style.display='none';
				
						BX('fly_coupon_coupon').querySelector('div.coupon_block button').remove()//.style.display='none';
						var input=BX('fly_coupon_coupon').querySelector('div.coupon_block input[type="text"]');
						input.disabled=false;
						input.value=data;
						input.style.display='inline-block';
						input.addEventListener('click',function(){
							var range = document.createRange();
							range.selectNode(input);
							window.getSelection().addRange(range);
							try{
								var successful = document.execCommand('copy');
							}catch(err){
								console.log(err);
							}
							window.getSelection().removeRange(range);
							BX('fly_coupon_coupon').querySelector('div.coupon_block input[type="text"]+span').style.display='inline';
							<?=$arResult['BUTTON_METRIC']?>
						});
						flyPopupTargetAction();
					}
				},
				onfailure: function(data){
					console.log(data);
				}
			});
		}else{
			BX('fly_coupon_coupon').querySelector('span.not_new').style.display='none';
			email.className="error";
			email.focus();
		}
	}
</script>
