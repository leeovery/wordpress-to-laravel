<?php
/**
 * Created by PhpStorm.
 * User: leeovery
 * Date: 18/11/2016
 * Time: 16:44
 */

namespace LeeOvery\WordpressToLaravel;

use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model implements TaggableInterface
{
    use TaggableTrait;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['tags', 'category', 'author'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function scopeWhereCategory(Builder $query, $category, $type = 'slug')
    {
        $query->whereHas('category', function ($query) use ($type, $category) {
            $query->where($type, $category);
        });

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  string                               $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    public function setCategory(array $categoryData)
    {
        if (! empty($categoryData)) {

            $category = Category::where('wp_id', $categoryData['wp_id'])
                                ->first();

            if (is_null($category)) {
                $category = Category::create($categoryData);
            }

            $this->category()->associate($category);
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function setAuthor(array $authorData)
    {
        if (! empty($authorData)) {

            $author = Author::where('wp_id', $authorData['wp_id'])
                            ->first();

            if (is_null($author)) {
                $author = Author::create($authorData);
            }

            $this->author()->associate($author);
        }
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}