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
JLoader::register('fieldattach', 'components/com_fieldsattach/helpers/fieldattach.php');

// require helper file 
JLoader::register('fieldsattachHelper',    'administrator/components/com_fieldsattach/helpers/fieldsattach.php');


/**
 * Example system plugin
 */
class plgContentfieldsattachment extends JPlugin
{
         private $path;
         private $array_fields;
         
	/**
	 * Handle extension uninstall
	 *
	 * @param	JInstaller	Installer instance
	 * @param	int			extension id
	 * @param	int			installation result
	 * @since	1.6
	 */
	function onExtensionAfterUninstall($installer, $eid, $result)
	{
		JError::raiseWarning(-1, 'plgExtensionExample::onExtensionAfterUninstall: Uninstallation of '. $eid .' was a '. ($result ? 'success' : 'failure'));
	}

	/**
	 * @since	1.6
	 */
	function onExtensionBeforeInstall($method, $type, $manifest, $eid)
	{
		JError::raiseWarning(-1, 'plgExtensionExample::onExtensionBeforeInstall: Installing '. $type .' from '. $method . ($method == 'install' ? ' with manifest supplied' : ' using discovered extension ID '. $eid));
	}


        /**
	 * Example after display content method
	 *
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param	string		The context for the content passed to the plugin.
	 * @param	object		The content object.  Note $article->text is also available
	 * @param	object		The content params
	 * @param	int			The 'page' number
	 * @return	string
	 * @since	1.6
	 */
	public function onContentBeforeDisplay($context, &$article, &$params)
	{
		$app = JFactory::getApplication();
                $db = &JFactory::getDBO(  );

                $this->url=  'images'.DS.'documents';
                $this->path=   JPATH_BASE .DS.'images'.DS.'documents';
  
 
                if ( JRequest::getVar('option')=="com_content" ){
                    $this->getAll($article);
                }
                 
		 
	}

