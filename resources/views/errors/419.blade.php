@extends('errors._layout')

@section('titre', 'Session expirée')
@section('code', '419')
@section('lead', 'Votre session a expiré.')
@section('message')
    Pour votre sécurité, la page a expiré après une période d'inactivité.
    Veuillez recharger la page ou vous reconnecter, puis réessayer.
@endsection

@section('actions')
    <a class="btn btn-primary btn-sm" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
        <span class="fas fa-sync-alt me-2"></span>{{ auth()->check() ? "Retour à l'accueil" : 'Se reconnecter' }}
    </a>
    <a class="btn btn-falcon-default btn-sm" href="javascript:location.reload()">
        <span class="fas fa-redo me-2"></span>Recharger la page
    </a>
@endsection
