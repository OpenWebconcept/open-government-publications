import "../scss/admin";
var $halt_govpub_import = false;

function open_govpub_import($button, $checked) {
	$checked = $checked || 0;

	// Disable button
	$button.attr("disabled", true);

	// Show halt button
	jQuery("#open_govpub--halt-import").show();

	// Run ajax request
	jQuery.ajax({
		type: "post",
		dataType: "json",
		url: open_govpub.ajaxurl,
		data: {
			action: "import_open_govpub",
			checked: $checked,
		},
		success: function (response) {
			// Check if import is running
			if (response && response.status == "running") {
				// Set progress
				var $progress = response.progress + "%";

				// Show progress bar
				jQuery(".open_govpub--import-bar").show();
				jQuery(".open_govpub--import-progress").width($progress);
				jQuery(".open_govpub--import-progress span").html($progress);

				// Set import string
				if (jQuery("#open_govpub--import-string").length > 0) {
					jQuery("#open_govpub--import-string").html(
						response.import_string
					);
				}

				if (!$halt_govpub_import) {
					// Re-run import after 3 second
					setTimeout(function () {
						open_govpub_import($button, 1);
					}, 3000);
				} else {
					// Enable button
					$button.attr("disabled", false);

					// Hide halt button and progress bar
					jQuery("#open_govpub--halt-import")
						.attr("disabled", false)
						.hide();
					jQuery(".open_govpub--import-bar").hide();
				}
			} else {
				// Enable button
				$button.attr("disabled", false);
			}
		},
	});
}

jQuery(document).on("click", "#open_govpub--start-import", function (e) {
	e.preventDefault();

	// Start import loop
	open_govpub_import(jQuery(this));
});

jQuery(document).on("click", "#open_govpub--halt-import", function (e) {
	e.preventDefault();

	// Disable button
	jQuery(this).attr("disabled", true);

	// Halt import
	$halt_govpub_import = true;
});
