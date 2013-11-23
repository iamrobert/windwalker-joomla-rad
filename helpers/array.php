<?php
/**
 * @package     Windwalker.Framework
 * @subpackage  Helpers
 *
 * @author      Simon Asika <asika32764@gmail.com>
 * @copyright   Copyright (C) 2013 Asikart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


// No direct access
defined('_JEXEC') or die;

/**
 * Enhance JArrayHelper, and add some useful functions.
 *
 * @package     Windwalker.Framework
 * @subpackage  Helpers
 */
class AKHelperArray {

    /**
     * Pivot a two-dimensional matrix array.
     * 
     * @param  array    $array  An array with two level.
     *
     * @return array    An pivoted array.
     */
    public static function pivot($array)
    {
        $array     = (array) $array ;
        $new    = array();
        $keys     = array_keys($array) ;
        
        foreach( $keys as $k => $val ):
            
            foreach( (array) $array[$val] as $k2 => $v2 ):
                
                $new[$k2][$val] = $v2 ;
                
            endforeach;
             
        endforeach;
        
        return $new ;
    }
    
    /**
     * Pivot Array, separate by key. Same as AKHelperArray::pivot().
     * 
     * From:
     * 
     *         [value] => Array
     *             (
     *                 [0] => aaa
     *                 [1] => bbb
     *             )
     *     
     *         [text] => Array
     *             (
     *                 [0] => aaa
     *                 [1] => bbb
     *             )
     *     
     *
     *  To:
     *
     *         [0] => Array
     *             (
     *                 [value] => aaa
     *                 [text] => aaa
     *             )
     *     
     *         [1] => Array
     *             (
     *                 [value] => bbb
     *                 [text] => bbb
     *             )
     *             
     * @param array $array An array with two level.
     *
     * @return array An pivoted array.
     */
    public static function pivotByKey($array)
    {
        $array     = (array) $array ;
        $new    = array();
        $keys     = array_keys($array) ;
        
        foreach( $keys as $k => $val ):
            
            foreach( (array) $array[$val] as $k2 => $v2 ):
                
                $new[$k2][$val] = $v2 ;
                
            endforeach;
             
        endforeach;
        
        return $new ;
    }
    
    /**
     * Same as AKHelperArray::pivot().
     * 
     * From:
     * 
     *          [0] => Array
     *             (
     *                 [value] => aaa
     *                 [text] => aaa
     *             )
     *     
     *         [1] => Array
     *             (
     *                 [value] => bbb
     *                 [text] => bbb
     *             )
     * 
     * To:
     *
     *         [value] => Array
     *             (
     *                 [0] => aaa
     *                 [1] => bbb
     *             )
     *     
     *         [text] => Array
     *             (
     *                 [0] => aaa
     *                 [1] => bbb
     *             )
     * 
     * @param array $array An array with two level.
     *
     * @return array An pivoted array.
     */
    public static function pivotBySort($array)
    {
        $array     = (array) $array ;
        $new    = array();
        
        $array2 = $array ;
        $first    = array_shift($array2) ;

        foreach( $array as $k => $v ):
            
            foreach( (array) $first as $k2 => $v2 ):
                
                $new[$k2][$k] = $array[$k][$k2] ;
                
            endforeach;
            
        endforeach;
        
        return $new ;
    }
    
    /**
     * Pivot $origin['prefix_xxx'] to $target['prefix']['xxx'].
     * 
     * @param    string   $prefix    A prefix text.
     * @param    array    $origin    Origin array to pivot.
     * @param    array    $target    A target array to store pivoted value.
     *
     * @return   array    Pivoted array.
     */
    public static function pivotFromPrefix( $prefix ,$origin, $target = null)
    {
        $target = is_object($target) ? (object)$target : (array)$target ;
        
        foreach( (array)$origin as $key => $row ):
            if( strpos( $key, $prefix ) === 0 ){
                $key2 = JString::substr($key, JString::strlen($prefix)) ;
                self::setValue($target, $key2, $row) ;
            }
        endforeach;
        
        return $target;
    }
    
    /**
     * Pivot $origin['prefix']['xxx'] to $target['prefix_xxx'].
     * 
     * @param    string   $prefix    A prefix text.
     * @param    array    $origin    Origin array to pivot.
     * @param    array    $target    A target array to store pivoted value.
     *
     * @return   array    Pivoted array.
     */
    public static function pivotToPrefix( $prefix ,$origin, $target = null)
    {
        $target = is_object($target) ? (object)$target : (array)$target ;

        foreach( (array)$origin as $key => $val ):
        
            $key = $prefix.$key ;
            
            if(!self::getValue($target, $key)){
                self::setValue($target, $key, $val) ;
            }
            
        endforeach;
        
        return $target;
    }
    
