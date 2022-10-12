<?php require_once "db_common.php";

function show_user_card(UserDetails $user, $prefix = "") {
	?>
	<div class="card mb-3 bg-gradient--orange-juice">
		<div class="row g-0">
			<div class="col-md-3 d-flex align-items-center rounded rounded-right-0 add-bg-noise">
				<?php if ($user->photo_path != "") { ?>
					<img
						src="<?= $user->photo_path ?>"
						style="max-height: 150px"
						class="img-fluid rounded mx-auto"
						alt="Photo">
				<?php } else { ?>
					<p class="mx-auto">No image available</p>
				<?php } ?>
			</div>
			<div class="col-md-9">
				<h5 class="card-header add-bg-noise rounded-left-0"><?= $prefix . $user->name ?></h5>
				<div class="card-body row">
					<p class="card-text col-md-6 col-lg-4">
						Email: <?= $user->email ?>
					</p>
					<p class="card-text col-md-6 col-lg-4">
						Phone: <?= $user->phone ?>
					</p>
					<pre class="card-text col-md-6 col-lg-4">
Address:
<?= $user->address ?></pre>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function show_lawyer_card(LawyerDetails $lawyer, $prefix = "", $no_open_request = false) {
	?>
	<div class="card mb-3 bg-gradient--grown-early">
		<div class="row g-0">
			<div class="col-md-3 d-flex align-items-center rounded rounded-right-0 add-bg-noise">
				<?php if ($lawyer->photo_path != "") { ?>
					<img
						src="<?= $lawyer->photo_path ?>"
						style="max-height: 150px"
						class="img-fluid rounded mx-auto"
						alt="Photo">
				<?php } else { ?>
					<p class="mx-auto">No image available</p>
				<?php } ?>
			</div>
			<div class="col-md-9">
				<h5 class="card-header add-bg-noise rounded-left-0"><?= $prefix . $lawyer->name ?></h5>
				<div class="card-body row">
					<p class="card-text col-md-6">
						Email: <?= $lawyer->email ?><br />
						Phone: <?= $lawyer->phone ?><br />
						Latitude & Longitude: (<?= round($lawyer->latitude, 3) ?>, <?= round($lawyer->longitude, 3) ?>)<br />
						<?php if (!eq_epsilon($lawyer->distance, 0)) { ?>
							Spherical distance from current location: <?= round($lawyer->distance, 1) ?> km
						<?php } ?>
					</p>
					<div class="col-md-6">
						<pre class="card-text">
Office Address:
<?= $lawyer->address ?></pre>
						<a
							href="<?= $lawyer->certification_link ?>"
							class="link card-link link-tertiary fw-normal">View certification</a>
					</div>
				</div>
				<?php if (!$no_open_request && isset($_SESSION[USER_TYPE]) && $_SESSION[USER_TYPE] === USER_CLIENT) { ?>
					<div class="card-footer add-bg-noise rounded-left-0">
						<a
							href="./open_appointment.php?lawyer_id=<?= $lawyer->id ?>&lawyer_name=<?= $lawyer->name ?>"
							class="link card-link link-secondary fw-bolder">Open an appointment request</a>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}

