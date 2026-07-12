<?php

if (!defined('IN_PHPBB'))
{
    exit;
}

$lang = array_merge($lang, [
    'ACP_NEXTCLOUDCALENDAR_TITLE' => 'Calendrier Nextcloud',
    'ACP_NEXTCLOUDCALENDAR_SETTINGS' => 'Parametres',
    'ACP_NEXTCLOUDCALENDAR_ENABLED' => "Activer les propositions d'evenements",
    'ACP_NEXTCLOUDCALENDAR_ENABLED_EXPLAIN' => "Controle si les utilisateurs peuvent ouvrir le formulaire et envoyer de nouvelles propositions d'evenements. Les moderateurs peuvent toujours traiter les propositions en attente.",
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION' => 'Afficher le lien public',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_EXPLAIN' => "Controle ou les utilisateurs voient le lien vers le formulaire en dehors d'un message. La tuile reprend l'apparence du theme actif. Le marqueur [nextcloudcalendar] dans les messages n'est pas modifie.",
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_NAVIGATION' => 'Navigation principale',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_QUICKLINKS' => 'Menu des liens rapides',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_INDEX_BUTTON' => 'Page d accueil comme bouton au-dessus de la liste des forums',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_INDEX_TILE' => 'Page d accueil comme tuile au-dessus de la liste des forums',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_FOOTER' => 'Pied de page',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_POSITION_NONE' => 'Ne pas afficher de lien automatique',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_ICON' => 'Icone FontAwesome',
    'ACP_NEXTCLOUDCALENDAR_FRONTEND_ICON_EXPLAIN' => 'Classe FA pour le lien et la tuile, par ex. fa-calendar-plus-o, fa-calendar ou calendar-check-o.',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL' => 'URL du calendrier CalDAV',
    'ACP_NEXTCLOUDCALENDAR_CALENDAR_URL_EXPLAIN' => 'URL complete du calendrier Nextcloud, par ex. https://cloud.example.org/remote.php/dav/calendars/utilisateur-calendrier/calendrier-association/',
    'ACP_NEXTCLOUDCALENDAR_USERNAME' => 'Utilisateur technique',
    'ACP_NEXTCLOUDCALENDAR_PASSWORD' => "Mot de passe d'application ou mot de passe",
    'ACP_NEXTCLOUDCALENDAR_PASSWORD_EXPLAIN' => 'Laisser vide pour conserver le mot de passe actuel.',
    'ACP_NEXTCLOUDCALENDAR_TIMEZONE' => 'Fuseau horaire',
    'ACP_NEXTCLOUDCALENDAR_TEST' => 'Tester la connexion',
    'ACP_NEXTCLOUDCALENDAR_TEST_OK' => 'Connexion reussie.',
]);
