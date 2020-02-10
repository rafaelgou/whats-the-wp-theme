<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
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
    // protected $with  = ['parent', 'child'];

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
     * Create a Theme based on WP style.css array
     * @param  array  $data
     * @return Theme
     */
    public static function createByArray(array $data)
    {
        $theme = new Theme;

        $theme->name           = $data['Name'];
        $theme->uri            = $data['ThemeURI'];
        $theme->description    = $data['Description'];
        $theme->author         = $data['Author'];
        $theme->author_uri     = $data['AuthorURI'];
        $theme->version        = $data['Version'];
        $theme->template       = $data['Template'];
        $theme->status         = $data['Status'];
        $theme->tags           = $data['Tags'];
        $theme->text_domain    = $data['TextDomain'];
        $theme->domain_path    = $data['DomainPath'];
        $theme->license        = array_key_exists('License', $data) ? $data['License'] : null;
        $theme->license_uri    = array_key_exists('LicenseURI', $data) ? $data['LicenseURI'] : null;
        $theme->style_uri      = array_key_exists('style_uri', $data) ? $data['style_uri'] : null;
        $theme->theme_id       = array_key_exists('theme_id', $data) ? $data['theme_id'] : null;
        $theme->screenshot_uri = array_key_exists('screenshot_uri', $data) ? $data['screenshot_uri'] : null;
        $theme->type           = empty($data['Template']) ? 'main' : 'child';

        $theme->save();

        return $theme;
    }

    /**
     * Set Parent
     * @param Theme $parent
     * @return Theme
     */
    public function setParent(Theme $parent)
    {
        $this->parent_id = $parent->id;
        $this->save();

        return $this;
    }

    public function hasParent()
    {
        return $this->type === 'child';
    }
}
