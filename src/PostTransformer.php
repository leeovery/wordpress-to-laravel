<?php
/**
 * Created by PhpStorm.
 * User: leeovery
 * Date: 19/11/2016
 * Time: 10:57
 */

namespace LeeOvery\WordpressToLaravel;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'author',
        'category',
        'tags',
    ];

    /**
     * @var
     */
    private $authorTransformer;

    /**
     * @var
     */
    private $categoryTransformer;

    /**
     * @var
     */
    private $tagTransformer;

    /**
     * PostTransformer constructor.
     *
     * @param $authorTransformer
     * @param $categoryTransformer
     * @param $tagTransformer
     */
    public function __construct($authorTransformer, $categoryTransformer, $tagTransformer)
    {
        $this->authorTransformer = $authorTransformer;
        $this->categoryTransformer = $categoryTransformer;
        $this->tagTransformer = $tagTransformer;
    }

    public function transform($post)
    {
        return [
            'wp_id'          => (int)$post->id,
            'title'          => $post->title->rendered,
            'slug'           => $post->slug,
            'sticky'         => $post->sticky ?? 0,
            'excerpt'        => $post->excerpt->rendered ?? '',
            'content'        => $post->content->rendered ?? '',
            'format'         => $post->format ?? null,
            'status'         => 'publish',
            'featured_image' => $this->getFeaturedImage($post),
            'published_at'   => $this->carbonDate($post->date),
            'created_at'     => $this->carbonDate($post->date),
            'updated_at'     => $this->carbonDate($post->modified),
        ];
    }

    private function getFeaturedImage($post)
    {
        $embedded = collect($post->_embedded ?? []);

        if ($embedded->has('wp:featuredmedia')) {
            $media = head($embedded['wp:featuredmedia']);

            if (isset($media->source_url)) {
                return $media->source_url;
            }
        }

        return null;
    }

    /**
     * @param $date
     * @return Carbon
     */
    private function carbonDate($date)
    {
        return Carbon::parse($date);
    }

    /**
     * Include author
     *
     * @param $post
     * @return \League\Fractal\Resource\Item
     */
    public function includeAuthor($post)
    {
        return $this->item($post, new $this->authorTransformer);
    }

    /**
     * Include category
     *
     * @param $post
     * @return \League\Fractal\Resource\Item
     */
    public function includeCategory($post)
    {
        return $this->item($post, new $this->categoryTransformer);
    }

    /**
     * Include tags
     *
     * @param $post
     * @return \League\Fractal\Resource\Item
     */
    public function includeTags($post)
    {
        return $this->item($post, new $this->tagTransformer);
    }
}