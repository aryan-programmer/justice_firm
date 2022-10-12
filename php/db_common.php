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

$uid = null;

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
	if (isset($uid) && $uid !== 0) return true;
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
WHERE `l`.`status` = 'waiting'
LIMIT 100;");
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

function get_lawyers_filtered($name, $address, $lat, $lon, $no_dist) {
	global $conn;
	$name    = "%$name%";
	$address = "%$address%";
	try {
		if ($no_dist) {
			$stmt = $conn->prepare("SELECT `u`.`id`,
       `u`.`name`,
       `u`.`email`,
       `u`.`phone`,
       `u`.`address`,
       `u`.`photo_path`,
       `l`.`latitude`,
       `l`.`longitude`,
       `l`.`certification_link`,
       0 AS `distance`
FROM `lawyer` `l`
JOIN `user` `u`
     ON `u`.`id` = `l`.`id`
WHERE `l`.`status` = 'confirmed'
  AND `u`.`name` LIKE ?
  AND `u`.`address` LIKE ?
ORDER BY `name` ASC
LIMIT 100;");
			$stmt->bind_param("ss", $name, $address);
		} else {
			$stmt = $conn->prepare("SELECT `u`.`id`,
       `u`.`name`,
       `u`.`email`,
       `u`.`phone`,
       `u`.`address`,
       `u`.`photo_path`,
       `l`.`latitude`,
       `l`.`longitude`,
       `l`.`certification_link`,
       ST_DISTANCE_SPHERE(POINT(`l`.`latitude`, `l`.`longitude`), POINT(?, ?)) AS `distance`
FROM `lawyer` `l`
JOIN `user` `u`
     ON `u`.`id` = `l`.`id`
WHERE `l`.`status` = 'confirmed'
  AND `u`.`name` LIKE ?
  AND `u`.`address` LIKE ?
ORDER BY `distance` ASC
LIMIT 100;");
			$stmt->bind_param("ddss", $lat, $lon, $name, $address);
		}
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
			$distance,
		);
		$res = [];
		while ($stmt->fetch()) {
			$res[] = new LawyerDetails(
				$id,
				$name,
				$email,
				$phone,
				$address,
				$photo_path,
				$latitude,
				$longitude,
				$certification_link,
				LAWYER_STATUS_MAP["y"],
				$distance / 1000
			);
		}
		return $res;
	} catch (mysqli_sql_exception $ex) {
		return [];
	}
}

function get_lawyer_by_id($id) {
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
       `l`.`certification_link`,
       `l`.`status`
FROM `lawyer` `l`
JOIN `user` `u`
     ON `u`.`id` = `l`.`id`
WHERE `u`.`id` = ?;");
		$stmt->bind_param("i", $id);
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
			$status
		);
		if ($stmt->fetch())
			return new LawyerDetails(
				$id,
				$name,
				$email,
				$phone,
				$address,
				$photo_path,
				$latitude,
				$longitude,
				$certification_link,
				$status,
				0
			);
		else return false;
	} catch (mysqli_sql_exception $ex) {
		return false;
	}
}

function get_user_by_id($id) {
	global $conn;

	try {
		$stmt = $conn->prepare("SELECT `u`.`id`,
       `u`.`name`,
       `u`.`email`,
       `u`.`phone`,
       `u`.`address`,
       `u`.`photo_path`
FROM `user` `u`
WHERE `id` = ?;");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($id,
			$name,
			$email,
			$phone,
			$address,
			$photo_path
		);
		if ($stmt->fetch())
			return new UserDetails(
				$id,
				$name,
				$email,
				$phone,
				$address,
				$photo_path
			);
		else return false;
	} catch (mysqli_sql_exception $ex) {
		return false;
	}
}

