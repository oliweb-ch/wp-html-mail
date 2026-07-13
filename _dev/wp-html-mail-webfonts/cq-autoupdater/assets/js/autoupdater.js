jQuery( function () {
	var $ = jQuery;
	$(".cq-license-activate").on('click', function (e) {
		e.preventDefault();
		$("#wpcontent").append(
			$(
				'<div class="cq-license-loading-overlay"><div class="spinner"></div></div>'
			)
		);
		var $button = $(this);
		var $license_field = $button.siblings("input.cq-license-key");

		if (!$license_field.val()) {
			alert("Please enter your license key.");
		} else {
			$.post(
				cq_ajax_object.ajax_url,
				{
					action: "cq_activate_license",
					key: $license_field.val(),
					slug: $license_field.data("pluginslug"),
				},
				function (response) {
					response = JSON.parse(response);
					$(".cq-license-loading-overlay").remove();
					if (response["message"]) alert(response["message"]);

					if (response["success"]) window.location.reload();
				}
			);
		}
	});

	$(".cq-license-deactivate").on('click', function (e) {
		e.preventDefault();
		$("#wpcontent").append(
			$(
				'<div class="cq-license-loading-overlay"><div class="spinner"></div></div>'
			)
		);
		var $button = $(this);
		var $license_field = $button.siblings("input.cq-license-key");

		$.post(
			cq_ajax_object.ajax_url,
			{
				action: "cq_deactivate_license",
				slug: $license_field.data("pluginslug"),
			},
			function (response) {
				response = JSON.parse(response);
				$(".cq-license-loading-overlay").remove();
				if (response["message"]) alert(response["message"]);

				if (response["success"]) window.location.reload();
			}
		);
	});

	$(".cq-license .cq-license-key").each(function () {
		var $license_field = $(this);

		$.post(
			cq_ajax_object.ajax_url,
			{
				action: "cq_check_license",
				slug: $license_field.data("pluginslug"),
			},
			function (response) {
				if (parseInt(response))
					$license_field
						.siblings(".license-status")
						.html(
							'<span class="dashicons dashicons-yes" style="color:green;"></span>'
						);
				else {
					$license_field
						.siblings(".license-status")
						.html(
							'<span class="dashicons dashicons-warning" style="color:red;"></span>'
						);
					$license_field
						.parent()
						.append(
							$(
								'<div class="description">Your license is not valid. Go to <a href="https://codemiq.com/en/purchase-history/" target="_blank">your codemiq account</a> to view your license status.</div>'
							)
						);
				}
			}
		);
	});
});
