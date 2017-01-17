<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', '');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', '');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', '');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', '');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N'y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'B;e_IrRddUllD&`6tfk=Wq^T^(K7>,zLz{chzvSXSFJKVp<7agm|,vOjrIqi|ORk');
define('SECURE_AUTH_KEY',  'UbW-l/lR!0KJ.D.<S!d#QHX%*4hT9Y>YIL#O7sYJdINL3#d2GK=tt[?I9>t65JrY');
define('LOGGED_IN_KEY',    '5k))f?^F5)E|(*&468x@xth39p=IGh*XsR(gVcDk~;t/9(D>jewi X5af|~Bl.g>');
define('NONCE_KEY',        ']1&A-khGlO=#cfD2!.B)y>4.{boIfezh:feJy?m08w{-cTn4<m/.]hr%EO4@RE!U');
define('AUTH_SALT',        '{ExfxZ&W@8J)kjE0,4+@fIQ6U`6{#j:R?0w+r9aKdo9Odg%ww(VBbK=D+d]f!jz{');
define('SECURE_AUTH_SALT', 'xmuz5S.-8l3Zd6VAZTV9^8i0tH7P5ZrDJS:rR`l!-K~0IL)=KDATgb98_Ker!}to');
define('LOGGED_IN_SALT',   '|.f*[i!iRn}Y;Pr|?!p5(:Z}?yi`u|j$a4mSrzTinJ@+hS*^dehv+=vdfr|=H[ZP');
define('NONCE_SALT',       'l%$-|l,Cnw-.,`^m >J=En-^6e:lNM?XWCi-_ul]8~+(=d>sN-uXG3J+T_,lAxb=');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'super_';

/**
 * Pour les développeurs : le mode deboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 */
define('WP_DEBUG', true);

define('WP_POST_REVISIONS', 5);
define('EMPTY_TRASH_DAYS', 10);
define('WP_AUTO_UPDATE_CORE', true);
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_UNFILTERED_HTML', true);

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
