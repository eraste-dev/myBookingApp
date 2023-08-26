<?php

namespace App\Http\Controllers\Api\Media;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Validator;


final class MediaController extends Controller
{
    private array $validationRule = [
        'file'        => 'required|file|mimes:jpg,jpeg,png,gif,bmp,svg,webp,mp4,mov,wmv,avi,flv,mkv,webm,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:153600',
        'destination' => 'required|max:50',
        'user_id'     => 'required|exists:users,id',
        'title'       => 'nullable|max:60'
    ];

    public function __construct()
    {
        //
    }

    /**
     * Cette fonction permet de télécharger plusieurs fichiers ou un seul fichier.
     *
     * @param Request $request Requête HTTP contenant les informations des fichiers à télécharger.
     * @return \Illuminate\Http\JsonResponse Retourne une réponse JSON indiquant le succès ou l'échec du téléchargement.
     */
    public function store(Request $request)
    {
        // Modification de la règle de validation pour accepter plusieurs fichiers
        // La clé 'file' est maintenant un tableau
        $this->validationRule['file'] = 'required|array';

        // Ajout d'une règle de validation pour chaque fichier dans le tableau
        $this->validationRule['file.*'] = 'file|mimes:jpg,jpeg,png,gif,bmp,svg,webp,mp4,mov,wmv,avi,flv,mkv,webm,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:153600';

        // Création d'un validateur avec les règles de validation et les données de la requête
        $validator = Validator::make($request->all(), $this->validationRule);

        // Si la validation échoue, retourne une réponse JSON avec les erreurs
        if ($validator->fails()) {
            return response()->json(Controller::standard([
                'message' => "Une erreur s'est produite",
                'error' => $validator->errors()
            ]), 400);
        }

        // Récupération des fichiers de la requête
        $files = $request->file('file');

        // Appel de la méthode uploadFile pour télécharger les fichiers
        $uploadFiles = Media::uploadFile($files, $request->destination, $request->user_id, $request->title);

        // Retourne une réponse JSON indiquant le succès du téléchargement
        return response()->json(Controller::standard([
            'data'    => $uploadFiles,
            'message' => 'Fichiers importés avec succès'
        ]));
    }

    public function update(Request $request, $id)
    {
        // Modification de la règle de validation pour accepter plusieurs fichiers
        // La clé 'file' est maintenant un tableau
        $this->validationRule['file'] = 'required|array';

        // Ajout d'une règle de validation pour chaque fichier dans le tableau
        $this->validationRule['file.*'] = 'file|mimes:jpg,jpeg,png,gif,bmp,svg,webp,mp4,mov,wmv,avi,flv,mkv,webm,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:153600';

        // Création d'un validateur avec les règles de validation et les données de la requête
        $validator = Validator::make($request->all(), $this->validationRule);

        // Si la validation échoue, retourne une réponse JSON avec les erreurs
        if ($validator->fails()) {
            return response()->json(Controller::standard([
                'message' => "Une erreur s'est produite",
                'error' => $validator->errors()
            ]), 400);
        }

        // Trouver l'hôtel par son ID
        $media = Media::find($id);

        // Vérifier si l'hôtel n'existe pas
        if (is_null($media)) {
            // Retourner une réponse d'erreur si l'hôtel n'est pas trouvé
            return response()->json(Controller::standard([
                'message' => 'Une erreur s\'est produite lors de la validation! Veuillez réessayer',
                'error'   => 'ressource non trouvée'
            ]), 404);
        }

        // Récupération des fichiers de la requête
        $files = $request->file('file');

        // Appel de la méthode uploadFile pour télécharger les fichiers
        $uploadFiles = Media::uploadFile($files, $request->destination, $request->user_id, $request->title);

        // Retourne une réponse JSON indiquant le succès du téléchargement
        return response()->json(Controller::standard([
            'data'    => $uploadFiles,
            'message' => 'Fichiers importés avec succès'
        ]));
    }
}
