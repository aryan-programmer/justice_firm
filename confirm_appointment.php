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

if (isset($_GET["timestamp"])) {
	$ts_date = strtotime($_GET["timestamp"]);
	if ($ts_date === false) {
		err_str("Enter a valid timestamp");
	} elseif (!set_appointment_status($a->a_id, APPOINTMENT_STATUS[APPOINTMENT_CONFIRMED], $ts_date)) {
		err_str("Failed to confirm appointment and set the given timestamp");
	} else {
		msg_str("Confirmed appointment");
	}
} elseif (!set_appointment_status($a->a_id, APPOINTMENT_STATUS[APPOINTMENT_CONFIRMED])) {
	err_str("Failed to confirm appointment");
} else {
	msg_str("Confirmed appointment");
}

redirect_path($loc);
