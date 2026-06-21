<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos identifiants de connexion</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f7fa;font-family:Arial,Helvetica,sans-serif;color:#3c4043;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f7fa;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e3e6ea;">
                    {{-- Bandeau --}}
                    <tr>
                        <td style="background-color:#2c7be5;padding:20px 28px;color:#ffffff;font-size:18px;font-weight:bold;">
                            {{ config('app.name') }}
                        </td>
                    </tr>
                    {{-- Corps --}}
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 16px;font-size:15px;">
                                Bonjour{{ filled($user->name) ? ' ' . e($user->name) : '' }},
                            </p>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">
                                Un compte d'accès à l'application <strong>{{ config('app.name') }}</strong> vient d'être
                                créé pour vous. Voici vos identifiants de connexion :
                            </p>

                            {{-- Encadré identifiants --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="background-color:#f5f7fa;border:1px solid #e3e6ea;border-radius:6px;margin:0 0 20px;">
                                <tr>
                                    <td style="padding:16px 20px;font-size:14px;line-height:1.8;">
                                        <strong>Identifiant (e-mail) :</strong>
                                        <span style="font-family:'Courier New',monospace;">{{ $user->email }}</span><br>
                                        <strong>Mot de passe :</strong>
                                        <span style="font-family:'Courier New',monospace;">{{ $motDePasse }}</span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 20px;font-size:14px;line-height:1.6;">
                                Pour des raisons de sécurité, nous vous recommandons de
                                <strong>modifier ce mot de passe</strong> dès votre première connexion.
                            </p>

                            {{-- Bouton --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
                                <tr>
                                    <td style="background-color:#2c7be5;border-radius:6px;">
                                        <a href="{{ $urlConnexion }}"
                                           style="display:inline-block;padding:12px 28px;color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">
                                            Se connecter
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0;font-size:12px;color:#8a94a6;line-height:1.6;">
                                Si vous n'êtes pas à l'origine de cette demande, ignorez ce message et
                                signalez-le à l'administrateur.
                            </p>
                        </td>
                    </tr>
                    {{-- Pied --}}
                    <tr>
                        <td style="background-color:#f9fafb;padding:16px 28px;font-size:11px;color:#8a94a6;border-top:1px solid #e3e6ea;">
                            Message automatique — {{ config('app.name') }}. Merci de ne pas répondre à cet e-mail.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
