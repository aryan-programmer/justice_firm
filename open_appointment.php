<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";

$l_id   = 0;
$set_ts = false;
$ts     = "";
$desc   = $l_name = "";

if (isset($_POST["submit"])) {
	$desc = $_POST["desc"] ?? "";
	if (isset($_POST["lawyer_id"])) {
		$l_id = $_POST["lawyer_id"];
		$l    = get_lawyer_by_id($l_id);
		if ($l === false) {
			err_str("Failed to find the lawyer with the given ID");
		}
	} else {
		err_str("Specify a lawyer to open an appointment request for");
	}

	if ($desc === "") {
		err_str("Enter a description");
	}

	$set_ts = isset($_POST["set-timestamp"]) && $_POST["set-timestamp"] === "y";
	$ts     = $_POST["timestamp"] ?? "";
	if ($set_ts && $ts === "") {
		err_str("If you have checked the set timestamp checkbox, you must set a timestamp");
	}

	$ts_date = $set_ts ? strtotime($ts) : false;
	if ($set_ts && $ts_date === false) {
		err_str("Enter a valid timestamp");
	}

	if (count(errs()) === 0) {
		$res = open_appointment_request($l_id, $desc, $ts_date);
		if ($res !== true) {
			err_str($res);
		} else {
			msg_str("Opened appointment request successfully.");
			redirect_page(PageIndex::SearchLawyer);
		}
	}
}

if (isset($_GET["lawyer_id"])) {
	$l_id = $_GET["lawyer_id"];
	$l    = get_lawyer_by_id($l_id);
	if ($l === false) {
		err_str("Failed to find the lawyer with the given ID");
		redirect_page(PageIndex::Home);
	} else {
		$l_name = $l->name;
	}
} else {
	err_str("Specify a lawyer to open an appointment request for");
	redirect_page(PageIndex::Home);
}

$h_body_end = function () { ?>
	<script>
	const dom_timestamp       = document.getElementById("timestamp");
	const dom_set_ts_checkbox = document.getElementById("set-timestamp");
	dom_set_ts_checkbox.addEventListener("click", ev => {
		dom_timestamp.disabled = !dom_set_ts_checkbox.checked;
	});
	</script>
<?php };

basic_setup(true);
redirect_if_not(USER_CLIENT);
show_html_start_block(PageIndex::None);
show_messages(); ?>
	<form class="form-card my-3 bg-gradient--deep-relief" action="#" method="post">
		<div class="form-title">
			Open appointment request
		</div>
		<div class="form">
			<?php
			form_input_custom("Lawyer", "lawyer", <<<TAG
<input name="lawyer_id" type="hidden" value="$l_id">
<input class="form-control" disabled type="text" value="$l_name"/>
TAG
			);
			form_input_custom("Case Description", "desc", <<<TAG
<textarea id="desc" name="desc" class="form-control">$desc</textarea>
TAG
			);
			form_input_custom("Set timestamp", "timestamp", function () {
				global $set_ts, $ts; ?>
				<div class="input-group mb-3">
					<div class="input-group-text">
						<input
							id="set-timestamp"
							class="form-check-input mt-0"
							type="checkbox"
							name="set-timestamp"
							value="y"
							data-bs-toggle="tooltip"
							data-bs-title="Check to fix appointment time and date"
							<?php if ($set_ts) echo "checked" ?>>
					</div>
					<input
						id="timestamp"
						name="timestamp"
						type="datetime-local"
						class="form-control"
						<?= $set_ts ? "value=\"$ts\"" : "disabled" ?>
					>
				</div>
			<?php });
			?>
		</div>
		<div class="form-footer">
			<button
				type="submit" name="submit" value="y" class="btn btn-lg btn-tertiary">Open appointment request
			</button>
		</div>
	</form>

<?php show_html_end_block();
