<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";

redirect_if_not(USER_ADMIN);

if (isset($_POST["submit"])) {
	$res = set_laywer_statuses($_POST["lawyer"]);
	if ($res !== true) {
		err_str("Unable to set status for all specified lawyers.");
	} else {
		msg_str("Set status successfully for all lawyers.");
	}
}

$waiting = get_waiting_lawyers();

basic_setup();
show_html_start_block(PageIndex::AdminDashboard);
show_messages(); ?>
	<h1>Administrator Dashboard</h1>
	<h3>Welcome administrator <?= $_COOKIE[USER_EMAIL] ?></h3>
	<form class="form-card bg-gradient--soft-grass" action="admin_dashboard.php" method="post">
		<div class="form-title">Waiting lawyers</div>
		<table class="form table table-sm">
			<tr>
				<th>❓</th>
				<th>✔️</th>
				<th>❌</th>
				<th>Photo</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Address</th>
				<th>Certification Link</th>
			</tr>
			<?php
			/** @var LawyerDetails $waiting_lawyer */
			foreach ($waiting as $waiting_lawyer) { ?>
				<tr>
					<td>
						<input
							class="form-check-input" type="radio" name="lawyer[<?= $waiting_lawyer->id ?>]" value="-"
							checked>
					</td>
					<td>
						<input
							class="form-check-input" type="radio" name="lawyer[<?= $waiting_lawyer->id ?>]" value="y">
					</td>
					<td>
						<input
							class="form-check-input" type="radio" name="lawyer[<?= $waiting_lawyer->id ?>]" value="n">
					</td>
					<td>
						<?php if ($waiting_lawyer->photo_path != "") { ?>
							<img src="<?= $waiting_lawyer->photo_path ?>" alt="Photo" width="150">
						<?php } ?>
					</td>
					<td><?= $waiting_lawyer->name ?></td>
					<td><?= $waiting_lawyer->email ?></td>
					<td><?= $waiting_lawyer->phone ?></td>
					<td>
						<pre class="p-0 m-0"><?= $waiting_lawyer->address ?></pre>
					</td>
					<td><a href="<?= $waiting_lawyer->certification_link ?>" class="link-dark">Certification</a></td>
				</tr>
			<?php }
			unset($waiting_lawyer);
			?>
		</table>
		<div class="form-footer sticky-bottom bg-success">
			<button type="submit" name="submit" value="y" class="btn btn-sm btn-tertiary">Ok</button>
		</div>
	</form>
<?php show_html_end_block();
