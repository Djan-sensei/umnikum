<meta charset="utf-8">

<link type="text/css" rel="stylesheet" href="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/css/style.css?time=<?php echo time(); ?>" />

<script src="https://vk.com/js/api/xd_connection.js?2" type="text/javascript"></script>
<script src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/js/jquery-1.10.1.min.js"></script>

<script src="<?php echo 'https://'.$_SERVER['HTTP_HOST']; ?>/js/js.js"></script>

<script language="javascript" type="text/javascript">
	
	/*** Аватарка пользователя ***/
	function user_photo() {			
		// Составляем запрос по API
		var code =  'return API.users.get({"<?php echo $id;?>":API.getProfiles({"v": "5.28"}), "fields": "photo_100", "v": "5.28"});';

		VK.api("execute", {code: code}, function(r) {
			if (r.response) {
				//console.log(r.response);
				$('#ppt').append('<img src="'+ r.response[0].photo_100 +'">');					
			} else {
				alert('Ошибка!');
			}
		});
	}
	user_photo();
	/*** [end]Аватарка пользователя ***/
	
	
	/*** Платные услуги ***/
	function order(coin) {
		// mobile sdk // VK.init(function() {  
		var params = {
			type: 'item',
			item: 'item_'+coin+'coin'
		};
		VK.callMethod('showOrderBox', params);

		var callbacksResults = document.getElementById('callbacks');

		VK.addCallback('onOrderSuccess', function(order_id) {
			//callbacksResults.innerHTML = 'Платеж <b>'+order_id+'</b> успешно завершен!';
			window.location.href = 'index.php<?php echo $url;?>&end=yes';
		});

		VK.addCallback('onOrderFail', function() {
			callbacksResults.innerHTML = 'Платеж не был обработан!';
			setTimeout(function() {  callbacksResults.innerHTML = ''; }, 3000);
		});

		VK.addCallback('onOrderCancel', function() {
			callbacksResults.innerHTML = 'Платеж отменен!';
			setTimeout(function() {  callbacksResults.innerHTML = ''; }, 3000);
		});
		// mobile sdk // }, function() { 
			// mobile sdk //  // API initialization failed 
			// mobile sdk //  // Can reload page here 
		// mobile sdk // }, '5.60'); 			
	}
	/*** [end]Платные услуги ***/
	
	
	function buy() {
		$('.podl2').show('100');
		$('#bby').show('1000');
		$('.podl2').css('z-index', '3');
	}
	function buy_cl() {
		$('.podl2').hide('1000');
		$('#bby').hide('1000');
		$('.podl2').css('z-index', '1');
	}
</script>