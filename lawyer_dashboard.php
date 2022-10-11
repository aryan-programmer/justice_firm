<?php require_once "php/common.php";
require_once "php/templates.php";

basic_setup();
show_html_start_block(PageIndex::LawyerDashboard);
show_messages(); ?>
	<div class="text-center w-fit-content mx-auto">
		<h1 class="text-center">Justice Firm</h1>
		<?=USER_TYPES[$_SESSION[USER_TYPE]]." ".$_COOKIE[USER_EMAIL]?>
	</div>
<?php show_html_end_block();