    /**
      * Pivot two-dimensional array to one-dimensional.
      * 
      * @param   array  $array  A two-dimension array.
      *
      * @return  array  Pivoted array.
      */
    public static function pivotFromTwoDimension(&$array)
    {
        foreach( (array)$array as $val ):
			if(is_array($val) || is_object($val)) {
				foreach( (array)$val as $key => $val2 ):
					self::setValue($array, $key, $val2) ;
				endforeach;
			}
		endforeach;
        
        return $array ;
    }
    
    /**
      * Pivot one-dimensional array to two-dimensional array by a key list.
      * 
      * @param   array  Array to pivot.
      * @param   array  The fields' key list.
      *
      * @return  array  Pivoted array.
      */
    public static function pivotToTwoDimension(&$array, $keys = array())
    {
        foreach( (array)$keys as $key ):
            if( is_object($array) ) {
                $array2 = clone $array ;
            }else{
                $array2 = $array ;
            }
            self::setValue($array, $key, $array2) ;
        endforeach;
        
        return $array ;
    }
    
    /**
    * Query a two-dimensional array values to get second level array.
    * 
    * @param    array    $array      An array to query.
    * @param    mixed    $queries    Query strings, may contain Comparison Operators: '>', '>=', '<', '<='.
    *                                 <br />Example:
    *                                     <br />array(
    *                                         <br />'id'             => 6 ,     // Get all elements where id=6
    *                                         <br />'>published'     => 0     // Get all elements where published>0
    *                                     <br />) ;
    * @param    boolean  $keepKey    Keep origin array keys.
    *
    * @return   array    An new two-dimensional array queried.
    */
    public static function query($array, $queries = array(), $keepKey = false)
    {
        $results = array();
        $queries = (array) $queries ;
        
        // Visit Array
        foreach( $array as $k => $v ):
            
            $data = (array) $v ;
            
            // Visit Query Rules
            foreach( $queries as $key => $val ):
                
                // Key: is query key
                // Val: is query value
                // Data: is array element
                $value = null ;
                
                if( substr($val, 0, 2) == '>=' ) {
                    
                    if( JArrayHelper::getValue( $data, $key ) >= substr($val, 2) ){
                        $value = $v ;
                    }
                    
                }elseif( substr($val, 0, 2) == '<=' ) {
                    
                    if( JArrayHelper::getValue( $data, $key ) <= substr($val, 2) ){
                        $value = $v ;
                    }
                    
                }elseif( substr($val, 0, 1) == '>' ) {
                    
                    if( JArrayHelper::getValue( $data, $key ) > substr($val, 1) ){
                        $value = $v ;
                    }
                    
                }elseif( substr($val, 0, 1) == '<' ) {
                    
                    if( JArrayHelper::getValue( $data, $key ) < substr($val, 1) ){
                        $value = $v ;
                    }
                    
                }else{
                    
                    if( JArrayHelper::getValue( $data, $key ) == $val ){
                        $value = $v ;
                    }
                    
                }
                
                
                 // Set Query results
                if($value) {
                    if($keepKey) {
                        $results[$k] = $value ;
                    }else{
                        $results[] = $value ;
                    }
                }
                
            endforeach;
            
        endforeach;
        
        return $results ;
    }
    
    /**
    * Set a value into array or object.
    * 
    * @param    mixed    $array  An array to set value.
    * @param    string   $key    Array key to store this value.
    * @param    mixed    $value  Value which to set into array or object.
    *
    * @return   mixed    Result array or object.
    */
    public static function setValue(&$array, $key, $value)
    {
        if( is_array($array) ) {
            $array[$key] = $value ;
        }elseif( is_object($array) ){
            $array->$key = $value ;
        }
        
        return $array;
    }
    
    /**
    * A function like JArrayHelper::getValue(), but support object.
    * 
    * @param    mixed    $array      An array or object to getValue.
    * @param    string   $key        Array key to get value. 
    * @param    mixed    $default    Default value if key not exists.
    *
    * @return   mixed    The value.
    */
    public static function getValue(&$array, $key, $default = null)
    {
        if( is_array($array) ) {
            return JArrayHelper::getValue( $array, $key, $default );
        }
        
        // if not Array, we do not detect it for warning not Object
        $result = null ;
        if( isset($array->$key) ) {
            $result = $array->$key ;
        }
        
        if(is_null($result)){
            $result = $default ;
        }
        
        return $result ;
    }
    
    /**
     * Convert an Array or Object keys to new name by an array index.
     * 
     * @param   mixed $origin   Array or Object to convert.
     * @param   mixed $map      Array or Object index for convert.
     *
     * @return  mixed   Mapped array or object.
     */
    public static function mapKey($origin, $map = array())
    {
        $result = is_array($origin) ? array() : new StdClass() ;
        
        foreach( (array) $origin as $key => $val ):
            $newKey = self::getValue( $map, $key );
            
            if( $newKey ) {
                self::setValue( $result, $newKey, $val );
            }else{
                self::setValue( $result, $key, $val );
            }
        endforeach;
        
        return $result ;
    }
}


