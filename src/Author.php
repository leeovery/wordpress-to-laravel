<?php
/**
 * Created by PhpStorm.
 * User: leeovery
 * Date: 18/11/2016
 * Time: 16:44
 */

namespace LeeOvery\WordpressToLaravel;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table   = 'post_author';

    protected $guarded = ['id'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}