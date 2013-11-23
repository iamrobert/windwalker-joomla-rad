<?php
/**
 * @package     Windwalker.Framework
 * @subpackage  Helpers
 *
 * @author      Simon Asika <asika32764@gmail.com>
 * @copyright   Copyright (C) 2013 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

 
// no direct access
defined('_JEXEC') or die;

/**
 * An extension path getter helper.
 *
 * @package     Windwalker.Framework
 * @subpackage  Helpers
 */
class AKHelperPath
{
    /**
     * Default component option.
     *
     * @var string 
     */
    static public $default_option = 'com_content' ;
    
    /**
     * Overrides a path, priority to use the new path, if not exists, use origin path.
     *
     * @var array
     */
    static public $overrides    = array();
    
    /**
      * Get compoent admin path.
      * 
      * @param	string	$option	Component option name.
      *
      * @return	string	Component admin path.
      */
    public static function getAdmin($option = null)
    {
        $option = $option ? $option : self::$default_option ;
        
        return $path = JPATH_ADMINISTRATOR."/components/{$option}" ;
    }
    
    /**
     * Get compoent site path.
     * 
     * @param   string    $option    Component option name.
     *
     * @return  string    Component site path.
     */
    public static function getSite($option = null)
    {
        $option = $option ? $option : self::$default_option ;
        
        return $path = JPATH_SITE."/components/{$option}" ;
    }
    
    /**
     * Get path on different client, site , admin ,or windwalker.
     * 
     * @param   string    $client    Client name, 'site', 'admin', 'administrator', 'ww' or 'windwalker'.
     * @param   string    $option    Component option name.
     *
     * @return  string    Component or WindWalker path.
     */
    public static function get( $client = null , $option = null)
    {
        $option = $option ? $option : self::$default_option ;
        
        if($client == 'site'){
            return self::getSite($option) ;
        }elseif($client == 'admin'){
            return self::getAdmin($option) ;
        }elseif($client == 'ww' || $client == 'windwalker'){
            return AKPATH_ROOT ;
        }else{
            return $path = JPATH_BASE."/components/{$option}" ;
        }
    }
    
    /**
     * Set a default component option.
     * 
     * @param   string    $option Component option name.
     */
    public static function setOption($option)
    {
        self::$default_option = $option ;
    }
    
    /**
     * Get current default option.
     *
     * @return  string    Component option name.
     */
    public static function getOption()
    {
        return self::$default_option ;
    }
    
    /**
     * Get Windwalker path.
     *
     * @return  string    Windwalker root path.    
     */
    public static function getWWPath()
    {
        return AKPATH_ROOT ;
    }
    
    /**
     * Get WindWalker URL.
     * 
     * @param   boolean    $absolute    True for absolute url.
     *
     * @return  string    Windwalker url.    
     */
    public static function getWWUrl($absolute = false)
    {
        $path = AKPATH_ROOT ;
        $root = JPATH_ROOT ;
        $path = str_replace(JPATH_ROOT, '', AKPATH_ROOT) ;
        $path = JPath::clean($path, '/');
        $path = trim($path, '/') ;
        
        if($absolute) {
            return $path = JURI::root() . $path;
        }
        else{
            return $path = JURI::root(true) . '/' . $path;
        }
        
    }
    
    /**
     * Get the file override folder path.
     *
     * Need to add override first for a folder path,
     * if override path not setted, will return origin path.
     * 
     * @param   string    $path    Origin path to get overridepath.
     *
     * @return  string    Result path.    
     */
    public static function getOverridePath($path)
    {
        if( !empty( self::$overrides[ $path ] ) ) {
            return self::$overrides[ $path ] ;
        }else{
            return $path ;
        }
    }
    
    /**
     * Add an origin path and override path to make them linked.
     * 
     * @param   string    $origin      Origin path to override.
     * @param   string    $override    Override path to replace origin path.
     */
    public static function addOverridePath($origin, $override)
    {
        self::$overrides[$origin] = $override ;
    }
    
    /**
     * Remove an override path, make it unlinked with origin path.
     * 
     * @param   string    $path    Override path to unlinked. 
     */
    public static function removeOverridePath($path)
    {
        unset( self::$overrides[$path] );
    }
}