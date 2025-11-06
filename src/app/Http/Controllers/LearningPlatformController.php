<?php

namespace App\Http\Controllers;

use App\Models\LearningPlatform;
use Illuminate\Http\Request;

class LearningPlatformController extends Controller
{
    public function index()
    {
        $platforms = LearningPlatform::orderBy('name')->paginate(20);
        return view('learning.platforms.index', compact('platforms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'type' => ['required','in:internal,external'],
            'url' => ['nullable','url'],
            'description' => ['nullable','string'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $data['is_active'] ?? true;
        LearningPlatform::create($data);
        return back()->with('status', 'Platform created');
    }

    public function update(Request $request, LearningPlatform $platform)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'type' => ['required','in:internal,external'],
            'url' => ['nullable','url'],
            'description' => ['nullable','string'],
            'is_active' => ['nullable','boolean'],
        ]);
        $platform->update($data);
        return back()->with('status', 'Platform updated');
    }

    public function destroy(LearningPlatform $platform)
    {
        $platform->delete();
        return back()->with('status', 'Platform deleted');
    }
}
