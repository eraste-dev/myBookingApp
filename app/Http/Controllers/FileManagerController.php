<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class FileManagerController extends Controller
{
    public function create(): View
    {
        return view('media.create');
    }

    public function store(Request $request)
    {
        $response = Http::post(route('media.store'));

        if ($response->successful()) {
            // Traiter la réponse ici
            dd($response);
        }
    }

}
