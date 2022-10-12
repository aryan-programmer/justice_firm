<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";

basic_setup();
show_html_start_block(PageIndex::Home);
show_messages(); ?>
	<div class="container-sm row">
		<div class="text-center w-fit-content mx-auto col-md-9">
			<h1 class="text-center">Justice Firm</h1>
			<p>Justice Firm is a website for lawyers and for clients to access their service.</p>
			<p>Justice Firm is a website where lawyers can register and clients/people/corporations can hire them according to their needs and requirement. </p>
			<a href="<?= Page::$pages[PageIndex::SearchLawyer]->path ?>">Search for a lawyer now!</a>
		</div>
	</div>
<?php show_html_end_block();
