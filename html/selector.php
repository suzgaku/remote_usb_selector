<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
	* {
    margin: 0px;
    padding: 0px;
	font-size: small;
}
</style>
</head>
<?php

if (($_GET['power-off'] == 'power-off') || ($_POST['power-off'] == 'power-off')) {
	?><script type="text/javascript">window.open("shutdown.html", "body", "");</script><?php
	system("sudo /sbin/shutdown -h now");

	exit;
}

exec("cat /sys/class/gpio/gpio6/value", $out, $ret); //Select

$select_now = (int)$out[0];

if ((($_GET['s'] == 'umount') || ($_POST['s'] == 'umount')) && ($select_now == 0)) {
	$sel = 'mount';
	?><script type="text/javascript">window.open("disconnect.html", "body", "");</script><?php

	exec("sync");
	exec("umount /dev/sda1");

	usleep(1000000); // 1.0sec

	exec("echo 0 > /sys/class/gpio/gpio5/value"); // Enable OFF
	exec("echo 1 > /sys/class/gpio/gpio6/value"); // Select USB#1
	exec("echo 1 > /sys/class/gpio/gpio5/value"); // Enable ON

} elseif ((($_GET['s'] == 'mount') || ($_POST['s'] == 'mount')) && ($select_now == 1)) {
//	?><script type="text/javascript">window.open("waiting.html", "body", "");</script><?php

	exec("echo 0 > /sys/class/gpio/gpio5/value"); // Enable OFF
	exec("echo 0 > /sys/class/gpio/gpio6/value"); // Select USB#0
	exec("echo 1 > /sys/class/gpio/gpio5/value"); // Enable ON

	usleep(3000000); // 3.0sec
/*
	for ($i = 0; $i < 30; $i++) {
		exec("ls /dev/sda1", $out, $ret);
		if ($out[0] == "/dev/sda1") {
			break;
		}
		usleep(500000); // 500msec
	}
*/
	exec("mount /dev/sda1");

	?><script type="text/javascript">window.open("elfinder.html", "body", "");</script><?php
	$sel = 'umount';
} else {
	if ($select_now == 1) {
		$sel = 'mount';
		?><script type="text/javascript">window.open("disconnect.html", "body", "");</script><?php

	} else {
		$sel = 'umount';
		?><script type="text/javascript">window.open("elfinder.html", "body", "");</script><?php
	}
}
?>
<body bgcolor="#4f4f72" text="#FFFFFF">
<form method="POST" action="selector.php">
	Remote USB Selector
<input type="hidden" name="s" value="<?php echo $sel; ?>">
<input type="submit" name="switch" value="switch">
　　
<input type="submit" name="power-off" value="power-off">
</form>
</div>
</body>
</html>
