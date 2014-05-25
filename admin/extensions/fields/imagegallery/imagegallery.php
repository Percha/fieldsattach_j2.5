<?php
/**
 * @version		$Id: fieldsattachement.php 15 2011-09-02 18:37:15Z cristian $
 * @package		fieldsattach
 * @subpackage		Components
 * @copyright		Copyright (C) 2011 - 2020 Open Source Cristian Grañó, Inc. All rights reserved.
 * @author		Cristian Grañó
 * @link		http://joomlacode.org/gf/project/fieldsattach_1_6/
 * @license		License GNU General Public License version 2 or later
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

// require helper file
$sitepath = JPATH_BASE ;
$sitepath = str_replace ("administrator", "", $sitepath); 
JLoader::register('fieldattach',  $sitepath.'components/com_fieldsattach/helpers/fieldattach.php'); 
JLoader::register('fieldsattachHelper',   $sitepath.DS.'administrator/components/com_fieldsattach/helpers/fieldsattach.php');
 
class plgfieldsattachment_imagegallery extends JPlugin
{
         protected $name;
         protected $path1;
         protected $documentpath;
        /**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
         
	function construct( )
	{
            $name = "imagegallery"; 
            if(isset($this)) $this->name = $name;
                  
            //LOAD LANGUAGE --------------------------------------------------------------
            //$lang   =&JFactory::getLanguage();
            //$lang->load( 'plg_fieldsattachment_'.$name  );
	    fieldsattachHelper::getLanguageFile($name);
            //-----------------------------------------------------------------------------
	}
	  
        function getName()
        {  
                return $this->name;
        }
        
        function renderInput($articleid, $fieldsid, $value, $extras=null )
        {
		  $directory = "";
		  if ( JFactory::getApplication()->isAdmin()) {
		      $directory = "";
		  }
        
		  $session =& JFactory::getSession();
		  $session->set('articleid',$articleid);
		  $session->set('fieldsattachid',$fieldsid);
			
		  $sitepath  =  fieldsattachHelper::getabsoluteURL();
		  $str_gallery = '<div id="gallery_'.$fieldsid.'" class="galleryfield" style="margin-top:50px;">'.plgfieldsattachment_imagegallery::getGallery1($articleid, $fieldsid).'</div>';
		  $str =''; 
		  $str .= $str_gallery; 
			
		  $str .= "<script type=\"text/javascript\"> 
					     window.addEvent('domready', function(){
								
							       update_gallery".$fieldsid."(); 
								
								      
					     }); 
		     
					     jQuery(document).ready(function (){update_gallery".$fieldsid."(); });
								      
					       
					     function update_gallery".$fieldsid."()
					     {
						       
							
						     var url_".$fieldsid." = \"".JURI::base(false)."/index.php?option=com_fieldsattach&view=fieldsattachimagesajax&tmpl=component&catid=".$articleid."&fieldsid=".$fieldsid."\";
						      
						     var xmlhttp;
						     if (window.XMLHttpRequest)
						     {// code for IE7+, Firefox, Chrome, Opera, Safari
						     xmlhttp=new XMLHttpRequest();
						     }
						     else
						     {// code for IE6, IE5
						     xmlhttp=new ActiveXObject(\"Microsoft.XMLHTTP\");
						     }
						     xmlhttp.onreadystatechange=function()
						     {
						     if (xmlhttp.readyState==4 && xmlhttp.status==200)
							 {
							      //document.getElementById(\"gallery_".$fieldsid."\").innerHTML=xmlhttp.responseText;
							      var bodyHtml = /<body.*?>([\s\S]*)<\/body>/.exec(xmlhttp.responseText)[1];
							      document.getElementById(\"gallery_".$fieldsid."\").innerHTML=bodyHtml;
							    
								     
							      //Sortable ********************************* 
								     jQuery( '#gallerysortable".$fieldsid."' ).sortable(
										       {
										       update:  function (event, ui) {
											       //Update the position
											       update_order(".$fieldsid.");
										       }});
								     jQuery( '#gallerysortable".$fieldsid."' ).disableSelection();
							      
							      SqueezeBox.initialize({});
							      SqueezeBox.assign($$('#gallery_".$fieldsid." a.modal'), { parse: 'rel'});
						   
							 }
						     }
						     xmlhttp.open(\"GET\",  url_".$fieldsid." ,true);
						     xmlhttp.send(); 
					     }
					    
					     function update_order(fieldsattachid)
					     {
					     
						     //Get order *****
						     var tmparray  = new Array();
						     var cont = 0;
						     jQuery( '#gallerysortable".$fieldsid." li' ).each
						     ( 
							      function(){
									
								       var id = jQuery(this).attr('id');
								       var  tmp = String(id).split('_'); 
								       tmparray[cont] = tmp[2];
								       cont++;
							      }
						     );
						     
						     //AJAX CALL *****
						     var url  = \"".JURI::root(false)."/administrator/index.php?option=com_fieldsattach&task=fieldsattachimagesorderajax&catid=".$articleid."&fieldsid=\"+fieldsattachid+\"&order=\"+tmparray.toString();
						      
						     var xmlhttp;
						     if (window.XMLHttpRequest)
						     {// code for IE7+, Firefox, Chrome, Opera, Safari
						     xmlhttp=new XMLHttpRequest();
						     }
						     else
						     {// code for IE6, IE5
						     xmlhttp=new ActiveXObject(\"Microsoft.XMLHTTP\");
						     }
						     xmlhttp.onreadystatechange=function()
						     {
						     if (xmlhttp.readyState==4 && xmlhttp.status==200)
							 {
								    // alert(xmlhttp.responseText);
							 }
						     }
						     xmlhttp.open(\"GET\",  url ,true);
						     xmlhttp.send();
					     }
			   </script>
		  ";
		  
		  
		   //FALANG
		  $option = JRequest::getVar("option");
		  $task = JRequest::getVar("task");
		  $reference_id =  JRequest::getVar("reference_id");
		  $catid = JRequest::getVar("catid");
		  $body = JResponse::getBody();
		  
		  
		  if((($option == "com_falang") && ($catid == "content") && (!empty($reference_id))) || (($option == "com_falang")&&($task == "translate.edit")&&($catid == "content"))) {
			   $str = $str_gallery;
		  }
		  
		  return  $str  ;
        }

        function getoptionConfig($valor)
        {
             $name = $this->name;
             $return ='<option value="imagegallery" ';
             if("imagegallery" == $valor)   $return .= 'selected="selected"';
             $return .= '>'.$name.'</option>';
             return $return ;
        }

        function getHTML($articleid, $fieldsid, $category = false, $write=false)
        {
            //$str =   fieldattach::getImageGallery($articleid, $fieldsid,$category);
            global $globalreturn;
            $html ="";
            //$html =  '<ul class="gallery">';
            $db = &JFactory::getDBO(  );
	    $query = 'SELECT  a.* FROM #__fieldsattach_images as a  WHERE a.fieldsattachid = '.$fieldsid.' AND a.articleid= '.$articleid.' ORDER BY a.ordering';
            if($category)
            {
                $query = 'SELECT  a.* FROM #__fieldsattach_images as a  WHERE a.fieldsattachid = '.$fieldsid.' AND a.catid= '.$articleid.' ORDER BY a.ordering';

            }
            $db->setQuery( $query );
	    $result = $db->loadObjectList();
            $firs_link = '';
            $cont = 0;

            $sitepath  =  fieldsattachHelper::getabsoluteURL();
            $title = fieldattach::getName( $articleid,  $fieldsid , $category  );
            $published = plgfieldsattachment_imagegallery::getPublished( $fieldsid  );

            if(!empty($result) && $published){
                $html = plgfieldsattachment_imagegallery::getTemplate($fieldsid);
                $line = plgfieldsattachment_imagegallery::getLineTemplate($fieldsid);
                $lines = "";
                
                /*
                Templating IMAGEGLLERY *****************************
               
                [URL1] - Image1
                [URL2] - Image2
                [URL3] - Image3
                [URL1] - Image1
                [FIELD_ID] - Field id 
                [ARTICLE_ID] - Article id
                [TITLE] - Article id
                [DESCRIPTION] - Description
               
                */ 

                
                foreach ($result as $obj){
                    $img1 = $sitepath.''.$obj->image1;
                    $img2 = $sitepath.''.$obj->image2;
                    $img3 = $sitepath.''.$obj->image3;
                    
                    $title = $obj->title;
                    $description = $obj->description;
                    
                    $tmp = $line;
                    
                    $tmp = str_replace("[URL1]", $img1, $tmp);
                    $tmp = str_replace("[URL2]", $img2, $tmp);
                    $tmp = str_replace("[URL3]", $img3, $tmp);
                    $tmp = str_replace("[ARTICLE_ID]", $articleid, $tmp);
                    $tmp = str_replace("[FIELD_ID]", $fieldsid, $tmp);
                    $tmp = str_replace("[TITLE]", $title, $tmp);
                    
                    
                    if (!JFile::exists( JPATH_SITE .DS. $obj->image2)  )
                    {
                        $tmp = str_replace('</a>', '', $tmp);
                        $tmp = preg_replace('/<a[^>]+href[^>]+>/', '', $tmp);
                    }
                    $tmp = str_replace("[DESCRIPTION]", $description, $tmp);
                   
                    $lines.= $tmp;
                }
                
                
                
                if(fieldattach::getShowTitle(   $fieldsid  )) $html = str_replace("[TITLE]", $title, $html); 
                else $html = str_replace("[TITLE]", "", $html); 

                $html = str_replace("[ARTICLE_ID]", $articleid, $html);
                $html = str_replace("[FIELD_ID]", $fieldsid, $html);
                $html = str_replace("[LINES]", $lines, $html);
                
                
                
                /*foreach ($result as $obj){
                    //if (JFile::exists( JPATH_SITE .DS."images".DS."documents".DS. $articleid .DS. $result->value)  )
                    $html .=  '<li>' ;
                    if (JFile::exists( JPATH_SITE .DS. $obj->image2)  )
                    {
                        $html .=  '<a href="'.$sitepath.''.$obj->image1.'" id="imgFiche" class="nyroModal" title="'.$obj->title.'" rel="gal_'.$articleid.'">';
                        $html .=  '<img src="'.$sitepath.''.$obj->image2.'"  alt="'.$obj->title.'" />';
                    }else{
                        $html .=  '<img src="'.$sitepath.''.$obj->image1.'"  alt="'.$obj->title.'" />';}

                    if (JFile::exists( JPATH_SITE .DS. $obj->image2)  )
                    {
                        $html .=  '</a>';
                    }
                    $html .=  '</li>';
                    $cont++;
                }*/
            }
            //$html .=  '</ul>';
	    
		 
	       
		  //WRITE THE RESULT
		 if($write)
		 {
		      echo $html;
		 }else{
		      $globalreturn = $html;
		      return $html; 
		 }
        }

        function action()
        {
	 
        }
        
        function getGallery1($articleid, $fieldsattachid)
        {
		  // Load the tooltip behavior.
		  
		  
		  //FALANG COMPATIBILITY
		  $option = JRequest::getVar("option");
		  $task = JRequest::getVar("task");
		  $reference_id =  JRequest::getVar("reference_id");
		  $catid = JRequest::getVar("catid");
		  $body = JResponse::getBody();
		  $falangedit = false;
		  if((($option == "com_falang") && ($catid == "content") && (!empty($reference_id))) || (($option == "com_falang")&&($task == "translate.edit")&&($catid == "content"))) {
			   $falangedit = true;
		  }
      
      
		  $db = JFactory::getDbo();
		  $directory = "administrator/";
		  if ( JFactory::getApplication()->isAdmin()) {
		      $directory = "";
		  }
      
		  $query = $db->getQuery(true);
		  // Select some fields
		  $query->select('*');
      
		  // From the hello table
		  $query->from('#__fieldsattach_images');
		  $query->where("articleid = ".$articleid." AND fieldsattachid=".$fieldsattachid);
      
		  $query->order("ordering");
       
		  // $db = JFactory::getDbo();
      
		  $db->setQuery($query);
		       //$str = $query;
		  $rows= $db->loadObjectList();
		  $str = '<div style="position:relative;  top:-20px;"><a class=\'modal\' rel=\'{handler: "iframe", size: {x: 980, y: 500}}\' href=\''.JURI::base(false).'/'.$directory.'index.php?option=com_fieldsattach&view=fieldsattachimage&layout=edit&tmpl=component&reset=2\'></a>
			      ';
		   //FALANG COMPATIBILITY
		  if(!$falangedit){
			   $str .= "<div class='button2-left'><div class='image'><a class='modal button' rel='{handler: \"iframe\", size: {x: 980, y: 500}}' href='".JURI::base(false)."index.php?option=com_fieldsattach&view=fieldsattachimage&layout=edit&tmpl=component&reset=2&fieldsattachid=".$fieldsattachid."'>";
			   $str .= JText::_("NEW IMAGE").'</a></div></div>';
			    $str .= "<a href='#' onclick='update_gallery".$fieldsattachid."();return false;'><img src='".JURI::base(false)."/".$directory."components/com_fieldsattach/images/icon-refresh.png' alt='refresh' /></a>";
			      //$str .= "<a class='modal' rel='{handler: \"iframe\", size: {x: 980, y: 500}}' href='index.php?option=com_fieldsattach&view=fieldsattachimage&layout=edit&tmpl=component&reset=2&fieldsattachid=".$fieldsattachid."'><img src='components/com_fieldsattach/images/icon-32-new.png' alt='refresh' /></a>";
			   $str .= '</div>';
			 

			   if (empty($_SERVER['HTTPS'])) {
				       $str .= '
					   <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
					   <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
					   <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
					   <script>jQuery.noConflict();</script>';
				}else{
					  $str .= '
					   <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
					   <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
					   <script src="https://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
					   <script>jQuery.noConflict();</script>';
				}
		  }
		 
	       
      
		   
		  $str .= " 
			   <style> 
			   .ui-sortable{overflow:hidden; margin:0; padding:0;}
			   .sortable li  {
			     cursor: move; margin: 0 10px 12px 0; border:1px #ccc solid; float:left;border-radius: 5px;
			     padding: 3px;   width:200px; height:200px; overflow:hidden;
			   } 
			   </style>"; 
				       
		  if(!$falangedit){ $str .= '<div   class="ui-sortable">';}else{$str .= '<div>';}
		
		  $str .='<ul id="gallerysortable'.$fieldsattachid.'" class="sortable" >';
		  $sitepath  =  fieldsattachHelper::getabsoluteURL(); 
		  if($rows>0){
			   foreach ($rows as $row)
			   {
				    //$url_edit ='index.php?option=com_fieldsattach&amp;task=fieldsattachimage.edit&amp;id='.$row->id.'&amp;tmpl=component&amp;reset=2&amp;fieldsattachid='.$fieldsattachid.'&amp;direct=true';
				    $url_edit =JURI::base(false).'/index.php?option=com_fieldsattach&view=fieldsattachimage&tmpl=component&layout=edit&id='.$row->id.'&fieldsattachid='.$fieldsattachid.'&reset=2';
				    $url_delete =JURI::base(false).'/index.php?option=com_fieldsattach&amp;view=fieldsattachimages&amp;task=delete&amp;id='.$row->id.'&amp;tmpl=component&amp;fieldsid='.$fieldsattachid;
				     
				    //FALANG COMPATIBILITY
				    if($falangedit){
					     $url_edit .= '&falang=true';
					     
				    }
				    
				    $str.= ' <li id="image_'.$fieldsattachid.'_'.$row->id.'"  style="width:150px; height:150px; ">
				    <div class="btn-group" style="margin-bottom:24px; overflow:hidden;" >';
				    if(!$falangedit){
					      $str .=' <div class="button2-left"><div class="blank"><a class="modal btn btn-mini" style="font-size:9px;" href="'.$sitepath.$row->image1.'">'.JText::_('ZOOM').'</a> </div> </div>';
				    }
				    if(!$falangedit){
					     $str .='<div class="button2-left"><div class="blank"><a class="modal btn btn-mini" style="font-size:9px;" href="'.$url_edit.'" rel="{handler: \'iframe\', size: {x: 980, y: 500}}">'.JText::_('EDIT').'</a> </div> </div>';
				    }
				    
				    if(!$falangedit){
					     $str .=' <div class="button2-left"><div class="blank"><a class="modal btn btn-mini" style="font-size:9px;" href="'.$url_delete.'" rel="{handler: \'iframe\', size: {x: 980, y: 500}}">'.JText::_('DELETE').'</a> </div> </div>';
				    };
				    $str .='  </div>
					     <img src="'.$sitepath.''.$row->image1.'" alt="'.$row->title.'" width="150" /></a>
			      
				    </li>';
			   }
		  }
		  
		  $str .= "</ul></div>";
		  
		   return $str;
        }
        
        
         /**
	 * getTemplate
	 *
	 * @access	public 
	 * @return  	html of field
	 * @since	1.0
	 */
        function getTemplate($fieldsids)
        {
             //Search field template GENERIC *****************************************************************
              $templateDir =  dirname(__FILE__).'/tmpl/imagegallery.tpl.php'; 
              $html = file_get_contents ($templateDir);
              
              //Search field template in joomla Template  ******************************************************  
              $app = JFactory::getApplication();
              $templateDir =  JPATH_BASE . '/templates/' . $app->getTemplate().'/html/com_fieldsattach/fields/imagegallery.tpl.php';
              
              if(file_exists($templateDir))
              {
                   
                  $html = file_get_contents ($templateDir);
              }
              
              //Search a specific field template in joomla Template  *********************************************  
              $app = JFactory::getApplication();
              $templateDir =  JPATH_BASE . '/templates/' . $app->getTemplate().'/html/com_fieldsattach/fields/'.$fieldsids.'_imagegallery.tpl.php';
              
              if(file_exists($templateDir))
              { 
                  $html = file_get_contents ($templateDir);
              }
              
              return $html;
        }
        
        /**
	 * getTemplate
	 *
	 * @access	public 
	 * @return  	html of field
	 * @since	1.0
	 */
        function getLineTemplate($fieldsids)
        {
             //Search field template GENERIC *****************************************************************
              $templateDir =  dirname(__FILE__).'/tmpl/imagegallery_line.tpl.php'; 
              $html = file_get_contents ($templateDir);
              
              //Search field template in joomla Template  ******************************************************  
              $app = JFactory::getApplication();
              $templateDir =  JPATH_BASE . '/templates/' . $app->getTemplate().'/html/com_fieldsattach/fields/imagegallery_line.tpl.php';
              
              if(file_exists($templateDir))
              {
                   
                  $html = file_get_contents ($templateDir);
              }
              
              //Search a specific field template in joomla Template  *********************************************  
              $app = JFactory::getApplication();
              $templateDir =  JPATH_BASE . '/templates/' . $app->getTemplate().'/html/com_fieldsattach/fields/'.$fieldsids.'_imagegallery_line.tpl.php';
              
              if(file_exists($templateDir))
              { 
                  $html = file_get_contents ($templateDir);
              }
              
              return $html;
        }

        /**
	 * getPublish
	 *
	 * @access	public
	 * @param	fieldsids	Id of fields
	 * @return  	published       published or not
	 * @since	1.0
	 */
        
        function getPublished( $fieldsids  )
        { 
             
            
	    $db = &JFactory::getDBO(  );

	    $query = 'SELECT  a.published  FROM #__fieldsattach  as a WHERE a.id = '.$fieldsids;
            $return="true|true";
            
            $db->setQuery( $query );
	    $published = $db->loadResult();  
            
            return $published;
        }
        
       

         

}
