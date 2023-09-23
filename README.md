# WordpressToLaravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

You want a blog, right? And you want to use the Wordpress backend? But you dislike Wordpress themes, and REALLY dislike creating them? Then this is for you my friend...

This package provides the tool-set to sync WP posts from the WP DB and into your Laravel DB (or wherever you want it to go). This way you can still use the WP backend to author your posts and manage the backend users etc, but gives you glorious freedom to custom code your own frontend, without the nonsense that is WP themes.

It exposes itself as an Artisan command, which means you can set it up to run on a schedule (using Laravel's scheduler) to sync your WP posts, categories, tags and authors to your Laravel DB.

On to installation...

## Install

Via Composer:

``` bash
$ composer require leeovery/wordpress-to-laravel
```

After adding the package, add the ServiceProvider to the providers array in config/app.php:

``` php
LeeOvery\WordpressToLaravel\WordpressToLaravelServiceProvider::class,
```

Publish the config file:

``` bash
$ php artisan vendor:publish --provider="LeeOvery\WordpressToLaravel\WordpressToLaravelServiceProvider" --tag="config"
```

Migrate your database to setup the posts, categories, tags & author tables:

``` bash
$ php artisan migrate
```

Setup the url to your WP blog in your env file:

``` env
WP_TO_LARAVEL_API_URL=https://blog.your-blog-domain.com/
```

If you are using wordpress.com to host your blog, set the following env variable to true (default is false). This is done because wordpress.com and wordpress.org (self hosted instances) have different url structure when fetching the posts
``` env
WP_TO_LARAVEL_IS_WORDPRESS_COM=true
```

Finally, we need to configure WP itself. If you're using Wordpress 4.7+, then you're all set - crack on! Otherwise, you'll need to install the WP API plugin to the WP site you wish to sync from:

[Wordpress API](http://v2.wp-api.org/)

## Usage

Firstly, it's best to perform a full sync to get all your posts etc across in one go. After this it'll only sync page 1 of modified posts (by default).

To force sync all published posts:

``` bash
$ php artisan wordpress-to-laravel:import -F -T
```

The `-F` flag forces all posts to be synced. The `-T` flag will truncate all the relevant DB tables prior to syncing.

You can rerun that at any time to truncate and resync all your posts etc.

Once that's done, you should setup the following in your Laravel app so that all recently modified WP posts are synced across to the local DB:

``` php
$schedule->command('wordpress-to-laravel:import')
                 ->everyMinute();
```

### Showing Posts

Syncing is only half the job. You'll want to show your posts etc on your blog. But that's super easy too.

Just use the Post, Category & Author Eloquent models that are supplied. Alternatively you can provide your own if you need extra methods or functionality. 

For ease I suggest extending from the supplied models. If you want to use your own you should update the config file with your models. Also make sure the transformers work with your model(s) too, otherwise you'll need to supply new versions of those too (they should extend the AbstractTransformer from Fractal), and update the appropriate config value for that models transformer.

Example usage of supplied models (this code would appear in your BlogController, BlogTagController & BlogCategoryController):

``` php
// to show newest 5 posts, paginated...
$posts = Post::orderBy('published_at', 'desc')
                     ->paginate(5);

// to fetch a post using the slug, or fail...
$post = Post::slug($slug)->firstOrFail();

// to fetch tag by tag slug, or fail...
$tag = Post::createTagsModel()->whereSlug($tag)->firstOrFail();

// to fetch newest 5 posts (paginated) by tag slug (from above)...
$posts = Post::whereTag($tag->slug)
             ->orderBy('published_at', 'desc')
             ->paginate(5);

// to fetch category by category slug, or fail...
$category = Category::whereSlug($category)->firstOrFail();

// to fetch newest 5 posts (paginated) by category slug (from above)...
$posts = Post::whereCategory($category->slug)
             ->orderBy('published_at', 'desc')
             ->paginate(5);
```

### Media

When you upload media using the WP backend the links will point to your WP blog url. This might be ok for you. But for us it wasn't. So we installed the [S3 plugin](https://wordpress.org/plugins/amazon-s3-and-cloudfront/) on our WP site. This means that all media uploaded via WP will be posted up to your S3 storage, and the URLs to said images will be rewritten on the fly to the S3 location. These updated media urls will be respected when you sync.

### Redirecting WP Frontend To New Frontend

Lets say your WP blog is at https://blog.example.com, and your new Laravel frontend is at https://example.com/blog/. Ideally, you'll want your old frontend to redirect to your new frontend. This is simple to achieve by creating a little empty theme and activating it in your WP backend.

1. Create new theme in your WP themes dir called 'redirection_theme'
2. Create a file called style.css, and insert the following:

    ``` css
    /*
    Theme Name: turn off frontend
    Theme URI:
    Description:
    Author:
    Version:
    License: GNU
    License URI:
    Tags:
    */
    ```
    
3. Create a file called index.php, and insert the following:

    ``` php
    <?php
    global $wp;
    $url = str_replace('blog.', '', home_url() . '/blog/' . $wp->request);
    wp_redirect($url, 301);
    die();
    ```
    
4. Finally, create a file called functions.php, and insert the following:

    ``` php
    <?php
    function redirection_theme_change_view_link($permalink)
    {
        $url = str_replace('blog.', '', env('WP_HOME') . '/blog');
    
        return str_replace(env('WP_HOME'), $url, $permalink);
    }
    add_filter('post_link', 'redirection_theme_change_view_link');
    ```

5. Don't forget to activate it in your WP backend.

Enjoy!



## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Credits

- [Lee Overy][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/leeovery/wordpress-to-laravel.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/leeovery/wordpress-to-laravel/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/leeovery/wordpress-to-laravel.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/leeovery/wordpress-to-laravel.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/leeovery/wordpress-to-laravel.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/leeovery/wordpress-to-laravel
[link-travis]: https://travis-ci.org/leeovery/wordpress-to-laravel
[link-scrutinizer]: https://scrutinizer-ci.com/g/leeovery/wordpress-to-laravel/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/leeovery/wordpress-to-laravel
[link-downloads]: https://packagist.org/packages/leeovery/wordpress-to-laravel
[link-author]: https://github.com/leeovery
[link-contributors]: ../../contributors
