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

function http_open_window($page_name, $window)
{
	?><script type="text/javascript">window.open("<?php echo $page_name; ?>", "<?php echo $window; ?>", "");</script><?php
}

if (($_GET['power-off'] == 'power-off') || ($_POST['power-off'] == 'power-off')) {
	http_open_window("shutdown.html", "body");
	system("sudo /sbin/shutdown -h now");

	exit;
}

exec("cat /sys/class/gpio/gpio6/value", $out, $ret); //Select
$select_now = (int)$out[0];

if ((($_GET['s'] == 'umount') || ($_POST['s'] == 'umount')) && ($select_now == 0)) {
	$sel = 'mount';
	http_open_window("shutdown.html", "body");

	exec("sync");
	exec("umount /dev/sda1");

	usleep(1000000); // 1.0sec

	exec("echo 0 > /sys/class/gpio/gpio5/value"); // Enable OFF
	exec("echo 1 > /sys/class/gpio/gpio6/value"); // Select USB#1
	exec("echo 1 > /sys/class/gpio/gpio5/value"); // Enable ON

} elseif ((($_GET['s'] == 'mount') || ($_POST['s'] == 'mount')) && ($select_now == 1)) {

	exec("echo 0 > /sys/class/gpio/gpio5/value"); // Enable OFF
	exec("echo 0 > /sys/class/gpio/gpio6/value"); // Select USB#0
	exec("echo 1 > /sys/class/gpio/gpio5/value"); // Enable ON

	usleep(3000000); // 3.0sec

	exec("mount /dev/sda1");

	http_open_window("elfinder.html", "body");
	$sel = 'umount';
} else {
	if ($select_now == 1) {
		$sel = 'mount';
		http_open_window("disconnect.html", "body");

	} else {
		$sel = 'umount';
		http_open_window("elfinder.html", "body");
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
