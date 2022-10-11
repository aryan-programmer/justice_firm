<?php require_once "consts.php";

class LawyerDetails {
	public $id;
	public $name;
	public $email;
	public $phone;
	public $address;
	public $photo_path;
	public $latitude;
	public $longitude;
	public $certification_link;
	public $status;

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
		$status) {
		$this->id                 = $id;
		$this->name               = $name;
		$this->email              = $email;
		$this->phone              = $phone;
		$this->address            = $address;
		$this->photo_path         = $photo_path;
		$this->latitude           = $latitude;
		$this->longitude          = $longitude;
		$this->certification_link = $certification_link;
		$this->status             = $status;
	}
}
