<?php

function isSafeFolder($folder){
	if (is_string($folder) && preg_match('/^([_-]?[ ]?[a-zA-Z0-9]+)+$/', $folder)){
		$folder = str_replace(' ', '%20', $folder);
		return urlencode($folder);
	}
	else
		return false;
}

function isSafeImageFile($imageName, $encode=false){
	if (is_string($imageName) && preg_match('/^([_-]*[ ]?)*[a-zA-Z0-9]+([ ]?[a-zA-Z0-9_-])*[.]((?i)jpg|jpeg|png|gif|bmp|tif|tiff|svg)$/', $imageName)){
		if ($encode){
			$imageName = str_replace(' ', '%20', $imageName);
			$imageName = urlencode($imageName);
		}
		return $imageName;
	}
	else
		return false;
}

function safeAttribute(&$attrName){
	if (is_string($attrName)){
		$attrName = trim($attrName);
		if (!preg_match('/^[a-zA-Z0-9]+([-_][a-zA-Z0-9]+)*$/'	, $attrName)){
			preg_replace('/[^a-zA-Z0-9]+/', '-', $attrName);
			preg_replace('/^-/', '', $attrName);
			preg_replace('/-$/', '', $attrName);
		}
	}
}

function getHTMLDir(){
	$imgdir = dirname(__FILE__);
	$basedir = 'htdocs';
	//$basedir = 'public_html';
	$pos = strpos($imgdir, $basedir);
	$pos += strlen($basedir);

	$htmldir = substr($imgdir, $pos);
	$htmldir = explode('\\', $htmldir);
	$htmldir = implode('/', $htmldir);

	return $htmldir;
}

function getSingleImage($collection, $imageID, $echoString=true){
	$collection = isSafeFolder($collection);
	$imgArray = '';
	if ($collection)
		$imgArray = dirname(__FILE__).'/'.$collection.'/img-array.php';
	if (file_exists($imgArray))
		include $imgArray;

	if (isset($images)){
		$target;
		if (is_int($imageID)){
			if ($imageID < count($images))
				$target = $images[$imageID];
		}
		else if (is_string($imageID)){
			$imageID = isSafeImageFile($imageID);
			foreach ($images as $image) {
				if ($image['name'] == $imageID)
					$target = $image;
			}
		}

		if (isset($target) && $echoString){
			$imgName = isSafeImageFile($target['name'], true);
			$src = getHTMLDir().'/'.$collection.'/'.$imgName;
			$alt = htmlspecialchars($target['alt']);
			echo '<img src="'.$src.'" alt="'.$alt.'">';
		}
		else if (isset($target))
			return $target;
	}
	return '';
}

function getFigure($collection, $imageID, $classes='', $getCaption=false){
	$collection = isSafeFolder($collection);
	$figString = '<figure';

	if (is_string($classes)){
		$classes = explode(' ', $classes);
		$safeClasses = array();
		foreach ($classes as $class) {
			safeAttribute($class);
			array_push($safeClasses, $class);
		}
		if (!empty($safeClasses)){
			$classes = implode(' ', $safeClasses);
			$figString .= ' class="'.$classes.'"';
		}
	}

	$image = getSingleImage($collection, $imageID, false);
	if (!empty($image)){
		$src = getHTMLDir().'/'.$collection.'/'.$image['name'];
		$alt = $image['alt'];
		$figString .= '><img src="'.$src.'" alt="'.$alt.'"';
		if ($getCaption){
			$caption = 'short';
			if ($getCaption == 'full' && isset($image['caption']))
				$caption = 'caption';
			if (isset($image[$caption]))
				$figString .= '><figcaption>'.$image[$caption].'</figcaption';
		}
	}

	$figString .= '></figure>';
	echo $figString;
}

