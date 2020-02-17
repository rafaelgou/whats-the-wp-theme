<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ThemeFinderService;
use Illuminate\Support\Facades\Log;

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
        $data = [
            'uri'    => $request->session()->get('uri'),
            'search' => $request->session()->get('search'),
            'error'  => $request->session()->get('error'),
        ];

        $uri    = $request->session()->get('uri', false);
        $search = $request->session()->get('search', false);
        $error  = $request->session()->get('error', false);
        if (!$uri && !$search && !$error) {
            $data['error'] = 'No search yet - do it below';
        }

        return view('results', $data);
    }

    /**
     * Results
     *
     * @param  int  $id
     * @return Response
     */
    public function search(Request $request)
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
            Log::error($e->getMessage());
        }

        if (null !== $search && null === $search->main) {
            $error = 'Unable to discover the theme - is that a Wordpress site?';
        }

        return redirect()->route('results')->with([
            'uri'    => $uri,
            'search' => $search,
            'error'  => $error,
        ]);
    }
}
