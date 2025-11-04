<?php

namespace App\Http\Controllers;

use App\Models\Direktorat;
use Illuminate\Http\Request;

class DirektoratController extends Controller
{
    public function index()
    {
        $direktorats = Direktorat::paginate(10);
        return view('direktorat.index', compact('direktorats'));
    }
}
