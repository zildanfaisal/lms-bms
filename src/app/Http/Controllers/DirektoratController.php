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

    public function create()
    {
        return view('direktorat.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_direktorat' => ['required', 'string', 'max:255', 'unique:direktorat,nama_direktorat'],
        ]);

        Direktorat::create($data);

        return redirect()->route('direktorat.index')->with('success', 'Direktorat berhasil dibuat.');
    }

    public function edit(Direktorat $direktorat)
    {
        return view('direktorat.edit', compact('direktorat'));
    }

    public function show(Direktorat $direktorat)
    {
        // load divisi count and paginated list
        $countDivisi = $direktorat->divisis()->count();
        $divisis = $direktorat->divisis()->orderBy('nama_divisi')->paginate(10);

        return view('direktorat.show', compact('direktorat', 'countDivisi', 'divisis'));
    }

    public function update(Request $request, Direktorat $direktorat)
    {
        $data = $request->validate([
            'nama_direktorat' => ['required', 'string', 'max:255', 'unique:direktorat,nama_direktorat,' . $direktorat->id],
        ]);

        $direktorat->update($data);

        return redirect()->route('direktorat.index')->with('success', 'Direktorat berhasil diperbarui.');
    }

    public function destroy(Direktorat $direktorat)
    {
        $count = $direktorat->divisis()->count();
        if ($count > 0) {
            return redirect()->route('direktorat.index')->with('error', "Tidak dapat menghapus direktorat karena terdapat {$count} divisi. Hapus atau pindahkan divisi terlebih dahulu.");
        }

        $direktorat->delete();

        return redirect()->route('direktorat.index')->with('success', 'Direktorat berhasil dihapus.');
    }
}
