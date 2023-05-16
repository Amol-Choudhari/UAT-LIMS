//Add junk sample delete junk sample  application ajax call
//added on 01-03-2023 by Shankhpal Shende

$(document).ready(function () {

	$("#sample_list_table").DataTable();
	
	$("#sample_details").on("click", "#markjunk", function () {
		
		var sample_code = $("#sample_code").val();
		var sample_type = $("#sample_type").text();
		var commodity = $("#commodity").text();
		var location = $("#location").text();
		var last_action = $("#last_action").text();
		var remark = $("#remark").val();

		var form_data = [];

		form_data.push(
			
			{ name: "sample_code", value: sample_code },
			{ name: "sample_type", value: sample_type },
			{ name: "commodity", value: commodity },
			{ name: "location", value: location },
			{ name: "last_action", value: last_action },
			{ name: "remark", value: remark }
		);

		if (validate_junk_sample() == true) {

			$.ajax({
				type: "POST",
				url: "../OtherModules/addSampleCode",
				data: form_data,
				beforeSend: function (xhr) {
					// Add this line
					xhr.setRequestHeader("X-CSRF-Token", $('[name="_csrfToken"]').val());
				},
				success: function (response) {
					if (response == "error_exist") {

						$.alert({
							content: "The Sample code already exist",
							closeIcon: true,
							onClose: function () {
								window.location = 'sample_details';
							}
						});
					
					} else if (response == "error_fg") {

						$.alert({
							content: "This Sample Code is Final Graded, It is not allowed to be Junked.",
							closeIcon: true,
							onClose: function () {
								window.location = 'sample_details';
							}
						});
					
					} else {

						$.alert({
							content: "The Sample code added sucessfully to the junk.",
							closeIcon: true,
							onClose: function () {
								window.location = 'sample_details';
							}
						});
					}
				},
			});
		}
	});
});

$("#sample_list_table").on("click", ".delete_sample_id", function () {

	// get the current row
	var currentRow = $(this).closest("tr");
	var id = currentRow.find("td:eq(0)").text(); // get current row 1st TD value
	var sample_code = currentRow.find("td:eq(1)").text(); // get current row 1st TD value
	var sample_type = currentRow.find("td:eq(2)").text(); // get current row 2nd TD value
	var commodity = currentRow.find("td:eq(3)").text(); // get current row 3rd TD value
	var location = currentRow.find("td:eq(4)").text(); // get current row 4th TD value
	var last_action = currentRow.find("td:eq(5)").text(); // get current row 5th TD value
	var remark = currentRow.find("td:eq(7) input").val(); // get current row 7th TD value

	if (remark == "") {

		$("#error_remark" + id).show().text("Please Enter Remark");
		setTimeout(() => {$("#error_remark" + id).fadeOut();}, 5000);
		$(".remark_" + id).addClass("is-invalid");

		$(".remark_" + id).click(function () {
			$("#error_remark" + id).hide().text;
			$(".remark_" + id).removeClass("is-invalid");
		});

	} else {

		var form_data = [];

		form_data.push(
			{ name: "sample_code", value: sample_code },
			{ name: "sample_type", value: sample_type },
			{ name: "commodity", value: commodity },
			{ name: "location", value: location },
			{ name: "last_action", value: last_action },
			{ name: "remark", value: remark }
		);

		$.ajax({
			type: "POST",
			url: "../OtherModules/deleteSampleCode",
			data: form_data,
			beforeSend: function (xhr) {
				// Add this line
				xhr.setRequestHeader("X-CSRF-Token", $('[name="_csrfToken"]').val());
			},
			success: function (response) {
				if (response == "error_exist") {

					$.alert({
						content: "The sample could not be unjunked.",
						closeIcon: true,
						onClose: function () {
							window.location = 'sample_details';
						}
					});

				} else {
					
					$.alert({
						content: "The Sample is removed from the Junked list sucessfully",
						closeIcon: true,
						onClose: function () {
							window.location = 'sample_details';
						}
					});
				}
			},
		});
	}
});

$("#get_details_btn").click(function () {

	var sample_code = $("#sample_code").val();
	if (sample_code == "") {
		$.alert("Please Enter Sample Code");
	} else {
		$.ajax({
			type: "POST",
			async: true,
			data: { sample_code: sample_code },
			url: "../OtherModules/searchSample",
			beforeSend: function (xhr) {
				// Add this line
				xhr.setRequestHeader("X-CSRF-Token", $('[name="_csrfToken"]').val());
			},
			success: function (response) {
				$("#sample_details").show();
				$("#sample_details_content").html(response);
			},
		});
	}
});

function validate_junk_sample() {

	var remark = $("#remark").val();
	var value_return = "true";

	if (remark == "") {

		$("#error_remark").show().text("Please Enter Remark");
		setTimeout(() => {$("#error_remark").fadeOut();}, 5000);
		$("#remark").addClass("is-invalid");
		
		$("#remark").click(function () {
			$("#error_remark").hide().text;
			$("#remark").removeClass("is-invalid");
		});

		value_return = "false";
	}

	if (value_return == "false") {
		var msg = "Please check some fields are missing or not proper.";
		renderToast("error", msg);
		return false;
	} else {
		return true;
	}
}
