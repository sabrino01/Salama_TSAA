<?php

namespace App\Http\Controllers;

use App\Models\Suivi;
use Illuminate\Http\Request;

class SuiviController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // Allow setting the number of items per page

        $suivi = Suivi::where('nom', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->paginate($perPage);

        return response()->json($suivi);
    }

    public function show($id)
    {
        $suivi = Suivi::find($id);
        if(!$suivi) {
            return response()->json([
                'message' => 'Suivi non trouvé', 401
            ]);
        }
        return response()->json($suivi);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        Suivi::create([
            'nom' => $validateData['nom'],
            'description' => $validateData['description']
        ]);

        return response()->json([
            'message' => 'Suivi ajouté avec succès', 201
        ]);
    }

    public function update(Request $request, $id)
    {
        $suivi = Suivi::find($id);
        if (!$suivi) {
            return response()->json([
                'message' => 'Suivi non trouvé',
            ], 404);
        }

        $validateData = $request->validate([
            'nom' => 'required|string|max:255' . $id,
            'description' => 'required|string|max:255'
        ]);

        $suivi->update([
            'nom' => $validateData['nom'],
            'description' => $validateData['description']
        ]);

        return response()->json([
            'message' => 'Suivi mis à jour avec succès',
        ]);
    }

    public function destroy($id)
    {
        $suivi = Suivi::find($id);
        if(!$suivi) {
            return response()->json([
                'message' => 'Suivi non trouvé'
            ]);
        }

        $suivi->delete();

        return response()->json([
            'message' => 'Suivi supprimé avec succès'
        ]);
    }
}
