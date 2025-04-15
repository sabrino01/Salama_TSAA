<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\Request;

class ResponsableController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // Allow setting the number of items per page

        $responsable = Responsable::where('code', 'like', "%$search%")
            ->orWhere('libelle', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->paginate($perPage);

        return response()->json($responsable);
    }

    public function show($id)
    {
        $responsable = Responsable::find($id);
        if(!$responsable) {
            return response()->json(['message' => 'Responsable non trouvé'], 404);
        }
        return response()->json($responsable);
    }
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'code' => 'required|string|max:255|unique:responsables',
            'libelle' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        Responsable::create([
            'code' => $validateData['code'],
            'libelle' => $validateData['libelle'],
            'description' => $validateData['description']
        ]);

        return response()->json(['message' => 'Responsable ajouté avec succès', 201]);
    }

    public function update(Request $request, $id)
    {
        $responsable = Responsable::find($id);
        if (!$responsable) {
            return response()->json([
                'message' => 'Responsable non trouvé',
            ], 404);
        }

        $validateData = $request->validate([
            'code' => 'required|string|max:255|unique:responsables,code,' . $id,
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $responsable->update([
            'code' => $validateData['code'],
            'libelle' => $validateData['libelle'],
            'description' => $validateData['description'],
        ]);

        return response()->json([
            'message' => 'Responsable mis à jour avec succès',
        ]);
    }

    public function destroy($id)
    {
        $responsable = Responsable::find($id);
        if(!$responsable) {
            return response()->json([
                'message' => 'Responsable non trouvé'
            ]);
        }

        $responsable->delete();

        return response()->json([
            'message' => 'Responsable supprimé avec succès'
        ]);
    }
}
