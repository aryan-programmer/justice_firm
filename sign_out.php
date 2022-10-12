<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";

basic_setup(true);
unset_user_id();
msg_str("Signed out successfully.");
redirect_page(PageIndex::Home);
