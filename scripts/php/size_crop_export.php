#!/usr/bin/php
<?PHP     ###>   Richard Eric Walts. Auto header from new-script.sh
###################################################################################################
###> New x php -> size_crop_export.php  -> Initial creation user => eric => 2025-05-26_01:22:55 ###
###################################################################################################
#_#>

###> CLI colors when using these colors, insure they use double quotes, vs single.
# Several version of my script included them with single quotes.
# $Red="\e[0;31m"; $BRed="\e[1;31m"; $BIRed="\e[1;91m"; $Gre="\e[0;32m"; $BGre="\e[1;32m"; $BBlu="\e[1;34m"; $BWhi="\e[1;37m"; $RCol="\e[0m";
function fail( $str){  print( "\e[0;31m".$str."\e[0m\n");}  
function pass( $str){     echo "\e[1;32m".$str ."\e[0m"."\n"; }
###>	$pass=false;
###>	if( !$pass){   fail( $Msg);  }else{     pass( $Msg); }

define("VERBOSE_EVENTS", True);
define("DEBUG", True);
define("TEMP_DIR", "/tmp/rewbin_grooom_images.".date('Y-m-d')."/");
###> >>> Including this main config includes the class as well ########
include('/home/eric/.rewbin/config/main.php');
###>-----------------------------------------------------------------------------/
class groom_images {
    public $dest_img_location;
    public $current_img;
    public $img_src_dir;
    public $ds_train_dir;
    public $iSrc;
    public $dir;
    public $cropped_dir;
    public $imgObj;
    public $imgFile;
    public $img_resized;
    public $file_R_Obj;
    public $file_W_Obj;
    public $file_A_Obj;
    public $imgX;
    public $imgY;
    public $newX;
    public $newY;
    public $ratio;
    public $wide;
    public $res;
    public $sizeMod;
    public $cropImg;
    public $resizeImg;
    public $RB;
    public $targetY;
    public $targetX;
###>-----------------------------------------------------------------------------/

    public function __construct(){
	$this->targetX = 1024;
	$this->targetY = 1024;
	$this->RB = new rewbin;
	if(!is_dir(TEMP_DIR)){
	    $this->tempDir=TEMP_DIR;
	    if(!mkdir (TEMP_DIR,0777,True)){
		$bt=debug_backtrace();
		$this->RB->_message_handler_('E',
		    'Failed to create tempdir',$bt);
	    }
	}
    }
###>-----------------------------------------------------------------------------/
    public function _target_dir_($dir){
	$this->cropped_dir=$dir;
	try{
	    if( is_dir( $this->cropped_dir))
		$this->RB->_message_handler_('I','Cropped directory found','');
	    else
		throw new exception('Cropped not found');
	}catch(exception $crd){
	    $this->RB->_message_handler_('I',$crd->getMessage,'');
	    if( !mkdir($this->cropped_dir)){
		$bt=debug_backtrace();
		fail('Failed to create new directory for files');
		$this->RB->_message_handler_('I','Failed to create new directory '.$this->cropped_dir,$bt);
		die();
	    }else{
		$this->RB->_message_handler_('I','Successuflly created new directory '.$this->cropped_dir,'');
	    }
	}
    }

###>-----------------------------------------------------------------------------/
    public function _img_dimensions_(){
	    $this->imgX=imageSX($this->imgObj);
	    $this->imgY=imageSY($this->imgObj);
	    if($this->imgX > $this->imgY)
		$this->wide=1;
	    else
		$this->wide=0;
    }
###>-----------------------------------------------------------------------------/
    public function _file_read_object_(){
	if(!$fObj = fopen($this->imgFile,'r')){
	    $bt=debug_backtrace();
	    $this->RB->_message_handler_('E','Failed to open file', $bt);
	}else
    	    $this->file_R_Obj=$fObj;
	return $fObj;
    }
###>-----------------------------------------------------------------------------/
    public function _file_write_object_(){
	if(!$fObj = fopen($this->imgFile,'w+')){
            $bt=debug_backtrace();
            $this->RB->_message_handler_('E','Failed to open file',$bt);
	}else
    	    $this->file_W_Obj=$fObj;
        return $fObj;
    }   
###>-----------------------------------------------------------------------------/
    public function _file_append_object_(){
	if(!$fObj = fopen($this->imgFile,'a')){
            $bt=debug_backtrace();
	    $this->RB->_message_handler_('E','Failed to open file',$bt);
    }else
        $this->file_A_Obj=$fObj;
        return $fObj;
    }   
###>-----------------------------------------------------------------------------/     
    public function _resize_or_not_(){
	if(($this->imgX == $this->targetX)&&($this->imgY==$this->targetY)){
	    $this->cropImg=False;
	    $this->resizeImg=False;// the image is already cropped/resized

	}elseif($this->imgX == $this->imgY){
	    $this->cropImg = False;
	    $this->resizeImg = True;
	    $this->sizeMod = True;
	}else{
	    $this->cropImg = True;
	    $this->resizeImg = True;
	    $this->sizeMod = True;
	}
    }

###>-----------------------------------------------------------------------------/     

