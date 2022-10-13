<?php
const USER_ID                       = "USER_ID";
const USER_EMAIL                    = "USER_EMAIL";
const USER_PASSWORD                 = "USER_PASSWORD";
const USER_TYPE                     = "USER_TYPE";
const USER_ID_SAVE_COOKIE_FROM_SESS = "USER_ID_SAVE_COOKIE_FROM_SESS";
const COOKIE_EXPIRY_TIME            = 60 * 60 * 24 * 30;
const MAX_FILE_SIZE                 = 5_000_000;
const MAX_FILE_SIZE_STR             = "5 MB";
const MAX_FILE_SIZE_FORM_INPUT      = '<input type="hidden" name="MAX_FILE_SIZE" value="' . MAX_FILE_SIZE . '" />';

const USER_ANY         = true;
const USER_CLIENT      = 0;
const USER_LAWYER      = 1;
const USER_ADMIN       = 2;
const USER_TYPE_TO_IDX = [
	'client' => USER_CLIENT,
	'lawyer' => USER_LAWYER,
	'admin'  => USER_ADMIN,
];
const USER_TYPES       = ['client', 'lawyer', 'admin'];

const NAV_CLASS_DEFAULT    = "bg-quaternary";
const NAV_CLASS_BY_USER = [
	USER_CLIENT => "bg-gradient-primary-secondary",
	USER_LAWYER => "bg-gradient-warning-tertiary",
	USER_ADMIN  => "navbar-dark bg-dark",
];

const LAWYER_STATUS_MAP = [
	'-' => 'waiting',
	'y' => 'confirmed',
	'n' => 'rejected',
];

const APPOINTMENT_WAITING       = 0;
const APPOINTMENT_REJECTED      = 1;
const APPOINTMENT_CONFIRMED     = 2;
const APPOINTMENT_STATUS        = ['waiting', 'rejected', 'confirmed'];
const APPOINTMENT_STATUS_TO_IDX = [
	'waiting'   => APPOINTMENT_WAITING,
	'rejected'  => APPOINTMENT_REJECTED,
	'confirmed' => APPOINTMENT_CONFIRMED,
];

const TITLE_DEFAULT = "Justice Firm";

const VALID_IMAGE_MIME_TYPES = ["image/jpeg", "image/png", "image/gif"];

const INVALID_IMAGE_ERR_MSG     = "Image must be a jpeg, png or a gif";
const INVALID_FILE_SIZE_ERR_MSG = "The file uploaded must be less than " . MAX_FILE_SIZE_STR . " in size.";

const DATE_MYSQL = "Y-m-d H:i:s";

const PHONE_REGEX = /** @lang PhpRegExp */
'/^[0-9]{3}([-\s]?)[0-9]{3}([-\s]?)[0-9]{4}$/';

function pvar_dump(...$v) {
	echo '<pre style="max-height: 20em; overflow-y: scroll;">';
	var_dump(...$v);
	echo '</pre>';
}

function h($v): string {
	return htmlentities($v, ENT_QUOTES | ENT_SUBSTITUTE);
}

function trim_str($s, $word_count = 50): string {
	$words = explode(" ", $s);
	if (count($words) > $word_count)
		return implode(" ", array_slice($words, 0, $word_count)) . "...";
	else return $s;
}

function rnd($v): float {
	return round((double)$v, 2);
}

function rnd_str($v): string {
	return number_format((float)$v, 2, '.', '');
}

function eq_epsilon(float $a, float $b) {
	if ($b == 0) {
		if ($a == 0) return true;
		return abs($b - $a) <= abs(($b - $a) / $a);
	}
	return abs($a - $b) <= abs(($a - $b) / $b);
}
