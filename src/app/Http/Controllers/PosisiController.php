<?php

namespace App\Http\Controllers;

use App\Models\Posisi;
use Illuminate\Http\Request;

class PosisiController extends Controller
{
    public function index()
    {
        $q = request()->query('q');

        $query = Posisi::orderBy('nama_posisi');
        if ($q) {
            $query->where('nama_posisi', 'like', "%{$q}%");
        }

        if (request()->wantsJson() || request()->ajax()) {
            $items = $query->limit(50)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama_posisi' => $item->nama_posisi,
                ];
            });
            return response()->json($items);
        }

        $posisis = $query->paginate(10)->withQueryString();
        return view('posisi.index', compact('posisis','q'));
    }

    public function create()
    {
        return view('posisi.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_posisi' => ['required','string','max:255','unique:posisi,nama_posisi'],
        ]);

        Posisi::create($data);

        return redirect()->route('posisi.index')->with('success', 'Posisi berhasil dibuat.');
    }

    public function edit(Posisi $posisi)
    {
        return view('posisi.edit', compact('posisi'));
    }

    public function update(Request $request, Posisi $posisi)
    {
        $data = $request->validate([
            'nama_posisi' => ['required','string','max:255','unique:posisi,nama_posisi,'.$posisi->id],
        ]);

        $posisi->update($data);

        return redirect()->route('posisi.index')->with('success', 'Posisi berhasil diperbarui.');
    }

    public function destroy(Posisi $posisi)
    {
        $count = $posisi->karyawan()->count() ?? 0;
        if ($count > 0) {
            return redirect()->route('posisi.index')->with('error', "Tidak dapat menghapus posisi karena terdapat {$count} karyawan. Pindahkan atau hapus karyawan terkait terlebih dahulu.");
        }

        $posisi->delete();
        return redirect()->route('posisi.index')->with('success', 'Posisi berhasil dihapus.');
    }
}
