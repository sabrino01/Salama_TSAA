<?php

namespace App\Http\Controllers;

use App\Models\Sources;
use Illuminate\Http\Request;

class SourcesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // Permettre de définir le nombre d'éléments par page

        $sources = Sources::where('code', 'like', "%$search%")
            ->orWhere('libelle', 'like', "%$search%")
            ->paginate($perPage);

        return response()->json($sources);
    }
    public function show($id)
    {
        $source = Sources::find($id);
        if (!$source) {
            return response()->json([
                'message' => 'Source non trouvée',
            ], 404);
        }
        return response()->json($source);
    }
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'code' => 'required|string|max:255|unique:sources',
            'libelle' => 'required|string|max:255',
            'sources_pour' => 'nullable|string|max:255',
        ]);

        Sources::create([
            'code' => $validateData['code'],
            'libelle' => $validateData['libelle'],
            'sources_pour' => $validateData['sources_pour'] ?? 'auditinterne',
        ]);
        return response()->json([
            'message' => 'Source ajoutée avec succès',
        ], 201);
    }
    public function update(Request $request, $id)
    {
        $source = Sources::find($id);
        if (!$source) {
            return response()->json([
                'message' => 'Source non trouvée',
            ], 404);
        }

        $validateData = $request->validate([
            'code' => 'required|string|max:255|unique:sources,code,' . $id,
            'libelle' => 'required|string|max:255',
            'sources_pour' => 'nullable|string|max:255',
        ]);

        $source->update([
            'code' => $validateData['code'],
            'libelle' => $validateData['libelle'],
            'sources_pour' => $validateData['sources_pour'] ?? 'auditinterne',
        ]);
        return response()->json([
            'message' => 'Source mise à jour avec succès',
        ], 200);
    }
    public function destroy($id)
    {
        $source = Sources::find($id);
        if (!$source) {
            return response()->json([
                'message' => 'Source non trouvée',
            ], 404);
        }
        $source->delete();
        return response()->json([
            'message' => 'Source supprimée avec succès',
        ], 200);
    }
}
