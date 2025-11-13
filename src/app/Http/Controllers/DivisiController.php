<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use App\Models\Direktorat;
use App\Models\Karyawan;

class DivisiController extends Controller
{
    public function index()
    {
        $q = request()->query('q');

        $query = Divisi::with('direktorat')->orderBy('nama_divisi');
        if ($q) {
            $query->where('nama_divisi', 'like', "%{$q}%")
                  ->orWhereHas('direktorat', function($d) use ($q) {
                      $d->where('nama_direktorat', 'like', "%{$q}%");
                  });
        }

        // if AJAX/JSON requested, return simple JSON array for instant search
        if (request()->wantsJson() || request()->ajax()) {
            $items = $query->limit(50)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama_divisi' => $item->nama_divisi,
                    'nama_direktorat' => $item->direktorat?->nama_direktorat,
                ];
            });
            return response()->json($items);
        }

        $divisis = $query->paginate(10)->withQueryString();
        return view('divisi.index', compact('divisis','q'));
    }

    public function create()
    {
        $direktorats = Direktorat::orderBy('nama_direktorat')->get();
        return view('divisi.create', compact('direktorats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_divisi' => ['required','string','max:255'],
            'direktorat_id' => ['required','exists:direktorat,id'],
        ]);

        Divisi::create($data);

        return redirect()->route('divisi.index')->with('success','Divisi berhasil dibuat.');
    }

    public function edit(Divisi $divisi)
    {
        $direktorats = Direktorat::orderBy('nama_direktorat')->get();
        return view('divisi.edit', compact('divisi','direktorats'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        $data = $request->validate([
            'nama_divisi' => ['required','string','max:255'],
            'direktorat_id' => ['required','exists:direktorat,id'],
        ]);

        $divisi->update($data);

        return redirect()->route('divisi.index')->with('success','Divisi berhasil diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        $count = $divisi->units()->count();
        if ($count > 0) {
            return redirect()->route('divisi.index')->with('error', "Tidak dapat menghapus divisi karena terdapat {$count} unit. Hapus atau pindahkan unit terlebih dahulu.");
        }

        $divisi->delete();
        return redirect()->route('divisi.index')->with('success','Divisi berhasil dihapus.');
    }

    public function show(Divisi $divisi)
    {
        $countUnits = $divisi->units()->count();
        $units = $divisi->units()->orderBy('nama_unit')->paginate(10);

        return view('divisi.show', compact('divisi','countUnits','units'));
    }

    /**
     * Return JSON list of divisi for given direktorat (for dependent selects)
     */
    public function ajaxByDirektorat($direktoratId)
    {
        $divisis = \App\Models\Divisi::where('direktorat_id', $direktoratId)->orderBy('nama_divisi')->get(['id','nama_divisi']);
        return response()->json($divisis);
    }

    /**
     * Precheck before deleting a divisi. Returns JSON.
     */
    public function precheckDelete(Divisi $divisi)
    {
        $unitCount = $divisi->units()->count();
        $karyawanCount = Karyawan::where('divisi_id', $divisi->id)->count();
        if ($unitCount > 0 || $karyawanCount > 0) {
            $parts = [];
            if ($unitCount > 0) $parts[] = $unitCount.' unit';
            if ($karyawanCount > 0) $parts[] = $karyawanCount.' karyawan';
            return response()->json([
                'ok' => false,
                'message' => 'Tidak dapat menghapus divisi karena terdapat '.implode(' dan ', $parts).'.',
                'issues' => ['units' => $unitCount, 'karyawan' => $karyawanCount],
            ]);
        }
        return response()->json(['ok' => true, 'message' => 'Divisi dapat dihapus. Lanjutkan?']);
    }
}
