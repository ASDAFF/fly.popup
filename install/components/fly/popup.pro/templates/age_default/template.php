<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="fly_age_default">
	<img src="<?=$arResult['IMG_1_SRC']?>">
	<h2><?=$arResult['TITLE']?></h2>
	<div class="buttons">
		<a rel="nofollow" href="javascript:void(0);" class="flTargetAction yesClick"><?=$arResult['BUTTON_TEXT_Y']?></a>
		<a rel="nofollow" href="<?=$arResult['HREF_LINK']?>" class="noClick"><?=$arResult['BUTTON_TEXT_N']?></a>
	</div>
	<script>
		setTimeout(function(){
			var tmpEl=document.querySelector('#fly_age_default .flTargetAction');
			tmpEl.addEventListener("click", pop_close);
			function pop_close(){
				
				BX.ajax({
					url: '<?=$templateFolder?>/ajax.php',
					data:{'id_popup':'<?=$arParams["ID_POPUP"]?>','template_name':'<?=$templateName?>','checked':'Y'},
					method: 'POST',
					dataType: 'html',
					scriptsRunFirst:false,
					timeout:300,
					onsuccess: function(data){
						flyPopups.currentPopup.close();
					},
					onfailure: function(data){
						console.log(data);
					}
				});
			}
		}, 50);
	</script>
</div>