<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatans = Jabatan::paginate(10);
        return view('jabatan.index', compact('jabatans'));
    }

    public function create()
    {
        return view('jabatan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_jabatan' => ['required','string','max:50','unique:jabatan,kode_jabatan'],
            'nama_jabatan' => ['required','string','max:255'],
            'level' => ['nullable','integer','min:0'],
        ]);

        Jabatan::create($data);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil dibuat.');
    }

    public function edit(Jabatan $jabatan)
    {
        return view('jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $data = $request->validate([
            'kode_jabatan' => ['required','string','max:50','unique:jabatan,kode_jabatan,'.$jabatan->id],
            'nama_jabatan' => ['required','string','max:255'],
            'level' => ['nullable','integer','min:0'],
        ]);

        $jabatan->update($data);

        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan)
    {
        // prevent deletion if any karyawan reference this jabatan
        $count = $jabatan->karyawan()->count() ?? 0;
        if ($count > 0) {
            return redirect()->route('jabatan.index')->with('error', "Tidak dapat menghapus jabatan karena terdapat {$count} karyawan. Pindahkan atau hapus karyawan terkait terlebih dahulu.");
        }

        $jabatan->delete();
        return redirect()->route('jabatan.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
