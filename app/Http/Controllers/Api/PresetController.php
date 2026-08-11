<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Preset;
use Illuminate\Http\Request;

class PresetController extends Controller
{
    // GET /api/presets
    public function index()
    {
        return response()->json(Preset::orderBy('created_at', 'desc')->get());
    }

    // GET /api/presets/{id}
    public function show($id)
    {
        $preset = Preset::findOrFail($id);
        return response()->json($preset);
    }

    // POST /api/presets
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'light'          => 'required|array',
            'fan'            => 'required|array',
        ]);

        $devicesJson = [
            'light' => $data['light'],
            'fan'   => $data['fan'],
        ];

        $preset = Preset::create([
            'name'         => $data['name'],
            'devices_json' => $devicesJson,
        ]);

        return response()->json($preset, 201);
    }

    // DELETE /api/presets/{id}
    public function destroy($id)
    {
        $preset = Preset::findOrFail($id);
        $preset->delete();

        return response()->json(['message' => 'Preset deleted']);
    }
}
