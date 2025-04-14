<?php

namespace App\Http\Controllers;

use App\Models\TypeActions;
use Illuminate\Http\Request;
use Mockery\Matcher\Type;

class TypeActionsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // Allow setting the number of items per page

        $typeActions = TypeActions::where('code', 'like', "%$search%")
            ->orWhere('libelle', 'like', "%$search%")
            ->paginate($perPage);

        return response()->json($typeActions);
    }

    public function show($id)
    {
        $typeActions = TypeActions::find($id);
        if (!$typeActions) {
            return response()->json([
                'message' => 'Type action non trouvé',
            ], 404);
        }
        return response()->json($typeActions);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'code' => 'required|string|max:255|unique:type_actions',
            'libelle' => 'required|string|max:255',
            'typeactions_pour' => 'nullable|string|max:255',
        ]);

        TypeActions::create([
            'code' => $validateData['code'],
            'libelle' => $validateData['libelle'],
            'typeactions_pour' => $validateData['typeactions_pour'] ?? 'auditinterne',
        ]);

        return response()->json([
            'message' => 'Type actions ajouté avec succès',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $typeActions = TypeActions::find($id);
        if (!$typeActions) {
            return response()->json([
                'message' => 'Type action non trouvé',
            ], 404);
        }

        $validateData = $request->validate([
            'code' => 'required|string|max:255|unique:type_actions,code,' . $id,
            'libelle' => 'required|string|max:255',
            'typeactions_pour' => 'nullable|string|max:255',
        ]);

        $typeActions->update([
            'code' => $validateData['code'],
            'libelle' => $validateData['libelle'],
            'typeactions_pour' => $validateData['typeactions_pour'] ?? 'auditinterne',
        ]);

        return response()->json([
            'message' => 'Type action mis à jour avec succès',
        ]);
    }

    public function destroy($id)
    {
        $typeActions = TypeActions::find($id);
        if (!$typeActions) {
            return response()->json([
                'message' => 'Type action non trouvé',
            ], 404);
        }

        $typeActions->delete();

        return response()->json([
            'message' => 'Type action supprimé avec succès',
        ]);
    }
}
