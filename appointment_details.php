<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";

$a = appointment_from_get_param();

$lawyer              = get_lawyer_by_id($a->l_id);
$user                = get_user_by_id($a->c_id);
$appointment_status  = APPOINTMENT_STATUS_TO_IDX[$a->status];
$waiting_appointment = $_SESSION[USER_TYPE] === USER_LAWYER && $appointment_status === APPOINTMENT_WAITING;
$use_ts_modal        = false;

basic_setup();
show_html_start_block(PageIndex::AppointmentDetails);
show_messages(); ?>

	<div class="card bg-gradient--mole-hall">
		<div class="card-header add-bg-noise h4">
			Appointment details
		</div>
		<div class="card-body">
			<?php show_lawyer_card($lawyer, "Laywer: ", true); ?>
			<?php show_user_card($user, "User: "); ?>
			<p class="card-text">
				Opened on: <?= $a->opened_on ?><br />
				Appointment time: <?= $a->timestamp ?? "Unset" ?>
			</p>
			<pre class="card-text">
Description:
<?= $a->description ?></pre>
			<?php
			switch ($appointment_status) {
			case APPOINTMENT_WAITING:
				?>
				<p class="card-text">Status: <span class="text-warning fw-bold">Waiting</span></p>
				<?php
				break;
			case APPOINTMENT_CONFIRMED:
				?>
				<p class="card-text">Status: <span class="text-success fw-bold">Confirmed</span></p>
				<?php
				break;
			case APPOINTMENT_REJECTED:
				?>
				<p class="card-text">Status: <span class="text-danger fw-bold">Rejected</span></p>
				<?php
				break;
			}
			?>
		</div>
		<?php
		if ($waiting_appointment) { ?>
			<div class="card-footer add-bg-noise">
				<?php if ($a->timestamp === null || $a->timestamp === "") {
					$use_ts_modal = true; ?>
					<button
						class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirm-select-ts-modal">Confirm
					</button>
				<?php } else { ?>
					<form class="d-inline" action="./confirm_appointment.php" method="get">
						<input type="hidden" value="<?= $a->a_id ?>" name="id">
						<button class="btn btn-success" type="submit" name="submit" value="y">Confirm</button>
					</form>
				<?php } ?>
				<button
					class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#reject-modal">Reject
				</button>
			</div>
		<?php } ?>
	</div>

<?php if ($use_ts_modal) { ?>
	<div class="form-modal modal fade" id="reject-modal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog form-modal-dialog">
			<form class="form-card bg-gradient--old-hat" action="reject_appointment.php" method="get">
				<div class="form-title h4-imp">
					<h4 class="h4-imp">Reject appointment</h4>
					<button type="button" class="btn-close h5-imp" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="form">
					<p>Are you sure you want to reject this appointment?</p>
					<input type="hidden" value="<?= $a->a_id ?>" name="id">
				</div>
				<div class="form-footer">
					<button
						type="button" class="btn btn-warning" data-bs-dismiss="modal">No, I do not want to reject.
					</button>
					<button
						class="btn btn-success" type="submit" name="submit" value="y">Yes, I do want to reject
					</button>
				</div>
			</form>
		</div>
	</div>
<?php } ?>

<?php if ($use_ts_modal) { ?>
	<div class="form-modal modal fade" id="confirm-select-ts-modal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog form-modal-dialog">
			<form class="form-card bg-gradient--wide-matrix--right" action="confirm_appointment.php" method="get">
				<div class="form-title h4-imp">
					<h4 class="h4-imp">Set appointment timestamp to confirm</h4>
					<button type="button" class="btn-close h5-imp" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="form">
					<input type="hidden" value="<?= $a->a_id ?>" name="id">
					<input
						id="timestamp"
						name="timestamp"
						type="datetime-local"
						class="form-control"
					>
				</div>
				<div class="form-footer">
					<button type="button" class="btn btn-outline-warning" data-bs-dismiss="modal">Close</button>
					<button class="btn btn-success" type="submit" name="submit" value="y">Confirm</button>
				</div>
			</form>
		</div>
	</div>
<?php } ?>


<?php show_html_end_block();
