<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Gestion des notifications in-app de l'utilisateur connecté (cloche du bandeau).
 */
class NotificationController extends Controller
{
    /** Marque une notification comme lue puis redirige vers sa cible. */
    public function lire(string $id): RedirectResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect()->to($url) : back();
    }

    /** Marque toutes les notifications non lues comme lues. */
    public function toutLire(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}
