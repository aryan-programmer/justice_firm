<?php require_once "php/common.php";
require_once "php/templates.php";
require_once "php/registration_common.php";

redirect_if_signed_in();

$name            = $email = $phone = $address = $lat = $lon = "";
$specializations = [];
if (isset($_POST["submit"])) {
	[$res, $name, $email, $password, $phone, $address, $photo_path, $lat, $lon, $certi_file, $specializations] = lawyer_registration();
	if ($res !== false) {
		$res = sign_up_lawyer($name, $email, $phone, $address, $password, $photo_path, $lat, $lon, $certi_file, $specializations);
		if ($res !== true) {
			err_str($res);
		} else {
			msg_str("Registered as a lawyer sucessfully.");
			switch_location($PAGES[PageIndex::Home]->path);
		}
	}
}

$h_body_end = function () { ?>
	<script src="./js/autofill_lat_lon.js"></script>
	<script>
	autofill_lat_lon(false);
	</script>
<?php };

basic_setup();
show_html_start_block(PageIndex::RegisterClient);
show_messages(); ?>
	<form class="form-card my-3" action="#" enctype="multipart/form-data" method="post">
		<div class="form-title">
			Register as a lawyer
		</div>
		<div class="form">
			<?php
			echo MAX_FILE_SIZE_FORM_INPUT;
			form_input("Name", "name", "name", "text", true, $name);
			form_input("Email", "email", "email", "email", true, $email);
			form_input("Password", "password", "password", "password", true);
			form_input("Retype Password", "rePassword", "rePassword", "password", true);
			form_input("Phone", "phone", "phone", "tel", false, $phone);
			form_input_custom("Address", "address", <<<TAG
<textarea id="address" name="address" class="form-control">$address</textarea>
TAG
			);
			form_input("Photo", "photo", "photo", "file", false);
			form_input_custom("Latitude", "latitude", <<<TAG
<input id="latitude" name="latitude" class="form-control" type="number" step="0.000000001" required value="$lat"/>
TAG
			);
			form_input_custom("Longitude", "longitude", <<<TAG
<input id="longitude" name="longitude" class="form-control" type="number" step="0.0000000001" required value="$lon"/>
TAG
			);
			form_input("Certification", "certification", "certification", "file", true);
			form_input_custom("Specializations", "specializations", function () {
				global $specializations; ?>
				<div id="specializations" class="dropdown">
					<button
						type="button"
						class="btn btn-secondary dropdown-toggle"
						data-bs-toggle="dropdown"
						aria-expanded="false"
						data-bs-auto-close="outside">
						Select case specializations
					</button>
					<ul class="dropdown-menu p-4"><?php
						foreach (get_case_types() as $id => $type) { ?>
							<li class="form-check">
								<input
									id="specializations-<?= $id ?>" class="form-check-input"
									name="specializations[<?= $id ?>]" type="checkbox" value="y"
									<?php if (isset($specializations[$id])) echo "checked"; ?>
								/>
								<label for="specializations-<?= $id ?>" class="form-check-label"><?= $type ?></label>
							</li>
						<?php } ?>
					</ul>
				</div>
			<?php });
			?>
		</div>
		<div class="form-footer">
			<button type="submit" name="submit" value="y" class="btn btn-lg btn-tertiary">Register</button>
			<button
				type="button" class="btn btn-sm btn-quaternary float-end"
				onclick="autofill_lat_lon(true)">Autofill latitude & longitude
			</button>
		</div>
	</form>
<?php show_html_end_block();

