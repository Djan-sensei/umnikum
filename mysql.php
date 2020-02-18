<?php

$mysqli = mysqli_connect("localhost", "*******", "*******", "*******");
mysqli_query($mysqli, "set names utf8");
session_start();


/*** переменные для ВК - $_GET (начало) ***/
$time_cach = time();
$url = '?time_cach='.$time_cach.'&api_url='.$_GET['api_url'].'&api_id='.$_GET['api_id'].'&api_settings='.$_GET['api_settings'].'&viewer_id='.$_GET['viewer_id'].'&sid='.$_GET['sid'].'&secret='.$_GET['secr
et'].'&user_id='.$_GET['user_id'].'&group_id='.$_GET['group_id'].'&is_app_user='.$_GET['is_app_user'].'&auth_key='.$_GET['auth_key'].'&language='.$_GET['language'].'&parent_language='.$_GET['parent_language'].'&lc_name='.$_GET['lc_name'];
/*** переменные для ВК - $_GET (конец) ***/

if(empty($_GET['viewer_id'])) {
	$id = 'none';
} else {
	$id = (int)$_GET['viewer_id']; // id пользователь в ВК
}
if($_GET['auth_key'] != md5('*******_'.$_GET['viewer_id'].'_*******')) {
	exit('Error!');
}
?>