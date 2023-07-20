<?php

namespace App\Http\Controllers\Api\Reservations;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Cette fonction récupère les dernières chambres et les pagine.
     * Elle renvoie ensuite une réponse JSON standardisée avec les données des chambres et un message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function index()
    {
        $rooms = Room::latest()->paginate();
        return response()->json(Controller::standard([
            'data'    => RoomResource::collection($rooms),
            'message' => 'Chambre trouvée'
        ]));
    }

    /**
     * Cette fonction permet de stocker une nouvelle chambre dans la base de données.
     * Elle valide d'abord les données de la requête avec un ensemble de règles.
     * Si la validation échoue, elle renvoie une réponse JSON avec un message d'erreur.
     * Si la validation réussit, elle crée une nouvelle chambre avec les données validées et renvoie une réponse JSON avec les données de la chambre et un message de succès.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Création du validateur avec les règles de validation
        $validator = Validator::make($request->all(), [
            'name'         => 'required|max:225',          // Le nom est obligatoire et ne doit pas dépasser 225 caractères
            'hotel_id'     => 'required|exists:hotels,id', // L'ID de l'hôtel est obligatoire et doit exister dans la table des hôtels
            'price'        => 'required',                  // Le prix est obligatoire
            'type'         => 'nullable|:max:225',         // Le type est facultatif et ne doit pas dépasser 225 caractères
            'availability' => 'nullable|boolean',          // La disponibilité est facultative et doit être un booléen
        ]);

        // Si la validation échoue, renvoie une réponse JSON avec un message d'erreur
        if ($validator->fails()) {
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => $validator->errors()
            ]), 422);
        }

        // Création d'une nouvelle chambre avec les données validées
        $room = Room::create($validator->validated());

        // Renvoie une réponse JSON avec les données de la chambre et un message de succès
        return response()->json(Controller::standard([
            'data'    => new RoomResource($room),
            'message' => 'Chambre enregistrée avec succès'
        ]));
    }

    /**
     * Cette fonction permet de mettre à jour une chambre existante dans la base de données.
     * Elle commence par trouver la chambre avec l'ID fourni.
     * Si la chambre n'est pas trouvée, elle renvoie une réponse JSON avec un message d'erreur.
     * Ensuite, elle valide les données de la requête avec un ensemble de règles.
     * Si la validation échoue, elle renvoie une réponse JSON avec un message d'erreur.
     * Si la validation réussit, elle met à jour la chambre avec les données validées et renvoie une réponse JSON avec les données de la chambre et un message de succès.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Trouver la chambre avec l'ID fourni
        $room =  Room::find($id);

        // Si la chambre n'est pas trouvée, renvoie une réponse JSON avec un message d'erreur
        if (is_null($room)) {
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'Chambre non trouvée'
            ]), 404);
        }

        // Création du validateur avec les règles de validation
        $validator = Validator::make($request->all(), [
            'name'         => 'required|max:225',          // Le nom est obligatoire et ne doit pas dépasser 225 caractères
            'hotel_id'     => 'required|exists:hotels,id', // L'ID de l'hôtel est obligatoire et doit exister dans la table des hôtels
            'price'        => 'required',                  // Le prix est obligatoire
            'type'         => 'nullable|:max:225',         // Le type est facultatif et ne doit pas dépasser 225 caractères
            'availability' => 'nullable|boolean',          // La disponibilité est facultative et doit être un booléen
        ]);

        // Si la validation échoue, renvoie une réponse JSON avec un message d'erreur
        if ($validator->fails()) {
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => $validator->errors()
            ]), 422);
        }

        // Mise à jour de la chambre avec les données validées
        $room->update($validator->validated());

        // Renvoie une réponse JSON avec les données de la chambre et un message de succès
        return response()->json(Controller::standard([
            'data'    => new RoomResource(Room::find($id)),
            'message' => 'Chambre mise à jour avec succès'
        ]));
    }

    /**
     * Cette fonction permet d'afficher les détails d'une chambre spécifique.
     * Elle commence par trouver la chambre avec l'ID fourni.
     * Si la chambre n'est pas trouvée, elle renvoie une réponse JSON avec un message d'erreur.
     * Si la chambre est trouvée, elle renvoie une réponse JSON avec les données de la chambre et un message de succès.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Trouver la chambre avec l'ID fourni
        $room = Room::find($id);

        // Si la chambre n'est pas trouvée, renvoie une réponse JSON avec un message d'erreur
        if (is_null($room)) {
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'Chambre non trouvée'
            ]), 404);
        }

        // Si la chambre est trouvée, renvoie une réponse JSON avec les données de la chambre et un message de succès
        return response()->json(Controller::standard([
            'data'    => new RoomResource($room),
            'message' => 'Chambre trouvée'
        ]));
    }

    /**
     * Cette fonction permet de supprimer une chambre spécifique de la base de données.
     * Elle commence par trouver la chambre avec l'ID fourni.
     * Si la chambre n'est pas trouvée, elle renvoie une réponse JSON avec un message d'erreur.
     * Si la chambre est trouvée, elle supprime la chambre et renvoie une réponse JSON avec un message de succès.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        // Trouver la chambre avec l'ID fourni
        $room = Room::find($id);

        // Si la chambre n'est pas trouvée, renvoie une réponse JSON avec un message d'erreur
        if (is_null($room)) {
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'Chambre non trouvée'
            ]), 404);
        }

        // Suppression de la chambre
        $room->delete();

        // Renvoie une réponse JSON avec un message de succès
        return response()->json(Controller::standard([
            'data'    => ["Chambre supprimée avec succès"],
            'message' => 'Chambre supprimée avec succès'
        ]));
    }
}
