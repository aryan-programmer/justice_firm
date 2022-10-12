<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";

$pending_appointments   = get_appointments(APPOINTMENT_STATUS[APPOINTMENT_WAITING], false, true);
$confirmed_appointments = get_appointments(APPOINTMENT_STATUS[APPOINTMENT_CONFIRMED], false, false);
$rejected_appointments  = get_appointments(APPOINTMENT_STATUS[APPOINTMENT_REJECTED], false, false);

basic_setup();
$oth_t = $_SESSION[USER_TYPE] === USER_CLIENT ? "Lawyer" : "Client";
show_html_start_block(PageIndex::Appointments);
show_messages(); ?>
	<a href="#appointments">View appointments</a>
	<h3>Pending appointment requests</h3>
	<table class="table table-sm table-bordered table-warning">
		<tr>
			<th><?= $oth_t ?></th>
			<th>Description</th>
			<th>Timestamp</th>
			<th>Opened on</th>
			<th>View more</th>
		</tr>
		<?php
		/** @var Appointment $a */
		foreach ($pending_appointments as $a) { ?>
			<tr>
				<td><?= $a->oth_name ?></td>
				<td><?= trim_str($a->description) ?></td>
				<td><?= $a->timestamp ?></td>
				<td><?= $a->opened_on ?></td>
				<td><a href="appointment_details.php?id=<?= $a->a_id ?>">View more</a></td>
			</tr>
		<?php }
		unset($a); ?>
	</table>
	<h3 id="appointments">Confirmed Appointments</h3>
	<table class="table table-sm table-bordered table-success">
		<tr>
			<th><?= $oth_t ?></th>
			<th>Description</th>
			<th>Timestamp</th>
			<th>View more</th>
		</tr>
		<?php
		/** @var Appointment $a */
		foreach ($confirmed_appointments as $a) { ?>
			<tr>
				<td><?= $a->oth_name ?></td>
				<td><?= trim_str($a->description) ?></td>
				<td><?= $a->timestamp ?></td>
				<td><a href="appointment_details.php?id=<?= $a->a_id ?>">View more</a></td>
			</tr>
		<?php }
		unset($a); ?>
	</table>
	<h3 id="appointments">Rejected Appointments</h3>
	<table class="table table-sm table-bordered table-danger">
		<tr>
			<th><?= $oth_t ?></th>
			<th>Description</th>
			<th>Timestamp</th>
			<th>View more</th>
		</tr>
		<?php
		/** @var Appointment $a */
		foreach ($rejected_appointments as $a) { ?>
			<tr>
				<td><?= $a->oth_name ?></td>
				<td><?= trim_str($a->description) ?></td>
				<td><?= $a->timestamp ?></td>
				<td><a href="appointment_details.php?id=<?= $a->a_id ?>">View more</a></td>
			</tr>
		<?php }
		unset($a); ?>
	</table>
<?php show_html_end_block();