    public function _create_temp_file_(){
	$tempFile = TEMP_DIR.'_'.$this->md5Sum.'_'.$this->imgName;
	$this->tempFile = $tempFile;
	return $tempFile;
    }

###>-----------------------------------------------------------------------------/
    public function _img_resize_($sd){
	pass("Entering _img_resize_");
        $cur_x = $this->imgX;
	$cur_y = $this->imgY;
	$name = $this->imgName;
	try{
	    if($cur_x == $cur_y){
		$this->newX = $this->targetX;
		$this->newY = $this->targetY;
		pass("curY=$cur_y, curX=$cur_x, newY=$this->newY, newX=$this->newX; Line=".__LINE__);
		throw new exception('');
	    }elseif($cur_x < $cur_y){
		 $this->ratio = ($cur_y / $cur_x);
		 $this->newX = $this->targetX;
		 if($cur_x < $this->targetX){
		     $off = ($this->targetX - $cur_x);  
		     $this->newY = ($this->targetY + ($off * $this->ratio));
		     pass("curY=$cur_y, curX=$cur_x, newY=$this->newY, newX=$this->newX; Line=".__LINE__);
		 }else{
		     $off = ($cur_x - $this->targetX);
		     $this->newY = ($this->targetY + ($off * $this->ratio));
		 }
		 throw new exception('');
	    }elseif($cur_y < $cur_x){
                 $this->ratio = ($cur_x / $cur_y);
                 $this->newY = $this->targetY;
                 if($cur_y < $this->targetY){ 
                     $off = ($this->targetY - $cur_y);  
		     $this->newX = ($this->targetX + ($off * $this->ratio));
		     pass("curY=$cur_y, curX=$cur_x, newY=$this->newY, newX=$this->newX; Line=".__LINE__);
                 }else{
                     $off = ($cur_y - $this->targetY);
		     $this->newX = ($this->targetX + ($off * $this->ratio));
		     pass("curY=$cur_y, curX=$cur_x, newY=$this->newY, newX=$this->newX; Line=".__LINE__);
		 }
		 throw new exception('');
	    } 
        }catch(exception $resize){
	    //    $this->_img_resize_($sd);
	    pass("curY=$cur_y, curX=$cur_x, newY=$this->newY, newX=$this->newX; Line=".__LINE__);
	    
        }

	$this->img_resized=ImageCreateTrueColor($this->newX, $this->newY);
        ImageCopyResized( $this->img_resized, $this->imgObj, 0 , 0 , 0 , 0,
	    $this->newX, $this->newY, $cur_x, $cur_y );
	pass("curY=$cur_y, curX=$cur_x, newY=$this->newY, newX=$this->newX; Line=".__LINE__);

	###  TEMP FILE CREATED HERE 
	$this->_create_temp_file_();
	ImageJpeg($this->img_resized, $this->tempFile);
	$this->imgFile = $this->tempFile;
    }
###>-----------------------------------------------------------------------------/
    public function _img_crop_resize_($sd){
	pass("Entering _img_crop_resize_");

	/** square for train, 1024 */
	//$this->tempFile = $this->_create_temp_file_();
        //$this->imgFile = $this->tempFile;
        $fileObject = $this->_file_read_object_();
        $imgObject = $this->_img_object_();
        $this->_img_dimensions_();

	$cur_x = $this->imgX;
	$cur_y = $this->imgY;
	$name = $this->imgName;
	$imgObj = $this->imgObj;
	
	try{ // We will only crop if the image won't really change in size
            if((($cur_x < $this->targetX)&&($cur_y < $this->targetY))||
		(($cur_y > $this->targetY)&&($cur_x > $this->targetX)))
		throw new exception('');
	}catch(exception $resize){
	    $this->_img_resize_($sd);
	    ###>  If the image was resized we need the new dimensions;
	    
	    $cur_x=$this->imgX;
	    $cur_y=$this->imgY;
	}
	finally{
	    if($cur_x > $cur_y)
		$this->ratio = ($cur_x / $cur_y);
	    elseif($cur_x < $cur_y)
		$this->ratio = ($cur_y / $cur_x);
	    else
		$crop_images=False;

	    if($crop_images){
		$this->longSide = ($this->ratio * $this->targetX);
		$this->cropBegin = round( 2 /($this->longSide - $this->targetX));
		$this->cropEnd = ($this->cropBegin + $this->targetX);
	    }
	
	    $xCrop=0;
	    $yCrop=0;
###>-----------------------------------------------------------------------------/
	    //$this->imgFile = $this->tempFile;
	    $fileObject = $this->_file_read_object_();
	    $imgObject = $this->_img_object_();

	    $this->_img_dimensions_();

	    if($this->wide == 1)
		$xCrop = $this->cropBegin;
	    else
		$yCrop = $this->cropBegin;
	    
	    #$size = min(imagesx($this->img_resized), imagesy($this->img_resized));
	    $imgC = imagecrop($this->imgObj, ['x' => $xCrop, 'y' => $yCrop, 
		'width' => $this->targetX, 'height' => $this->targetY]);

	    if ($imgC !== FALSE) {
		$this->imgC = $imgC;
    		return $imgC;
	    }
	}
    }
###>-----------------------------------------------------------------------------/

