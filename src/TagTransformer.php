<?php
/**
 * Created by PhpStorm.
 * User: leeovery
 * Date: 19/11/2016
 * Time: 10:57
 */

namespace LeeOvery\WordpressToLaravel;

use League\Fractal\TransformerAbstract;

class TagTransformer extends TransformerAbstract
{
    public function transform($post)
    {
        $embedded = collect($post->_embedded);

        if ($embedded->has('wp:term')) {

            return $embedded->only('wp:term')
                            ->flatten(2)
                            ->where('taxonomy', 'post_tag')
                            ->pluck('name')
                            ->map(function ($tag) {
                                return title_case($tag);
                            })
                            ->toArray();
        }

        return [];
    }
}