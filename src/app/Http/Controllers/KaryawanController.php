<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Direktorat;
use App\Models\Divisi;
use App\Models\Unit;
use App\Models\Jabatan;
use App\Models\Posisi;

class KaryawanController extends Controller
{
    public function index()
    {
        $q = request()->query('q');

        $query = Karyawan::query()->with(['direktorat','divisi','unit','jabatan']);

        if ($q) {
            $query->where(function($where) use ($q) {
                $where->where('nik', 'like', "%{$q}%")
                      ->orWhere('nama', 'like', "%{$q}%")
                      ->orWhereHas('user', function($u) use ($q) {
                          $u->where('email', 'like', "%{$q}%");
                      })
                      ->orWhereHas('direktorat', function($d) use ($q) {
                          $d->where('nama_direktorat', 'like', "%{$q}%");
                      })
                      ->orWhereHas('divisi', function($d) use ($q) {
                          $d->where('nama_divisi', 'like', "%{$q}%");
                      })
                      ->orWhereHas('unit', function($u) use ($q) {
                          $u->where('nama_unit', 'like', "%{$q}%");
                      })
                      ->orWhereHas('jabatan', function($j) use ($q) {
                          $j->where('nama_jabatan', 'like', "%{$q}%");
                      });
            });
        }

        // if AJAX/JSON requested, return simple JSON array for instant search
        if (request()->wantsJson() || request()->ajax()) {
            $items = $query->orderBy('nama')->limit(50)->get()->map(function($k) {
                return [
                    'id' => $k->id,
                    'nik' => $k->nik,
                    'nama' => $k->nama,
                    'direktorat' => $k->direktorat?->nama_direktorat,
                    'divisi' => $k->divisi?->nama_divisi,
                    'unit' => $k->unit?->nama_unit,
                    'jabatan' => $k->jabatan?->nama_jabatan,
                ];
            });
            return response()->json($items);
        }

        $karyawans = $query->orderBy('nama')->paginate(10)->withQueryString();

        return view('karyawan.index', compact('karyawans','q'));
    }

    public function create()
    {
        $direktorats = Direktorat::orderBy('nama_direktorat')->get();
        $divisis = Divisi::orderBy('nama_divisi')->get();
        $units = Unit::orderBy('nama_unit')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        $posisis = Posisi::orderBy('nama_posisi')->get();

        return view('karyawan.create', compact('direktorats','divisis','units','jabatans','posisis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => ['required','string','max:50','unique:karyawan,nik'],
            'email' => ['required','email','max:255','unique:users,email'],
            'direktorat_id' => ['required','exists:direktorat,id'],
            'divisi_id' => ['required','exists:divisi,id'],
            'unit_id' => ['required','exists:unit,id'],
            'jabatan_id' => ['required','exists:jabatan,id'],
            'posisi_id' => ['nullable','exists:posisi,id'],
            'nama' => ['required','string','max:255'],
            'status_karyawan' => ['required','string','max:50'],
            'no_wa' => ['nullable','string','max:30'],
            'tanggal_masuk' => ['nullable','date'],
        ]);

        // create user automatically with default password
        $user = User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => bcrypt('BimasaktiJaya123'),
            // new users are active by default; actual active/inactive is stored on users.is_aktif
            'is_aktif' => 1,
        ]);

        $data['user_id'] = $user->id;

        Karyawan::create($data);

        return redirect()->route('karyawan.index')->with('success','Karyawan dan akun user berhasil dibuat.');
    }

    public function edit(Karyawan $karyawan)
    {
        $direktorats = Direktorat::orderBy('nama_direktorat')->get();
        $divisis = Divisi::orderBy('nama_divisi')->get();
        $units = Unit::orderBy('nama_unit')->get();
        $jabatans = Jabatan::orderBy('nama_jabatan')->get();
        $posisis = Posisi::orderBy('nama_posisi')->get();

        return view('karyawan.edit', compact('karyawan','direktorats','divisis','units','jabatans','posisis'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $data = $request->validate([
            'nik' => ['required','string','max:50','unique:karyawan,nik,'.$karyawan->id],
            'email' => ['required','email','max:255','unique:users,email,'.$karyawan->user_id],
            'direktorat_id' => ['required','exists:direktorat,id'],
            'divisi_id' => ['required','exists:divisi,id'],
            'unit_id' => ['required','exists:unit,id'],
            'jabatan_id' => ['required','exists:jabatan,id'],
            'posisi_id' => ['nullable','exists:posisi,id'],
            'nama' => ['required','string','max:255'],
            'status_karyawan' => ['required','string','max:50'],
            'is_aktif' => ['nullable','in:0,1'],
            'no_wa' => ['nullable','string','max:30'],
            'tanggal_masuk' => ['nullable','date'],
        ]);

        // update related user name/email
        if ($karyawan->user) {
            $userData = [
                'name' => $data['nama'],
                'email' => $data['email'],
            ];

            // if the form supplied is_aktif, persist it to users.is_aktif
            if (array_key_exists('is_aktif', $data)) {
                $userData['is_aktif'] = (int)$data['is_aktif'];
            }

            $karyawan->user->update($userData);
        }

        // update karyawan record (user_id remains unchanged)
        $karyawan->update(array_merge($data, ['user_id' => $karyawan->user_id]));

        return redirect()->route('karyawan.index')->with('success','Karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        // delete related user as well
        $user = $karyawan->user;

        // delete karyawan first
        $karyawan->delete();

        // if there is an associated user, delete it too
        if ($user) {
            // double-check there are no other karyawan records pointing to this user
            // (shouldn't be, because user_id is unique on karyawan), but check to be safe
            if (! $user->karyawan()->exists()) {
                $user->delete();
            }
        }

        return redirect()->route('karyawan.index')->with('success','Karyawan dan akun user berhasil dihapus.');
    }
}
