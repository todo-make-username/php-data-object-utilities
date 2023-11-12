<?php
	/**
	 * These demo files are quick and dirty to showcase the library, not to demo best practices.
	 * Please never do this.
	 *
	 * This is the main entrypoint into the demo.
	 * If any post data is present, it will go to submit.php, otherwise this file will run.
	 */

	if (!empty($_POST)) {
		include_once("submit.php");
		exit;
	}

	$section_name_map   = [];
	$dir_contents       = scandir(__DIR__.'/components/sections') ?: [];
	$section_file_names = array_diff($dir_contents, [ '..', '.' ]);

	foreach ($section_file_names as $file_name) {
		if (!str_contains($file_name, '.section.html')) {
			continue;
		}
		$stripped_file_name = str_replace('.section.html', '', $file_name);
		$stripped_file_name = trim($stripped_file_name, '_');

		$section_name_map[$file_name] = [
			'id'    => $stripped_file_name,
			'file'  => $file_name,
			'title' => str_replace(' And ', ' & ', ucwords(str_replace('_', ' ', $stripped_file_name))),
		];
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>DataLoader Demo</title>

	<!-- JQuery -->
	<script 
		src="https://code.jquery.com/jquery-3.6.4.min.js"
		integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8="
		crossorigin="anonymous">
	</script>
	
	<!-- PureCSS -->
	<link
		rel="stylesheet"
		href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css"
		integrity="sha384-X38yfunGUhNzHpBaEBsWLO+A0HDYOQi8ufWDkZ0k9e0eXz/tH3II7uKZ9msv++Ls"
		crossorigin="anonymous">
</head>
<body>
	<style>
		body {
			padding: 5px;
		}

		legend {
			font-weight: bold;
		}

		code {
			background-color: lightgray;
			color: black;
		}

		section {
			display: none;
		}

		#menu {
			padding-right: 25px;
		}

		#output_container {
			padding-left: 25px;
		}

		.text-right {
			text-align: right;
		}

		.text-left {
			text-align: left;
		}

		.text-center {
			text-align: center;
		}

		.align-top {
			vertical-align: top;
		}
	</style>
	<script>
		function appendFileData(formData) {
			var file_single_ele = document.getElementById('#file_single');
			if (file_single_ele && file_single_ele.files.length > 0) {
				formData.append("type_file", file_single_ele.files[0]);
			}

			var file_multi_ele = document.getElementById('#file_multiple');
			if (file_multi_ele && file_multi_ele.files.length > 0) {
				formData.append("type_file_multiple[]", file_multi_ele.files[0]);
			}
			
			return FormData;
		}

		function showSection(sectionName) {
			$('section').hide();
			$('#' + sectionName).show();
		}

		function resetForm() {
			$('#form :disabled').prop('disabled', false);
			$('#form')[0].reset();
			return false;
		}

		function populateSetDropdown() {
			inputs = [];
			$('form :input[name]').not('[name="test_set_using"]').each(function() {
				name = $(this).attr('name').replace('[]', '');
				ele = $('<option>'+name+'</option>');
				
				if (!inputs.includes(name)) {
					inputs.push(name);
					$('#set_test_property').append(ele);
				}
			});
		}

		function setupMenu() {
			$('#menu .pure-menu-link.section-link').on('click', function() {
				showSection(this.dataset.target)
			});

			$('#menu .pure-menu-link.section-link:first').click();
		}

		$(function() {
			populateSetDropdown();
			setupMenu();

			$('#form-reset').on('click', function() {
				return resetForm();
			});

			$('#form').on('submit', function(e) {
				e.preventDefault();
				formData = new FormData(this);

				formData = appendFileData(formData);

				fetch('', {
					method: 'POST',
					body: formData,
				}).then(
					response => response.json()
				).then(response => {
					$('#output_container #message').text(response['message']);
					$('#output_container #hydrated').text(response['data']['hydrated']);
					$('#output_container #dataObj').text(JSON.stringify(response['data']['dataObj'], null, 4));
					$('#output_container #post').text(JSON.stringify(response['data']['post'], null, 4));
					$('#output_container #files').text(JSON.stringify(response['data']['files'], null, 4));
				});

				return false;
			});
		});
	</script>

	<div class="pure-g text-center">
		<div class="pure-u-1">
			<h1>Demo Page</h1>
		</div>
	</div>

	<div class="pure-g text-center">
		<div class="pure-u-1">
			<div class="pure-g">
				<div class="pure-u-1-2 text-right">
					<button id="form-reset" class="pure-button">Reset Form</button>
				</div>
				<div class="pure-u-1-2 text-left">
					<button type="submit" class="pure-button">Submit</button>
				</div>
			</div>
		</div>
	</div>

	<div class="pure-g">
		<div class="pure-u-1-5">
		<h2 class="text-center">Demo Sections</h2>
		</div>
		<div class="pure-u-2-5">
			<h2 class="text-center">Form</h2>
		</div>
		<div class="pure-u-2-5">
			<h2 class="text-center">Response</h2>
		</div>
	</div>

	<div class="pure-g" style="display: flex;">
		<div class="pure-u-1-5">
			<div id="menu" class="pure-menu">
				<ul class="pure-menu-list">
					<?php foreach ($section_name_map as $section) { ?>
						<li class="pure-menu-item">
							<a href="#" class="pure-menu-link section-link" data-target="section-<?= $section['id'] ?>"><?= $section['title'] ?></a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="pure-u-2-5">
			<form id="form" class="pure-form pure-form-aligned align-top" action="" method="post" enctype="multipart/form-data">
				<?php
				foreach ($section_name_map as $section) {
					include('components/sections/'.$section['file']);
				}
				?>
			</form>
		</div>
		<div class="pure-u-2-5">
			<div id="output_container">
				<div class="pure-g">
					<div class="pure-u-1-4 align-top">Hydrated:</div>
					<pre id="hydrated" class="pure-u-3-4"></pre>
				</div>
				<div class="pure-g">
					<div class="pure-u-1-4 align-top">Message:</div>
					<pre id="message" class="pure-u-3-4"></pre>
				</div>
				<div class="pure-g">
					<div class="pure-u-1-4 align-top">Serialize:</div>
					<pre id="dataObj" class="pure-u-3-4"></pre>
				</div>
				<div class="pure-g">
					<div class="pure-u-1-4 align-top">$_POST:</div>
					<pre id="post" class="pure-u-3-4"></pre>
				</div>
				<div class="pure-g">
					<div class="pure-u-1-4 align-top">$_FILES:</div>
					<pre id="files" class="pure-u-3-4"></pre>
				</div>
			</div>
		</div>
	</div>

</body>
</html>