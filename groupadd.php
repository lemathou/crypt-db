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
var_dump($user);

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

if (empty($argv[3]))
	die('Group name required'."\n");
$group_name = $argv[3];
$key_length = 256;
$group_key = shell_exec('head /dev/urandom | sha256sum');
$group_key = substr($group_key, 0, strpos($group_key, ' '));
//$group_key = sodium_crypto_aead_aes256gcm_keygen();
//base64_encode(openssl_random_pseudo_bytes($key_length));
var_dump($group_key);
// Encrypt group key
openssl_public_encrypt($group_key, $group_key_e, $user['pubkey']);
var_dump($group_key_e);

$sql = 'INSERT INTO group (uuid, ref, label, description)
	VALUES ("'.rand(1, 8484545454545).'", "'.$group_name.'", "'.$group_name.'", "")';
$db->query($sql);
$group_id = $db->insert_id;

$sql = 'INSERT INTO user_has_group
	(user_id, group_id, key)
        VALUES ('.$user['id'].', '.$group_id.', "'.$db->real_escape_string($group_key_e).'")';
$db->query($sql);

die();

$sql = 'UPDATE user_has_group
	SET `key`="'.$db->real_escape_string($group_key_e).'"
	WHERE user_id=1 AND group_id=1';
$db->query($sql);

