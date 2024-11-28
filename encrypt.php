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

$cmd = 'openssl rsa -in data/user_keys/'.$username.'.pem --passin pass:'.$passphrase;
$user_privkey = shell_exec($cmd);
var_dump($user_privkey);
if (empty($user_privkey))
	die('Username OR Passphrase incorrect');

// Group
// Search by uuid

if (empty($argv[3]))
	die('group ID required'."\n");

$group_id = $argv[3];
$sql = 'SELECT *
	FROM `group` g
	INNER JOIN user_has_group ug ON ug.group_id=g.id
	WHERE g.id="'.$group_id.'" AND ug.user_id="'.$user['id'].'"';
$q = $db->query($sql);
if ($q->num_rows==0)
        die('Group ID unknown OR not authorized');
$group = $q->fetch_assoc();
var_dump($group);

// Encrypt

$group_key_e = file_get_contents('data/group_keys/'.$group['id']);
var_dump($group_key_e);
openssl_private_decrypt($group_key_e, $group_key, $user_privkey);
$group_key = trim($group_key);
var_dump($group_key);

// Password

if (empty($argv[4]))
        die('password required'."\n");

$passdata = $argv[4];
$passdata_e = openssl_encrypt($passdata, 'aes-256-cbc', $group_key);
//$cmd = 'openssl rsautl -decrypt -in data/group_keys/'.$group['id'].' -inkey data/user_keys/'.$username.'.pem.clear';
var_dump($passdata_e);

$sql = 'INSERT INTO `pass` (uuid, group_id, ref, label, description, data) VALUES ("'.guidv4().'", '.$group['id'].', "", "", "", "'.$passdata_e.'")';
$q = $db->query($sql);
$pass_id = $db->insert_id;
var_dump($pass_id);

//openssl_encrypt($passdata, 'aes-256-cbc', $group_key);

