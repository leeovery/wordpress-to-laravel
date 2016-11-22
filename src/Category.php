<?php
/**
 * Created by PhpStorm.
 * User: leeovery
 * Date: 18/11/2016
 * Time: 16:44
 */

namespace LeeOvery\WordpressToLaravel;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table   = 'post_categories';

    protected $guarded = ['id'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}