@extends('errors._layout')

@section('titre', 'Erreur serveur')
@section('code', '500')
@section('lead', 'Une erreur inattendue est survenue.')
@section('message')
    Un incident technique empêche l'affichage de cette page.
    Veuillez réessayer dans quelques instants ; si le problème persiste,
    rapprochez-vous de votre administrateur.
@endsection

@section('actions')
    <a class="btn btn-primary btn-sm" href="{{ url('/') }}">
        <span class="fas fa-home me-2"></span>Retour à l'accueil
    </a>
    <a class="btn btn-falcon-default btn-sm" href="javascript:location.reload()">
        <span class="fas fa-redo me-2"></span>Réessayer
    </a>
@endsection
