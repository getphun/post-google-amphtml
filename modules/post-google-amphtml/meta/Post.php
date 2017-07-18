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
        $meta_image = $post->cover;
        if(!$meta_image)
            $meta_image = $base_url . 'theme/site/static/logo/500x500.png';
        $meta_url   = $post->page;
        $meta_title = $post->meta_title;
        $meta_keys  = $post->meta_keywords;
        if(!$meta_title == '')
            $meta_title = $post->title;
        
        // metas
        $single = (object)[
            '_schemas' => [],
            '_metas'   => [
                'title'         => $meta_title,
                'canonical'     => $meta_url,
                'description'   => $meta_desc,
                'keywords'      => $meta_keys,
                'image'         => $meta_image,
                'type'          => 'website'
            ]
        ];
        
        // schemas 
        if(!$post->schema_type)
            $post->schema_type = 'Article';
        
        $schema = [
            '@context'      => 'http://schema.org',
            '@type'         => $post->schema_type,
            'name'          => $post->title,
            'description'   => $meta_desc,
            'publisher'     => [
                '@type'         => 'Organization',
                'name'          => $dis->config->name,
                'url'           => $base_url,
                'logo'          => $base_url . 'theme/site/static/logo/500x500.png'
            ],
            'url'           => $meta_url,
            'image'         => $meta_image
        ];
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