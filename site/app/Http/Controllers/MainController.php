<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ThemeFinderService;
use App\Models\Search;

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
        return view('index');
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
            'uri'    => $uri,
            'search' => $search,
            'error'  => $error
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
            'uri'    => $uri,
            'search' => $search,
            'error'  => $error
        ]);
    }
}
