<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use Illuminate\Http\Request;

class PosisiController extends Controller
{
    public function index()
    {
        $posisis = Posisi::paginate(10);
        return view('posisi.index', compact('posisis'));
    }
}
