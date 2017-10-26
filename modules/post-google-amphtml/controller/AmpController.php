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
        
        $ctn = $post->content->value;
        
        // let put ads to the content
        $ads = [];
        if(module_exists('banner')){
            $banners = $this->banner->get('amphtml');
            if($banners){
                $cptl = substr_count($ctn, '</p>');
                $adsc = count($banners);
                $fecp = ceil($cptl/$adsc);
                $fecs = 0;
                foreach($banners as $ban){
                    $ads[$fecs] = $this->banner->render($ban);
                    $fecs+= $fecp;
                }
                
                $strpl = 0;
                for($i=0; $i<$cptl; $i++){
                    $lpos = strpos($ctn, '</p>', $strpl);
                    if(!$lpos)
                        break;
                    $strpl = $lpos+4;
                    if(isset($ads[$i]))
                        $ctn = substr_replace($ctn, "\n\n".'#{ADS/'.$i.'}', $strpl, 0);
                }
                
                foreach($ads as $ind => $ban)
                    $ctn = str_replace('#{ADS/'.$ind.'}', $ban, $ctn);
            }
        }
        
        // fix img src tinymce
        $ctn = str_replace('../../', '/', $ctn);
        
        $camp_opt = [
            'localImagePath' => BASEPATH,
            'localHost'      => $this->router->to('siteHome')
        ];
        
        $camp = new \Camp($ctn, $camp_opt);
        $post->a_content = $camp->amp;
        
        $params = [
            'post' => $post,
            'comps' => $camp->components
        ];
        
        // let convert embed as well
        if($post->embed->html){
            $embed = new \Camp($post->embed->html);
            $post->embed = $embed->amp;
            foreach($embed->components as $comp){
                if(!in_array($comp, $params['comps']))
                    $params['comps'][] = $comp;
            }
        }else{
            $post->embed = null;
        }
        
        $params['post']->meta = _Post::single($post);
        
        $this->respond('post/google-amphtml/single', $params, $cache);
    }
}