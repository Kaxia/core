<?php

/*
    Copyright (C) 2014-2015 Deciso B.V.
    Copyright (C) 2007 Marcel Wiget <mwiget@mac.com>.
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

require_once("guiconfig.inc");
require_once("functions.inc");
require_once("filter.inc");
require_once("shaper.inc");
require_once("captiveportal.inc");
require_once("voucher.inc");

$cpzone = $_GET['zone'];
if (isset($_POST['zone']))
	$cpzone = $_POST['zone'];

if (empty($cpzone)) {
	header("Location: status_captiveportal.php");
	exit;
}

if (!is_array($config['captiveportal']))
	$config['captiveportal'] = array();
$a_cp =& $config['captiveportal'];
$pgtitle = array(gettext("Status"), gettext("Captive portal"), gettext("Vouchers"), $a_cp[$cpzone]['zone']);
$shortcut_section = "captiveportal-vouchers";

function clientcmp($a, $b) {
	global $order;
	return strcmp($a[$order], $b[$order]);
}

if (!is_array($config['voucher'][$cpzone]['roll'])) {
	$config['voucher'][$cpzone]['roll'] = array();
}
$a_roll = $config['voucher'][$cpzone]['roll'];

$db = array();

foreach($a_roll as $rollent) {
	$roll = $rollent['number'];
	$minutes = $rollent['minutes'];

	if (!file_exists("/var/db/voucher_{$cpzone}_active_{$roll}.db")) {
		continue;
	}

	$active_vouchers = file("/var/db/voucher_{$cpzone}_active_{$roll}.db", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach($active_vouchers as $voucher => $line) {
		list($voucher,$timestamp, $minutes) = explode(",", $line);
		$remaining = (($timestamp + 60*$minutes) - time());
		if ($remaining > 0) {
			$dbent[0] = $voucher;
			$dbent[1] = $roll;
			$dbent[2] = $timestamp;
			$dbent[3] = intval($remaining/60);
			$dbent[4] = $timestamp + 60*$minutes; // expires at
			$db[] = $dbent;
		}
	}
}

if ($_GET['order']) {
	$order = $_GET['order'];
	usort($db, "clientcmp");
}

include("head.inc");
?>


<body>
<?php include("fbegin.inc"); ?>


	<section class="page-content-main">
		<div class="container-fluid">
			<div class="row">

			    <section class="col-xs-12">

					<?php
						$tab_array = array();
						$tab_array[] = array(gettext("Active Users"), false, "status_captiveportal.php?zone={$cpzone}");
						$tab_array[] = array(gettext("Active Vouchers"), true, "status_captiveportal_vouchers.php?zone={$cpzone}");
						$tab_array[] = array(gettext("Voucher Rolls"), false, "status_captiveportal_voucher_rolls.php?zone={$cpzone}");
						$tab_array[] = array(gettext("Test Vouchers"), false, "status_captiveportal_test.php?zone={$cpzone}");
						$tab_array[] = array(gettext("Expire Vouchers"), false, "status_captiveportal_expire.php?zone={$cpzone}");
						display_top_tabs($tab_array);
					?>

					<div class="tab-content content-box col-xs-12">

				    <div class="container-fluid">

	                        <form action="status_captiveportal_vouchers.php" method="post" enctype="multipart/form-data" name="iform" id="iform">

					<div class="table-responsive">
						<table class="table table-striped table-sort">
									  <tr>
									    <td class="listhdrr"><a href="?order=0&amp;showact=<?=htmlspecialchars($_GET['showact']);?>"><?=gettext("Voucher"); ?></a></td>
									    <td class="listhdrr"><a href="?order=1&amp;showact=<?=htmlspecialchars($_GET['showact']);?>"><?=gettext("Roll"); ?></a></td>
									    <td class="listhdrr"><a href="?order=2&amp;showact=<?=htmlspecialchars($_GET['showact']);?>"><?=gettext("Activated at"); ?></a></td>
									    <td class="listhdrr"><a href="?order=3&amp;showact=<?=htmlspecialchars($_GET['showact']);?>"><?=gettext("Expires in"); ?></a></td>
									    <td class="listhdr"><a href="?order=4&amp;showact=<?=htmlspecialchars($_GET['showact']);?>"><?=gettext("Expires at"); ?></a></td>
									    <td class="list"></td>
									  </tr>
										<?php foreach ($db as $dbent): ?>
										  <tr>
										    <td class="listlr"><?=$dbent[0];?></td>
										    <td class="listr"><?=$dbent[1];?></td>
										    <td class="listr"><?=htmlspecialchars(date("m/d/Y H:i:s", $dbent[2]));?></td>
										    <td class="listr"><?=$dbent[3];?> <?=gettext("min"); ?></td>
										    <td class="listr"><?=htmlspecialchars(date("m/d/Y H:i:s", $dbent[4]));?></td>
										    <td class="list"></td>
										  </tr>
										<?php endforeach; ?>
										</table>
					</div>
	                        </form>
				    </div>
					</div>
			    </section>
			</div>
		</div>
	</section>

<?php include("foot.inc"); ?>
