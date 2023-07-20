<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use Illuminate\Support\Facades\Validator;

/**
 * Cette classe gère toutes les opérations liées aux pays.
 * Elle permet de créer, lire, mettre à jour et supprimer des pays.
 */
class CountryController extends Controller
{
    /**
     * Règles de validation pour les requêtes entrantes.
     * Ces règles sont utilisées pour valider les données fournies dans les requêtes avant de les utiliser pour créer ou mettre à jour un pays.
     *
     */
    private array $validationRule = [
        'name'      => 'required|string|max:255',
        'iso_code'  => 'required|string|max:5|unique:countries',
        'latitude'  => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ];

    public function __construct()
    {
    }

    /**
     * Cette fonction récupère la liste des pays enregistrés dans la base de données.
     * Elle trie les pays par ordre de création (le plus récent en premier) et les pagine.
     * Ensuite, elle retourne une réponse JSON avec les données des pays et un message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function index()
    {
        // Récupérer les pays de la base de données, triés par ordre de création et paginés
        $countries = Country::latest()->paginate();

        // Retourner une réponse JSON avec les données des pays et un message
        return response()->json(Controller::standard([
            'data'    => CountryResource::collection($countries),
            'message' => 'Pays trouvés'
        ]));
    }

    /**
     * Cette fonction crée un nouveau pays avec les données fournies dans la requête.
     * Elle valide d'abord les données de la requête.
     * Si la validation échoue, elle retourne une réponse avec un message d'erreur et les erreurs de validation.
     * Si le pays est créé avec succès, elle retourne une réponse avec les données du pays créé.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Créer un validateur pour les données de la requête
        $validator = Validator::make($request->all(), $this->validationRule);

        // Vérifier si la validation échoue
        if ($validator->fails()) {
            // Retourner une réponse avec un message d'erreur et les erreurs de validation
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => $validator->errors()
            ]), 422);
        }

        // Créer un nouveau pays avec les données validées
        $country = Country::create($validator->validated());

        // Retourner une réponse avec les données du pays créé
        return response()->json(Controller::standard([
            'data'    => new CountryResource($country),
            'message' => 'Pays enregistré avec succès'
        ]));
    }

    /**
     * Cette fonction met à jour un pays existant avec les données fournies dans la requête.
     * Elle trouve d'abord le pays par son ID.
     * Si le pays n'est pas trouvé, elle retourne une réponse d'erreur.
     * Ensuite, elle valide les données de la requête.
     * Si la validation échoue, elle retourne une réponse avec un message d'erreur et les erreurs de validation.
     * Si le pays est mis à jour avec succès, elle retourne une réponse avec les données du pays mis à jour.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Trouver le pays par son ID
        $countries =  Country::find($id);

        // Vérifier si le pays n'existe pas
        if (is_null($countries)) {
            // Retourner une réponse d'erreur si le pays n'est pas trouvé
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Créer un validateur pour les données de la requête
        $validator = Validator::make($request->all(), $this->validationRule);

        // Vérifier si la validation échoue
        if ($validator->fails()) {
            // Retourner une réponse avec un message d'erreur et les erreurs de validation
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => $validator->errors()
            ]), 422);
        }

        // Mettre à jour le pays avec les données validées
        $countries->update($validator->validated());

        // Retourner une réponse avec les données du pays mis à jour
        return response()->json(Controller::standard([
            'data'    => new CountryResource(Country::find($id)),
            'message' => 'Pays mis à jour avec succès'
        ]));
    }

    /**
     * Cette fonction trouve un pays par son ID et le retourne.
     * Si le pays n'est pas trouvé, elle retourne une réponse d'erreur.
     * Si le pays est trouvé, elle retourne une réponse avec les données du pays.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Trouver le pays par son ID
        $country = Country::find($id);

        // Vérifier si le pays n'existe pas
        if (is_null($country)) {
            // Retourner une réponse d'erreur si le pays n'est pas trouvé
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Retourner une réponse avec les données du pays trouvé
        return response()->json(Controller::standard([
            'data'    => new CountryResource($country),
            'message' => 'Pays trouvé avec succès'
        ]));
    }

    /**
     * Cette fonction supprime un pays en utilisant son ID.
     * Elle trouve d'abord le pays par son ID.
     * Si le pays n'est pas trouvé, elle retourne une réponse d'erreur.
     * Si le pays est trouvé, il est supprimé et une réponse de succès est retournée.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        // Trouver le pays par son ID
        $country =  Country::find($id);

        // Vérifier si le pays n'existe pas
        if (is_null($country)) {
            // Retourner une réponse d'erreur si le pays n'est pas trouvé
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Supprimer le pays
        $country->delete();

        // Retourner une réponse indiquant que le pays a été supprimé avec succès
        return response()->json(Controller::standard([
            'data'    => ["Pays supprimé avec succès"],
            'message' => 'Pays supprimé avec succès'
        ]));
    }
}
