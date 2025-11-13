<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\Divisi;
use App\Models\Karyawan;

class UnitController extends Controller
{
    public function index()
    {
        $q = request()->query('q');

        $query = Unit::with('divisi')->orderBy('nama_unit');
        if ($q) {
            $query->where('nama_unit', 'like', "%{$q}%")
                  ->orWhereHas('divisi', function($d) use ($q) {
                      $d->where('nama_divisi', 'like', "%{$q}%");
                  });
        }

        if (request()->wantsJson() || request()->ajax()) {
            $items = $query->limit(50)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'nama_unit' => $item->nama_unit,
                    'nama_divisi' => $item->divisi?->nama_divisi,
                ];
            });
            return response()->json($items);
        }

        $units = $query->paginate(10)->withQueryString();
        return view('unit.index', compact('units','q'));
    }

    public function create()
    {
        $divisis = Divisi::orderBy('nama_divisi')->get();
        return view('unit.create', compact('divisis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_unit' => ['required','string','max:255'],
            'divisi_id' => ['required','exists:divisi,id'],
        ]);

        Unit::create($data);

        return redirect()->route('unit.index')->with('success','Unit berhasil dibuat.');
    }

    public function edit(Unit $unit)
    {
        $divisis = Divisi::orderBy('nama_divisi')->get();
        return view('unit.edit', compact('unit','divisis'));
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'nama_unit' => ['required','string','max:255'],
            'divisi_id' => ['required','exists:divisi,id'],
        ]);

        $unit->update($data);

        return redirect()->route('unit.index')->with('success','Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $karyawanCount = Karyawan::where('unit_id', $unit->id)->count();
        if ($karyawanCount > 0) {
            return redirect()->route('unit.index')->with('error', "Tidak dapat menghapus unit karena terdapat {$karyawanCount} karyawan.");
        }

        $unit->delete();
        return redirect()->route('unit.index')->with('success','Unit berhasil dihapus.');
    }

    /**
     * Return JSON list of units for given divisi (for dependent selects)
     */
    public function ajaxByDivisi($divisiId)
    {
        $units = \App\Models\Unit::where('divisi_id', $divisiId)->orderBy('nama_unit')->get(['id','nama_unit']);
        return response()->json($units);
    }

    /**
     * Precheck before deleting a unit. Returns JSON.
     */
    public function precheckDelete(Unit $unit)
    {
        $karyawanCount = Karyawan::where('unit_id', $unit->id)->count();
        if ($karyawanCount > 0) {
            return response()->json([
                'ok' => false,
                'message' => "Tidak dapat menghapus unit karena terdapat {$karyawanCount} karyawan.",
                'issues' => ['karyawan' => $karyawanCount],
            ]);
        }
        return response()->json(['ok' => true, 'message' => 'Unit dapat dihapus. Lanjutkan?']);
    }
}
