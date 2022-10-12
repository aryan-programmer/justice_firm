<?php require_once "php/db_common.php";
require_once "php/main_html_templates.php";

$name = $address = $lat = $lon = "";

$lawyers = null;

$no_dist = false;
if (isset($_GET["submit"])) {
	$name = $_GET["name"] ?? "";
	$address = $_GET["address"] ?? "";
	$lat = $_GET["latitude"] ?? 0;
	$lon = $_GET["longitude"] ?? 0;
	$lat = is_numeric($lat) ? floatval($lat) : 0;
	$lon = is_numeric($lon) ? floatval($lon) : 0;

	if (eq_epsilon($lat, 0) || eq_epsilon($lon, 0)) $no_dist = true;

	$lawyers = get_lawyers_filtered($name, $address, $lat, $lon, $no_dist);
}
//pvar_dump($lawyers);

$h_body_end = function () { ?>
	<script src="./js/autofill_lat_lon.js"></script>
<?php };

basic_setup();
show_html_start_block(PageIndex::SearchLawyer);
show_messages(); ?>
	<form class="form-card my-3 bg-gradient--aqua-splash" action="#" method="get">
		<div class="form-title h4-imp">
			Search for a lawyer
		</div>
		<div class="form py-3">
			<div class="input-group input-group-sm mb-3">
				<label for="name" class="input-group-text">Name</label>
				<input type="text" class="form-control" id="name" name="name" value="<?= $name ?>">
			</div>
			<div class="input-group input-group-sm mb-3">
				<label for="address" class="input-group-text">Address</label>
				<textarea
					type="text" class="form-control" id="address" name="address" rows="1"><?= $address ?></textarea>
			</div>
			<div
				class="input-group input-group-sm" data-bs-toggle="tooltip" data-bs-html="true"
				data-bs-title="<span class='font-xs'>Enter zero for both values to sort by name instead</span>">
				<label class="input-group-text">Sort by distance</label>
				<input
					id="latitude"
					class="form-control" name="latitude" placeholder="Latitude" type="number" step="0.000000001"
					value="<?= $lat ?>">
				<input
					id="longitude"
					class="form-control" name="longitude" placeholder="Longitude" type="number" step="0.000000001"
					value="<?= $lon ?>">
				<button
					type="button" class="btn btn-sm btn-quaternary"
					onclick="autofill_lat_lon(true)">Autofill
				</button>
			</div>
		</div>
		<div class="form-footer mt-0">
			<button type="submit" name="submit" value="y" class="btn btn btn-info">Search</button>
		</div>
	</form>
<?php if ($lawyers !== null) { ?>
	<?php if (count($lawyers) === 0) { ?>
		<h4>Sorry no lawyers match these criterion</h4>
	<?php } else { ?>
		<h4>Found lawyers:</h4>
		<div>
			<?php /** @var LawyerDetails $lawyer */
			foreach ($lawyers as $lawyer) {
				show_lawyer_card($lawyer);
			}
			unset($lawyer); ?>
		</div>
	<?php }
}
show_html_end_block();
