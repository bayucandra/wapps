<html>
<head>
	<title>{$title}</title>
</head>
<body>

<div>
	<form method="post" action="{$login_action_form}">
		<table style="text-align:left;">
			<tr>
				<td><label>Username</label></td>
				<td>:</td>
				<td><input name="email" maxlength="20" type="text" autocomplete="off" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td>:</td>
				<td><input name="password" maxlength="20" type="password" /></td>
			</tr>
			<tr>
				<td colspan="3" align="right"><input name="wapps_login" type="submit" value="Login" /></td>
			</tr>
		</table>
	</form>
</div>
<div>{$error_messages}</div>

</body>
</html>
