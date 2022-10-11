<?php require_once "php/common.php";
require_once "php/templates.php";

$name = $lat = $lon = "";


$h_body_end = function () { ?>
	<script src="./js/autofill_lat_lon.js"></script>
<?php };

basic_setup();
show_html_start_block(PageIndex::SearchLawyer);
show_messages(); ?>
	<form class="form-card my-3" action="#" method="get">
		<div class="form-title h4-imp">
			Search for a lawyer
		</div>
		<div class="form py-3">
			<div class="input-group input-group-sm mb-3">
				<label for="name" class="input-group-text">Name</label>
				<input type="text" class="form-control" id="name" name="name" required value="<?= $name ?>">
			</div>
			<div class="input-group input-group-sm">
				<input id="latitude"
					class="form-control" name="latitude" placeholder="Latitude" type="number" step="0.000000001"
					value="<?= $lat ?>">
				<input id="longitude"
					class="form-control" name="longitude" placeholder="Longitude" type="number" step="0.000000001"
					value="<?= $lon ?>">
				<button
					type="button" class="btn btn-sm btn-quaternary"
					onclick="autofill_lat_lon(true)">Autofill
				</button>
			</div>
		</div>
		<div class="form-footer mt-0">
			<button type="submit" name="submit" value="y" class="btn btn-sm btn-tertiary">Search</button>
		</div>
	</form>
<?php show_html_end_block();
