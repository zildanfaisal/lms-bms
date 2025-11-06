@extends('layouts.master')

@section('title','Profile not found')
@section('header')
  <h1 class="text-xl font-semibold text-gray-800">Profile not found</h1>
@endsection

@section('content')
  <div class="bg-white shadow-sm sm:rounded-lg p-6">
    <p class="mb-4">Sepertinya akun Anda belum dipetakan ke data <strong>Karyawan</strong>. Aplikasi membutuhkan profil Karyawan untuk menampilkan halaman Learning (My Learning / Approvals).</p>

    <div class="space-y-3">
      @can('create karyawan')
        <p>You can create your Karyawan profile now:</p>
        <a href="{{ route('karyawan.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded">Buat Profil Karyawan</a>
      @else
        <p>If you believe this is a mistake, please contact your HR administrator to create a Karyawan record for your account (link your User to a Karyawan).</p>
      @endcan

      <p class="text-sm text-gray-500 mt-4">Technical note: controllers look up Karyawan by <code>karyawan.user_id</code>. If you prefer I can create a Karyawan record for you (requires selecting Direktorat/Divisi/Unit/Jabatan).</p>

      <div class="mt-4 text-sm">
        <strong>Quick fix using Tinker (example):</strong>
        <pre class="bg-gray-100 p-3 rounded mt-2">php artisan tinker --execute="\$u=\App\Models\User::where('email','you@domain.com')->first(); \$dir=\App\Models\Direktorat::first(); \$div=\App\Models\Divisi::where('direktorat_id',\$dir->id)->first(); \$unit=\App\Models\Unit::where('divisi_id',\$div->id)->first(); \$jab=\App\Models\Jabatan::first(); \App\Models\Karyawan::create(['nik'=>'AUTO'.time(),'user_id'=>\$u->id,'direktorat_id'=>\$dir->id,'divisi_id'=>\$div->id,'unit_id'=>\$unit->id,'jabatan_id'=>\$jab->id,'nama'=>\$u->name,'status_karyawan'=>'active']);"
        </pre>
      </div>
    </div>
  </div>
@endsection
