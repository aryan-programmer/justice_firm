<?php require_once "db_common.php";
require_once "interaction_common.php";
require_once "pages.php";
require_once "html_templates.php";
$h_title      = TITLE_DEFAULT;
$h_head       = function () {
};
$h_body_end   = function () {
};
$h_show_links = function ($currPageIdx) {
	global $uid;
	/** @var Page $page */
	foreach (Page::$pages as $idx => $page) {
		if (!$page->shows_on_header) continue;
		if ($page->requires_user !== false) {
			if ($uid === null) {
				continue;
			}
			if ($page->requires_user !== USER_ANY) {
				if (is_array($page->requires_user)) {
					if ($_SESSION[USER_TYPE] === $page->requires_user[1]) {
						continue;
					}
				} elseif ($_SESSION[USER_TYPE] !== $page->requires_user) {
					continue;
				}
			}
		}
		if ($page->requires_no_user && $uid !== null) continue;
		if ($idx === $currPageIdx) { ?>
			<li class="nav-item">
				<a class="nav-link active fw-bold" aria-current="page" href="#"><?= h($page->name) ?></a>
			</li>
		<?php } else { ?>
			<li class="nav-item">
				<a class="nav-link fw-normal" href="<?= $page->path ?>"><?= h($page->name) ?></a>
			</li>
		<?php } ?>
	<?php }
};

function form_input_custom(string $text, string $id, $element) { ?>
	<div class="mb-3 row">
	<label for="<?= $id ?>" class="col-sm-2 col-form-label"><?= $text ?></label>
	<div class="col-sm-10">
		<?php if (is_callable($element)) $element(); else echo $element; ?>
	</div>
	</div><?php
}

function form_input(
	string $text,
	string $id,
	string $name,
	string $type = "text",
	       $required = false,
	string $default = "") { ?>
	<div class="mb-3 row">
	<label for="<?= $id ?>" class="col-sm-2 col-form-label"><?= $text ?></label>
	<div class="col-sm-10">
		<input
			id="<?= $id ?>" name="<?= $name ?>" type="<?= $type ?>"
			class="form-control" <?= $required ? "required" : "" ?>
			value="<?= $default ?>"
		>
	</div>
	</div><?php
}

// region ...Errors & Messages
function get_stored_errors(): array {
	if (isset($_SESSION["errors"]) && is_array($_SESSION["errors"]) && count($_SESSION["errors"]) > 0) {
		$ret                = $_SESSION["errors"];
		$_SESSION["errors"] = [];
		return $ret;
	}
	return [];
}

function store_errors($errors) {
	$_SESSION["errors"] = $errors;
}

function get_stored_messages(): array {
	if (isset($_SESSION["messages"]) && is_array($_SESSION["messages"]) && count($_SESSION["messages"]) > 0) {
		$ret                  = $_SESSION["messages"];
		$_SESSION["messages"] = [];
		return $ret;
	}
	return [];
}

function store_messages($messages) {
	$_SESSION["messages"] = $messages;
}

$errors = get_stored_errors();
function err_str($e) {
	global $errors;
	$errors[] = $e;
}

function errs() {
	global $errors;
	return $errors;
}

$messages = get_stored_messages();
function msg_str($m) {
	global $messages;
	$messages[] = $m;
}

// endregion Errors & Messages

function redirect_path($loc) {
	global $errors, $messages;
	store_errors($errors);
	store_messages($messages);
	header("Location: $loc");
	die();
}

function redirect_page($page) {
	global $errors, $messages;
	store_errors($errors);
	store_messages($messages);
	$loc = Page::$pages[$page];
	header("Location: $loc->path");
	die();
}

function redirect_if_not($user_t): void {
	if (restore_user_id() === false || $_SESSION[USER_TYPE] !== $user_t) {
		err_str("You must be a " . USER_TYPES[$user_t] . " to use that feature.");
		redirect_page(PageIndex::Home);
	}
}

function redirect_if_signed_in(): void {
	if (isset($_SESSION[USER_ID])) {
		msg_str("Already signed in.");
		redirect_page(PageIndex::Home);
	}
}

function redirect_if_not_signed_in(): void {
	if (restore_user_id() === false) {
		$errors = [];
		err_str("You haven't signed in yet.");
		redirect_page(PageIndex::Home);
		die();
	}
}

function redirect_with_error($str, $page = PageIndex::Home) {
	err_str($str);
	redirect_page($page);
	die();
}

function basic_setup(bool $force_user = false) {
	$r = restore_user_id();
	if ($force_user && $r === false) {
		$errors = [];
		err_str("You haven't signed in yet.");
		redirect_page(PageIndex::Home);
		die();
	}
}

function show_html_start_block($currPageIdx = PageIndex::None) {
	global $h_title, $h_head, $h_show_links;
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<title><?= $h_title . " - " . Page::$pages[$currPageIdx]->name ?></title>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link
			href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
			rel="stylesheet">
		<link
			href="https://fonts.googleapis.com/css?family=Inconsolata:400,700&display=swap&subset=latin-ext,vietnamese"
			rel="stylesheet">
		<!--|Quicksand:300,400,500,600,700-->
		<link href="style/style.css" type="text/css" rel="stylesheet" />
		<?php $h_head(); ?>
	</head>
	<body>
	<header class="sticky-top">
		<nav class="navbar navbar-expand-lg bg-quaternary">
			<div class="container-fluid">
				<a class="navbar-brand" href="#"><?= TITLE_DEFAULT ?></a>
				<button
					class="navbar-toggler" type="button" data-bs-toggle="collapse"
					data-bs-target="#navbarSupportedContent"
					aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
						<?php $h_show_links($currPageIdx); ?>
					</ul>
				</div>
			</div>
		</nav>
	</header>

	<main class="container mt-2">
<?php }

function show_html_end_block() {
	global $h_body_end; ?>
	</main>
	<script
		src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
		crossorigin="anonymous"></script>
	<script>
	const tooltipTriggerList = document.querySelectorAll("[data-bs-toggle=\"tooltip\"]");
	const tooltipList        = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
	</script>
<?php $h_body_end(); ?>
	</body>
	</html>
<?php }

function show_messages() {
	global $errors, $messages;
	if (count($errors) > 0) { ?>
		<div class="alert alert-danger">
			<h5>Error(s):</h5>
			<?php foreach ($errors as $error) { ?>
				<?= $error ?><br />
			<?php } ?>
		</div>
	<?php }
	if (count($messages) > 0) { ?>
		<div class="alert alert-info">
			<h5>Message(s):</h5>
			<?php foreach ($messages as $message) { ?>
				<?= $message ?><br />
			<?php } ?>
		</div>
	<?php }
}
