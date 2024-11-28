<?php

require_once 'inc/common.inc.php';

$sql = 'SELECT * FROM user';
$q = $db->query($sql);
while($row=$q->fetch_assoc())
	var_dump($row);
