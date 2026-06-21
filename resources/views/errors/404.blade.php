@extends('errors._layout')

@section('titre', 'Page introuvable')
@section('code', '404')
@section('lead', "La page que vous recherchez est introuvable.")
@section('message')
    Vérifiez que l'adresse est correcte ou que la page n'a pas été déplacée.
    Si vous pensez qu'il s'agit d'une erreur, rapprochez-vous de votre administrateur.
@endsection
