
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/zabbix/styles/blue-theme.css" />
<?php 
echo $_COOKIE["zbx_sessionid"];

	if(!$_COOKIE["zbx_sessionid"]) {
		echo '
			<table class="warningTable" align="center">
				<tbody>
					<tr class="header">
						<td>Voc&ecirc; n&atilde;o est&aacute; logado.</td>
					</tr>
					<tr class="content">
						<td>
							<span>Voc&ecirc; deve entrar para ver est&aacute; p&aacute;gina.<br>
Se voc&ecirc; acha que esta mensagem &eacute; errado, por favor consulte os administradores sobre como obter as permiss&otilde;es necess&aacute;rias.</span>
						</td>
					<tr class="footerx">
						<td>
							<div class="buttons">
								<input class="input formlist" type="button" id="login" name="login" value="Login" onclick="javascript: document.location=\'http://'.$_SERVER['SERVER_NAME'].'/zabbix/index.php\'">
							</div>
						</td>
					</tr>
				</tbody>
			</table>';
	//break;		
	}
?>
