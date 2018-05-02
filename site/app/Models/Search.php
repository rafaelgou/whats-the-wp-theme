<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Loading relationships
     *
     * @var array
     */
    protected $with  = ['parent', 'child'];

    /**
     * Get the phone record associated with the user.
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Theme', 'parent_id');
    }

    /**
     * Get the phone record associated with the user.
     */
    public function child()
    {
        return $this->hasOne('App\Models\Theme', 'parent_id');
    }

    /**
     * Create a Search based on  array
     * @param  array  $data
     * @return Search
     */
    public static function createByArray(array $data)
    {
        $search = new Search;

        $search->uri               = $data['uri'];
        $search->success           = $data['success'];
        $search->title             = $data['title'];
        $search->wordpress_version = $data['wordpress_version'];
        $search->ip                = $data['ip'];
        $search->main_theme_id     = $data['main_theme_id'];
        $search->child_theme_id    = $data['child_theme_id'];

        $search->save();

        return $search;
    }

}
