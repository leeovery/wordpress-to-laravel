<?php
/**
 * Created by PhpStorm.
 * User: leeovery
 * Date: 19/11/2016
 * Time: 10:57
 */

namespace LeeOvery\WordpressToLaravel;

use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform($post)
    {
        $embedded = collect($post->_embedded);

        if ($embedded->has('wp:term')) {

            $category = $embedded->only('wp:term')
                                 ->flatten(2)
                                 ->where('taxonomy', 'category')
                                 ->first();

            return [
                'wp_id' => $category->id,
                'name'  => $category->name,
                'slug'  => $category->slug,
            ];
        }

        return [];
    }
}