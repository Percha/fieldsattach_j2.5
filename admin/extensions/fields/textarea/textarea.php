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
$sitepath = JPATH_ROOT ; 
JLoader::register('fieldattach',  $sitepath.DS.'components/com_fieldsattach/helpers/fieldattach.php');
JLoader::register('fieldsattachHelper',   $sitepath.DS.'administrator/components/com_fieldsattach/helpers/fieldsattach.php');
 
 
class plgfieldsattachment_textarea extends JPlugin
{
    protected $name;

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
        function plgfieldsattachment_textarea(& $subject, $config)
	{
		parent::__construct($subject, $config);
        }
	function construct( )
	{
            $name = "textarea";
            if(isset($this)) $this->name = $name; 

            //Load the Plugin language file out of the administration
            //$lang = & JFactory::getLanguage();
            //$lang->load('plg_fieldsattachment_'.$name, JPATH_ADMINISTRATOR);
	    
	    fieldsattachHelper::getLanguageFile($name);
            
	}
	  
        function getName()
        {  
                return $this->name;
        } 


        function renderInput($articleid, $fieldsid, $value, $extras=null )
        {
            $required="";
            $boolrequired = fieldattach::isRequired($fieldsid);
            if($boolrequired) $required="required";
            
            $tmp = $extras;
            $lineas = explode(chr(13),  $tmp);
            $height = 300;
            $str=""; 
            
            //Add CSS ***********************
            $str =  '<link rel="stylesheet" href="'.JURI::root() .'plugins/fieldsattachment/textarea/textarea.css" type="text/css" />'; 
            $app = JFactory::getApplication();
            $templateDir = JURI::base() . 'templates/' . $app->getTemplate();
            $css =  JPATH_SITE ."/administrator/templates/". $app->getTemplate(). "/html/com_fieldsattach/css/textarea.css";
            $pathcss= JURI::root()."administrator/templates/". $app->getTemplate()."/html/com_fieldsattach/css/textarea.css"; 
            if(file_exists($css)){ $str .=  '<link rel="stylesheet" href="'.$pathcss.'" type="text/css" />'; } 
            
            
            $defaultvalue="";
            $tipo="";
            
            
            if(!empty($extras))
            { 
                    
                    $tmp = explode('|',  $extras);
                    if(!empty( $tmp[0])) $tipo = $tmp[0]; 
                    if(count($tmp)>=1) if(!empty( $tmp[1])) $defaultvalue = $tmp[1]; 
                  
                
            }
            
            if(empty($value)) $value = $defaultvalue;
            
            $value = stripslashes($value);
                         
            if($tipo == "RichText")
            {
                $editor =& JFactory::getEditor();
                $str .=  $editor->display('field_'.$fieldsid.'', $value , '100%', ''.$height.'', '60', '20', true);

            }else{
                 $str .= '<textarea style="width:100%; height:'.$height.'px;" name="field_'.$fieldsid.'" >'.$value.'</textarea>';

            }
            
            //$str .= '<script>window.addEvent("load", function() { $("field_'.$fieldsid.'").addClass("'.$required.'"); } );</script>';
            return  $str;
        }

        function getoptionConfig($valor)
        {
             $name = $this->name;
             $return ='<option value="textarea" ';
             if("textarea" == $valor)   $return .= 'selected="selected"';
             $return .= '>'.$name.'</option>';
             return $return ;
        }

        function getHTML($articleid, $fieldsid, $category = false, $write=false )
        {
            global $globalreturn;
            
            $valor = fieldattach::getValue($articleid, $fieldsid, $category);
            $title = fieldattach::getName( $articleid,  $fieldsid , $category  );
            
            $html ="";
            $published = plgfieldsattachment_textarea::getPublished( $fieldsid  );
            if(!empty($valor) && $published){ 
                $html = plgfieldsattachment_textarea::getTemplate($fieldsid);

                /*
                    Templating INPUT *****************************

                    [TITLE] - Title of field
                    [FIELD_ID] - Field id 
                    [VALUE] - Value of input

                */ 

                if(fieldattach::getShowTitle(   $fieldsid  )) $html = str_replace("[TITLE]", $title, $html); 
                else $html = str_replace("[TITLE]", "", $html); 

                $html = str_replace("[VALUE]", $valor, $html);
                $html = str_replace("[FIELD_ID]", $fieldsid, $html);
                $html = str_replace("[ARTICLE_ID]", $articleid, $html);
                
                
            }
            

             
             //WRITE THE RESULT
           if($write)
           {
                echo $html;
           }else{
                $globalreturn = $html;
                return $html; 
           }
        }
        
        
        /**
	 * getTemplate
	 *
	 * @access	public 
	 * @return  	html of field
	 * @since	1.0
	 */
        function getTemplate($fieldsids, $file="textarea")
        {
             //Search field template GENERIC *****************************************************************
              $templateDir =  dirname(__FILE__).'/tmpl/'.$file.'.tpl.php'; 
              $html = file_get_contents ($templateDir);
              
              //Search field template in joomla Template  ******************************************************  
              $app = JFactory::getApplication();
              $templateDir =  JPATH_BASE . '/templates/' . $app->getTemplate().'/html/com_fieldsattach/fields/'.$file.'.tpl.php';
              
              if(file_exists($templateDir))
              {
                   
                  $html = file_get_contents ($templateDir);
              }
              
              //Search a specific field template in joomla Template  *********************************************  
              $app = JFactory::getApplication();
              $templateDir =  JPATH_BASE . '/templates/' . $app->getTemplate().'/html/com_fieldsattach/fields/'.$fieldsids.'_'.$file.'.tpl.php';
              
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

        function action()
        {

        }
       

}
