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

    // /**
    //  * Loading relationships
    //  *
    //  * @var array
    //  */
    // protected $with  = ['main', 'child'];

    /**
     * Get the main theme record associated with the search
     */
    public function main()
    {
        return $this->hasOne('App\Models\Theme', 'id', 'main_theme_id');
    }

    /**
     * Get the child theme record associated with the search
     */
    public function child()
    {
        return $this->hasOne('App\Models\Theme', 'id', 'child_theme_id');
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
