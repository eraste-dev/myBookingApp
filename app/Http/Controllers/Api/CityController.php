<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\CityCollection;
use Illuminate\Support\Facades\Validator;

/**
 * Classe CityController
 *
 * Cette classe est responsable de la gestion des villes. Elle permet de créer, lire, mettre à jour et supprimer des villes.
 * Chaque méthode de cette classe correspond à une de ces opérations.
 */

class CityController extends Controller
{
    private array $validationRule = [
        'name'       => 'required|string|max:255',
        'latitude'   => 'required|numeric',
        'longitude'  => 'required|numeric',
        'country_id' => 'required|integer|exists:countries,id',
    ];

    public function __construct()
    {
    }

    /**
     * Cette fonction récupère les villes de la base de données, triées par ordre de création et paginées.
     * Elle retourne une réponse JSON avec les données des villes et un message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // $per_page = $request->per_page | 15;
        // Récupérer les villes de la base de données, triées par ordre de création et paginées
        $cities = City::latest()->paginate();

        // Retourner une réponse JSON avec les données des villes et un message
        return response()->json(Controller::standard([
            'data'    => new CityCollection($cities),
            'message' => 'Villes trouvées'
        ]));
    }


    /**
     * Cette fonction crée une nouvelle ville avec les données fournies dans la requête.
     * 1. Elle valide d'abord les données de la requête.
     * 2. Si la validation échoue, elle retourne une réponse avec un message d'erreur et les erreurs de validation.
     * 3. Si la ville est créée avec succès, elle retourne une réponse avec les données de la ville créée.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Étape 1: Créer un validateur pour les données de la requête
        $validator = Validator::make($request->all(), $this->validationRule);

        // Étape 2: Vérifier si la validation échoue
        if ($validator->fails()) {
            // Si la validation échoue, retourner une réponse avec un message d'erreur et les erreurs de validation
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => $validator->errors()
            ]), 422);
        }

        // Étape 3: Créer une nouvelle ville avec les données validées
        $city = City::create($validator->validated());

        // Si la ville est créée avec succès, retourner une réponse avec les données de la ville créée
        return response()->json(Controller::standard([
            'data'    => new CityResource($city),
            'message' => 'Ville enregistrée avec succès'
        ]));
    }

    /**
     * Cette fonction met à jour une ville existante avec les données fournies dans la requête.
     * 1. Elle trouve d'abord la ville par son ID.
     * 2. Si la ville n'est pas trouvée, elle retourne une réponse d'erreur.
     * 3. Ensuite, elle valide les données de la requête.
     * 4. Si la validation échoue, elle retourne une réponse avec un message d'erreur et les erreurs de validation.
     * 5. Si la ville est mise à jour avec succès, elle retourne une réponse avec les données de la ville mise à jour.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Étape 1: Trouver la ville par son ID
        $city =  City::find($id);

        // Étape 2: Vérifier si la ville n'existe pas
        if (is_null($city)) {
            // Si la ville n'est pas trouvée, retourner une réponse d'erreur
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Étape 3: Créer un validateur pour les données de la requête
        $validator = Validator::make($request->all(), $this->validationRule);

        // Étape 4: Vérifier si la validation échoue
        if ($validator->fails()) {
            // Si la validation échoue, retourner une réponse avec un message d'erreur et les erreurs de validation
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => $validator->errors()
            ]), 422);
        }

        // Étape 5: Mettre à jour la ville avec les données validées
        $city->update($validator->validated());

        // Si la ville est mise à jour avec succès, retourner une réponse avec les données de la ville mise à jour
        return response()->json(Controller::standard([
            'data'    => new CityResource(City::find($id)),
            'message' => 'Ville mise à jour avec succès'
        ]));
    }

    /**
     * Cette fonction est utilisée pour trouver une ville spécifique par son ID et la retourner.
     * Elle commence par chercher la ville dans la base de données en utilisant l'ID fourni.
     * Si la ville n'est pas trouvée, elle retourne une réponse d'erreur indiquant que la ressource n'a pas été trouvée.
     * Si la ville est trouvée, elle retourne une réponse avec les données de la ville.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Étape 1: Trouver la ville par son ID
        $city = City::find($id);

        // Étape 2: Vérifier si la ville n'existe pas
        if (is_null($city)) {
            // Si la ville n'est pas trouvée, retourner une réponse d'erreur
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Étape 3: Retourner une réponse avec les données de la ville trouvée
        return response()->json(Controller::standard([
            'data'    => new CityResource($city),
            'message' => 'Ville trouvée avec succès'
        ]));
    }

    /**
     * Cette fonction supprime une ville en utilisant son ID.
     * 1. Elle trouve d'abord la ville par son ID.
     * 2. Si la ville n'est pas trouvée, elle retourne une réponse d'erreur.
     * 3. Si la ville est trouvée, elle est supprimée et une réponse de succès est retournée.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        // Étape 1: Trouver la ville par son ID
        $city =  City::find($id);

        // Étape 2: Vérifier si la ville n'existe pas
        if (is_null($city)) {
            // Si la ville n'est pas trouvée, retourner une réponse d'erreur
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Étape 3: Supprimer la ville
        $city->delete();

        // Retourner une réponse indiquant que la ville a été supprimée avec succès
        return response()->json(Controller::standard([
            'data'    => ["Ville supprimée avec succès"],
            'message' => 'Ville supprimée avec succès'
        ]));
    }
}
