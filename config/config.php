<?php

return [

    /*
	|--------------------------------------------------------------------------
	| API URL
	|--------------------------------------------------------------------------
    |
    | This is the url of your blog where you've installed the Wordpress API
    | plugin.
    |
    | e.g. http://blog.example.dev/ or https://coolname.wordpress.com/
	*/
    'api_url'        => env('WP_TO_LARAVEL_API_URL'),

    /*
	|--------------------------------------------------------------------------
	| IS WORDPRESS COM
	|--------------------------------------------------------------------------
    |
    | Is your site hosted on wordpress.com
    | There are two types of wordpress, wordpress.com which hosts the blog, or
    | wordpress.org which is self hosted
    | This matters because they have differnt urls for the API
    |
    | e.g. true (defaults false)
    */
    'is_wordpress_com'  => env('WP_TO_LARAVEL_IS_WORDPRESS_COM', false),

    /*
	|--------------------------------------------------------------------------
	| Models
	|--------------------------------------------------------------------------
    |
    | These are the models for the Post and Category. You can overwrite them if
    | you want to add additional methods. Just create your own and extend
    | from these base models.
    |
	*/
    'post_model'     => \LeeOvery\WordpressToLaravel\Post::class,
    'category_model' => \LeeOvery\WordpressToLaravel\Category::class,
    'author_model'   => \LeeOvery\WordpressToLaravel\Author::class,


    /*
	|--------------------------------------------------------------------------
	| Transformers
	|--------------------------------------------------------------------------
    |
    | These are the transformers for all the parts of the post. You can create
    | your own and define them here if you want to transform the data another
    | way.
    |
	*/
    'transformers'   => [
        'post'     => \LeeOvery\WordpressToLaravel\PostTransformer::class,
        'category' => \LeeOvery\WordpressToLaravel\CategoryTransformer::class,
        'author'   => \LeeOvery\WordpressToLaravel\AuthorTransformer::class,
        'tag'      => \LeeOvery\WordpressToLaravel\TagTransformer::class,
    ],

];
