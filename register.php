<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";
require_once "php/interaction_common.php";

redirect_if_signed_in();

[$name, $email, $phone, $address] = ["", "", "", ""];
if (isset($_POST["submit"])) {
	[$res, $name, $email, $password, $phone, $address, $photo_path] = get_registration_common();
	if ($res !== false) {
		$res = sign_up_client($name, $email, $phone, $address, $password, $photo_path);
		if ($res !== true) {
			err_str($res);
		} else {
			msg_str("Registered as a client sucessfully.");
			redirect_page(PageIndex::Home);
		}
	}
}

basic_setup();
show_html_start_block(PageIndex::RegisterClient);
show_messages(); ?>
	<form class="form-card my-3" action="#" enctype="multipart/form-data" method="post">
		<div class="form-title">
			Register as a client
			<a class="card-link link-dark h6-imp" href="register_lawyer.php">Are a lawyer? Register as one.</a>
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
			?>
		</div>
		<div class="form-footer">
			<button type="submit" name="submit" value="y" class="btn btn-lg btn-tertiary">Register</button>
		</div>
	</form>
<?php show_html_end_block();

