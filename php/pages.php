<?php
const TITLE_DEFAULT = "Justice Firm";
abstract class PageIndex {
	const None            = -1;
	const Home            = self::None + 1;
	const RegisterClient  = self::Home + 1;
	const SignIn          = self::RegisterClient + 1;
	const SignOut         = self::SignIn + 1;
	const ClientDashboard = self::SignOut + 1;
	const LawyerDashboard = self::ClientDashboard + 1;
	const AdminDashboard  = self::LawyerDashboard + 1;
	const SearchLawyer    = self::AdminDashboard + 1;


	private function __construct() {

	}
}

class Page {
	public $path;
	public $name;
	public $shows_on_header;
	public $requires_no_user;
	public $requires_user;

	public function __construct($path, $name, $shows_on_header, $requires_no_user = false, $requires_user = false) {
		$this->path             = (string)$path;
		$this->name             = (string)$name;
		$this->shows_on_header  = $shows_on_header === true;
		$this->requires_no_user = $requires_no_user === true;
		$this->requires_user    = $requires_user;
	}

	public function withId($id): string {
		$ids = urlencode((string)$id);
		return "$this->path?id=$ids";
	}
}

$PAGES = [
	PageIndex::Home            => new Page(
		"index.php",
		"Home",
		true
	),
	PageIndex::SearchLawyer         => new Page(
		"search_lawyer.php",
		"Search Lawyer",
		true,
	),
	PageIndex::RegisterClient  => new Page(
		"register.php",
		"Register",
		true,
		true,
	),
	PageIndex::ClientDashboard => new Page(
		"client_dashboard.php",
		"Client Dashboard",
		true,
		false,
		USER_CLIENT
	),
	PageIndex::LawyerDashboard => new Page(
		"lawyer_dashboard.php",
		"Lawyer Dashboard",
		true,
		false,
		USER_LAWYER
	),
	PageIndex::AdminDashboard  => new Page(
		"admin_dashboard.php",
		"Admin Dashboard",
		true,
		false,
		USER_ADMIN
	),
	PageIndex::SignIn          => new Page(
		"sign_in.php",
		"Sign In",
		true,
		true,
	),
	PageIndex::SignOut         => new Page(
		"sign_out.php",
		"Sign Out",
		true,
		false,
		true,
	),
];
