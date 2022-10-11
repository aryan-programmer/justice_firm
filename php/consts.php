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

const VALID_IMAGE_MIME_TYPES = ["image/jpeg", "image/png", "image/gif"];

const INVALID_IMAGE_ERR_MSG     = "Image must be a jpeg, png or a gif";
const INVALID_FILE_SIZE_ERR_MSG = "The file uploaded must be less than " . MAX_FILE_SIZE_STR . " in size.";

const MAPS_API_KEY = "AIzaSyDSpFLDwXfwMLJi1irqs7wTrOAuRGIt-Zs";

const LAWYER_STATUS_MAP = [
	'-' => 'waiting',
	'y' => 'confirmed',
	'n' => 'rejected',
];

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

function rnd($v): float {
	return round((double)$v, 2);
}

function rndStr($v): string {
	return number_format((float)$v, 2, '.', '');
}