    public function _define_temp_file_(){
	$this->tempFile = TEMP_DIR.'/_'.$this->md5Sum.'_'.$this->imgName;
	return $this->tempFile;
    }
###>-----------------------------------------------------------------------------/
    public function _finalize_image_ ($Type){
	pass("Entering finailze_image_");
        switch (trim($Type) ){
	    case 'png':
	        $Msg="creating png the file ".$this->cropped_dir.$this->imgName;
                pass($Msg);
                $this->RB->_message_handler_('I',$Msg,'');
		imageresolution($this->imgC, 200, 200);
                ImagePng($this->imgC, $this->cropped_dir.$this->imgName);
                break;
	    case 'jepg':
		$Msg="creating jpeg the file ".$this->cropped_dir.$this->imgName;
                pass($Msg);
                $this->RB->_message_handler_('I',$Msg,'');
		imageresolution($this->imgC, 200, 200);
                ImageJpeg($this->imgC, $this->cropped_dir.$this->imgName);
                break;
	    case 'gif':
		$Msg= "creating gif the file ".$this->cropped_dir.$this->imgName;
                pass($Msg);
                $this->RB->_message_handler_('I',$Msg,'');
                imageresolution($this->imgC, 200, 200);
		ImageGif($this->imgC, $this->cropped_dir.$this->imgName);
                break;
	    default:
		$Msg= "Defualt creating jpeg the file ".$this->cropped_dir.$this->imgName;
                pass($Msg);
                $this->RB->_message_handler_('I',$Msg,'');
		imageresolution($this->imgC, 200, 200);
		ImageJpeg($this->imgC, $this->cropped_dir.$this->imgName);
		
            break;
        }
    }
###>-----------------------------------------------------------------------------/
    public function _img_mime_type_(){
	 //   $iH=$this->_file_read_object_($file);
	    $iT=trim(strtolower(mime_content_type($this->imgFile)));
	    $this->imgType=$iT;
	    return $iT;
    }
###>-----------------------------------------------------------------------------/
    
