<?php

/*
    Copyright (C) 2014 Deciso B.V.
    Copyright (C) 2007 Sam Wenham
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

$nocsrf = true;

require_once("guiconfig.inc");
require_once("pfsense-utils.inc");
require_once("functions.inc");
require_once("widgets/include/carp_status.inc");

$carp_enabled = get_carp_status();

?>
<table class="table table-striped" width="100%" border="0" cellspacing="0" cellpadding="0" summary="carp status">
<?php
	if(is_array($config['virtualip']['vip'])) {
		$carpint=0;
		foreach($config['virtualip']['vip'] as $carp) {
			if ($carp['mode'] != "carp")
				continue;
			$ipaddress = $carp['subnet'];
			$password = $carp['password'];
			$netmask = $carp['subnet_bits'];
			$vhid = $carp['vhid'];
			$advskew = $carp['advskew'];
			$status = get_carp_interface_status("{$carp['interface']}_vip{$vhid}");
?>
<tr>
	<td class="vncellt" width="35%">
		<span alt="cablenic" class="glyphicon glyphicon-transfer text-success"></span>&nbsp;
		<strong><a href="/system_hasync.php">
		<span><?=htmlspecialchars(convert_friendly_interface_to_friendly_descr($carp['interface']) . "@{$vhid}");?></span></a></strong>
	</td>
	<td width="65%"  class="listr">
<?php
			if($carp_enabled == false) {
				$status = "DISABLED";
				echo "<span class=\"glyphicon glyphicon-remove text-danger\" title=\"$status\" alt=\"$status\" ></span>";
			} else {
				if($status == "MASTER") {
					echo "<span class=\"glyphicon glyphicon-play text-success\" title=\"$status\" alt=\"$status\" ></span>";
				} else if($status == "BACKUP") {
					echo "<span class=\"glyphicon glyphicon-play text-muted\" title=\"$status\" alt=\"$status\" ></span>";
				} else if($status == "INIT") {
					echo "<span class=\"glyphicon glyphicon-info-sign\" title=\"$status\" alt=\"$status\" ></span>";
				}
			}
			if ($ipaddress){ ?> &nbsp;
				<?=htmlspecialchars($status);?> &nbsp;
				<?=htmlspecialchars($ipaddress);}?>
</td></tr><?php	}
	} else { ?>
		<tr><td class="listr">No CARP Interfaces Defined. Click <a href="carp_status.php">here</a> to configure CARP.</td></tr>
<?php	} ?>
</table>
