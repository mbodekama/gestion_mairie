<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentStoreRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function store(DocumentStoreRequest $request): RedirectResponse
    {
        $modelType = $request->input('model_type');
        $modelId   = (int) $request->input('model_id');

        // Vérifier que l'enregistrement existe
        $modelType::findOrFail($modelId);

        $fichier     = $request->file('fichier');
        $nomOriginal = $fichier->getClientOriginalName();
        $extension   = strtolower($fichier->getClientOriginalExtension());
        $nomStockage = Str::uuid() . '.' . $extension;
        $dossier     = 'documents/' . Str::snake(class_basename($modelType)) . '/' . $modelId;
        $chemin      = $fichier->storeAs($dossier, $nomStockage, 'local');

        Document::create([
            'doc_type_id'    => $request->input('doc_type_id'),
            'model_type'     => $modelType,
            'model_id'       => $modelId,
            'nom'            => $request->input('nom'),
            'nom_original'   => $nomOriginal,
            'chemin'         => $chemin,
            'mime_type'      => $fichier->getMimeType(),
            'taille'         => $fichier->getSize(),
            'uploaded_by'    => auth()->id(),
            'uploaded_by_nom' => auth()->user()?->name,
        ]);

        return back()->with('success', 'Document « ' . $request->input('nom') . ' » ajouté avec succès.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        Storage::disk('local')->delete($document->chemin);
        $nom = $document->nom;
        $document->delete();

        return back()->with('success', 'Document « ' . $nom . ' » supprimé.');
    }

    public function telecharger(Document $document): StreamedResponse|RedirectResponse
    {
        if (!$document->existeSurDisque()) {
            return back()->with('error', 'Fichier introuvable sur le serveur.');
        }

        return Storage::disk('local')->download($document->chemin, $document->nom_original);
    }
}