    public function _img_object_(){
	$imgType=trim(str_replace('image/','',$this->_img_mime_type_()));
        switch ($imgType){
            case 'png':
                $img=ImageCreateFromPng($this->imgFile);
             //  echo "here in png\n";
                break;
	    case 'jepg':
                $img=ImageCreateFromJpeg($this->imgFile);
             //  echo "here in jpeg\n";
                break;
            case 'gif':
                $img=ImageCreateFromGif($this->imgFile);
             //  echo "here in gif\n";
                break;
            default:
             //  echo "here at default\n";
		$img=ImageCreateFromJpeg($this->imgFile);
	    break;
	}
	//if(!fclose($this->file_R_Obj))
	  //  $bt=debug_backtrace();
	    //$this->RB->_message_handler_('E','Problem closing file object.',$bt);


        $this->imgObj = $img;
        return $img;
    }

    public function _check_resolution_(){
	$res=ImageResolution($this->imgObj);
	$this->res=$res;
	return $res;
    }

###>-----------------------------------------------------------------------------/
    public function _get_images_($dir){
        try{
	    if( !$dirH=opendir($dir))
		throw new exception('Failed to oen '.$dir);
	    else
		$this->RB->_message_handler_('I','Suuccessfully opened Target directory ['.$dir.'], Searching for image files','');
	}catch( exception $e){
	    fail($e->getMessage."\nn");
	}
	###>-----------------------------------------------------------------------------/
	$c=0;
	while($img=readdir($dirH)){
	    if((preg_match('/jpg/', $img))||(preg_match( '/png/',$img))||(preg_match( '/jpeg/',$img))){
		$c++;
		pass("Loop the while [$c]");
		$this->imgName=$img;
		$this->imgFile = $dir.$img;
		$this->md5Sum = md5($this->imgName);
        	$this->_file_read_object_($dir.$img);
        	$imgObj = $this->_img_object_();
        	$Type = $this->_img_mime_type_();
		$this->_img_dimensions_();	
		$this->_check_resolution_();
        	 ###> creating the array of information passed to create.
        	$imgA=array( 'path' => $dir, 'name' => $img, 'type' => $Type, 
		    'Y' => $this->imgY, 'X' =>$this->imgX );
		$Msg="Image picked up for process, current - type=[$Type]\n"
		    ."path=[$dir], name=[$img],\n"
		    ."type=[$Type],resolution=[x=".$this->res[0].",y=".$this->res[1]."], 
		    'Y=>[$this->imgY]X=.[$this->imgX]";
		$this->RB->_message_handler_('I',$Msg,'');
		pass($Msg);

//		$this->RB->_message_handler_('I',$Msg,'');
		$this->_resize_or_not_();
		if(($this->cropImg)||($this->resizeImg)){
		    if(!$this->_img_crop_resize_($imgA)){
			$Msg='There was a problem processing the image '
			."path=[$dir]name=[$img],type=[$Type]
			    'Y=>[$this->imgY]X=.[$this->imgX]";
			$bt=debug_backtrace();
			$this->RB->_message_handler_('I',$Msg,$bt);
		    }
		}else{	
		    $Msg="Img: $this->imgFile did not require resisizing";
		    $this->RB->_message_handler_('I',$Msg,'');
		}
	//	if(($this->res[0]!=200)||($this->res[1]!=200)||($this->sizeMOd)){
	        $this->_finalize_image_($this->imgType);
	//	}
        	$Msg="path=[$dir]name=[$img],type=[$Type]
        		'Y=>[$this->imgY]X=.[$this->imgX]";
        	$this->RB->_message_handler_('I',$Msg,'');
            
        	    //$this->_new_image_($imgA);
	    }    
	}
    }

}
###>-----------------------------------------------------------------------------/     
###>-----------------------------------------------------------------------------/     
###>-----------------------------------------------------------------------------/
$GI = new groom_images;

$dir = trim(readline("Which directory contains the images? "));
$GI->dir = $dir;
$cropped_dir = $dir.'cropped/'; 
$GI->_target_dir_($cropped_dir); 
$GI->RB->_message_handler_('I','Image manipulation process began', '' );
$GI->RB->_message_handler_('I','Target directory ['.$dir.']','');

$GI->_get_images_($dir);



?>
