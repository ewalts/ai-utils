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
    public $RB;
###>-----------------------------------------------------------------------------/

    public function __construct(){
	$this->RB = new rewbin;
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
	$fObj = fopen($this->imgFile,'r');
	$this->file_R_Obj=$fObj;
	return $fObj;
    }
###>-----------------------------------------------------------------------------/
    public function _file_write_object_(){
       // $fObj = fopen($this->imgFile,'w+');
        $this->file_W_Obj=$fObj;
        return $fObj;
    }   
###>-----------------------------------------------------------------------------/
    public function _file_append_object_(){
        $fObj = fopen($this->imgFile,'a');
        $this->file_A_Obj=$fObj;
        return $fObj;
    }   

###>-----------------------------------------------------------------------------/
    public function _img_resize_($sd){
        $cur_x = $this->imgX;
	$cur_y = $this->imgY;
	$name = $this->imgName;
	//$img = $sd['img'];
	//fclose($this->imgFile);
	//$tmpFile = '/tmp/image.jpg';
	//$tmpH = fopen($tmpFile, 'w+');
	//$tmpH = $this->_file_write_object_();
	if($cur_x < $cur_y){
	    //  if($cur_x>1024)
	    $this->newX=1024;
	    $this->wide=0;
	    $this->newY=$this->longSide;
	}else{
	    $this->newY = 1024;
	    $this->newX = $this->longSide;
            $this->wide=1;
	}

	$this->img_resized=ImageCreateTrueColor($this->newX, $this->newY);
        ImageCopyResized( $this->img_resized, $this->imgObj, 0 , 0 , 0 , 0,
	    $this->newX, $this->newY, $cur_x, $cur_y );
###>-----------------------------------------------------------------------------/
	###  TEMP FILE CREATED HERE 
	ImageJpeg($this->img_resized, '/tmp/temp.jpg');

###>-----------------------------------------------------------------------------/

    }
###>-----------------------------------------------------------------------------/
    public function _img_crop_resize_($sd){
	/** square fior train, 1024 */
	$cur_x = $this->imgX;
	$cur_y = $this->imgY;
	$name = $this->imgName;
	if($cur_x > $cur_y)
	    $this->ratio = ($cur_x / $cur_y);
	else
	    $this->ratio = ($cur_y / $cur_x);
	$this->longSide = round($this->ratio * 1024);
	$this->cropBegin = round( 2 /($this->longSide - 1024));
	$this->cropEnd = ($this->cropBegin + 1024);
	try{ 
	    if(($cur_x != 1024)&&($cur_y != 1024))
		throw new exception('');
	}catch(exception $resize){
		$this->_img_resize_($sd);
	}
	finally{
	    $xCrop=0;
	    $yCrop=0;
###>-----------------------------------------------------------------------------/
	#######  TEMP FILE USED HERE
	
###>-----------------------------------------------------------------------------/
	    $this->imgFile = '/tmp/temp.jpg';
	    $fileObject = $this->_file_read_object_();
	    $imgObject = $this->_img_object_();

	    $this->_img_dimensions_();

	    if($this->wide == 1)
		$xCrop = $this->cropBegin;
	    else
		$yCrop = $this->cropBegin;
	    
	    #$size = min(imagesx($this->img_resized), imagesy($this->img_resized));
	    $imgC = imagecrop($this->imgObj, ['x' => $xCrop, 'y' => $yCrop, 
		'width' => 1024, 'height' => 1024]);

	    if ($imgC !== FALSE) {
		$this->imgC = $imgC;
    		return $imgC;
	    }
	}
    }
    
###>-----------------------------------------------------------------------------/
    public function _finalize_image_ ($Type){

        switch ($Type ){
	    case 'png':
	        echo "creating png the file ".$this->cropped_dir.$this->imgName."\n";
		imageresolution($this->imgC, 200, 200);
                ImagePng($this->imgC, $this->cropped_dir.$this->imgName);
                break;
	    case 'jepg':
		echo "creating jpeg the file ".$this->cropped_dir.$this->imgName."\n";
		imageresolution($this->imgC, 200, 200);
                ImageJpeg($this->imgC, $this->cropped_dir.$this->imgName);
                break;
	    case 'gif':
		 echo "creating gif the file ".$this->cropped_dir.$this->imgName."\n";
                imageresolution($this->imgC, 200, 200);
		ImageGif($this->imgC, $this->cropped_dir.$this->imgName);
                break;
	    default:
		echo "Defualt creating jpeg the file ".$this->cropped_dir.$this->imgName."\n";
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
	$imgType=str_replace('image/','',$this->_img_mime_type_());
        switch ($imgType){
            case 'png':
                $img=ImageCreateFromPng($this->imgFile);
             //   echo "here in png\n";
                break;
	    case 'jepg':
                $img=ImageCreateFromJpeg($this->imgFile);
             //   echo "here in jpeg\n";
                break;
            case 'gif':
                $img=ImageCreateFromGif($this->imgFile);
            //    echo "here in gif\n";
                break;
            default:
          //      echo "here at default\n";
	
		$img=ImageCreateFromJpeg($this->imgFile);
	    break;
	}
	if(!fclose($this->file_R_Obj))
	    $bt=debug_backtrace();
	    $this->RB->_message_handler_('E','Problem closing file object.',$bt);


        $this->imgObj = $img;
        return $img;
    }



###>-----------------------------------------------------------------------------/
    public function _get_images_($dir){
        try{
	    if( !$dirH=opendir($dir))
		throw new exception('Failed to oen '.$dir);
	    else
		$this->RB->_message_handler_('I','Suuccessfully opened Target directory ['.$dir.']',$l .'  Searching for image files','');
	}catch( exception $e){
	    fail($e->getMessage."\nn");
	}
    ###>-----------------------------------------------------------------------------/
	while($img=readdir($dirH)){
	    if((preg_match('/jpg/', $img))||(preg_match( '/png/',$img))||(preg_match( '/jpeg/',$img))){
		$this->imgName=$img;
            	$this->imgFile = $dir.$img;
        	$this->_file_read_object_($dir.$img);
        	$imgObj = $this->_img_object_();
        	$Type = $this->_img_mime_type_();
        	$this->_img_dimensions_();	
        	 ###> creating the array of information passed to create.
        	$imgA=array( 'path' => $dir, 'name' => $img, 'type' => $Type, 
        	    'Y' => $this->imgY, 'X' =>$this->imgX );
		if(!$this->_img_crop_resize_($imgA))

        
        	$Msg="path=[$dir]name=[$img],type=[$Type]
        		'Y=>[$this->imgY]X=.[$this->imgX]";
        	$this->RB->_message_handler_('I',$Msg,'');
        	$this->_finalize_image_($Type);
        	    //$this->_new_image_($imgA);
	    }    
	}
    }

}

###>-----------------------------------------------------------------------------/
$GI = new groom_images;

$dir = '/001_media/p/_cavern_/0_SeaArtAI/PacificIslandSchoolGirls/my_temp/';
$GI->dir = $dir;
$cropped_dir = $dir.'cropped/'; 
$GI->_target_dir_($cropped_dir); 
$GI->RB->_message_handler_('I','Image manipulation process began', '' );
$GI->RB->_message_handler_('I','Target directory ['.$dir.']','');

$GI->_get_images_($dir);



?>
