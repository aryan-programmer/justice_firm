<?php

function get_registration_common() {
	$name       = $_POST["name"] ?? "";
	$email      = $_POST["email"] ?? "";
	$password   = $_POST["password"] ?? "";
	$rePassword = $_POST["rePassword"] ?? "";
	$address    = $_POST["address"] ?? "";
	$phone      = $_POST["phone"] ?? "";

	if ($name === "") {
		err_str("No name specified");
	}

	if ($email === "" || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		err_str("Invalid email");
	}

	if ($password === "") {
		err_str("No password specified");
	}

	if ($rePassword === "") {
		err_str("The password wasn't re-entered");
	}

	if ($password !== $rePassword) {
		err_str("The passwords don't match");
	}

	if ($phone !== "") {
		if (!preg_match(PHONE_REGEX, $phone)) {
			err_str("The phone number should be of the form 1234567890, 123 456 7890, or 123-456-7890");
		}
	}

	$photo      = $_FILES["photo"];
	$photo_file = null;
	if (isset($photo["name"]) && $photo["name"] !== '') {
		$target_dir = "./upload/images/";
		$photo_file = $target_dir . date('YmdHis_') . basename($photo["name"]);
		$check      = getimagesize($photo["tmp_name"]);
		$ok         = false;
		if ($check === false) {
			err_str("You didn't upload a valid photo.");
		} elseif (file_exists($photo_file)) {
			err_str("Server error.");
		} elseif ($photo["size"] > MAX_FILE_SIZE) {
			err_str(INVALID_FILE_SIZE_ERR_MSG);
		} elseif (!in_array($check["mime"], VALID_IMAGE_MIME_TYPES)) {
			err_str($check["mime"].INVALID_IMAGE_ERR_MSG);
		} else {
			if (count(errs()) > 0) {
				return [false, $name, $email, $password, $phone, $address, $photo_file];
			}
			if (!move_uploaded_file($photo["tmp_name"], $photo_file)) {
				err_str("Failed to upload photo");
			} else {
				$ok = true;
			}
		}
		if (!$ok) {
			return [false, $name, $email, $password, $phone, $address, $photo_file];
		}
	}

	return [count(errs()) === 0, $name, $email, $password, $phone, $address, $photo_file];
}

function lawyer_registration() {
	[$ok, $name, $email, $password, $phone, $address, $photo_file] = get_registration_common();

	$lat = $_POST["latitude"] ?? "";
	$lon = $_POST["longitude"] ?? "";
	$specializations = $_POST["specializations"] ?? [];

	if ($lat === "" || !is_numeric($lat)) {
		err_str("Enter a latitude");
	} else {
		$lat = floatval($lat);
	}

	if ($lon === "" || !is_numeric($lon)) {
		err_str("Enter a longitude");
	} else {
		$lon = floatval($lon);
	}

	$certi      = $_FILES["certification"];
	$certi_file = null;
	if (isset($certi["name"]) && $certi["name"] !== '') {
		$target_dir = "./upload/certifications/";
		$certi_file = $target_dir . date('YmdHis_') . basename($certi["name"]);
		$ok         = false;
		if (file_exists($certi_file)) {
			err_str("Server error.");
		} elseif ($certi["size"] > MAX_FILE_SIZE) {
			err_str(INVALID_FILE_SIZE_ERR_MSG);
		} else {
			if (count(errs()) > 0) {
				return [false, $name, $email, $password, $phone, $address, $photo_file, $lat, $lon, $certi_file, $specializations];
			}
			if (!move_uploaded_file($certi["tmp_name"], $certi_file)) {
				err_str("Failed to upload certificate file");
			} else {
				$ok = true;
			}
		}
		if (!$ok) {
			return [false, $name, $email, $password, $phone, $address, $photo_file, $lat, $lon, $certi_file, $specializations];
		}
	}

	return [count(errs()) === 0, $name, $email, $password, $phone, $address, $photo_file, $lat, $lon, $certi_file, $specializations];
}

