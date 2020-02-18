$(window).load(function() {
	$('#loading').hide();
});

// защита от копи
document.ondragstart = noselect; 
document.onselectstart = noselect; 
document.oncontextmenu = noselect; 
function noselect() {return false;} 