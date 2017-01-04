<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="/path/to/jquery.js"></script>
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
      <?php include_once 'path/to/image-gallery.php'; ?>
      <section id="main-slide-section">
        <div id="main-slide-container" class="container">
          <?php 
            /* You must have a 'collection' to establish an ImgGallery. */
            $main = new ImgGallery('main-slide');

            /* All the various options and sample values for a carousel, as well as defaults. */
            $options = array(
                'id' => 'my-main-slideshow', //Default 'main-slide-1'
                'circular' => true, //Default false
                'arrowControls' => 'hover', //Default 'hover'; other values are 'none' and 'bottom'
                'autoscroll' => 4000, //Default false; if set to 'true', then default 5000
                'noPause' => false, //Default false
                'transSpeed' => 1500, //Default 700
                'showDots' => true, //Default false
              );
            $main->addCarousel($options);
          ?>
        </div>
      </section>
      <section id="latest-images-section">
        <div id="latest-images-container" class="container">
          <?php 
            /* When you get a figure, if the image name (or index) doesn't exist you get an empty figure tag with your given classes.  */
            $main->getFigure(0, 'col-xs-6 col-sm-4');
          ?>
          <div id="non-figure-image" class="col-xs-4">
            <?php 
              /* When you get an image, if the image name or index doesn't exist the function echoes an empty string.  */
              $main->getSingleImg(1) 
            ?>
          </div>
        </div>
      </section>
      <?php 
        /* Popup carousels should always be direct children of a full-screen-width element such as <body> or <main>. Fewer options are available to popup carousels.  */

        $popupOptions = array(
          'id' => 'my-popup', //Default '[collection]-popup', i.e. 'main-slide-popup'
          'circular' => true, //Default false
          'arrowControls' => 'bottom', //Default 'hover'
          'showDots' => false, //Default false
        ); 

        $main->addCarousel($popupOptions, true); //The true value makes it a popup carousel.
      ?>
      <section id="gallery-section">
        <div id="gallery-container" class="container">
          <?php
            /* Creates a thumbnail gallery with images that will open our $popup carousel when clicked.  
            Takes an array for # of columns at each screen size; otherwise, defaults to 4 columns at medium screen size, 3 at small (tablet), and 2 at extra-small (mobile). 

            USES BOOTSTRAP:  # of columns MUST divide 12 */

            $columns = array('lg'=>6, 'md'=>4, 'sm'=>3, 'xs'=>2);

            $main->addGallery($columns);

            /* This will give you a Bootstrap row with internal columns.  
            This means you can place two galleries side by side by placing them in adjacent 'col-md-6' divs, for example. */
          ?>
        </div>
      </section>
    </main>
  </body>
</html>
