<?php
/**
 * Post amphtml controller
 * @package post-google-amphtml
 * @version 0.0.1
 * @upgrade true
 */

namespace PostGoogleAmphtml\Controller;
use PostGoogleAmphtml\Meta\Post as _Post;
use Post\Model\Post as Post;

class AmpController extends \SiteController
{
    public function indexAction(){
        $slug = $this->param->slug;
        
        $post = Post::get(['slug'=>$slug], false);
        if(!$post)
            return $this->show404();
        
        $cache = 60*60*24*7;
        if(is_dev())
            $cache = null;
        
        $post = \Formatter::format('post', $post, true);
        
        $ctn = '';
        if($post->embed)
            $ctn.= $post->embed->html;
        $ctn.= '<p>' . $post->content->value . '</p>';
        
        $camp = new \Camp($ctn);
        $post->a_content = $camp->amp;
        
        $params = [
            'post' => $post,
            'comps' => $camp->components
        ];
        
        $params['post']->meta = _Post::single($post);
        
        $this->respond('post/google-amphtml/single', $params, $cache);
    }
}