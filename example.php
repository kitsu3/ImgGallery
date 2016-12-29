<!DOCTYPE html>
<html lang="en">
	<head>
		<script src="/path/to/carousel.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				initiateCarousels();
			});
		</script>

		<link href="/path/to/bootstrap.css" rel="stylesheet">
		<link href="/path/to/carousel.css" rel="stylesheet">
	</head>
	<body>
		<main>
			<?php include_once 'path/to/image-carousel.php'; ?>
			<section id="main-slide-section">
				<div id="main-slide-container" class="container">
					<?php 
						/* All the various options and sample values. 
							The only essential value is 'collection'.  
							Almost all non-essential values default to false. The only exceptions are 
							  'transition', which defaults to 700, and 
							  'id', which will default to the value of 'collection'.  
						*/
						$carousel = array(
								'collection' => 'main-slide',
								'id' => 'main-slide',
								'carousel' => true,
								'circular' => true,
								'popup' => false,
								'noPause' => false,
								'autoscroll' => 4000, 
								'transition' => 1500,
								'showDots' => true,
							);
						createContainer($carousel);
					?>
				</div>
			</section>
			<section id="latest-images-section">
				<div id="latest-images-container" class="container">
					<?php 
						getFigure('main-slide', 0, 'col-xs-4');
					?>
					<div id="non-figure-image" class="col-xs-4">
						<?php getSingleImg('main-slide', 1) ?>
					</div>
				</div>
			</section>
			<section id="gallery-section">
				<div id="gallery-container" class="container">
					<?php 
						// Creates a popup gallery associated with the 'main-slide' collection of images.
						$popup = array(
							'collection' => 'main-slide', 
							'id' => 'main-slide-popup', 
							'carousel' => true,
							'popup' => true,
						);

						// Creates a thumbnail gallery with images that will open our $popup carousel when clicked.
						$gallery = array('collection' => 'main-slide');
						createContainer($gallery); 
					?>
				</div>
			</section>
		</main>
	</body>
</html>
