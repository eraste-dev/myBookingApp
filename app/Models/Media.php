<?php

namespace App\Models;

use App\Http\Resources\MediaResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'file_path',
        'type',
        'extension',
        'user_id',
        'deleted'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'delete'     => 'boolean',
    ];

    private static string $mediaRoot = "uploads/";

    /**
     * Récupère une collection de médias en fonction d'un tableau d'identifiants.
     *
     * @param array<int> $array Un tableau d'identifiants de médias.
     * @return \Illuminate\Database\Eloquent\Collection Une collection de médias correspondant aux identifiants fournis.
     */
    static function ByIds(string|null $array)
    {
        // Vérifie si $array est null, si il est null retourne un tableau vide
        if (is_null($array) || $array === "") {
            return [];
        }

        // Utilise la méthode whereIn pour filtrer les médias par identifiants
        // Utilise la méthode get pour récupérer les résultats sous forme de collection
        return self::whereIn('id', explode(";", implode(";", $array)))->get();
    }

    /**
     * Télécharge un fichier, crée un nouvel objet Media pour chaque fichier,
     * sauvegarde l'objet dans la base de données et renvoie un tableau contenant
     * les objets Media et leurs identifiants.
     *
     * @param array $files Les fichiers à télécharger.
     * @param string $destination Le chemin de destination des fichiers.
     * @param int $user_id L'ID de l'utilisateur authentifié.
     * @param string|null $title Le titre du fichier.
     * @return array Un tableau contenant les objets Media et leurs identifiants.
     */
    static function uploadFile(array $files, string $destination, int $user_id, string|null $title = null)
    {
        $mediaItems = [];

        foreach ($files as $file) {
            // Génération du nom du fichier
            $fileName = is_null($title) ? $file->getClientOriginalName() : ($title . "." . $file->getClientOriginalExtension());

            // Vérification de l'existence du fichier
            if (file_exists(public_path(self::$mediaRoot . $destination . "/" . $fileName))) {
                // Si le fichier existe déjà, générer un nouveau nom de fichier
                $fileName = Str::random(10) . "_" . now() . "." . $file->getClientOriginalExtension();
            }

            // Définition du chemin de destination
            $destinationPath = self::$mediaRoot . $destination;
            $path = $destinationPath . "/" . $fileName;

            // Déplacement du fichier vers le dossier de destination
            $file->move(public_path($destinationPath), $fileName);

            // Création d'un nouvel objet Media
            $media = new Media();

            // Attribution des propriétés à l'objet Media
            $media->title     = $fileName;                           // Nom original du fichier
            $media->file_path = $path;                               // Chemin du fichier
            $media->type      = $file->getClientMimeType();          // Type MIME du fichier
            $media->extension = $file->getClientOriginalExtension(); // Extension du fichier
            $media->user_id   = $user_id;                            // ID de l'utilisateur authentifié
            $media->deleted   = false;                               // Le fichier n'est pas supprimé

            // Sauvegarde de l'objet Media dans la base de données
            $media->save();

            $mediaItems[] = new MediaResource($media);
        }

        // Faire un mapping sur mediaItems et enregistre les id dans un tableau
        $mediaIds = array_map(function ($mediaItem) {
            return $mediaItem->id;
        }, $mediaItems);

        // Convertir le tableau en string
        $mediaIdsString = implode(";", $mediaIds);

        return ["media" => $mediaItems, "ids" => $mediaIdsString];
    }

}
