<?php
/**
 * Meta provider
 * @package post-google-amphtml
 * @version 0.0.1
 * @upgrade true
 */

namespace PostGoogleAmphtml\Meta;

class Post
{
    static function single($post){
        $dis = \Phun::$dispatcher;
        
        $base_url = $dis->router->to('siteHome');
        
        $meta_desc  = $post->meta_description->safe;
        if(!$meta_desc && $post->content)
            $meta_desc = $post->content->chars(160);
        
        if(!$post->cover)
            $meta_image = $base_url . 'theme/site/static/logo/500x500.png';
        else
            $meta_image = $post->cover->abs;
        
        $meta_url   = $post->page;
        $meta_title = $post->meta_title;
        $meta_keys  = $post->meta_keywords;
        if($meta_title == '')
            $meta_title = $post->title;
        
        // metas
        $single = (object)[
            'isamp'    => true,
            '_schemas' => [],
            '_metas'   => []
        ];
        
        $metas = [
            'title'         => $meta_title,
            'canonical'     => $meta_url,
            'description'   => $meta_desc,
            'keywords'      => $meta_keys,
            'image'         => $meta_image,
            'type'          => 'article',
            'updated_time'  => $post->updated->format('c'),
            'published_time'=> $post->published->format('c'),
            
            // TODO
            // use fb profile url instead
            'article:author'=> $post->user->fullname
        ];
        if($dis->setting->social_facebook)
            $metas['article:publisher'] = $dis->setting->social_facebook;
        
        if(isset($post->tag) && $post->tag){    
            $tags = [];
            foreach($post->tag as $tag)
                $tags[] = $tag->name;
            if($tags)
                $metas['article:tag'] = $tags;
        }
        
        if(is_object($post->canal))
            $metas['article:section'] = $post->canal->name;
        elseif(isset($post->category) && $post->category)
            $metas['article:section'] = $post->category[0]->name;
        
        $single->_metas = $metas;
        
        // schemas 
        if(!$post->schema_type)
            $post->schema_type = 'Article';
        
        $schema = [
            '@context'      => 'http://schema.org',
            '@type'         => $post->schema_type,
            'name'          => $post->title,
            'description'   => $meta_desc,
            'url'           => $meta_url,
            'image'         => $meta_image,
            'headline'      => substr($meta_desc, 0, 110),
            'datePublished' => $post->published->format('c'),
            'dateModified'  => $post->published->format('c'),
            'author'        => [
                '@type'         => 'Person',
                'name'          => $post->user->fullname
            ],
            'mainEntityOfPage' => [
                '@type'             => 'WebPage',
                '@id'               => $meta_url
            ],
            'publisher'     => $dis->meta->schemaOrganization(),
            'discussionUrl' => $post->page . '#comment',
            'isAccessibleForFree' => true
        ];
        if($post->source)
            $schema['isBasedOn'] = $post->source;
        if($post->meta_keywords)
            $schema['keywords'] = $post->meta_keywords;
        $single->_schemas[] = $schema;
        
        // schema breadcrumbs
        $second_item = [
            '@type' => 'ListItem',
            'position' => 2,
            'item' => [
                '@id' => $base_url . '#post',
                'name' => $dis->setting->post_index_meta_title
            ]
        ];
        
        if($dis->setting->post_index_enable){
            $second_item = [
                '@type' => 'ListItem',
                'position' => 2,
                'item' => [
                    '@id' => $dis->router->to('sitePost'),
                    'name' => $dis->setting->post_index_meta_title
                ]
            ];
        }
        
        $schema = [
            '@context'  => 'http://schema.org',
            '@type'     => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'item' => [
                        '@id' => $base_url,
                        'name' => $dis->config->name
                    ]
                ],
                $second_item
            ]
        ];
        
        $single->_schemas[] = $schema;
        
        return $single;
    }
}