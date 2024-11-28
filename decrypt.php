<?php

require_once('inc/common.inc.php');

// USername

if (empty($argv[1]))
	die('Username required'."\n");

$username = $argv[1];
$sql = 'SELECT * FROM user WHERE ref="'.$username.'"';
$q = $db->query($sql);
if ($q->num_rows==0)
	die('User unknown');
$user = $q->fetch_assoc();
var_dump($user['id']);

// Passphrase

if (empty($argv[2]))
	die('Passphrase required'."\n");
$passphrase = $argv[2];

// @todo
//$user_privkey = openssl_pkey_get_private($user['privkey'], $passphrase);
//var_dump($user_privkey);
$cmd = 'openssl rsa -in data/user_keys/'.$username.'.pem --passin pass:'.$passphrase;
$user_privkey = shell_exec($cmd);
var_dump($user_privkey);
if (empty($user_privkey))
	die('Username OR Passphrase incorrect');

// Password
// @todo Search by uuid

if (empty($argv[3]))
	die('passdata ID required'."\n");

$pass_id = $argv[3];
$sql = 'SELECT p.*
	FROM `pass` p
	INNER JOIN user_has_group ug ON ug.group_id=p.group_id
	WHERE p.id="'.$pass_id.'" AND ug.user_id="'.$user['id'].'"';
$q = $db->query($sql);
if ($q->num_rows==0)
        die('Pass ID unknown');
$pass = $q->fetch_assoc();
var_dump($pass);
$passdata_e = $pass['data'];
//file_get_contents('data/pass/'.$pass_id);
var_dump($passdata_e);

// Group

$sql = 'SELECT *
	FROM `group` g
	INNER JOIN user_has_group ug ON ug.group_id=g.id
	WHERE g.id="'.$pass['group_id'].'"';
$q = $db->query($sql);
$group = $q->fetch_assoc();
var_dump($group);

// Decrypt

//$group_key_e = file_get_contents('data/group_keys/'.$group['id']);
//var_dump($group_key_e);
openssl_private_decrypt($group['key'], $group_key, $user_privkey);
$group_key = trim($group_key);
var_dump($group_key);

$passdata = openssl_decrypt($passdata_e, 'aes-256-cbc', $group_key);
//$cmd = 'openssl rsautl -decrypt -in data/group_keys/'.$group['id'].' -inkey data/user_keys/'.$username.'.pem.clear';
var_dump($passdata);

//openssl_encrypt($passdata, 'aes-256-cbc', $group_key);

