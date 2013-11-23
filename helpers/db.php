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
 * Database helper to handle some query strings.
 *
 * @package     Windwalker.Framework
 * @subpackage  Helpers
 */
class AKHelperDb
{
    /**
     * Store table to detect is nested.
     *
     * @var    array 
     */
    public static $nested = array();
    
    /**
     * Get select query from tables array. A proxy for AKHelperQuery::getSelectList().
     * 
     * @param   array    $tables   Tables name to get columns.
     * @param   boolean  $all      Contain a.*, b.* etc.
     *
     * @return  array   Select column list.
     * @deprecated  4.0
     */
    public static function getSelectList( $tables = array() , $all = true )
    {
        // Deprecation warning.
        JLog::add( __CLASS__.'::'.__FUNCTION__.'() is deprecated, Use AKHelperQuery::getSelectList() instead.', JLog::WARNING, 'deprecated');
        
        return AKHelper::_('query.getSelectList', $tables , $all);
    }
    
    /**
     * Merge filter_fields with table columns. A proxy for AKHelperQuery::mergeFilterFields().
     * 
     * @param    array    $filter_fields    Filter fields from Model.
     * @param    array    $tables            Tables name to get columns.
     *
     * @return    array    Filter fields list.
     * @deprecated  4.0
     */
    public static function mergeFilterFields( $filter_fields , $tables = array() )
    {
        // Deprecation warning.
        JLog::add( __CLASS__.'::'.__FUNCTION__.'() is deprecated. Use AKHelperQuery::mergeFilterFields() instead.', JLog::WARNING, 'deprecated');
        
        return AKHelper::_('query.mergeFilterFields', $filter_fields , $tables);
    }
    
    /**
     * Detect a view is nested table.
     * 
     * @param    string    $name    Table name.
     * @param    string    $option  Component element.
     *
     * @return  boolean  is this table a nested table?
     */
    public static function nested($name, $option = null)
    {
        if( !$option ){
            $option = AKHelper::_('path.getOption') ;
        }
        $option = str_replace('com_', '', $option) ;
        
        if(isset(self::$nested[$option. '.' .$name])) {
            return self::$nested[$option. '.' .$name] ;
        }
        
        JTable::addIncludePath( AKHelper::_('path.getAdmin').'/components/'.$opntion.'/tables' ) ;
        $table = JTable::getInstance($name, ucfirst($option).'Table');
        if($table instanceof JTableNested) {
            return self::$nested[$option. '.' .$name] = true ;
        }else {
            return self::$nested[$option. '.' .$name] = false ;
        }
    }
}
