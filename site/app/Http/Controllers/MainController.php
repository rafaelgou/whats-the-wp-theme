<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

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
        return view('index', ['user' => ['name' => 'Rafael']]);
    }

    /**
     * Results
     *
     * @param  int  $id
     * @return Response
     */
    public function results(Request $request, $url)
    {
        $remoteIp = $request->getClientIp();



        return view('index', [
            'url'     => $url,
            'results' => []
        ]);
    }
}
