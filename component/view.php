<?php
/**
 * @package     Windwalker.Framework
 * @subpackage  Component
 *
 * @author      Simon Asika <asika32764@gmail.com>
 * @copyright   Copyright (C) 2013 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Base class for a Joomla View
 *
 * Class holding methods for displaying presentation data.
 *
 * @package     Windwalker.Framework
 * @subpackage  Component 
 */
class AKView extends JViewLegacy
{
	/**
	 * Display this view, if in front-end, will show toolbar and submenus.
	 * 
	 * @param   string	$tpl	View layout name.
	 * @param   type	$path	The panel layout from?
	 *
	 * @return  string	Render result.
	 */
    public function displayWithPanel($tpl=null, $path = null)
    {
        $path = $path ? $path : AKPATH_LAYOUTS ;
        
        $this->innerLayout = JRequest::getVar('layout','default');
        $this->setLayout('panel');
        
        $this->addTemplatePath($path);
        $result = $this->loadTemplate($tpl);
        
        if (JError::isError($result)) {
            return $result;
        }
 
        echo $result;
    }
    
	/**
	 * Load the layout from component view, and put in panel layout.
	 * 
	 * @param   string	$tpl	View layout name.
	 *
	 * @return  string	Render result. 
	 */
    public function loadInnerTemplate($tpl=null)
    {
        $innerLayout = $this->setLayout($this->innerLayout);
        $result = $this->loadTemplate($tpl);
        
        return $result;
    }
    
    /**
	 * Set page title by JDocument.
	 * 
	 * @param   string	$title Title.  
	 */
    public function setTitle($title)
    {
        $doc = JFactory::getDocument();
        $doc->setTitle($title) ;
    }
    
    /**
	 * A quick function to show item information.
	 * 
	 * @param   JTable	$item	Item object.
	 * @param   string	$key	Information key.
	 * @param   string	$label	Information label. If is null, will use JText.
	 * @param   integer	$strip	If label use JText, strip key beginning units number, eg: 'a_item' => 'item'.
	 * @param   string	$link	Has link URL?
	 * @param   string	$class	Set class to this wrap.
	 *
	 * @return  string	Information HTML.    
	 */
    public function showInfo( $item, $key = null, $label = null, $strip = 2, $link = null ,$class = null)
    {
        if(empty($item->$key)){
            return false ;
        }
        
        $lang  = $strip ? substr($key, $strip) : $key ;
        
        if(!$label){
            $label = JText::_(strtoupper($this->option).'_'.strtoupper($lang)) ;
        }else{
            $label = JText::_(strtoupper($label)) ;
        }
        
        $value = $item->$key ;
        
        if($link){
            $value = JHtml::_('link', $link, $value);
        }
        
        $lang = str_replace( '_', '-', $lang );
        
        $info =
<<<INFO
        <div class="{$lang} {$class}">
            <span class="label">{$label}:</span>
            <span class="value">{$value}</span>
        </div>
INFO;
        return $info ;
    }
}