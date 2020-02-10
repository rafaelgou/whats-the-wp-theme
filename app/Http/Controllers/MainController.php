<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ThemeFinderService;
use App\Models\Search;
use App\Models\Theme;
use DB;

class MainController extends Controller
{

    /**
     * Home page
     *
     * @param  int  $id
     * @return Response
     */
    public function index()
    {
        return view('index', [
            'topSearched' => $this->topSearched()
        ]);
    }

    /**
     * Results
     *
     * @param  int  $id
     * @return Response
     */
    public function results(Request $request)
    {
        $error = false;

        try {
            $remoteIp    = $request->getClientIp();
            $uri         = $request->input('uri', null);
            $themeFinder = new ThemeFinderService($uri, $remoteIp);
            $search      = $themeFinder->search();
        } catch (\Exception $e) {
            $search = null;
            $error  = $e->getMessage();
        }

        if (null !== $search && null === $search->main) {
            $error = 'Unable to discover the theme - is that a Wordpress site?';
        }

        return view('results', [
            'uri'         => $uri,
            'search'      => $search,
            'error'       => $error,
            'topSearched' => $this->topSearched()
        ]);
    }

    /**
     * Search
     *
     * @param  int  $id
     * @return Response
     */
    public function search(Request $request, $id)
    {
        $error = false;
        $search = Search::find($id);

        if (null === $search || (null !== $search && null === $search->main)) {
          $error = null !== $search->main ? 'Theme not found' : 'Unable to discover the theme - is that a Wordpress site?';
          $uri = null !== $search ? $search->uri : false;
        } else {
          $uri = $search->uri;
        }

        return view('results', [
            'uri'         => $uri,
            'search'      => $search,
            'error'       => $error,
            'topSearched' => $this->topSearched()
        ]);
    }

    /**
     * Search
     *
     * @param  int  $id
     * @return Response
     */
    public function theme(Request $request, $themeName)
    {
        $theme = Theme::where('name', $themeName)->firstOrFail();

        return view('theme', [
            'theme' => $theme,
            'topSearched' => $this->topSearched()
        ]);
    }

    /**
     * Top Searched Query
     * @return array
     */
    protected function topSearched()
    {
        /*
        SELECT name, count(*) as total
        FROM themes
        WHERE `type` = 'main'
        GROUP BY name
        ORDER BY count(*) DESC
        LIMIT 10
        */
        return DB::table('themes')
            ->select(DB::raw('name, count(*) as total'))
            ->where([
              ['type', 'main'],
              ['name', '<>', ''],
            ])
            ->groupBy('name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
    }
}