function createContainer(&$container){
	$contString = '';

	$collection = 'main-slide';
	if (isset($container['collection'])){
		safeAttribute($container['collection']);
		$collection = $container['collection'];
	}

	$id = $collection;
	if (isset($container['id'])){
		safeAttribute($container['id']);
		$id = $container['id'];
	}

	$carousel = false;
	if (isset($container['carousel']))
		if ($container['carousel'] === true){
			$carousel = true;
			$contString .= '<div id="'.$id.'" class="carousel-container';
		}

	$popup = false;
	if (isset($container['popup']) && $carousel)
		if ($container['popup'] === true){
			$popup = true;
			$contString .= ' popup';
		}

	$bottomControls = false;
	if (isset($container['bottomControls']) && $carousel)
		if ($container['bottomControls'] === true){
			$bottomControls = true;
			$contString .= ' bottom-controls';
		}

	$circular = false;
	if (isset($container['circular']) && $carousel)
		if ($container['circular'] === true){
			$circular = true;
			$contString .= ' circular';
		}

	if (isset($container['noPause']) && $carousel)
		if ($container['noPause'] === true)
			$contString .= ' no-pause';

	$autoscroll = false;
	if (isset($container['autoscroll']) && $carousel){
		if (!$container['autoscroll'] === false)
			$autoscroll = true;
		$interval = 5000;
		if (is_int($container['autoscroll']))
			$interval = $container['autoscroll'];

		$contString .= '" data-autoscroll="'.$interval;
	}

	$transition = 700;
	if (isset($container['transition']) && $carousel){
		if (is_int($container['transition']))
			$transition = $container['transition'];
		$contString .= '" data-trans-speed="'.$transition;
	}

	$showDots = false;
	if (isset($container['showDots']))
		if ($container['showDots'] === true)
			$showDots = true;

	if (!$carousel)
		$contString .= '<div class="thumbnails gallery" data-collection="'.$collection;

	$contString .= '" data-collection="'.$collection.'">';

	$imgArray = dirname(__FILE__).'/'.$collection.'/img-array.php';

	if (file_exists($imgArray))
		include $imgArray;

	if ($popup)
		echo '<div class="fixed-wrap">';

	echo $contString;

	if ($carousel) {
		if ($popup) { ?>
			<div class='close-window-container'>
		    <span class='glyphicon glyphicon-remove close-window' aria-hidden='true'></span>
		    <span class='sr-only close-window'>Close</span>
		 	</div>
		<?php } ?>
			<div class='inner-container'>
	<?php }

	$i = 0;
	if (isset($images))
		foreach ($images as $image) { 
			$classes = 'fig-container';
			if ($carousel){
				if ($i===0)
					$classes .= ' active target';
				if ($i===count($images)-1)
					$classes .= ' last';
			}
			else{
				$classes .= ' col-xs-6 col-sm-4 col-md-3';
			}

			$anchor = '';
			$endA = '';
			if (isset($image['href'])){
				$href = filter_var($image['href'], FILTER_VALIDATE_URL);
				if ($href){
					$anchor .= '<a href="'.$href.'"';
					$endA = '</a>';

					if (isset($image['target']) && !empty($image['target']))
						$anchor .= 'target="'.isSafeFolder($image['target']).'"';
						$anchor .= '>';
				}
			}

			$name = isSafeImageFile($image['name']);
			$src = '';
			if ($name)
				$src = getHTMLDir().'/'.$id.'/'.$name;

			$caption = '';
			if (isset($image['caption']) && $carousel)
				$caption .= htmlspecialchars($image['caption']);
			else if (isset($image['short']))
				$caption .= htmlspecialchars($image['short']);

			?>
			<div class='<?php echo $classes; ?>' data-index='<?php echo $i; ?>'>
				<figure>
					<?php echo $anchor; ?>
						<img src='<?php echo $src; ?>' alt ='<?php echo $image['alt']; ?>'>
					<?php echo $endA;
						if ($caption != '') echo '
							<figcaption>'.$anchor.$caption.$endA.'</figcaption>
						';
					?>
				</figure>
			</div>
			<?php if (!$carousel) {
				$j = $i+1;

				$clearfix = '';
				if ($j%2===0 || $j%3===0 || $j===count($images))
					$clearfix .= '<div class="clearfix';

				if ($j===count($images) || ($j%4===0 && $j%3===0))
					$clearfix .= '';
				else if ($j%4===0)
						$clearfix .= ' hidden-sm';
				else if ($j%3===0)
					$clearfix .= ' visible-sm'; 
				else if ($j%2===0)
					$clearfix .= ' visible-xs';

				if ($j%2===0 || $j%3===0 || $j===count($images))
					$clearfix .= '"></div>';

				echo $clearfix;
			}
		$i++;
		}
	if ($carousel) {
		if (!$bottomControls) { ?>
			<div class='control-container control-left'>
				<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>
				<span class='sr-only'>Scroll Left</span>
			</div>
			<div class='control-container control-right'>
				<span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>
				<span class='sr-only'>Scroll Right</span>
			</div>
		<?php } ?>
		</div>
		<?php if ($bottomControls) { ?>
			<div class='bottom-controls'>
				<div class='control-container control-left'>
					<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>
					<span class='sr-only'>Scroll Left</span>
				</div>
		<?php }
		if ($showDots) { ?>
			<div class='dot-controls'>
				<?php for($j=0; $j<$i; $j++) { ?>
					<div class='control-container dot<?php if ($j===0) echo ' active'; ?>' data-index='<?php echo $j; ?>'></div>
				<?php } ?>
			</div>
		<?php } 
		if ($bottomControls) { ?>
				<div class='control-container control-right'>
					<span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>
					<span class='sr-only'>Scroll Right</span>
				</div>
			</div>
		<?php }
	}

	echo '</div>';

	if ($popup)
		echo '</div>';
}
?>
