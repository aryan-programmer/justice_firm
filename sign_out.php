<?php require_once "php/common.php";
require_once "php/templates.php";

basic_setup(true);
unset_user_id();
msg_str("Signed out successfully.");
switch_location($PAGES[PageIndex::Home]->path);
