<?php require_once "php/common.php";
require_once "php/templates.php";

redirect_if_signed_in();

$email = "";
if (isset($_POST["submit"])) {
	$email    = $_POST["email"] ?? "";
	$password = $_POST["password"] ?? "";

	if ($email === "" || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		err_str("Invalid email");
	}

	if ($password === "") {
		err_str("No password specified");
	}

	if (count(errs()) === 0) {
		$res = sign_in($_POST["email"], $_POST["password"]);
		if($res !== true){
			err_str($res);
		} else {
			msg_str("Signed in sucessfully.");
			switch_location($PAGES[PageIndex::Home]->path);
		}
	}
}

basic_setup();
show_html_start_block(PageIndex::SignIn);
show_messages(); ?>
	<form class="form-card my-3" action="#" enctype="multipart/form-data" method="post">
		<div class="form-title">
			Sign In
		</div>
		<div class="form">
			<?php
			form_input("Email", "email", "email", "email", true, $email);
			form_input("Password", "password", "password", "password", true);
			?>
		</div>
		<div class="form-footer">
			<button type="submit" name="submit" value="y" class="btn btn-lg btn-tertiary">Sign In</button>
		</div>
	</form>
<?php show_html_end_block();

