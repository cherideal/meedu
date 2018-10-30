<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) XiaoTeng <616896861@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    const SHOW_YES = 1;
    const SHOW_NO = -1;

    protected $table = 'books';

    protected $fillable = [
        'user_id', 'title', 'slug', 'thumb',
        'view_num', 'charge', 'short_description',
        'description', 'seo_keywords', 'seo_description',
        'published_at', 'is_show',
    ];

    protected $appends = [
        'edit_url', 'destroy_url',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $model->chapters()->delete();
        });
    }

    /**
     * @return string
     */
    public function getEditUrlAttribute()
    {
        return route('backend.book.edit', $this);
    }

    /**
     * @return string
     */
    public function getDestroyUrlAttribute()
    {
        return route('backend.book.destroy', $this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 作用域：不显示.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeNotShow($query)
    {
        return $query->where('is_show', self::SHOW_NO);
    }

    /**
     * 作用域：上线的视频.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', date('Y-m-d H:i:s'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chapters()
    {
        return $this->hasMany(BookChapter::class, 'book_id');
    }
}
