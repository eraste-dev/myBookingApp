<?php

namespace App\Http\Controllers\Api\Media;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

final class MediaController extends Controller
{
    private string $mediaRoot = "public/uploads/";

    private array $validationRule = [
        'file'        => 'required|file|mimes:jpg,jpeg,png,gif,bmp,svg,webp,mp4,mov,wmv,avi,flv,mkv,webm,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:153600',
        'destination' => 'required|max:50',
        'title'       => 'nullable|max:60'
    ];

    public function __construct()
    {
        //
    }

    /**
     * Cette fonction permet de télécharger un fichier.
     *
     * @param Request $request Requête HTTP contenant les informations du fichier à télécharger.
     * @return \Illuminate\Http\JsonResponse Retourne une réponse JSON indiquant le succès ou l'échec du téléchargement.
     */
    public function store(Request $request)
    {
        // Création d'un validateur pour vérifier les données de la requête
        $validator = Validator::make($request->all(), $this->validationRule);

        // Si la validation échoue, retourne une réponse JSON avec les erreurs
        if ($validator->fails()) {
            return response()->json(Controller::standard([
                'message' => "Une erreur s'est produite",
                'error' => $validator->errors()
            ]), 400);
        }

        // Récupération du fichier de la requête
        $file = $request->file('file');
        $fileName = is_null($request->title) ? $file->getClientOriginalName() : $request->title;

        // Stockage du fichier dans le dossier 'public/uploads'
        $path = $file->store($this->mediaRoot . $request->destination);
        $path =  $file->storeAs('public', $fileName);

        // Création d'un nouvel objet Media
        $media = new Media();
        // Attribution des propriétés à l'objet Media
        $media->title = $fileName; // Nom original du fichier
        $media->file_path = $path; // Chemin du fichier
        $media->type = $file->getClientMimeType(); // Type MIME du fichier
        $media->extension = $file->getClientOriginalExtension(); // Extension du fichier
        $media->user_id = Auth::guard('api')->user()->id; // ID de l'utilisateur authentifié
        $media->deleted = false; // Le fichier n'est pas supprimé
        // Sauvegarde de l'objet Media dans la base de données
        $media->save();

        // Retourne une réponse JSON indiquant le succès du téléchargement
        return response()->json(Controller::standard([
            'data'    => new MediaResource($media),
            'message' => 'Fichier importé avec succès'
        ]));
    }
}
