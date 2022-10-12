<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";

basic_setup(true);
redirect_if_not(USER_LAWYER);

$a = appointment_from_get_param();
if ($uid !== $a->l_id) {
	// Redirection was already done so execution won't reach here.
	die();
}

$loc = Page::$pages[PageIndex::AppointmentDetails]->withId($a->a_id);

if ($a->status != APPOINTMENT_STATUS[APPOINTMENT_WAITING]) {
	err_str("This appointment already has it's status set.");
	redirect_path($loc);
	die();
}

if (!set_appointment_status($a->a_id, APPOINTMENT_STATUS[APPOINTMENT_REJECTED])) {
	err_str("Failed to reject appointment");
} else {
	msg_str("Rejected appointment");
}

redirect_path($loc);
