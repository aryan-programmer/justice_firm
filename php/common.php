<?php session_start();
require_once "consts.php";
require_once "classes.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli(
	"p:" . $_SERVER["DATABASE_HOST"],
	$_SERVER["DATABASE_USER"],
	$_SERVER["DATABASE_PASSWORD"],
	"justice_firm"
);
if ($conn->connect_errno != 0) {
	die("Failed to connect");
}

//if (isset($_SESSION[USER_ID_SAVE_COOKIE_FROM_SESS]) && $_SESSION[USER_ID_SAVE_COOKIE_FROM_SESS] === true) {
//	unset($_SESSION[USER_ID_SAVE_COOKIE_FROM_SESS]);
//}

$uid = null; // DEBUG

function save_user($user_id, $email, $password, $type, $save_cookie = true) {
	global $uid;
	$uid                 = $user_id;
	$_SESSION[USER_ID]   = $uid;
	$_SESSION[USER_TYPE] = $type;
//	$_SESSION[USER_ID_SAVE_COOKIE_FROM_SESS] = true;
	if ($save_cookie) {
		setcookie(USER_EMAIL, $email, time() + COOKIE_EXPIRY_TIME);
		setcookie(USER_PASSWORD, $password, time() + COOKIE_EXPIRY_TIME);
	}
}

function restore_user_id(): bool {
	global $uid;
	if (isset($_SESSION[USER_ID])) {
		$uid = $_SESSION[USER_ID];
		return true;
	} elseif (isset($_COOKIE[USER_EMAIL]) && isset($_COOKIE[USER_PASSWORD])) {
		return sign_in($_COOKIE[USER_EMAIL], $_COOKIE[USER_PASSWORD], false) === true;
	}
	return false;
}

function unset_user_id() {
	global $uid;
	$uid                    = null;
	$_SESSION[USER_ID]      = null;
	$_SESSION[USER_TYPE]    = null;
	$_COOKIE[USER_EMAIL]    = null;
	$_COOKIE[USER_PASSWORD] = null;
	unset($_SESSION[USER_ID]);
	unset($_SESSION[USER_TYPE]);
	unset($_COOKIE[USER_EMAIL]);
	unset($_COOKIE[USER_PASSWORD]);
	setcookie(USER_EMAIL, null, time() - 1);
	setcookie(USER_PASSWORD, null, time() - 1);
}

function sign_in($email, $password, $save_cookie = true) {
	global $conn;

	try {
		$stmt = $conn->prepare("SELECT `id`, `email`, `password_hash`, `type`
FROM `user`
WHERE `email` = ?;");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$res   = $stmt->get_result();
		$assoc = $res->fetch_all(MYSQLI_ASSOC);
		if (count($assoc) < 1) {
			return "Email not used to sign up a user";
		}
		$db_pwd_hash = $assoc[0]["password_hash"];
		if (!password_verify($password, $db_pwd_hash)) {
			return "Invalid password";
		}
		$uid       = (int)$assoc[0]["id"];
		$user_type = USER_TYPE_TO_IDX[$assoc[0]["type"]];
		save_user($uid, $email, $password, $user_type, $save_cookie);
		return true;
	} catch (mysqli_sql_exception $ex) {
		return $ex->getMessage();
	}
}

function sign_up_client($name, $email, $phone, $address, $password, $photo_path) {
	global $conn;

	$conn->begin_transaction();
	try {
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		$stmt          = $conn->prepare("INSERT INTO `user`(`name`, `email`, `phone`, `address`, `password_hash`, `photo_path`, `type`)
VALUES (?, ?, ?, ?, ?, ?, 'client');");
		$stmt->bind_param("ssssss", $name, $email, $phone, $address, $password_hash, $photo_path);
		$stmt->execute();
		$uid  = $conn->insert_id;
		$stmt = $conn->prepare("INSERT INTO `client`(`id`)
VALUES (?);");
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$conn->commit();
		save_user($uid, $email, $password, USER_CLIENT, true);
		return true;
	} catch (mysqli_sql_exception $ex) {
		$conn->rollback();
		return $ex->getMessage();
	}
}

function sign_up_lawyer($name, $email, $phone, $address, $password, $photo_path, $lat, $lon, $certi_path, $specializations) {
	global $conn;

	$conn->begin_transaction();
	try {
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		$stmt          = $conn->prepare("INSERT INTO `user`(`name`, `email`, `phone`, `address`, `password_hash`, `photo_path`, `type`)
VALUES (?, ?, ?, ?, ?, ?, 'lawyer');");
		$stmt->bind_param("ssssss", $name, $email, $phone, $address, $password_hash, $photo_path);
		$stmt->execute();

		$uid  = $conn->insert_id;
		$stmt = $conn->prepare("INSERT INTO `lawyer`(`id`, `latitude`, `longitude`, `certification_link`)
VALUES (?, ?, ?, ?);");
		$stmt->bind_param("idds", $uid, $lat, $lon, $certi_path);
		$stmt->execute();

		$stmt = $conn->prepare("INSERT INTO `lawyer_specialization`(`lawyer_id`, `case_type_id`)
VALUES (?, ?);");
		$stmt->bind_param("ii", $uid, $case_t_id);
		foreach ($specializations as $case_t_id => $specialization_name) {
			$stmt->execute();
		}
		unset($case_t_id);
		unset($specialization_name);

		$conn->commit();
		save_user($uid, $email, $password, USER_LAWYER, true);
		return true;
	} catch (mysqli_sql_exception $ex) {
		$conn->rollback();
		return $ex->getMessage();
	}
}

function get_case_types() {
	global $conn;

	try {
		$stmt = $conn->prepare("SELECT `id`, `name`
FROM `case_type`;");
		$stmt->execute();
		$stmt->bind_result($id,
			$name);
		$res = [];
		while ($stmt->fetch()) {
			$res[(int)$id] = $name;
		}
		return $res;
	} catch (mysqli_sql_exception $ex) {
		return [];
	}
}

function get_waiting_lawyers() {
	global $conn;

	try {
		$stmt = $conn->prepare("SELECT `u`.`id`,
       `u`.`name`,
       `u`.`email`,
       `u`.`phone`,
       `u`.`address`,
       `u`.`photo_path`,
       `l`.`latitude`,
       `l`.`longitude`,
       `l`.`certification_link`
FROM `lawyer` `l`
JOIN `user` `u`
     ON `u`.`id` = `l`.`id`
WHERE `l`.`status` = 'waiting';");
		$stmt->execute();
		$stmt->bind_result($id,
			$name,
			$email,
			$phone,
			$address,
			$photo_path,
			$latitude,
			$longitude,
			$certification_link,
		);
		$res = [];
		while ($stmt->fetch()) {
			$res[] = new LawyerDetails($id, $name, $email, $phone, $address, $photo_path, $latitude, $longitude, $certification_link, "waiting");
		}
		return $res;
	} catch (mysqli_sql_exception $ex) {
		return [];
	}
}

function set_laywer_statuses($statuses) {
	global $conn;

	try {
		$stmt = $conn->prepare("UPDATE `lawyer`
SET `status` = ?
WHERE `id` = ?;");
		$stmt->bind_param('si', $status, $id);
		foreach ($statuses as $id => $sv) {
			$status = LAWYER_STATUS_MAP[$sv];
			$stmt->execute();
		}
		unset($case_t_id);
		unset($specialization_name);
		return true;
	} catch (mysqli_sql_exception $ex) {
		return false;
	}
}
