@extends('errors._layout')

@section('titre', 'Accès refusé')
@section('code', '403')
@section('lead', 'Accès refusé.')
@section('message')
    Vous n'avez pas l'autorisation d'accéder à cette page.
    Si vous pensez qu'il s'agit d'une erreur, rapprochez-vous de votre administrateur.
@endsection
