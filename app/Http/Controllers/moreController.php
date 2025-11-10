<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class moreController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->input('name', 'Another Random Name');
        $url = $request->fullUrlWithoutQuery(['type']);
        $host = $request->host();
        $httpHost = $request->httpHost();
        $schemeAndHttpHost = $request->schemeAndHttpHost();
        $input = $request->input('name', 'default');
        return view('more', ['name' => $name, 'url' => $url, 'host' => $host, 'httpHost' => $httpHost, 'schemeAndHttpHost' => $schemeAndHttpHost, 'input' => $input]);
    }
}
