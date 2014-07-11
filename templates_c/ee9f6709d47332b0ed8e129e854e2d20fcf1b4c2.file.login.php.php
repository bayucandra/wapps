<?php /* Smarty version Smarty-3.1.14, created on 2014-06-06 11:26:58
         compiled from "res/php/tpl/login.php" */ ?>
<?php /*%%SmartyHeaderCode:195326478153914312c00e87-59968119%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ee9f6709d47332b0ed8e129e854e2d20fcf1b4c2' => 
    array (
      0 => 'res/php/tpl/login.php',
      1 => 1390185677,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '195326478153914312c00e87-59968119',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'login_action_form' => 0,
    'error_messages' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_53914312c696a6_72791782',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53914312c696a6_72791782')) {function content_53914312c696a6_72791782($_smarty_tpl) {?><html>
<head>
	<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
</head>
<body>

<div>
	<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['login_action_form']->value;?>
">
		<table style="text-align:left;">
			<tr>
				<td><label>Username</label></td>
				<td>:</td>
				<td><input name="username" maxlength="20" type="text" autocomplete="off" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td>:</td>
				<td><input name="password" maxlength="20" type="password" /></td>
			</tr>
			<tr>
				<td colspan="3" align="right"><input name="wsys_login" type="submit" value="Login" /></td>
			</tr>
		</table>
	</form>
</div>
<div><?php echo $_smarty_tpl->tpl_vars['error_messages']->value;?>
</div>

</body>
</html>
<?php }} ?>