<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opd;

class PublicController extends Controller
{
    public function index()
    {
        // Fetch random OPDs for the "Instansi" section
        $opds = Opd::inRandomOrder()->take(6)->get();

        return view('landing', compact('opds'));
    }

    public function show($slug)
    {
        $instansi = Opd::where('slug', $slug)->with('bidangs')->firstOrFail();
        return view('instansi.detail', compact('instansi'));
    }
}