function open_appointment_request($lawyer_id, $description, $unix_ts) {
	global $conn, $uid;

	redirect_if_not(USER_CLIENT);

	$conn->begin_transaction();
	try {
		$stmt = $conn->prepare("INSERT INTO `group`(`client_id`, `lawyer_id`)
VALUES (?, ?);");
		$stmt->bind_param("ii", $uid, $lawyer_id);
		$stmt->execute();

		$group_id  = $conn->insert_id;
		$timestamp = $unix_ts ? date(DATE_MYSQL, $unix_ts) : null;

		$stmt = $conn->prepare("INSERT INTO `appointment`(`client_id`, `lawyer_id`, `group_id`, `description`, `timestamp`)
VALUES (?, ?, ?, ?, ?);");
		$stmt->bind_param("iiiss", $uid, $lawyer_id, $group_id, $description, $timestamp);
		$stmt->execute();
		$conn->commit();
		return true;
	} catch (mysqli_sql_exception $ex) {
		$conn->rollback();
		return $ex->getMessage();
	}
}

function get_appointments($status, $status_not = false, $order_by_opened_on = false) {
	global $conn, $uid;

	redirect_if_not_signed_in();

	try {
		$query = "SELECT `a`.`id`,
       `c`.`id`,
       `c`.`name`,
       `l`.`id`,
       `l`.`name`,
       `a`.`description`,
       `a`.`group_id`,
       `a`.`timestamp`,
       `a`.`opened_on`,
       `a`.`status`
FROM `appointment` `a`
JOIN `user` `c`
     ON `c`.`id` = `a`.`client_id`
JOIN `user` `l`
     ON `l`.`id` = `a`.`lawyer_id`";

		switch ($_SESSION[USER_TYPE]) {
		case USER_CLIENT:
			$query .= " WHERE `c`.`id` = ?";
			break;
		case USER_LAWYER:
			$query .= " WHERE `l`.`id` = ?";
			break;
		default:
			err_str("You must be a client or lawyer to use this feature");
			redirect_page(PageIndex::Home);
			return false;
		}

		if ($status_not) {
			$query .= " AND `a`.`status` != ?";
		} else {
			$query .= " AND `a`.`status` = ?";
		}

		if ($order_by_opened_on) {
			$query .= " ORDER BY `a`.`opened_on`";
		} else {
			$query .= " ORDER BY `a`.`timestamp`";
		}
		$query .= ' LIMIT 20;';

		$stmt = $conn->prepare($query);
		$stmt->bind_param("is", $uid, $status);
		$stmt->execute();
		$stmt->bind_result(
			$a_id,
			$c_id,
			$c_name,
			$l_id,
			$l_name,
			$description,
			$group_id,
			$timestamp,
			$opened_on,
			$status,
		);
		$res = [];
		if ($_SESSION[USER_TYPE] === USER_CLIENT) {
			while ($stmt->fetch()) {
				$res[] = new Appointment(
					$a_id,
					$c_id,
					$c_name,
					$l_id,
					$l_name,
					$description,
					$group_id,
					$timestamp,
					$opened_on,
					$status,
					$l_id,
					$l_name
				);
			}
		} else {
			while ($stmt->fetch()) {
				$res[] = new Appointment(
					$a_id,
					$c_id,
					$c_name,
					$l_id,
					$l_name,
					$description,
					$group_id,
					$timestamp,
					$opened_on,
					$status,
					$c_id,
					$c_name
				);
			}
		}
		return $res;
	} catch (mysqli_sql_exception $ex) {
		return [];
	}
}

function get_appointment_by_id($id) {
	global $conn, $uid;

	redirect_if_not_signed_in();

	try {
		$query = "SELECT `a`.`id`,
       `c`.`id`,
       `c`.`name`,
       `l`.`id`,
       `l`.`name`,
       `a`.`description`,
       `a`.`group_id`,
       `a`.`timestamp`,
       `a`.`opened_on`,
       `a`.`status`
FROM `appointment` `a`
JOIN `user` `c`
     ON `c`.`id` = `a`.`client_id`
JOIN `user` `l`
     ON `l`.`id` = `a`.`lawyer_id`
WHERE `a`.`id` = ?";

		$stmt = $conn->prepare($query);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result(
			$a_id,
			$c_id,
			$c_name,
			$l_id,
			$l_name,
			$description,
			$group_id,
			$timestamp,
			$opened_on,
			$status,
		);
		if (!$stmt->fetch()) {
			return false;
		}
		if ($uid !== $l_id && $uid !== $c_id) {
			redirect_with_error("You are not allowed access the details of that appointment");
		}
		return new Appointment(
			$a_id,
			$c_id,
			$c_name,
			$l_id,
			$l_name,
			$description,
			$group_id,
			$timestamp,
			$opened_on,
			$status,
			$l_id,
			$l_name
		);
	} catch (mysqli_sql_exception $ex) {
		return false;
	}
}

function set_appointment_status($id, $status, $unix_ts = null): bool {
	global $conn, $uid;

	redirect_if_not_signed_in();

	try {
		if ($unix_ts === null) {
			$stmt = $conn->prepare("UPDATE `appointment`
SET `status`    = ?
WHERE `id` = ?;");
			$stmt->bind_param("si", $status, $id);
		} else {
			$stmt      = $conn->prepare("UPDATE `appointment`
SET `status`    = ?,
    `timestamp` = ?
WHERE `id` = ?;");
			$timestamp = date(DATE_MYSQL, $unix_ts);
			$stmt->bind_param("ssi", $status, $timestamp, $id);
		}
		return $stmt->execute();
	} catch (mysqli_sql_exception $ex) {
		return false;
	}
}
