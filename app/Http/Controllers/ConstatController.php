<?php

namespace App\Http\Controllers;

use App\Models\Constat;
use Illuminate\Http\Request;
use SebastianBergmann\CodeUnit\FunctionUnit;

class ConstatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // Allow setting the number of items per page

        $constat = Constat::where('code', 'like', "%$search%")
            ->orWhere('libelle', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->paginate($perPage);

        return response()->json($constat);
    }

    public function show($id)
    {
        $constat = Constat::find($id);
        if(!$constat) {
            return response()->json([
                'message' => 'Constat ou Action non trouvé', 404
            ]);
        }
        return response()->json($constat);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'code' => 'required|string|max:255|unique:constats',
            'libelle' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        Constat::create([
            'code' => $validateData['code'],
            'libelle' => $validateData['libelle'],
            'description' => $validateData['description']
        ]);

        return response()->json([
            'message' => 'Constat ou Action ajouté avec succès'
        ]);
    }

    public function update(Request $request, $id)
    {
        $constat = Constat::find($id);
        if(!$constat) {
            return response()->json([
                'message' => 'Constat ou Action non trouvé', 404
            ]);
        }

        $validateData = $request->validate([
            'code' => 'required|string|max:255|unique:constats,code,' .$id,
            'libelle' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        $constat->update($validateData);

        return response()->json([
            'message' => 'Constat ou Action mis à jour avec succès'
        ]);
    }

    public function destroy($id)
    {
        $constat = Constat::find($id);
        if(!$constat) {
            return response()->json([
                'message' => 'Constat ou Action non trouvé', 404
            ]);
        }

        $constat->delete();

        return response()->json([
            'message' => 'Constat ou Action supprimé avec succès'
        ]);
    }
}
