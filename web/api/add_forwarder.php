<?php
require('_bootstrap.inc.php');
Authentication::check();

Request::need_parameters('email', 'forward');

$target = new stdClass;
$target->email = Request::$data['forward'];

$existing = false;

MailboxIterator::forMatchingForwarder(Request::$email, function ($mailbox) {
	global $target, $existing;
	if (in_array($target->email, Config::$config->protected_forwarders)) {
		Response::$error = 'forwarding_address_protected';
		Response::send();
	}

	$existing = true;
	if (!in_array($target, $mailbox->targets)) {
			array_push($mailbox->targets, $target);
	} else {
		Response::$error = 'duplicated_forwarding_address';
	}
	Config::save();
});

if (!$existing) {
	Response::$error = 'address_not_found';
}
Response::send();