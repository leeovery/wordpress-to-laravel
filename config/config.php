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
    | e.g. http://blog.example.dev/
	*/
    'api_url'        => env('WP_TO_LARAVEL_API_URL'),


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
