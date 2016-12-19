<?php
/**
 * Created by PhpStorm.
 * User: leeovery
 * Date: 19/11/2016
 * Time: 10:57
 */

namespace LeeOvery\WordpressToLaravel;

use League\Fractal\TransformerAbstract;

class AuthorTransformer extends TransformerAbstract
{
    public function transform($post)
    {
        $embedded = collect($post->_embedded ?? []);

        if ($embedded->has('author')) {

            $author = head($embedded['author']);

            return [
                'wp_id'  => (int)$author->id,
                'name'   => $author->name ?? null,
                'slug'   => $author->slug ?? null,
                'email'  => $author->email ?? null,
                'avatar' => $author->avatar_urls->{96} ?? $author->avatar_urls->{48} ?? null,
            ];

        }

        return [];
    }
}