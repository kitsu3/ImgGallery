<?php

class ImgGallery{

  private $collection;
  private $images;
  private $carouselNum;
  private $popupNum;

  private function isSafeFolder($folder){
    if (is_string($folder) && preg_match('/^([_-]?[ ]?[a-zA-Z0-9]+)+$/', $folder)){
      $folder = str_replace(' ', '%20', $folder);
      return urlencode($folder);
    }
    else
      return false;
  }

  public function __construct($collection){
    $collection = $this->isSafeFolder($collection);
    $imgPath = '';
    $this->carouselNum = 0;
    $this->popupNum = 0;

    if (is_string($collection) && !empty($collection)){
      $this->collection = $collection;
      $imgPath = dirname(__FILE__).'/'.$collection;
    }

    if (file_exists($imgPath.'/img-array.php')){
      include $imgPath.'/img-array.php';
    }

    if (isset($images) && is_array($images) && !empty($images)){
      $this->images = &$images;
      unset($images);
    }
  }

  private function isSafeImageFile($imageName, $encode=false){
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

  private function safeAttribute(&$attrName){
    if (is_string($attrName)){
      $attrName = trim($attrName);
      if (!preg_match('/^[-_]?[a-zA-Z0-9]+([-_][a-zA-Z0-9]+)*[_-]?$/' , $attrName)){
        preg_replace('/[^a-zA-Z0-9]+/', '-', $attrName);
        preg_replace('/^-/', '', $attrName);
        preg_replace('/-$/', '', $attrName);
      }
    }
  }

  private function getHTMLDir(){
    $imgdir = dirname(__FILE__);
    //$basedir = 'htdocs';
    $basedir = 'public_html';
    $pos = strpos($imgdir, $basedir);
    $pos += strlen($basedir);

    $htmldir = substr($imgdir, $pos);

    return $htmldir;
  }

  private function getImgData($imageID){
    if (isset($this->images)){
      $target;
      $images = $this->images;

      if (is_string($imageID)){
        $imageID = $this->isSafeImageFile($imageID);
        if ($imageID){
          if (isset($images)){
            foreach ($images as $image)
              if ($image['name'] == $imageID)
                $target = $image;
          }
        }
      }

      else if (is_int($imageID)){
        if ($imageID > -1 && $imageID < count($images))
          $target = $images[$imageID];
      }

      if (isset($target)){
        $src = $this->getHTMLDir().'/'.$this->collection.'/'.$target['name'];
        $alt = htmlspecialchars($target['alt']);
        $short = '';
        $caption = '';
        if (isset($target['short']))
          $short = trim($target['short']);
        if (isset($target['caption']))
          $caption = trim($target['caption']);

        return array('src'=>$src, 'alt'=>$alt, 'short'=>$short, 'caption'=>$caption);
      }
    }
    return '';
  }

  public function getSingleImage($imageID){
    if (isset($this->images)){
      $target = $this->getImgData($imageID);

      if (!empty($target))
        echo '<img src="'.$target['src'].'" alt="'.$target['alt'].'">';
    }
    return '';
  }

  public function getFigure($imageID, $classes='', $getCaption=false){
    $figString = '<figure';

    if (is_string($classes)){
      $classes = explode(' ', $classes);
      $safeClasses = array();
      foreach ($classes as $class) {
        $this->safeAttribute($class);
        array_push($safeClasses, $class);
      }
      if (!empty($safeClasses)){
        $classes = implode(' ', $safeClasses);
        $figString .= ' class="'.$classes.'"';
      }
    }

    $image = $this->getImgData($imageID);
    if (!empty($image)){
      $figString .= '><img src="'.$image['src'].'" alt="'.$image['alt'].'"';
      if ($getCaption){
        $caption = 'short';
        if ($getCaption == 'full' && !empty($image['caption']))
          $caption = 'caption';
        if (!empty($image[$caption]))
          $figString .= '><figcaption>'.$image[$caption].'</figcaption';
      }
    }

    $figString .= '></figure>';
    echo $figString;
  }

  public function addGallery($columns=array()){
    if (isset($this->images)){
      $valid = true;
      if (!empty($columns) && is_array($columns)){
        $negative = array();
        foreach ($columns as $screenSize => $num){

          if (!is_int($num) || $num<=0 || 12%$num!=0)
            array_push($negative, $num);

          switch ($screenSize) {
            case 'lg':
              break;
            case 'md':
              break;
            case 'sm':
              break;
            case 'xs':
              break;
            default:
              array_push($negative, $num);
              break;
          }
        }
        array_diff($columns, $negative);
      }

      if (!is_array($columns) || empty($columns))
        $columns = array('md'=>4, 'sm'=>3, 'xs'=>2);

      $collection = $this->collection;
      $this->safeAttribute($collection);

      $contString = '<div class="row thumbnails gallery" data-collection="'.$collection.'">
      ';

      $figString = '<div class="fig-container';
      foreach ($columns as $screenSize => $num){
        $btsCols = 12/$num;
        $figString .= ' col-'.$screenSize.'-'.$btsCols;
      }
      $figString .= '"';

      echo $contString;
      foreach ($this->images as $index => $image) { 
        if (isset($image['name']) && isset($image['alt']) && $this->isSafeImageFile($image['name'])){

          $src = $this->getHTMLDir().'/'.$this->collection.'/'.$this->isSafeImageFile($image['name'], true);

          echo $figString.' data-index="'.$index.'">
          <figure>
            <img src="'.$src.'" alt="'.htmlspecialchars($image['alt']).'">
            ';
            if (isset($image['short'])){
              echo '<figcaption>'.htmlspecialchars($image['short']).'</figcaption>';
            }
          echo '
          </figure>
        </div>';

          $j = $index+1;
          $clearfix = '';

          $xs = 12; $sm = 12; $md = 12; $lg = 12;
          if (isset($columns['xs']))
            $xs = $columns['xs'];
          if (isset($columns['sm']))
            $sm = $columns['sm'];
          if (isset($columns['md']))
            $md = $columns['md'];
          if (isset($columns['lg']))
            $lg = $columns['lg'];

          if ($j%12===0 || $j%$lg===0 || $j%$md===0 || $j%$sm===0 || $j%$xs===0 || $j===count($this->images)){
            $clearfix .= '<div class="clearfix';
            if ($j===count($this->images) || $j%12===0)
              $clearfix .= '';
            else{
              if ($j%$lg===0) 
                $clearfix .= ' visible-lg';
              if ($j%$md===0)
                $clearfix .= ' visible-md';
              if ($j%$sm===0)
                $clearfix .= ' visible-sm';
              if ($j%$xs===0)
                $clearfix .= ' visible-xs';
            }
            $clearfix .= '"></div>';
            echo $clearfix;
          }
        }
      }
      echo '
    </div>';
    }
  }

  public function addCarousel(&$options=array(), $popup=false){
    if ($popup && $this->popupNum>0){}

    else if (isset($this->images)){
      $id = $this->collection;
      $this->safeAttribute($id);

      if (isset($options['id']) && is_string($options['id'])){
        $this->safeAttribute($options['id']);
        $id = $options['id'];
      }

      elseif (!$popup){
        $this->carouselNum++;
        $id .= '-'.$this->carouselNum;
      }

      else{
        $this->popupNum++;
        $id .= '-popup';
      }

      $contString = '<div id="'.$id.'" class="carousel-container';

      if ($popup)
        $contString .= ' popup';

      $arrowControls = 'hover-controls';
      if (isset($options['arrowControls']))
        switch ($options['arrowControls']){
          case 'none':
            $arrowControls = false;
            break;
          case 'bottom':
            $arrowControls = 'bottom';
            $contString .= ' bottom-controls';
            break;
          default:
            break;
        }

      $circular = false;
      if (isset($options['circular']))
        if ($options['circular'] === true)
          $contString .= ' circular';

      $showDots = false;
      if (isset($options['showDots']))
        if ($options['showDots'] === true)
          $showDots = true;

      if (!$popup){
        if (isset($options['noPause']))
          if ($options['noPause'] === true)
            $contString .= ' no-pause';

        $autoscroll = false;
        if (isset($options['autoscroll'])){
          if (!$options['autoscroll'] === false)
            $autoscroll = true;
          $interval = 5000;
          if (is_int($options['autoscroll']))
            $interval = $options['autoscroll'];

          $contString .= '" data-autoscroll="'.$interval;
        }

        $transition = 700;
        if (isset($options['transSpeed'])){
          if (is_int($options['transSpeed']))
            $transition = $options['transSpeed'];
          $contString .= '" data-trans-speed="'.$transition;
        }
      }

      $collection = $this->collection;
      $this->safeAttribute($collection);

      $contString .= '" data-collection="'.$collection.'">';

      if ($arrowControls){
        $leftArrow = '<div class="control-container control-left">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Scroll Left</span>
        </div>';
        $rightArrow = '<div class="control-container control-right">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Scroll Right</span>
        </div>';
      }

      if ($popup)
        echo '<div class="fixed-wrap">';

      echo $contString;


      if ($popup)
        echo '
        <div class="close-window-container">
          <span class="glyphicon glyphicon-remove close-window" aria-hidden="true"></span>
          <span class="sr-only close-window">Close</span>
        </div>
        ';

      echo '<div class="inner-container">'; 
          
      foreach ($this->images as $index => $image) { 
        if (isset($image['name']) && isset($image['alt']) && $this->isSafeImageFile($image['name'])){
          $classes = 'fig-container';
          if ($index===0)
            $classes .= ' active target';
          if ($index===count($this->images)-1)
            $classes .= ' last';

          $anchor = '';
          $endA = '';
          if (isset($image['href'])){
            $href = filter_var($image['href'], FILTER_VALIDATE_URL);
            if ($href){
              $anchor .= '<a href="'.$href.'"';
              $endA = '</a>';

              if (isset($image['target']) && !empty($image['target']))
                $anchor .= 'target="'.$this->safeAttribute($image['target']).'"';
                $anchor .= '>';
            }
          }

          $src = $this->getHTMLDir().'/'.$this->collection.'/'.$this->isSafeImageFile($image['name']);

          $caption = '';
          if (isset($image['caption']))
            $caption = htmlspecialchars($image['caption']);
          else if (isset($image['short']))
            $caption = htmlspecialchars($image['short']);

          echo '<div class="'.$classes.'" data-index="'.$index.'">
          <figure>
          '.$anchor.'<img src="'.$src.'" alt="'.htmlspecialchars($image['alt']).'">'.$endA;
          if (!empty($caption))
            echo '
          <figcaption>'.$anchor.$caption.$endA.'</figcaption>
                ';
          echo '
          </figure>
          </div>';
        }
      }
      if ($arrowControls && $arrowControls != 'bottom'){
        echo $leftArrow;
        echo $rightArrow;
      }

      echo '</div>'; //Closes inner-container div

      if ($arrowControls == 'bottom') {
        echo '<div class="bottom-controls">
        ';
        echo $leftArrow;
      }
      if ($showDots){
        echo '<div class="dot-controls">';
        for($j=0; $j<count($this->images); $j++){
          echo '<div class="control-container dot';
          if ($j===0)
            echo ' active';
          echo '" data-index="'.$j.'"></div>
          ';
        }
        echo '</div>
        ';
      }
      if ($arrowControls == 'bottom'){
        echo $rightArrow;
        echo '</div>
        ';
      }

      echo '</div>'; //Closes carousel-container div

      if ($popup)
        echo '</div>'; //Closes fixed-wrap div
    }
  }
}
