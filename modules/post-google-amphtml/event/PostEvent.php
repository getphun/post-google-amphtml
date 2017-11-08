<?php
/**
 * Post events
 * @package post-google-amphtml
 * @version 0.0.1
 * @upgrade false
 */

namespace PostGoogleAmphtml\Event;

class PostEvent{
    
    static function general($object, $old=null){
        $dis = \Phun::$dispatcher;
        
        $page = $dis->router->to('sitePostGoogleAmphtml', ['slug'=>$object->slug]);
        $dis->cache->removeOutput($page);
        
        if($old){
            $page = $dis->router->to('sitePostGoogleAmphtml', ['slug'=>$old->slug]);
            $dis->cache->removeOutput($page);
        }
    }
    
    static function updated($object, $old=null){
        self::general($object, $old);
    }
    
    static function deleted($object){
        self::general($object);
    }
}