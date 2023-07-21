<?php

namespace App\Http\Controllers\Api\Reservations;

use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    private array $validationRule = [
        'name'            => 'required|max:225', // Le nom est requis et ne doit pas dépasser 225 caractères
        'location'        => 'required|max:225', // L'emplacement est requis et ne doit pas dépasser 225 caractères
        'hotel_latitude'  => 'nullable|numeric', // La latitude de l'hôtel est facultative et doit être numérique
        'hotel_longitude' => 'nullable|numeric', // La longitude de l'hôtel est facultative et doit être numérique
        'city_id'         => 'nullable|exists:cities,id', // L'ID de la ville est facultative
        'description'     => 'nullable',         // La description est facultative
        'thumbnail'       => 'nullable|file|mimes:jpg,jpeg,png,gif,bmp,svg,webp,mp4,mov,wmv,avi,flv,mkv,webm,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:153600', // La vignette est facultative
        'images'          => 'nullable|array|mimes:jpg,jpeg,png,gif,bmp,svg,webp,mp4,mov,wmv,avi,flv,mkv,webm,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:153600', // Les images sont facultatives
    ];

    private string $pathUpload = 'hotels';

    public function __construct()
    {
        // $this->middleware('spartie')->only('store');
    }

    /**
     * Cette fonction récupère la liste des hôtels dans l'ordre le plus récent et les pagine.
     * Elle renvoie ensuite une réponse JSON contenant les données des hôtels et un message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function index()
    {
        $hotels = Hotel::latest()->paginate();
        return response()->json(Controller::standard([
            'data'    => HotelResource::collection($hotels),
            'message' => 'Hotel found'
        ]));
    }

    /**
     * Cette fonction crée un nouvel hôtel avec les données fournies dans la requête.
     * Elle valide d'abord les données, puis crée l'hôtel si les données sont valides.
     * Si la validation échoue, elle retourne une réponse avec un message d'erreur et les erreurs de validation.
     * Si l'hôtel est créé avec succès, elle retourne une réponse avec les données de l'hôtel nouvellement créé.
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

        // Les données validées sont stockées dans la variable $dataValidated
        $dataValidated = $validator->validated();

        // Vérifier si une vignette a été fournie dans la requête
        if (!is_null($request->thumbnail) && !is_null($request->file('thumbnail'))) {
            // Si une vignette a été fournie, la télécharger et stocker son ID dans $dataValidated['thumbnail']
            $uploadThumbnail = Media::uploadFile([$request->file('thumbnail')], $this->pathUpload, Auth::guard('api')->user()->id);
            $dataValidated['thumbnail'] = $uploadThumbnail['ids'];
        }

        // Vérifier si des images ont été fournies dans la requête
        if (!is_null($request->images) && !is_null($request->file('images'))) {
            // Si des images ont été fournies, les télécharger et stocker leurs ID dans $dataValidated['images']
            $uploadImages = Media::uploadFile($request->file('images'), $this->pathUpload, Auth::guard('api')->user()->id);
            $dataValidated['images'] = $uploadImages['ids'];
        }

        // Créer un nouvel hôtel avec les données validées
        $hotel = Hotel::create($dataValidated);

        // Retourner une réponse avec les données de l'hôtel nouvellement créé
        return response()->json(Controller::standard([
            'data'    => new HotelResource($hotel),
            'message' => 'Hôtel enregistré avec succès'
        ]));
    }


    /**
     * Cette fonction met à jour un hôtel existant avec les données fournies dans la requête.
     * Elle trouve d'abord l'hôtel par son ID, puis valide les données de la requête.
     * Si l'hôtel n'est pas trouvé, elle retourne une réponse d'erreur.
     * Si la validation échoue, elle retourne une réponse avec un message d'erreur et les erreurs de validation.
     * Si l'hôtel est mis à jour avec succès, elle retourne une réponse avec les données de l'hôtel mis à jour.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Trouver l'hôtel par son ID
        $hotel =  Hotel::find($id);

        // Vérifier si l'hôtel n'existe pas
        if (is_null($hotel)) {
            // Retourner une réponse d'erreur si l'hôtel n'est pas trouvé
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Créer un validateur pour les données de la requête
        $validator = Validator::make($request->all(), $this->validationRule);

        // Vérifier si la validation échoue
        if ($validator->fails()) {
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => $validator->errors()
            ]), 422);
        }

        // Les données validées sont stockées dans la variable $dataValidated
        $dataValidated = $validator->validated();

        // Vérifier si une vignette a été fournie dans la requête
        if (!is_null($request->thumbnail) && !is_null($request->file('thumbnail'))) {
            // Si une vignette a été fournie, la télécharger et stocker son ID dans $dataValidated['thumbnail']
            $uploadThumbnail = Media::uploadFile([$request->file('thumbnail')], $this->pathUpload, Auth::guard('api')->user()->id);
            $dataValidated['thumbnail'] = $uploadThumbnail['ids'];
        }

        // Vérifier si des images ont été fournies dans la requête
        if (!is_null($request->images) && !is_null($request->file('images'))) {
            // Si des images ont été fournies, les télécharger et stocker leurs ID dans $dataValidated['images']
            $uploadImages = Media::uploadFile($request->file('images'), $this->pathUpload, Auth::guard('api')->user()->id);
            $dataValidated['images'] = $uploadImages['ids'];
        }

        // Mettre à jour l'hôtel avec les données validées
        $hotel->update($dataValidated);

        // Retourner une réponse avec les données de l'hôtel mis à jour
        return response()->json(Controller::standard([
            'data'    => new HotelResource(Hotel::find($id)),
            'message' => 'Hôtel mis à jour avec succès'
        ]));
    }

    /**
     * Cette fonction trouve un hôtel par son ID et le retourne.
     * Si l'hôtel n'est pas trouvé, elle retourne une réponse d'erreur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Trouver l'hôtel par son ID
        $hotel =  Hotel::find($id);

        // Vérifier si l'hôtel n'existe pas
        if (is_null($hotel)) {
            // Retourner une réponse d'erreur si l'hôtel n'est pas trouvé
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Retourner une réponse avec les données de l'hôtel mis à jour
        return response()->json(Controller::standard([
            'data'    => new HotelResource($hotel),
            'message' => 'Hotel found successfully'
        ]));
    }

    /**
     * Cette fonction trouve un hôtel par son ID et le supprime.
     * Si l'hôtel n'est pas trouvé, elle retourne une réponse d'erreur.
     * Si la suppression est réussie, elle retourne une réponse indiquant que l'hôtel a été supprimé avec succès.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        // Trouver l'hôtel par son ID
        $hotel =  Hotel::find($id);

        // Vérifier si l'hôtel n'existe pas
        if (is_null($hotel)) {
            // Retourner une réponse d'erreur si l'hôtel n'est pas trouvé
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        $hotel->delete();

        // Retourner une réponse avec les données de l'hôtel mis à jour
        return response()->json(Controller::standard([
            'data'    => ["Hotel deleted successfully"],
            'message' => 'Hotel deleted successfully'
        ]));
    }
}
