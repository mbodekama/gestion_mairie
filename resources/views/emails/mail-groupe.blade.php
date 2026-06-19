@php
    $nomDestinataire = $contribuable->type_personne === 'PP'
        ? trim(($contribuable->nom ?? '') . ' ' . ($contribuable->prenoms ?? ''))
        : ($contribuable->raison_sociale ?? '');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $objet }}</title>
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
                                Bonjour{{ filled($nomDestinataire) ? ' ' . e($nomDestinataire) : '' }},
                            </p>
                            <div style="margin:0 0 16px;font-size:14px;line-height:1.6;">
                                {!! nl2br(e($corps)) !!}
                            </div>
                            <p style="margin:24px 0 0;font-size:14px;">
                                Cordialement,<br>
                                <strong>{{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>
                    {{-- Pied --}}
                    <tr>
                        <td style="background-color:#f9fafb;padding:16px 28px;border-top:1px solid #e3e6ea;font-size:12px;color:#748194;">
                            Ce message vous est adressé par les services fiscaux de la collectivité.
                            Merci de ne pas répondre directement à cet e-mail.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
