<?php /* Smarty version Smarty-3.1.14, created on 2014-06-06 11:26:32
         compiled from "login.php" */ ?>
<?php /*%%SmartyHeaderCode:1603802657539142f802e142-59520484%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a7beef4050e4205c8b59eb204ec9bb15ce01d9c0' => 
    array (
      0 => 'login.php',
      1 => 1402028785,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1603802657539142f802e142-59520484',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_539142f810ca27_64328530',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_539142f810ca27_64328530')) {function content_539142f810ca27_64328530($_smarty_tpl) {?><<?php ?>?php
// print_r($_POST);
// print_r($_SESSION);
	session_start();
	require("res/php/functions/general.php");
	require("res/php/config.php");
	require("res/php/connect/db.php");
	require("res/php/classes/badmin.php");
	$OBAdmin=new BAdmin($db);
	$OBAdmin->logged_in_protect("index.php");
	$OBAdmin->display_login();
?<?php ?>>
<?php }} ?>