<?php
/* $Id$ */
/*
	Copyright (C) 2014 Deciso B.V.
	Copyright (C) 2004-2009 Scott Ullrich
	Copyright (C) 2003-2004 Manuel Kasper <mk@neon1.net>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/

/*	
	pfSense_MODULE:	dhcpserver
*/

##|+PRIV
##|*IDENT=page-diagnostics-logs-dhcp
##|*NAME=Diagnostics: Logs: DHCP page
##|*DESCR=Allow access to the 'Diagnostics: Logs: DHCP' page.
##|*MATCH=diag_logs_dhcp.php*
##|-PRIV

require("guiconfig.inc");

$dhcpd_logfile = "{$g['varlog_path']}/dhcpd.log";

$nentries = $config['syslog']['nentries'];
if (!$nentries)
	$nentries = 50;

if ($_POST['clear']) {
	clear_log_file($dhcpd_logfile);
	killbyname("dhcpd");
	services_dhcpd_configure();
}

$pgtitle = array(gettext("Status"),gettext("System logs"),gettext("DHCP"));
$shortcut_section = "dhcp";
include("head.inc");

?>

<body>
<?php include("fbegin.inc"); ?>

	<section class="page-content-main">
		<div class="container-fluid">	
			<div class="row">
				
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				
			    <section class="col-xs-12">
    				
    					
    					<? include('diag_logs_tabs.php'); ?>

					
						<div class="tab-content content-box col-xs-12">	    					
	    				    <div class="container-fluid">	    					
		    						
	    						<p> <?php printf(gettext("Last %s DHCP service log entries"), $nentries);?></p>
								<pre> <?php dump_clog($dhcpd_logfile, $nentries); ?></pre>
								
								<form action="diag_logs_dhcp.php" method="post">
									<input name="clear" type="submit" class="btn" value="<?= gettext("Clear log");?>" />
								</form>
								<p>NOTE: Clearing the log file will restart the DHCP daemon.</p>
    						</div>
    				    </div>
			    </section>
			</div>
		</div>
	</section>
	
<?php include("foot.inc"); ?>