        private function getAll(&$article)
        {
            if(!empty($article->id) && (JRequest::getVar("view")=="article")){
            $db = &JFactory::getDBO(  );
            $query = 'SELECT *  FROM #__extensions as a WHERE a.folder = "fieldsattachment"  AND a.enabled= 1';
            $db->setQuery( $query );
            $results_plugins = $db->loadObjectList();
            
            $tmp_fields[]=array();
            $tmp_fields = fieldsattachHelper::getfieldsForAll($article->id);
            
            $fields[]=array();
           // $fields = $this->getfields($article->id);
            $fields =fieldsattachHelper::getfields($article->id);


            $fields = array_merge($tmp_fields, $fields );

            $fields_tmp2[]=array();
            $fields_tmp2 = fieldsattachHelper::getfieldsForArticlesid($article->id, $fields);

            $fields = array_merge($fields, $fields_tmp2 );
                
            $idgroup = "";
            
            if(count($fields)>0){
                        //$body = str_replace('</head>', $header_code.'</head>', $body); 
                         // $idgroup =  $fields[0]->idgroup;
                          
                          $htmlwrapper = $this->params->def('htmlwrapper', 0);
                          $htmlfieldsattachwrapper = $this->params->def('htmlfieldsattachwrapper', 0);
                          
                          $str_before ='';
                          $str = '';
                         
                          
                          $fields_before = '';
                          $fields_after = '';
                          
                          
                          $cont = 0;
                          $idgroup = $fields[0]->idgroup;
                          //echo "GROUP INI: ".$idgroup;
                          
                          foreach($fields as $field)
                            { 
                                
                                //echo "<br><br><br><br>Field:".$cont." ID FIELD:".$field->id." -> ".$field->idgroup;
                                //NEW
                                JPluginHelper::importPlugin('fieldsattachment'); // very important
                                //select  
                                foreach ($results_plugins as $obj)
                                {
                                    $function  = "plgfieldsattachment_".$obj->element."::construct();";
                                    //NEW PACTH CRISTIAN 10_04_2012 =======================
                                    $base = JPATH_SITE;
                                    $file = $base.'/plugins/fieldsattachment/'.$obj->element.'/'.$obj->element.'.php';
                                    //echo "<br>".$file;
                                    if( JFile::exists($file)){
                                        //file exist
                                        eval($function);
                                                                    // eval($function);
                                        $i = count($this->array_fields);
                                        $this->array_fields[$i] = $obj->element;
                                        //$str .= "<br> ".$field->type." == ".$obj->element;
                                        if (($field->type == $obj->element)&&($field->visible ))
                                        {
                                            $function  = "plgfieldsattachment_".$obj->element."::getHTML(".$article->id.",". $field->id.");";
                                            //$str .= "<br> ".$function ;
                                            if($field->positionarticle==1){

                                                eval("\$fields_before .=".  $function.""); 
                                            }else
                                            {
                                                    eval("\$fields_after .=".  $function.""); 
                                            }
                                            // $str .= $function;  	
                                        }
                                    }
                                    //=====================================================
                                    
                                }

                               
                              
                                $idgroupanterior = $fields[$cont]->idgroup;
                                $cont++;
                                //It's a new group
                                $idgrouptmp = -1;
                                
                                //echo '<br>ILENGHT:: '.count($fields).' > '.($cont+1)."<br>";
                                if(count($fields)>($cont)){
                                    
                                     //echo '<br>IDGROUP: '.$fields[$cont]->idgroup;
                                    $idgrouptmp = $fields[$cont]->idgroup; 
                                }
                                
                                if($idgroup != $idgrouptmp)
                                {
                                   
                                    
                                  //  echo "<br><br>...----<br>Grupo : ".$idgroup." -- ".$idgrouptmp;
                                    //echo "<br>BEFORE :".$str_before;
                                    //echo "<br>AFETER :".$str;
                                    if(count($fields)>($cont)){ $idgroup = $fields[$cont]->idgroup; }
                                    if($htmlwrapper==1){
                                        if($cont > 0) {
                                            //$str_before .= '</div>';
                                            //$str .= '</div>';
                                            if(!empty($fields_before)) $str_before .= '<div class="fieldgroup before" id="group_'.$idgroupanterior.'_before">'.$fields_before.'</div>';
                                            if(!empty($fields_after)) $str .= '<div class="fieldgroup after" id="group_'.$idgroupanterior.'_after">'.$fields_after.'</div>';
                                            
                                            
                                        }else{
                                            $str_before .= $fields_before;
                                            $str .= $fields_after;
                                            
                                        }
                                        $fields_before = '';
                                        $fields_after = '';

                                        //$str_before .= '<div class="fieldgroup before" id="group_'.$idgroup.'_before">';
                                        //$str .=  '<div class="fieldgroup after" id="group_'.$idgroup.'_after">';
                                    }else{
                                         $str_before .= $fields_before;
                                         $str .= $fields_after;
                                    }
                                }
                                
                                
                           } 
                            
                           $user = JFactory::getUser();
			   //For register user AND no register
			   if(( $article->access==2) && !($user->id > 0) ){
				    
				     if($htmlfieldsattachwrapper==1)
					     $article->introtext = '<div class="fieldsattach_before">'.$str_before.'</div>'.$article->introtext.'<div class="fieldsattach_after">'.$str.'</div>';
					else
					     $article->introtext = $str_before.$article->text.$str;
			   }else{
			   
				    if($htmlfieldsattachwrapper==1)
					 $article->text = '<div class="fieldsattach_before">'.$str_before.'</div>'.$article->text.'<div class="fieldsattach_after">'.$str.'</div>';
				    else
					 $article->text = $str_before.$article->text.$str;
					 
				    
				    //echo "<br>ACCESS::: ". $article->access;
			   }
                            
                                
                 }
            }
	   

          
        }
/*
        private function getfields($id)
        {
            jimport('joomla.language.helper');
            $result ="";
            $languages	= JLanguageHelper::getLanguages();
            $app	= JFactory::getApplication();
            $db	= & JFactory::getDBO();
            $query = 'SELECT  a.catid,a.language FROM #__content as a WHERE a.id='. $id  ;

            $db->setQuery( $query );
            $elid = $db->loadObject(); 
            $this->recursivecat($elid->catid, "");
           //  echo "<br>".$query." ".$elid->catid." ->  ".$this->str;
            //echo "<br>recursivecat : ".$this->str;
            //echo $db->loadObject();
            if(!empty($elid->catid)){
                $db	= & JFactory::getDBO();

                $query = 'SELECT a.id as idgroup, a.title as titlegroup, a.showtitle as shortitlegroup, a.catid, a.recursive, b.* , b.showtitle  FROM #__fieldsattach_groups as a INNER JOIN #__fieldsattach as b ON a.id = b.groupid ';
                $query .= ' WHERE a.catid IN ('. $this->str .') AND a.published=1 AND b.published = 1';
                //echo "Language: ".$elid->language;
                if ($app->getLanguageFilter() && (JRequest::getVar("language") == $elid->language ) ) {
                     $query .= ' AND (a.language="'.$elid->language.'" OR a.language="*" ) AND (b.language="'.$elid->language.'" OR b.language="*") ' ;
                    // echo "filter::". $app->getLanguageFilter();
                    // echo "filter::". JRequest::getVar("language");
                }
                $query .= ' ORDER BY a.ordering, a.title,b.ordering';
                $db->setQuery( $query );
                $result = $db->loadObjectList();

      //echo $query;

                //***********************************************************************************************
                //Mirar cual de los grupos es RECURSIVO  ****************  ****************  ****************
                //***********************************************************************************************
                $cont = 0;
                foreach ($result as $field)
                {
                    
                    if( $field->catid != $elid->catid )
                    {
                        //Mirar si recursivamente si
                        if(!$field->recursive)
                            {
                                //echo "ELIMINO DE LA LISTA " ;
                                unset($result[$cont]);
                            }
                    }
                    $cont++;
                } 
                 
            }

            return $result;
        }

        private function getfieldsvalue($fieldsid, $articleid)
        {
            $result ="";
            $db	= & JFactory::getDBO();
            $query = 'SELECT a.value FROM #__fieldsattach_values as a WHERE a.fieldsid='. $fieldsid.' AND a.articleid='.$articleid  ;
            // echo "<br>GET VALUE:: ".$query;
            $db->setQuery( $query );
            $elid = $db->loadObject();
            $return ="";
            if(!empty($elid))  $return =$elid->value;
            return $return ;
        }
 

        function recursivecat($catid)
        { 
                if(!empty($this->str)) $this->str .=  ",";
                $this->str .= $catid ;
                //echo "SUMO:".$str."<br>";
                $db	= & JFactory::getDBO();
                $query = 'SELECT parent_id FROM #__categories as a WHERE a.id='.$catid   ;
                //echo $query."<br>";
                $db->setQuery( $query );
                $tmp = $db->loadObject();

                if($tmp->parent_id>0) $this->recursivecat($tmp->parent_id);



        } */

} 
