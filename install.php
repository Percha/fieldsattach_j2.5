<?php
defined('JPATH_PLATFORM') or die;
jimport('joomla.installer.helper');


//this is intializer.php
defined('DS')?  null :define('DS',DIRECTORY_SEPARATOR);


function destroy_dir($dir) { 
    if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
        foreach (scandir($dir) as $file) { 
            if ($file == '.' || $file == '..') continue; 
            if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) { 
                chmod($dir . DIRECTORY_SEPARATOR . $file, 0777); 
                if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) return false; 
            }; 
        } 
        return rmdir($dir); 
    } 


//TEXT *****************************************************************************
?>
<div style="position:relative;">
<div style=" overflow:hidden; margin:8px 0 8px 0; padding:5px;" >
<div style=" font-size:12px; margin:0px 0 0px 0; padding:5px; position:relative; float:right;"><div>Powered by Percha.com</div></div>
<div style="float:left; margin:0 20px 20px 0;"><img src="http://www.fieldsattach.com/images/logo_fieldsattach_small.png" alt="fieldsattach.com" /></div>
<div style="   margin:30px 0 8px 0; padding:5px; font-size:23px; color:#4892AB;">Thanks for install the Fieldsattach component.</div>
</div>
<?php 
//INSTALL THE PLUGINS *******************************************************************************
$installer = new JInstaller();
//$installer->_overwrite = true; 
$pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fieldsattach'.DS.'extensions'.DS;
$pkgs = array( 
		'input.zip'=>'Plugin fieldsattachment <strong>Input</strong>',
                'file.zip'=>'Plugin fieldsattachment <strong>File</strong>' ,
		'image.zip'=>'Plugin fieldsattachment <strong>image</strong>' ,
		'imagegallery.zip'=>'Plugin fieldsattachment <strong>imagegallery</strong>' ,
		'select.zip'=>'Plugin fieldsattachment <strong>select</strong>' , 
		'textarea.zip'=>'Plugin fieldsattachment <strong>textarea</strong>' ,
                'content_fieldsattachment.zip'=>'Plugin Content FieldsAttachment',
		'system_fieldsattachment.zip'=>'Plugin System FieldsAttachment',
		'advancedsearch_fieldsattachment.zip'=>'Plugin Advanced Search FieldsAttachment',
                'filterarticles.zip'=>'Plugin Advanced FILTER FieldsAttachment'
                     );

foreach( $pkgs as $pkg => $pkgname ):
  $package = JInstallerHelper::unpack( $pkg_path.$pkg );
  if( $installer->install( $package['dir'] ) )
  {
    $msgcolor = "#4892AB";
    $msgtext  = "$pkgname successfully installed.";

	//ACTIVE IT
	 
  }
  else
  {
    $msgcolor = "#880000";
    $msgtext  = "ERROR: Could not install the $pkgname. Please install manually.";
  }

//ACTIVE THE PLUGINS *******************************************************************************

$db =& JFactory::getDBO();
$sql =  "UPDATE #__extensions  SET enabled = 1 WHERE  element = 'fieldsattachment'";
$db->setQuery($sql);
$db->query(); 

$db =& JFactory::getDBO();
$sql =  "UPDATE #__extensions  SET enabled = 1 WHERE  element = 'fieldsattachmentadvanced'";
$db->setQuery($sql);
$db->query(); 

$db =& JFactory::getDBO();
$sql =  "UPDATE #__extensions  SET enabled = 1 WHERE  folder = 'fieldsattachment'";
$db->setQuery($sql);
$db->query();


//DESACTIVE OLD SEARCH
$db =& JFactory::getDBO();
$sql =  "UPDATE #__extensions  SET enabled = 0 WHERE  element = 'fieldsattachment' AND folder='search'";
$db->setQuery($sql);
$db->query();





  ?>
  <div style="border:1px dashed <?php echo $msgcolor; ?>; font-size:14px; margin:8px 0 8px 0; padding:5px;" ><?php echo $msgtext; ?></div>   
<?php
JInstallerHelper::cleanupInstall( $pkg_path.$pkg, $package['dir'] ); 
endforeach; 

//DELETE EXTENSIONS
$pkg_path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fieldsattach'.DS.'extensions'.DS;

$msgcolor = "#009900";
destroy_dir($pkg_path);
$msgtext = "Clean install directory:  ".$pkg_path 
?>
<div style="border:1px dashed <?php echo $msgcolor; ?>; font-size:14px; margin:8px 0 8px 0; padding:5px;" ><?php echo $msgtext; ?></div> 
<div style="border:1px dashed #4892AB;  font-size:14px; margin:8px 0 8px 0; padding:5px;">Component install</div>
<div style="position:absolute; top:30px; right:0px; width:170px; height:30px;border:1px dashed #494; background-color:#4892AB; color:#FFF;  font-size:17px; margin:8px 0 8px 0; padding:10px;">Component install OK</div>
</div>
