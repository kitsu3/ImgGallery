# ImgGallery
This is a very simple PHP class which automatically creates photo galleries and carousels.  It requires a bit of legwork, but with the result of being able to easily grab and reuse images in a particular folder over and over again.

<strong>THIS CLASS REQUIRES BOOTSTRAP CSS TO RUN PROPERLY.</strong>
The carousels use the glyphicon font that come with Bootstrap.
The galleries use the bootstrap column classes (col-xs-6, col-sm-4, etc).

<strong>This class assumes that your base directory is 'public_html'.</strong>
If this is not the case, please be sure to change the `$basedir` variable in the getHTMLDir() method!

<h2>Setting Up the ImgGallery</h2>

1) Inside the 'carousels' folder, create a new folder for your images and name it whatever you like.  This will be your <strong>'collection'</strong>. 

2) Inside your new folder, you will need two things: your images (of course), and a file called <strong>img-array.php</strong>.  Your img-array.php file should contain only one thing:  an array called `$images`.  Each element of `$images` should itself be an associative array representing one image: 

    $images = array(
            array(
                'name' => 'image1.jpg',
                'alt' => 'SEO image1 description',
                ),
            array(
                'name' => 'image2.png',
                'alt' => 'SEO image2 description',
                'href' => 'http://github.com',
                'target' => '_blank',
                ),
            array(
                'name' => 'image3.tif',
                'alt' => 'SEO image3 description',
                'short' => 'Short caption seen under gallery thumbnail',
                'caption' => 'long caption seen in carousel',
        );

Every other function depends on this array, so if your images aren't loading make sure they're in the array, and that the names are all correct!

<h2>ImgGallery Methods</h2>
<h3>The createContainer Method</h3>
This method is the reason this class was created!  The createContainer Method takes one array as an argument, with several options.  To initialize a gallery, the only thing you need to write is: `createContainer(array('collection'=>'collection-name'));`.

Otherwise, it's probably best to create the array beforehand and use that to initiate your carousel.  See <a href="./example.php">my example file</a> in order to see all the options available to you.  

<h3>The getFigure Method</h3>
This method echos a figure wherever it is called.  It takes the following arguments:
<strong>collection (<em>string</em>)</strong> -  Required. This is the same as the name of your initial collection folder.
<strong>imageID (<em>string</em> or <em>int</em>)</strong> - Required. If imageID is an integer n, looks for the nth image in the /path/to/collection/img-array.php array.  Otherwise, looks for an image in that array of name imageID.
<strong>classes (<em>string</em>)</strong> - Optional, defaults to ''. Adds the classes string to the figure's class.  Separate classes with a space, like you would in HTML.
<strong>getCaption (<em>string</em> or <em>boolean</em>)</strong> - Optional, defaults to false. If getCaption is passed the string "full", it will return a figure with the full image caption (that is, it will grab the 'caption' value of the image).  Otherwise, if getCaption is passed a true value, it will return a figure with the short image caption.

If the image isn't found, echos an empty figure.

<h3>The getSingleImage Method</h3>
This method echos a single image, without being called inside a figure.  It takes the following arguments:
<strong>collection (<em>string</em>)</strong> -  Required. This is the same as the name of your initial collection folder.
<strong>imageID (<em>string</em> or <em>int</em>)</strong> - Required. If imageID is an integer n, looks for the nth image in the /path/to/collection/img-array.php array.  Otherwise, looks for an image in that array of name imageID.
<strong>echoString (<em>boolean</em>)</strong> - Optional, defaults to true.  If set to false, it instead returns an array containing the src, alt, short, and caption values of the image.

If the image isn't found, returns an empty string.
