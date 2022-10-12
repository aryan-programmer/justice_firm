<?php require_once "consts.php";

class UserDetails {
	public $id;
	public $name;
	public $email;
	public $phone;
	public $address;
	public $photo_path;

	function __construct(
		$id,
		$name,
		$email,
		$phone,
		$address,
		$photo_path) {
		$this->id         = $id;
		$this->name       = $name;
		$this->email      = $email;
		$this->phone      = $phone;
		$this->address    = $address;
		$this->photo_path = $photo_path;
	}
}

class LawyerDetails extends UserDetails {
	public $latitude;
	public $longitude;
	public $certification_link;
	public $status;
	public $distance;

	function __construct(
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
		$distance = 0) {
		parent::__construct($id, $name, $email, $phone, $address, $photo_path);
		$this->latitude           = (double)$latitude;
		$this->longitude          = (double)$longitude;
		$this->certification_link = $certification_link;
		$this->status             = $status;
		$this->distance           = (double)$distance;
	}
}

class Appointment {
	public $a_id;
	public $c_id;
	public $c_name;
	public $l_id;
	public $l_name;
	public $description;
	public $group_id;
	public $timestamp;
	public $opened_on;
	public $status;
	public $oth_id;
	public $oth_name;

	public function __construct(
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
		$oth_id,
		$oth_name
	) {
		$this->a_id        = $a_id;
		$this->c_id        = $c_id;
		$this->c_name      = $c_name;
		$this->l_id        = $l_id;
		$this->l_name      = $l_name;
		$this->description = $description;
		$this->group_id    = $group_id;
		$this->timestamp   = $timestamp;
		$this->opened_on   = $opened_on;
		$this->status      = $status;
		$this->oth_id      = $oth_id;
		$this->oth_name    = $oth_name;
	}


}
