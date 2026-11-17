<?php
/**
 * BeBeats Theme Functions
 * 
 * @package BeBeats
 */

/**
 * Enqueue styles and scripts
 */
function bebeats_enqueue_styles() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', array(), '5.3.2');
    
    // Enqueue fonts CSS
    wp_enqueue_style('bebeats-fonts', get_template_directory_uri() . '/fonts/fonts.css', array(), '1.0');
    
    // Enqueue theme stylesheet (style.css avec en-têtes WordPress contient déjà tout le CSS)
    wp_enqueue_style('bebeats-style', get_stylesheet_uri(), array('bootstrap-css', 'bebeats-fonts'), '1.0');
    
    // Enqueue page-specific styles
    if (is_page('connexion') || is_page('inscription') || is_page('welcome') ||
        is_page('mot-de-passe-oublie') || is_page('reinitialiser-mot-de-passe') || 
        is_page('inscription-fan-step1') || 
        is_page('inscription-fan-step2') || is_page('inscription-fan-step3') || 
        is_page('inscription-fan-step4') ||
        is_page('inscription-artiste-step1') || is_page('inscription-artiste-step2') ||
        is_page('inscription-artiste-step3') || is_page('inscription-artiste-step4') ||
        is_page('inscription-artiste-step5')) {
        wp_enqueue_style('bebeats-auth-style', get_template_directory_uri() . '/css/auth.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('auth-start') || is_page('debut-authentification')) {
        wp_enqueue_style('bebeats-auth-start-style', get_template_directory_uri() . '/css/pages/auth-start.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('event')) {
        wp_enqueue_style('bebeats-event-style', get_template_directory_uri() . '/css/pages/event.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('contribuer')) {
        wp_enqueue_style('bebeats-contribuer-style', get_template_directory_uri() . '/css/pages/contribuer.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('recherche')) {
        wp_enqueue_style('bebeats-recherche-style', get_template_directory_uri() . '/css/pages/recherche.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('reglages')) {
        wp_enqueue_style('bebeats-reglages-style', get_template_directory_uri() . '/css/pages/reglages.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('profil')) {
        wp_enqueue_style('bebeats-profil-style', get_template_directory_uri() . '/css/pages/profil.css', array('bebeats-style'), '1.0');
        // Charger aussi les styles du feed pour harmoniser l'affichage des posts et interactions
        wp_enqueue_style('bebeats-feed-style', get_template_directory_uri() . '/css/pages/feed.css', array('bebeats-style'), '1.0');
        wp_enqueue_script('bebeats-profil', get_template_directory_uri() . '/js/profil.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-profil', 'bebeatsProfil', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bebeats_reaction_action'),
            'homeUrl' => home_url('/')
        ));
    }
    
    if (is_page('feed-page')) {
        wp_enqueue_style('bebeats-feed-style', get_template_directory_uri() . '/css/pages/feed.css', array('bebeats-style'), '1.0');
    }
    
    if (is_page('resultats') || is_search()) {
        wp_enqueue_style('bebeats-resultats-style', get_template_directory_uri() . '/css/pages/resultats.css', array('bebeats-style'), '1.0');
    }
}

function bebeats_enqueue_scripts() {
    // Enqueue Bootstrap JS
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), '5.3.2', true);
    
    // Suivi d'artistes + notifications (utilisateur connecte)
    if (is_user_logged_in()) {
        wp_enqueue_script('bebeats-social', get_template_directory_uri() . '/js/social.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-social', 'bebeatsSocial', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'followNonce' => wp_create_nonce('bebeats_follow_action'),
            'notificationsNonce' => wp_create_nonce('bebeats_notifications_action'),
            'badgeNonce' => wp_create_nonce('bebeats_badge_action'),
            'isLoggedIn' => true
        ));
    }
    
    // Enqueue custom scripts
    wp_enqueue_script('bebeats-search', get_template_directory_uri() . '/js/search.js', array('jquery'), '1.0', true);
    
    // Passer l'URL d'accueil à JavaScript
    wp_localize_script('bebeats-search', 'bebeatsAjax', array(
        'homeUrl' => home_url('/')
    ));
    
    // Page-specific scripts
    
    // Charger toggles.js sur toutes les pages pour le mode de couleur
    wp_enqueue_script('bebeats-toggles', get_template_directory_uri() . '/js/toggles.js', array('jquery'), '1.0', true);
    
    if (is_page('reglages')) {
        wp_enqueue_script('bebeats-file-preview', get_template_directory_uri() . '/js/file-preview.js', array(), '1.0', true);
        wp_enqueue_script('bebeats-account', get_template_directory_uri() . '/js/account.js', array(), '1.0', true);
    }
    
    if (is_page('feed-page')) {
        wp_enqueue_script('bebeats-feed', get_template_directory_uri() . '/js/feed.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-feed', 'bebeatsFeed', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bebeats_reaction_action')
        ));
        wp_enqueue_script('bebeats-time-update', get_template_directory_uri() . '/js/time-update.js', array(), '1.0', true);
    }
    
    if (is_page('profil')) {
        wp_enqueue_script('bebeats-time-update', get_template_directory_uri() . '/js/time-update.js', array(), '1.0', true);
    }
    
    if (is_page('recherche')) {
        wp_enqueue_script('bebeats-recherche', get_template_directory_uri() . '/js/profil.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-recherche', 'bebeatsProfil', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bebeats_reaction_action'),
            'homeUrl' => home_url('/')
        ));
        wp_enqueue_script('bebeats-time-update', get_template_directory_uri() . '/js/time-update.js', array(), '1.0', true);
    }
    
    if (is_page('resultats') || is_search()) {
        wp_enqueue_script('bebeats-time-update', get_template_directory_uri() . '/js/time-update.js', array(), '1.0', true);
    }
    
    if (is_page('contribuer')) {
        wp_enqueue_script('bebeats-contribuer', get_template_directory_uri() . '/js/contribuer.js', array('jquery'), '1.0', true);
        wp_localize_script('bebeats-contribuer', 'bebeatsContribuer', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bebeats_search_users_action')
        ));
    }
    
    // Enqueue file-preview.js pour les pages d'inscription
    if (is_page('inscription-fan-step3') || is_page('inscription-artiste-step3') || is_page('inscription-artiste-step4')) {
        wp_enqueue_script('bebeats-file-preview', get_template_directory_uri() . '/js/file-preview.js', array(), '1.0', true);
    }
}

// Empêcher la redirection RSS pour la page feed
function bebeats_disable_feed_redirect() {
    global $wp_query, $wp;
    
    // Vérifier si l'URL demandée est /feed-page/
    $requested_url = trim($wp->request, '/');
    
    if ($requested_url === 'feed-page') {
        // Vérifier si c'est une page WordPress
        $page = get_page_by_path($requested_url);
        if ($page) {
            $wp_query->is_feed = false;
            $wp_query->is_404 = false;
            $wp_query->is_page = true;
            $wp_query->queried_object = $page;
            $wp_query->queried_object_id = $page->ID;
        }
    }
}
add_action('parse_request', 'bebeats_disable_feed_redirect', 1);
add_action('parse_query', 'bebeats_disable_feed_redirect', 1);

// Hook into WordPress
add_action('wp_enqueue_scripts', 'bebeats_enqueue_styles');
add_action('wp_enqueue_scripts', 'bebeats_enqueue_scripts');

/**
 * Theme setup
 */
function bebeats_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus if needed
    register_nav_menus(array(
        'primary' => __('Menu Principal', 'bebeats'),
    ));
}
add_action('after_setup_theme', 'bebeats_setup');

/**
 * Listes blanches de types MIME autorises pour les uploads
 */
function bebeats_allowed_image_mimes() {
    return array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp'
    );
}

function bebeats_allowed_audio_mimes() {
    return array(
        'mp3|m4a|m4b' => 'audio/mpeg',
        'mp4|m4a' => 'audio/mp4',
        'wav' => 'audio/wav',
        'ogg|oga' => 'audio/ogg'
    );
}

function bebeats_allowed_media_mimes() {
    return array_merge(
        bebeats_allowed_image_mimes(),
        array(
            'mp4|m4v' => 'video/mp4',
            'webm' => 'video/webm',
            'mov|qt' => 'video/quicktime'
        )
    );
}

/**
 * Upload valide : verifie l'extension, le type MIME reel et la taille
 * avant de deplacer le fichier. Retourne le resultat de wp_handle_upload
 * ou null en cas de rejet.
 */
function bebeats_handle_validated_upload($file, $allowed_mimes, $max_size = 20971520) {
    if (empty($file['name'])) {
        return null;
    }

    if (!empty($file['size']) && $file['size'] > $max_size) {
        return null;
    }

    require_once(ABSPATH . 'wp-admin/includes/file.php');

    $check = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], $allowed_mimes);
    if (empty($check['ext']) || empty($check['type'])) {
        return null;
    }

    $upload = wp_handle_upload($file, array(
        'test_form' => false,
        'mimes' => $allowed_mimes
    ));

    if (isset($upload['error'])) {
        return null;
    }

    return $upload;
}

/**
 * Statistiques de reactions pour un ensemble de posts en un minimum de requetes.
 * Retourne un tableau [post_id] => array(likes_count, reposts_count, comments_count, user_liked, user_favorited, user_reposted)
 */
function bebeats_get_reactions_stats($post_ids, $current_user_id = 0) {
    global $wpdb;
    $stats = array();

    if (empty($post_ids)) {
        return $stats;
    }

    $table = $wpdb->prefix . 'bebeats_post_reactions';
    $ids = array_map('intval', (array) $post_ids);
    $ids = array_values(array_filter($ids));
    if (empty($ids)) {
        return $stats;
    }
    $in = implode(',', $ids);

    foreach ($ids as $id) {
        $stats[$id] = array(
            'likes_count' => 0,
            'reposts_count' => 0,
            'comments_count' => 0,
            'user_liked' => false,
            'user_favorited' => false,
            'user_reposted' => false
        );
    }

    $rows = $wpdb->get_results("
        SELECT post_id,
            COUNT(DISTINCT CASE WHEN reaction_type = 'like' THEN user_id END) as likes,
            COUNT(DISTINCT CASE WHEN reaction_type = 'repost' THEN user_id END) as reposts,
            COUNT(DISTINCT CASE WHEN reaction_type = 'comment' AND (parent_comment_id IS NULL OR parent_comment_id = 0) THEN id END) as comments
        FROM $table
        WHERE post_id IN ($in)
        GROUP BY post_id
    ");

    foreach ((array) $rows as $r) {
        $stats[(int) $r->post_id]['likes_count'] = (int) $r->likes;
        $stats[(int) $r->post_id]['reposts_count'] = (int) $r->reposts;
        $stats[(int) $r->post_id]['comments_count'] = (int) $r->comments;
    }

    if ($current_user_id > 0) {
        $urows = $wpdb->get_results("
            SELECT post_id, reaction_type
            FROM $table
            WHERE user_id = " . intval($current_user_id) . "
              AND post_id IN ($in)
              AND reaction_type IN ('like', 'favorite', 'repost')
        ");

        foreach ((array) $urows as $r) {
            if ($r->reaction_type === 'like') {
                $stats[(int) $r->post_id]['user_liked'] = true;
            } elseif ($r->reaction_type === 'favorite') {
                $stats[(int) $r->post_id]['user_favorited'] = true;
            } elseif ($r->reaction_type === 'repost') {
                $stats[(int) $r->post_id]['user_reposted'] = true;
            }
        }
    }

    return $stats;
}

/**
 * Traitement du formulaire de connexion
 */
function bebeats_handle_login() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_login_nonce']) || !wp_verify_nonce($_POST['bebeats_login_nonce'], 'bebeats_login_action')) {
        wp_redirect(home_url('/connexion?error=1'));
        exit;
    }
    
    // Récupérer les données du formulaire
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Vérifier que les champs ne sont pas vides
    if (empty($username) || empty($password)) {
        wp_redirect(home_url('/connexion?error=1'));
        exit;
    }
    
    // Tenter de connecter l'utilisateur
    // WordPress peut utiliser email ou username pour se connecter
    $user = wp_authenticate($username, $password);
    
    if (is_wp_error($user)) {
        // Erreur d'authentification
        wp_redirect(home_url('/connexion?error=1'));
        exit;
    } else {
        // Connexion réussie
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true); // true pour "remember me"
        
        // Déclencher l'action de connexion WordPress
        do_action('wp_login', $user->user_login, $user);
        
        // Rediriger vers la page de bienvenue avec le nom d'utilisateur
        wp_redirect(home_url('/welcome?user=' . urlencode($user->user_login)));
        exit;
    }
}
add_action('admin_post_bebeats_login', 'bebeats_handle_login');
add_action('admin_post_nopriv_bebeats_login', 'bebeats_handle_login');

/**
 * Créer la table de base de données pour les inscriptions
 */
function bebeats_create_registration_table() {
    global $wpdb;
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        registration_key varchar(100) NOT NULL,
        user_type varchar(20) NOT NULL DEFAULT 'fan',
        email varchar(255) NOT NULL,
        username varchar(100) NOT NULL,
        password_hash varchar(255) NOT NULL,
        genres text,
        profile_photo varchar(255),
        banner varchar(255),
        description text,
        sounds text,
        step_completed int(1) DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY registration_key (registration_key),
        KEY email (email),
        KEY username (username)
    ) $charset_collate;";
    
    dbDelta($sql);
    
    // Ajouter la colonne sounds si elle n'existe pas déjà (pour les mises à jour)
    $column_exists = $wpdb->get_results($wpdb->prepare(
        "SHOW COLUMNS FROM $table_name LIKE %s",
        'sounds'
    ));
    
    if (empty($column_exists)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN sounds text AFTER description");
    }
}

// Créer la table à l'activation du thème
/**
 * Creer automatiquement les pages du theme (slug + modele) a l'activation,
 * uniquement si elles n'existent pas deja.
 */
function bebeats_create_default_pages() {
    $pages = array(
        'connexion'                    => 'Connexion Page',
        'inscription'                  => 'Inscription Page',
        'auth-start'                   => 'Auth Start Page',
        'inscription-fan-step1'        => 'Inscription Fan - Etape 1',
        'inscription-fan-step2'        => 'Inscription Fan - Etape 2',
        'inscription-fan-step3'        => 'Inscription Fan - Etape 3',
        'inscription-fan-step4'        => 'Inscription Fan - Etape 4',
        'inscription-artiste-step1'   => 'Inscription Artiste - Etape 1',
        'inscription-artiste-step2'   => 'Inscription Artiste - Etape 2',
        'inscription-artiste-step3'   => 'Inscription Artiste - Etape 3',
        'inscription-artiste-step4'   => 'Inscription Artiste - Etape 4',
        'inscription-artiste-step5'   => 'Inscription Artiste - Etape 5',
        'feed-page'                    => 'Feed Page',
        'event'                        => 'Event Page',
        'contribuer'                   => 'Contribuer Page',
        'recherche'                    => 'Recherche Page',
        'resultats'                    => 'Resultats Page',
        'reglages'                     => 'Reglages Page',
        'profil'                       => 'Profil Page',
        'welcome'                      => 'Welcome Page',
        'mot-de-passe-oublie'          => 'Mot de passe oublie',
        'reinitialiser-mot-de-passe'   => 'Reinitialiser mot de passe',
        'succes'                       => 'Succes Page'
    );

    $template_files = array(
        'connexion'                  => 'page-connexion.php',
        'inscription'                => 'page-inscription.php',
        'auth-start'                 => 'page-auth-start.php',
        'inscription-fan-step1'      => 'page-inscription-fan-step1.php',
        'inscription-fan-step2'      => 'page-inscription-fan-step2.php',
        'inscription-fan-step3'      => 'page-inscription-fan-step3.php',
        'inscription-fan-step4'      => 'page-inscription-fan-step4.php',
        'inscription-artiste-step1' => 'page-inscription-artiste-step1.php',
        'inscription-artiste-step2' => 'page-inscription-artiste-step2.php',
        'inscription-artiste-step3' => 'page-inscription-artiste-step3.php',
        'inscription-artiste-step4' => 'page-inscription-artiste-step4.php',
        'inscription-artiste-step5' => 'page-inscription-artiste-step5.php',
        'feed-page'                  => 'page-feed.php',
        'event'                      => 'page-event.php',
        'contribuer'                 => 'page-contribuer.php',
        'recherche'                  => 'page-recherche.php',
        'resultats'                  => 'page-resultats.php',
        'reglages'                   => 'page-reglages.php',
        'profil'                     => 'page-profil.php',
        'welcome'                    => 'page-welcome.php',
        'mot-de-passe-oublie'        => 'page-mot-de-passe-oublie.php',
        'reinitialiser-mot-de-passe' => 'page-reinitialiser-mot-de-passe.php',
        'succes'                     => 'page-succes.php'
    );

    foreach ($pages as $slug => $title) {
        if (get_page_by_path($slug) === null) {
            $page_id = wp_insert_post(array(
                'post_title'   => ucfirst($slug),
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page'
            ));

            if ($page_id && !is_wp_error($page_id) && isset($template_files[$slug])) {
                update_post_meta($page_id, '_wp_page_template', $template_files[$slug]);
            }
        }
    }

    flush_rewrite_rules();
}
add_action('after_switch_theme', 'bebeats_create_default_pages');

/**
 * Suivi d'artistes et notifications
 */

function bebeats_create_follows_table() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . 'bebeats_follows';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        follower_id bigint(20) NOT NULL,
        artist_id bigint(20) NOT NULL,
        notify tinyint(1) DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY unique_follow (follower_id, artist_id),
        KEY artist_id (artist_id)
    ) $charset_collate;";
    dbDelta($sql);
    
    // Migration : ajouter la colonne notify sur les installations existantes
    $column_exists = $wpdb->get_results($wpdb->prepare(
        "SHOW COLUMNS FROM $table_name LIKE %s",
        'notify'
    ));
    if (empty($column_exists)) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN notify tinyint(1) DEFAULT 1 AFTER artist_id");
    }
}

function bebeats_create_notifications_table() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . 'bebeats_notifications';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        actor_id bigint(20) NOT NULL,
        type varchar(30) NOT NULL DEFAULT 'new_post',
        post_id bigint(20) DEFAULT NULL,
        is_read tinyint(1) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id, is_read),
        KEY created_at (created_at)
    ) $charset_collate;";
    dbDelta($sql);
}

// Garantir les tables (et leurs migrations de colonnes) a chaque chargement :
// CREATE TABLE IF NOT EXISTS + verifications ALTER sont rapides et idempotentes
function bebeats_ensure_social_tables() {
    bebeats_create_follows_table();
    bebeats_create_notifications_table();
}
add_action('init', 'bebeats_ensure_social_tables');
add_action('after_switch_theme', 'bebeats_create_follows_table');
add_action('after_switch_theme', 'bebeats_create_notifications_table');

/**
 * Suivre / ne plus suivre un artiste (AJAX)
 */
function bebeats_ajax_toggle_follow() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connexion requise'));
    }
    
    check_ajax_referer('bebeats_follow_action', 'nonce');
    
    $current_user_id = get_current_user_id();
    $artist_id = isset($_POST['artist_id']) ? intval($_POST['artist_id']) : 0;
    
    if ($artist_id <= 0 || $artist_id === $current_user_id) {
        wp_send_json_error(array('message' => 'Requete invalide'));
    }
    
    $artist = get_userdata($artist_id);
    if (!$artist || get_user_meta($artist_id, 'bebeats_user_type', true) !== 'artiste') {
        wp_send_json_error(array('message' => 'Cet utilisateur n\'est pas un artiste'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_follows';
    
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE follower_id = %d AND artist_id = %d",
        $current_user_id, $artist_id
    ));
    
    if ($existing) {
        $wpdb->delete($table_name, array('id' => $existing), array('%d'));
        $following = false;
    } else {
        $wpdb->insert($table_name, array(
            'follower_id' => $current_user_id,
            'artist_id' => $artist_id
        ), array('%d', '%d'));
        $following = true;
        // Gamification : +50 points par nouvel abonnement
        bebeats_award_points($current_user_id, 50);
    }
    
    $followers_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE artist_id = %d",
        $artist_id
    ));
    
    wp_send_json_success(array(
        'following' => $following,
        'followers_count' => $followers_count
    ));
}
add_action('wp_ajax_bebeats_toggle_follow', 'bebeats_ajax_toggle_follow');

/**
 * Activer / desactiver les notifications d'un artiste suivi (AJAX)
 */
function bebeats_ajax_toggle_follow_notifications() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connexion requise'));
    }
    
    check_ajax_referer('bebeats_follow_action', 'nonce');
    
    $current_user_id = get_current_user_id();
    $artist_id = isset($_POST['artist_id']) ? intval($_POST['artist_id']) : 0;
    
    if ($artist_id <= 0) {
        wp_send_json_error(array('message' => 'Requete invalide'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_follows';
    
    $notify_state = $wpdb->get_var($wpdb->prepare(
        "SELECT notify FROM $table_name WHERE follower_id = %d AND artist_id = %d",
        $current_user_id, $artist_id
    ));
    
    if ($notify_state === null) {
        wp_send_json_error(array('message' => 'Tu ne suis pas cet artiste'));
    }
    
    $new_state = $notify_state ? 0 : 1;
    
    $wpdb->update(
        $table_name,
        array('notify' => $new_state),
        array('follower_id' => $current_user_id, 'artist_id' => $artist_id),
        array('%d'),
        array('%d', '%d')
    );
    
    wp_send_json_success(array('notify' => (bool) $new_state));
}
add_action('wp_ajax_bebeats_toggle_follow_notifications', 'bebeats_ajax_toggle_follow_notifications');

/**
 * Notifier les followers quand un artiste publie
 */
function bebeats_notify_followers($artist_id, $post_id) {
    global $wpdb;
    $follows_table = $wpdb->prefix . 'bebeats_follows';
    $notif_table = $wpdb->prefix . 'bebeats_notifications';
    
    $followers = $wpdb->get_col($wpdb->prepare(
        "SELECT follower_id FROM $follows_table WHERE artist_id = %d AND notify = 1 LIMIT 500",
        $artist_id
    ));
    
    if (empty($followers)) {
        return;
    }
    
    $artist = get_userdata($artist_id);
    $artist_name = $artist ? ($artist->display_name ?: $artist->user_login) : '';
    
    foreach ($followers as $follower_id) {
        $wpdb->insert($notif_table, array(
            'user_id' => $follower_id,
            'actor_id' => $artist_id,
            'type' => 'new_post',
            'post_id' => $post_id
        ), array('%d', '%d', '%s', '%d'));
    }
}

/**
 * Liste des notifications de l'utilisateur courant (AJAX)
 */
function bebeats_ajax_get_notifications() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connexion requise'));
    }
    
    check_ajax_referer('bebeats_notifications_action', 'nonce');
    
    global $wpdb;
    $current_user_id = get_current_user_id();
    $table = $wpdb->prefix . 'bebeats_notifications';
    
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT n.*, u.display_name, u.user_login
         FROM $table n
         LEFT JOIN {$wpdb->users} u ON n.actor_id = u.ID
         WHERE n.user_id = %d
         ORDER BY n.created_at DESC
         LIMIT 20",
        $current_user_id
    ));
    
    $unread = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE user_id = %d AND is_read = 0",
        $current_user_id
    ));
    
    $items = array();
    foreach ((array) $rows as $r) {
        $actor_name = $r->display_name ?: $r->user_login;
        $link = home_url('/feed-page#post-' . $r->post_id);
        
        if ($r->type === 'new_post') {
            $message = sprintf('%s a publie un nouveau contenu', $actor_name);
        } elseif ($r->type === 'tagged') {
            $message = sprintf("%s t'a identifie dans un post", $actor_name);
        } else {
            $message = sprintf('%s a une nouvelle activite', $actor_name);
        }
        
        $items[] = array(
            'id' => (int) $r->id,
            'message' => $message,
            'link' => $link,
            'is_read' => (bool) $r->is_read,
            'time' => human_time_diff(strtotime($r->created_at), current_time('timestamp'))
        );
    }
    
    wp_send_json_success(array(
        'notifications' => $items,
        'unread' => $unread
    ));
}
add_action('wp_ajax_bebeats_get_notifications', 'bebeats_ajax_get_notifications');

/**
 * Marquer toutes les notifications comme lues (AJAX)
 */
function bebeats_ajax_mark_notifications_read() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connexion requise'));
    }
    
    check_ajax_referer('bebeats_notifications_action', 'nonce');
    
    global $wpdb;
    $table = $wpdb->prefix . 'bebeats_notifications';
    
    $wpdb->update(
        $table,
        array('is_read' => 1),
        array('user_id' => get_current_user_id()),
        array('%d'),
        array('%d')
    );
    
    wp_send_json_success(array('unread' => 0));
}
add_action('wp_ajax_bebeats_mark_notifications_read', 'bebeats_ajax_mark_notifications_read');

/**
 * Mise a jour du compte : pseudo visible + mot de passe
 */
function bebeats_handle_update_account() {
    if (!is_user_logged_in()) {
        wp_redirect(home_url('/auth-start'));
        exit;
    }
    
    if (!isset($_POST['bebeats_update_account_nonce']) || !wp_verify_nonce($_POST['bebeats_update_account_nonce'], 'bebeats_update_account_action')) {
        wp_redirect(home_url('/reglages?account_error=1'));
        exit;
    }
    
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // --- Pseudo visible ---
    $display_name = isset($_POST['display_name']) ? sanitize_text_field($_POST['display_name']) : '';
    if (!empty($display_name) && $display_name !== $current_user->display_name) {
        if (mb_strlen($display_name) < 2 || mb_strlen($display_name) > 40) {
            wp_redirect(home_url('/reglages?account_error=pseudo'));
            exit;
        }
        $update = wp_update_user(array(
            'ID'           => $user_id,
            'display_name' => $display_name
        ));
        if (is_wp_error($update)) {
            wp_redirect(home_url('/reglages?account_error=1'));
            exit;
        }
    }
    
    // --- Changement de mot de passe (optionnel) ---
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (!empty($new_password) || !empty($current_password)) {
        if (empty($current_password) || !wp_check_password($current_password, $current_user->user_pass, $user_id)) {
            wp_redirect(home_url('/reglages?account_error=current'));
            exit;
        }
        
        if (strlen($new_password) < 8) {
            wp_redirect(home_url('/reglages?account_error=length'));
            exit;
        }
        
        if ($new_password !== $confirm_password) {
            wp_redirect(home_url('/reglages?account_error=confirm'));
            exit;
        }
        
        wp_set_password($new_password, $user_id);
        
        // wp_set_password deconnecte toutes les sessions : reconnexion immediate
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
    }
    
    wp_redirect(home_url('/reglages?account_success=1'));
    exit;
}
add_action('admin_post_bebeats_update_account', 'bebeats_handle_update_account');

/**
 * Mot de passe oublie : envoi du mail de reinitialisation
 */
function bebeats_handle_forgot_password() {
    if (!isset($_POST['bebeats_forgot_password_nonce']) || !wp_verify_nonce($_POST['bebeats_forgot_password_nonce'], 'bebeats_forgot_password_action')) {
        wp_redirect(home_url('/mot-de-passe-oublie?forgot_error=1'));
        exit;
    }
    
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    
    if (empty($email)) {
        wp_redirect(home_url('/mot-de-passe-oublie?forgot_error=empty'));
        exit;
    }
    
    $user = get_user_by('email', $email);
    
    // Toujours le meme message : ne pas reveler si l'email existe
    if ($user) {
        $key = wp_generate_password(32, false);
        
        update_user_meta($user->ID, 'bebeats_reset_key_hash', wp_hash_password($key));
        update_user_meta($user->ID, 'bebeats_reset_key_time', time());
        
        $reset_url = add_query_arg(
            array(
                'login' => urlencode($user->user_login),
                'key'   => urlencode($key)
            ),
            home_url('/reinitialiser-mot-de-passe')
        );
        
        $subject = 'Reinitialisation de ton mot de passe BeBeats';
        $message = "Bonjour " . ($user->display_name ?: $user->user_login) . ",\n\n"
                 . "Quelqu'un (toi, on l'espere) a demande la reinitialisation de ton mot de passe BeBeats.\n\n"
                 . "Clique sur ce lien pour choisir un nouveau mot de passe (valide 1 heure) :\n"
                 . $reset_url . "\n\n"
                 . "Si tu n'es pas a l'origine de cette demande, ignore simplement ce mail.\n\n"
                 . "L'equipe BeBeats";
        
        wp_mail($user->user_email, $subject, $message);
    }
    
    wp_redirect(home_url('/mot-de-passe-oublie?forgot_sent=1'));
    exit;
}
add_action('admin_post_bebeats_forgot_password', 'bebeats_handle_forgot_password');
add_action('admin_post_nopriv_bebeats_forgot_password', 'bebeats_handle_forgot_password');

/**
 * Mot de passe oublie : definition du nouveau mot de passe
 */
function bebeats_handle_reset_password() {
    if (!isset($_POST['bebeats_reset_password_nonce']) || !wp_verify_nonce($_POST['bebeats_reset_password_nonce'], 'bebeats_reset_password_action')) {
        wp_redirect(home_url('/mot-de-passe-oublie?forgot_error=1'));
        exit;
    }
    
    $login = isset($_POST['login']) ? sanitize_user($_POST['login']) : '';
    $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    $user = !empty($login) ? get_user_by('login', $login) : false;
    
    if (!$user || empty($key)) {
        wp_redirect(home_url('/mot-de-passe-oublie?forgot_error=invalid'));
        exit;
    }
    
    $key_hash = get_user_meta($user->ID, 'bebeats_reset_key_hash', true);
    $key_time = (int) get_user_meta($user->ID, 'bebeats_reset_key_time', true);
    
    // Cle expiree apres 1 heure
    if (empty($key_hash) || (time() - $key_time) > 3600 || !wp_check_password($key, $key_hash, $user->ID)) {
        delete_user_meta($user->ID, 'bebeats_reset_key_hash');
        delete_user_meta($user->ID, 'bebeats_reset_key_time');
        wp_redirect(home_url('/mot-de-passe-oublie?forgot_error=expired'));
        exit;
    }
    
    if (strlen($new_password) < 8) {
        wp_redirect(home_url('/reinitialiser-mot-de-passe?login=' . urlencode($login) . '&key=' . urlencode($key) . '&reset_error=length'));
        exit;
    }
    
    if ($new_password !== $confirm_password) {
        wp_redirect(home_url('/reinitialiser-mot-de-passe?login=' . urlencode($login) . '&key=' . urlencode($key) . '&reset_error=confirm'));
        exit;
    }
    
    wp_set_password($new_password, $user->ID);
    
    delete_user_meta($user->ID, 'bebeats_reset_key_hash');
    delete_user_meta($user->ID, 'bebeats_reset_key_time');
    
    wp_redirect(home_url('/connexion?reset_success=1'));
    exit;
}
add_action('admin_post_bebeats_reset_password', 'bebeats_handle_reset_password');
add_action('admin_post_nopriv_bebeats_reset_password', 'bebeats_handle_reset_password');

/**
 * Gamification : points, badges et titres
 */

function bebeats_award_points($user_id, $points) {
    $current = (int) get_user_meta($user_id, 'bebeats_points', true);
    update_user_meta($user_id, 'bebeats_points', $current + (int) $points);
}

function bebeats_get_user_points($user_id) {
    return (int) get_user_meta($user_id, 'bebeats_points', true);
}

/**
 * Statistiques d'activite d'un utilisateur (pour le calcul des badges)
 */
function bebeats_get_user_activity($user_id) {
    global $wpdb;
    $posts_table = $wpdb->prefix . 'bebeats_posts';
    $reactions_table = $wpdb->prefix . 'bebeats_post_reactions';
    $follows_table = $wpdb->prefix . 'bebeats_follows';
    
    return array(
        'posts' => (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $posts_table WHERE user_id = %d", $user_id
        )),
        'comments' => (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $reactions_table WHERE user_id = %d AND reaction_type = 'comment'", $user_id
        )),
        'likes_given' => (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $reactions_table WHERE user_id = %d AND reaction_type = 'like'", $user_id
        )),
        'following' => (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $follows_table WHERE follower_id = %d", $user_id
        )),
        'points' => bebeats_get_user_points($user_id)
    );
}

/**
 * Helper : l'utilisateur courant suit-il l'auteur du post donne ?
 * (utilise pour doubler les points de like/commentaire)
 */
function bebeats_user_follows_author($user_id, $post_id) {
    global $wpdb;
    $posts_table = $wpdb->prefix . 'bebeats_posts';
    $follows_table = $wpdb->prefix . 'bebeats_follows';
    
    $author_id = $wpdb->get_var($wpdb->prepare(
        "SELECT user_id FROM $posts_table WHERE id = %d",
        $post_id
    ));
    
    if (!$author_id || (int) $author_id === (int) $user_id) {
        return false;
    }
    
    return (bool) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $follows_table WHERE follower_id = %d AND artist_id = %d",
        $user_id, $author_id
    ));
}

/**
 * Seuils d'XP par niveau (le niveau 10 = 100 000 points, maximum actuel)
 */
function bebeats_get_level_thresholds() {
    return array(
        1 => 0,
        2 => 500,
        3 => 2000,
        4 => 6000,
        5 => 15000,
        6 => 30000,
        7 => 50000,
        8 => 70000,
        9 => 85000,
        10 => 100000
    );
}

/**
 * Niveau atteint pour un total d'XP donne
 */
function bebeats_get_level_from_points($points) {
    $level = 1;
    foreach (bebeats_get_level_thresholds() as $lvl => $threshold) {
        if ($points >= $threshold) {
            $level = $lvl;
        }
    }
    return $level;
}

/**
 * Catalogue des titres epinglables sur le profil
 */
function bebeats_get_title_catalog() {
    return array(
        'bebebeats' => array(
            'title' => 'BebeBeats',
            'display' => 'BebeBeats - Niveau 1',
            'min_level' => 1,
            'icon' => 'footsteps'
        ),
        'fan' => array(
            'title' => 'Fan',
            'display' => 'Fan - Niveau 5',
            'min_level' => 5,
            'icon' => 'heart'
        ),
        'super_fan' => array(
            'title' => 'Super Fan',
            'display' => 'Super Fan - Niveau 8',
            'min_level' => 8,
            'icon' => 'star'
        ),
        'fan_atik' => array(
            'title' => 'Fan-atik',
            'display' => 'Fan-atik - Niveau 10',
            'min_level' => 10,
            'icon' => 'trophy'
        )
    );
}

/**
 * Etat des titres d'un utilisateur : debloque ou non selon son niveau
 */
function bebeats_get_user_titles($user_id) {
    $points = bebeats_get_user_points($user_id);
    $level = bebeats_get_level_from_points($points);
    $catalog = bebeats_get_title_catalog();
    $selected = get_user_meta($user_id, 'bebeats_selected_badge', true);
    
    $titles = array();
    foreach ($catalog as $key => $title) {
        $titles[$key] = array(
            'title' => $title['title'],
            'display' => $title['display'],
            'icon' => $title['icon'],
            'min_level' => $title['min_level'],
            'unlocked' => $level >= $title['min_level'],
            'selected' => ($selected === $key)
        );
    }
    
    return $titles;
}

/**
 * Selection du titre epingle sur le profil (AJAX)
 */
function bebeats_ajax_select_badge() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connexion requise'));
    }
    
    check_ajax_referer('bebeats_badge_action', 'nonce');
    
    $title_key = isset($_POST['badge']) ? sanitize_key($_POST['badge']) : '';
    $user_id = get_current_user_id();
    
    if (empty($title_key)) {
        // Deselection : revenir au titre par defaut
        delete_user_meta($user_id, 'bebeats_selected_badge');
        wp_send_json_success(array('selected' => ''));
    }
    
    $titles = bebeats_get_user_titles($user_id);
    if (!isset($titles[$title_key]) || !$titles[$title_key]['unlocked']) {
        wp_send_json_error(array('message' => 'Titre non debloque'));
    }
    
    update_user_meta($user_id, 'bebeats_selected_badge', $title_key);
    wp_send_json_success(array('selected' => $title_key));
}
add_action('wp_ajax_bebeats_select_badge', 'bebeats_ajax_select_badge');

/**
 * Catalogue des badges (succes bases sur l'activite, distincts des titres)
 */
function bebeats_get_badge_catalog() {
    return array(
        'premier_pas' => array(
            'title' => 'Premier pas',
            'description' => 'Publier son premier contenu',
            'icon' => 'footsteps',
            'condition' => array('type' => 'posts', 'value' => 1)
        ),
        'voix_du_peuple' => array(
            'title' => 'Voix du peuple',
            'description' => 'Publier 5 contenus',
            'icon' => 'megaphone',
            'condition' => array('type' => 'posts', 'value' => 5)
        ),
        'commentateur' => array(
            'title' => 'Commentateur',
            'description' => 'Ecrire 10 commentaires',
            'icon' => 'chat',
            'condition' => array('type' => 'comments', 'value' => 10)
        ),
        'soutien_fidele' => array(
            'title' => 'Soutien fidele',
            'description' => 'Suivre 3 artistes',
            'icon' => 'heart',
            'condition' => array('type' => 'following', 'value' => 3)
        ),
        'super_fan_badge' => array(
            'title' => 'Super Fan',
            'description' => 'Atteindre 100 points',
            'icon' => 'star',
            'condition' => array('type' => 'points', 'value' => 100)
        ),
        'veteran_badge' => array(
            'title' => 'Veteran BeBeats',
            'description' => 'Atteindre 500 points',
            'icon' => 'trophy',
            'condition' => array('type' => 'points', 'value' => 500)
        )
    );
}

/**
 * Etat des badges d'un utilisateur : debloque ou non + epingle ou non
 */
function bebeats_get_user_badges($user_id) {
    $activity = bebeats_get_user_activity($user_id);
    $catalog = bebeats_get_badge_catalog();
    $pinned = get_user_meta($user_id, 'bebeats_pinned_badges', true);
    $pinned = is_array($pinned) ? $pinned : array();
    
    $badges = array();
    foreach ($catalog as $key => $badge) {
        $current = isset($activity[$badge['condition']['type']]) ? $activity[$badge['condition']['type']] : 0;
        $target = (int) $badge['condition']['value'];
        
        $badges[$key] = array(
            'title' => $badge['title'],
            'description' => $badge['description'],
            'icon' => $badge['icon'],
            'current' => $current,
            'target' => $target,
            'unlocked' => $current >= $target,
            'pinned' => in_array($key, $pinned)
        );
    }
    
    return $badges;
}

/**
 * Epingler / desepingler un badge (AJAX, max 5 epingles)
 */
function bebeats_ajax_toggle_badge_pin() {
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connexion requise'));
    }
    
    check_ajax_referer('bebeats_badge_action', 'nonce');
    
    $badge_key = isset($_POST['badge']) ? sanitize_key($_POST['badge']) : '';
    $user_id = get_current_user_id();
    
    if (empty($badge_key)) {
        wp_send_json_error(array('message' => 'Requete invalide'));
    }
    
    $badges = bebeats_get_user_badges($user_id);
    if (!isset($badges[$badge_key]) || !$badges[$badge_key]['unlocked']) {
        wp_send_json_error(array('message' => 'Badge non debloque'));
    }
    
    $pinned = get_user_meta($user_id, 'bebeats_pinned_badges', true);
    $pinned = is_array($pinned) ? $pinned : array();
    
    if (in_array($badge_key, $pinned)) {
        $pinned = array_diff($pinned, array($badge_key));
        $is_pinned = false;
    } else {
        if (count($pinned) >= 5) {
            wp_send_json_error(array('message' => 'Maximum 5 badges epingles'));
        }
        $pinned[] = $badge_key;
        $is_pinned = true;
    }
    
    update_user_meta($user_id, 'bebeats_pinned_badges', array_values($pinned));
    
    wp_send_json_success(array('pinned' => $is_pinned, 'pinned_count' => count($pinned)));
}
add_action('wp_ajax_bebeats_toggle_badge_pin', 'bebeats_ajax_toggle_badge_pin');

add_action('after_switch_theme', 'bebeats_create_registration_table');
add_action('after_switch_theme', 'bebeats_create_posts_table');
add_action('after_switch_theme', 'bebeats_create_post_reactions_table');

/**
 * Créer la table de base de données pour les posts
 */
function bebeats_create_posts_table() {
    global $wpdb;
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $table_name = $wpdb->prefix . 'bebeats_posts';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        post_type varchar(20) NOT NULL DEFAULT 'post',
        content text,
        event_title varchar(255),
        media_url varchar(500),
        media_type varchar(20),
        allow_comments tinyint(1) DEFAULT 1,
        allow_repost tinyint(1) DEFAULT 1,
        show_likes tinyint(1) DEFAULT 1,
        tagged_users varchar(500),
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY created_at (created_at),
        KEY post_type (post_type),
        KEY event_title (event_title)
    ) $charset_collate;";
    
    dbDelta($sql);
    
    // Ajouter les colonnes pour les options si elles n'existent pas déjà (pour les mises à jour)
    $columns_to_add = array(
        'allow_comments' => "ALTER TABLE $table_name ADD COLUMN allow_comments tinyint(1) DEFAULT 1 AFTER media_type",
        'allow_repost' => "ALTER TABLE $table_name ADD COLUMN allow_repost tinyint(1) DEFAULT 1 AFTER allow_comments",
        'show_likes' => "ALTER TABLE $table_name ADD COLUMN show_likes tinyint(1) DEFAULT 1 AFTER allow_repost",
        'event_title' => "ALTER TABLE $table_name ADD COLUMN event_title varchar(255) NULL AFTER content",
        'tagged_users' => "ALTER TABLE $table_name ADD COLUMN tagged_users varchar(500) NULL AFTER show_likes"
    );
    
    foreach ($columns_to_add as $column => $alter_sql) {
        $column_exists = $wpdb->get_results($wpdb->prepare(
            "SHOW COLUMNS FROM $table_name LIKE %s",
            $column
        ));
        
        if (empty($column_exists)) {
            $wpdb->query($alter_sql);
        }
    }
}

/**
 * Créer la table de base de données pour les réactions aux posts
 */
function bebeats_create_post_reactions_table() {
    global $wpdb;
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $table_name = $wpdb->prefix . 'bebeats_post_reactions';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        reaction_type varchar(20) NOT NULL,
        comment_text text,
        parent_comment_id bigint(20) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY user_id (user_id),
        KEY reaction_type (reaction_type),
        KEY parent_comment_id (parent_comment_id),
        KEY user_post_reaction (user_id, post_id, reaction_type, parent_comment_id)
    ) $charset_collate;";
    
    dbDelta($sql);
    
    // Supprimer la contrainte unique si elle existe (pour éviter les problèmes)
    $index_exists = $wpdb->get_results($wpdb->prepare(
        "SHOW INDEX FROM $table_name WHERE Key_name = %s",
        'unique_user_post_reaction'
    ));
    
    if (!empty($index_exists)) {
        $wpdb->query("ALTER TABLE $table_name DROP INDEX unique_user_post_reaction");
    }
    
    // Mettre à jour les NULL en 0 pour les réactions sans parent
    $wpdb->query("
        UPDATE $table_name 
        SET parent_comment_id = 0 
        WHERE parent_comment_id IS NULL 
        AND reaction_type IN ('like', 'repost', 'favorite')
    ");
    
    // Supprimer les doublons potentiels (garder le plus ancien)
    $wpdb->query("
        DELETE r1 FROM $table_name r1
        INNER JOIN $table_name r2 
        WHERE r1.id > r2.id 
        AND r1.user_id = r2.user_id 
        AND r1.post_id = r2.post_id 
        AND r1.reaction_type = r2.reaction_type 
        AND COALESCE(r1.parent_comment_id, 0) = COALESCE(r2.parent_comment_id, 0)
        AND r1.reaction_type IN ('like', 'repost', 'favorite', 'comment_like')
    ");
}

/**
 * Traitement de l'étape 1 d'inscription (Fan et Artiste) : identifiants
 */
function bebeats_handle_register_step1($user_type) {
    $base_url = '/inscription-' . $user_type . '-step1';
    $nonce_field = 'bebeats_register_' . $user_type . '_step1_nonce';
    $nonce_action = 'bebeats_register_' . $user_type . '_step1_action';
    
    // Vérifier le nonce
    if (!isset($_POST[$nonce_field]) || !wp_verify_nonce($_POST[$nonce_field], $nonce_action)) {
        wp_redirect(home_url($base_url . '?error=1'));
        exit;
    }
    
    // Récupérer les données
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validation
    if (empty($email) || empty($username) || empty($password)) {
        wp_redirect(home_url($base_url . '?error=1'));
        exit;
    }
    
    // Vérifier si l'email existe déjà
    if (email_exists($email)) {
        wp_redirect(home_url($base_url . '?error=2'));
        exit;
    }
    
    // Vérifier si le username existe déjà
    if (username_exists($username)) {
        wp_redirect(home_url($base_url . '?error=3'));
        exit;
    }
    
    // Générer une clé unique pour cette inscription
    $registration_key = wp_generate_password(32, false);
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    bebeats_create_registration_table();
    
    // Stocker le mot de passe haché (jamais en clair ni réversible)
    $password_encoded = wp_hash_password($password);
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'registration_key' => $registration_key,
            'user_type' => $user_type,
            'email' => $email,
            'username' => $username,
            'password_hash' => $password_encoded,
            'step_completed' => 1
        ),
        array('%s', '%s', '%s', '%s', '%s', '%d')
    );
    
    if ($result === false) {
        wp_redirect(home_url($base_url . '?error=1'));
        exit;
    }
    
    // Stocker la clé dans un cookie pour les étapes suivantes
    setcookie('bebeats_registration_key', $registration_key, time() + 3600, '/');
    
    // Rediriger vers l'étape 2
    wp_redirect(home_url('/inscription-' . $user_type . '-step2?key=' . urlencode($registration_key)));
    exit;
}

function bebeats_handle_register_fan_step1() {
    bebeats_handle_register_step1('fan');
}

function bebeats_handle_register_artiste_step1() {
    bebeats_handle_register_step1('artiste');
}
add_action('admin_post_bebeats_register_fan_step1', 'bebeats_handle_register_fan_step1');
add_action('admin_post_nopriv_bebeats_register_fan_step1', 'bebeats_handle_register_fan_step1');
add_action('admin_post_bebeats_register_artiste_step1', 'bebeats_handle_register_artiste_step1');
add_action('admin_post_nopriv_bebeats_register_artiste_step1', 'bebeats_handle_register_artiste_step1');

/**
 * Traitement de l'étape 2 d'inscription (Fan et Artiste) : genres musicaux
 */
function bebeats_handle_register_step2($user_type, $min_genres, $max_genres) {
    $base_url = '/inscription-' . $user_type . '-step2';
    $nonce_field = 'bebeats_register_' . $user_type . '_step2_nonce';
    $nonce_action = 'bebeats_register_' . $user_type . '_step2_action';
    
    // Vérifier le nonce
    if (!isset($_POST[$nonce_field]) || !wp_verify_nonce($_POST[$nonce_field], $nonce_action)) {
        wp_redirect(home_url($base_url . '?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription (priorité: POST > GET > COOKIE)
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-' . $user_type . '-step1?error=1'));
        exit;
    }
    
    // Récupérer les genres sélectionnés
    $genres = isset($_POST['genres']) ? array_map('sanitize_text_field', $_POST['genres']) : array();
    
    // Vérifier le nombre de genres requis
    if (count($genres) < $min_genres || count($genres) > $max_genres) {
        wp_redirect(home_url($base_url . '?key=' . urlencode($registration_key) . '&error=1'));
        exit;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $result = $wpdb->update(
        $table_name,
        array(
            'genres' => json_encode($genres),
            'step_completed' => 2
        ),
        array('registration_key' => $registration_key),
        array('%s', '%d'),
        array('%s')
    );
    
    if ($result === false) {
        wp_redirect(home_url($base_url . '?key=' . urlencode($registration_key) . '&error=1'));
        exit;
    }
    
    // Rediriger vers l'étape 3
    wp_redirect(home_url('/inscription-' . $user_type . '-step3?key=' . urlencode($registration_key)));
    exit;
}

function bebeats_handle_register_fan_step2() {
    bebeats_handle_register_step2('fan', 2, 2);
}

function bebeats_handle_register_artiste_step2() {
    bebeats_handle_register_step2('artiste', 1, 2);
}
add_action('admin_post_bebeats_register_fan_step2', 'bebeats_handle_register_fan_step2');
add_action('admin_post_nopriv_bebeats_register_fan_step2', 'bebeats_handle_register_fan_step2');
add_action('admin_post_bebeats_register_artiste_step2', 'bebeats_handle_register_artiste_step2');
add_action('admin_post_nopriv_bebeats_register_artiste_step2', 'bebeats_handle_register_artiste_step2');

/**
 * Traitement de l'étape 3 d'inscription Fan (profil)
 */
function bebeats_handle_register_fan_step3() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_fan_step3_nonce']) || !wp_verify_nonce($_POST['bebeats_register_fan_step3_nonce'], 'bebeats_register_fan_step3_action')) {
        wp_redirect(home_url('/inscription-fan-step3?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription (priorité: POST > GET > COOKIE)
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-fan-step1?error=1'));
        exit;
    }
    
    // Récupérer les données
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    
    // Gérer l'upload de la photo de profil
    $profile_photo = '';
    if (!empty($_FILES['profile_photo']['name'])) {
        $upload = bebeats_handle_validated_upload($_FILES['profile_photo'], bebeats_allowed_image_mimes());
        if ($upload !== null) {
            $profile_photo = $upload['url'];
        }
    }
    
    // Gérer l'upload de la bannière
    $banner = '';
    if (!empty($_FILES['banner']['name'])) {
        $upload = bebeats_handle_validated_upload($_FILES['banner'], bebeats_allowed_image_mimes());
        if ($upload !== null) {
            $banner = $upload['url'];
        }
    }
    
    // Mettre à jour la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $update_data = array();
    $update_format = array();
    
    if (!empty($profile_photo)) {
        $update_data['profile_photo'] = $profile_photo;
        $update_format[] = '%s';
    }
    
    if (!empty($banner)) {
        $update_data['banner'] = $banner;
        $update_format[] = '%s';
    }
    
    $update_data['description'] = $description;
    $update_format[] = '%s';
    
    $update_data['step_completed'] = 3;
    $update_format[] = '%d';
    
    $result = $wpdb->update(
        $table_name,
        $update_data,
        array('registration_key' => $registration_key),
        $update_format,
        array('%s')
    );
    
    // Créer le compte utilisateur WordPress
    $registration = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE registration_key = %s",
        $registration_key
    ));
    
    if ($registration) {
        // Créer l'utilisateur WordPress avec un mot de passe aléatoire,
        // puis appliquer directement le hash stocké (le mot de passe clair n'existe plus)
        $user_id = wp_create_user(
            $registration->username,
            wp_generate_password(32),
            $registration->email
        );
        
        if (!is_wp_error($user_id)) {
            global $wpdb;
            $wpdb->update(
                $wpdb->users,
                array('user_pass' => $registration->password_hash),
                array('ID' => $user_id),
                array('%s'),
                array('%d')
            );
            clean_user_cache($user_id);
            
            // Rôle auteur : permet la publication de posts (fan et artiste)
            $user_obj = get_userdata($user_id);
            if ($user_obj) {
                $user_obj->set_role('author');
            }
            
            // Mettre à jour les meta utilisateur
            update_user_meta($user_id, 'bebeats_user_type', 'fan');
            update_user_meta($user_id, 'bebeats_genres', json_decode($registration->genres, true));
            if (!empty($registration->description)) {
                update_user_meta($user_id, 'description', $registration->description);
            }
            if (!empty($registration->profile_photo)) {
                update_user_meta($user_id, 'bebeats_profile_photo', $registration->profile_photo);
            }
            if (!empty($registration->banner)) {
                update_user_meta($user_id, 'bebeats_banner', $registration->banner);
            }
            
            // Connecter l'utilisateur
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            
            // Supprimer la ligne d'inscription (contient encore le hash temporaire)
            $wpdb->delete($table_name, array('registration_key' => $registration_key), array('%s'));
            
            // Supprimer le cookie
            setcookie('bebeats_registration_key', '', time() - 3600, '/');
            
            // Rediriger vers l'étape 4
            wp_redirect(home_url('/inscription-fan-step4'));
            exit;
        } else {
            wp_redirect(home_url('/inscription-fan-step3?key=' . urlencode($registration_key) . '&error=1'));
            exit;
        }
    } else {
        wp_redirect(home_url('/inscription-fan-step1?error=1'));
        exit;
    }
}
add_action('admin_post_bebeats_register_fan_step3', 'bebeats_handle_register_fan_step3');
add_action('admin_post_nopriv_bebeats_register_fan_step3', 'bebeats_handle_register_fan_step3');



/**
 * Traitement de l'étape 3 d'inscription Artiste (profil)
 */
function bebeats_handle_register_artiste_step3() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_artiste_step3_nonce']) || !wp_verify_nonce($_POST['bebeats_register_artiste_step3_nonce'], 'bebeats_register_artiste_step3_action')) {
        wp_redirect(home_url('/inscription-artiste-step3?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
    
    // Récupérer les données
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    
    // Gérer l'upload de la photo de profil
    $profile_photo = '';
    if (!empty($_FILES['profile_photo']['name'])) {
        $upload = bebeats_handle_validated_upload($_FILES['profile_photo'], bebeats_allowed_image_mimes());
        if ($upload !== null) {
            $profile_photo = $upload['url'];
        }
    }
    
    // Gérer l'upload de la bannière
    $banner = '';
    if (!empty($_FILES['banner']['name'])) {
        $upload = bebeats_handle_validated_upload($_FILES['banner'], bebeats_allowed_image_mimes());
        if ($upload !== null) {
            $banner = $upload['url'];
        }
    }
    
    // Mettre à jour la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $update_data = array();
    $update_format = array();
    
    if (!empty($profile_photo)) {
        $update_data['profile_photo'] = $profile_photo;
        $update_format[] = '%s';
    }
    
    if (!empty($banner)) {
        $update_data['banner'] = $banner;
        $update_format[] = '%s';
    }
    
    $update_data['description'] = $description;
    $update_format[] = '%s';
    
    $update_data['step_completed'] = 3;
    $update_format[] = '%d';
    
    $result = $wpdb->update(
        $table_name,
        $update_data,
        array('registration_key' => $registration_key),
        $update_format,
        array('%s')
    );
    
    // Rediriger vers l'étape 4
    wp_redirect(home_url('/inscription-artiste-step4?key=' . urlencode($registration_key)));
    exit;
}
add_action('admin_post_bebeats_register_artiste_step3', 'bebeats_handle_register_artiste_step3');
add_action('admin_post_nopriv_bebeats_register_artiste_step3', 'bebeats_handle_register_artiste_step3');

/**
 * Traitement de l'étape 4 d'inscription Artiste (ajout de sons)
 */
function bebeats_handle_register_artiste_step4() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_register_artiste_step4_nonce']) || !wp_verify_nonce($_POST['bebeats_register_artiste_step4_nonce'], 'bebeats_register_artiste_step4_action')) {
        wp_redirect(home_url('/inscription-artiste-step4?error=1'));
        exit;
    }
    
    // Récupérer la clé d'inscription
    $registration_key = '';
    if (isset($_POST['registration_key']) && !empty($_POST['registration_key'])) {
        $registration_key = sanitize_text_field($_POST['registration_key']);
    } elseif (isset($_GET['key']) && !empty($_GET['key'])) {
        $registration_key = sanitize_text_field($_GET['key']);
    } elseif (isset($_COOKIE['bebeats_registration_key']) && !empty($_COOKIE['bebeats_registration_key'])) {
        $registration_key = sanitize_text_field($_COOKIE['bebeats_registration_key']);
    }
    
    if (empty($registration_key)) {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
    
    // Gérer l'upload des sons (optionnel)
    $sounds = array();
    if (!empty($_FILES['sounds']['name'][0])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        foreach ($_FILES['sounds']['name'] as $key => $value) {
            if ($_FILES['sounds']['name'][$key]) {
                $file = array(
                    'name' => $_FILES['sounds']['name'][$key],
                    'type' => $_FILES['sounds']['type'][$key],
                    'tmp_name' => $_FILES['sounds']['tmp_name'][$key],
                    'error' => $_FILES['sounds']['error'][$key],
                    'size' => $_FILES['sounds']['size'][$key]
                );
                
                $upload = bebeats_handle_validated_upload($file, bebeats_allowed_audio_mimes());
                if ($upload !== null) {
                    $sounds[] = $upload['url'];
                }
            }
        }
    }
    
    // Mettre à jour la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_registrations';
    
    $update_data = array();
    $update_format = array();
    
    if (!empty($sounds)) {
        $update_data['sounds'] = json_encode($sounds); // Stocker les sons en JSON
        $update_format[] = '%s';
    }
    
    $update_data['step_completed'] = 4;
    $update_format[] = '%d';
    
    $result = $wpdb->update(
        $table_name,
        $update_data,
        array('registration_key' => $registration_key),
        $update_format,
        array('%s')
    );
    
    // Créer le compte utilisateur WordPress
    $registration = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE registration_key = %s",
        $registration_key
    ));
    
    if ($registration) {
        // Créer l'utilisateur WordPress avec un mot de passe aléatoire,
        // puis appliquer directement le hash stocké (le mot de passe clair n'existe plus)
        $user_id = wp_create_user(
            $registration->username,
            wp_generate_password(32),
            $registration->email
        );
        
        if (!is_wp_error($user_id)) {
            global $wpdb;
            $wpdb->update(
                $wpdb->users,
                array('user_pass' => $registration->password_hash),
                array('ID' => $user_id),
                array('%s'),
                array('%d')
            );
            clean_user_cache($user_id);
            
            // Rôle auteur : permet la publication de posts (fan et artiste)
            $user_obj = get_userdata($user_id);
            if ($user_obj) {
                $user_obj->set_role('author');
            }
            
            // Mettre à jour les meta utilisateur
            update_user_meta($user_id, 'bebeats_user_type', 'artiste');
            update_user_meta($user_id, 'bebeats_genres', json_decode($registration->genres, true));
            if (!empty($registration->description)) {
                update_user_meta($user_id, 'description', $registration->description);
            }
            if (!empty($registration->profile_photo)) {
                update_user_meta($user_id, 'bebeats_profile_photo', $registration->profile_photo);
            }
            if (!empty($registration->banner)) {
                update_user_meta($user_id, 'bebeats_banner', $registration->banner);
            }
            if (!empty($sounds)) {
                update_user_meta($user_id, 'bebeats_sounds', $sounds);
            }
            
            // Connecter l'utilisateur
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            
            // Supprimer la ligne d'inscription (contient encore le hash temporaire)
            $wpdb->delete($table_name, array('registration_key' => $registration_key), array('%s'));
            
            // Supprimer le cookie
            setcookie('bebeats_registration_key', '', time() - 3600, '/');
            
            // Rediriger vers l'étape 5
            wp_redirect(home_url('/inscription-artiste-step5'));
            exit;
        } else {
            wp_redirect(home_url('/inscription-artiste-step4?key=' . urlencode($registration_key) . '&error=1'));
            exit;
        }
    } else {
        wp_redirect(home_url('/inscription-artiste-step1?error=1'));
        exit;
    }
}
add_action('admin_post_bebeats_register_artiste_step4', 'bebeats_handle_register_artiste_step4');
add_action('admin_post_nopriv_bebeats_register_artiste_step4', 'bebeats_handle_register_artiste_step4');

/**
 * Traitement de la mise à jour du profil
 */
function bebeats_handle_update_profile() {
    // Vérifier que l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_redirect(home_url('/auth-start'));
        exit;
    }
    
    // Vérifier le nonce
    if (!isset($_POST['bebeats_update_profile_nonce']) || !wp_verify_nonce($_POST['bebeats_update_profile_nonce'], 'bebeats_update_profile_action')) {
        wp_redirect(home_url('/reglages?error=1'));
        exit;
    }
    
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // Récupérer la description
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
    
    // Gérer l'upload de la photo de profil
    if (!empty($_FILES['profile_photo']['name'])) {
        $upload = bebeats_handle_validated_upload($_FILES['profile_photo'], bebeats_allowed_image_mimes());
        if ($upload !== null) {
            update_user_meta($user_id, 'bebeats_profile_photo', $upload['url']);
        }
    }
    
    // Gérer l'upload de la bannière
    if (!empty($_FILES['banner']['name'])) {
        $upload = bebeats_handle_validated_upload($_FILES['banner'], bebeats_allowed_image_mimes());
        if ($upload !== null) {
            update_user_meta($user_id, 'bebeats_banner', $upload['url']);
        }
    }
    
    // Mettre à jour la description
    if (!empty($description)) {
        update_user_meta($user_id, 'description', $description);
    }
    
    // Rediriger vers les réglages avec un message de succès
    wp_redirect(home_url('/reglages?success=1'));
    exit;
}
add_action('admin_post_bebeats_update_profile', 'bebeats_handle_update_profile');

/**
 * Traitement de la déconnexion
 */
function bebeats_handle_logout() {
    // Vérifier le nonce
    if (!isset($_POST['bebeats_logout_nonce']) || !wp_verify_nonce($_POST['bebeats_logout_nonce'], 'bebeats_logout_action')) {
        wp_redirect(home_url('/reglages?error=1'));
        exit;
    }
    
    // Déconnecter l'utilisateur
    wp_logout();
    
    // Rediriger vers la page d'accueil
    wp_redirect(home_url('/'));
    exit;
}
add_action('admin_post_bebeats_logout', 'bebeats_handle_logout');

/**
 * Traitement de la création d'un post
 */
function bebeats_handle_create_post() {
    // Vérifier que l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_redirect(home_url('/auth-start'));
        exit;
    }
    
    // Vérifier le nonce
    if (!isset($_POST['bebeats_create_post_nonce'])) {
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    $nonce_verified = wp_verify_nonce($_POST['bebeats_create_post_nonce'], 'bebeats_create_post_action');
    if (!$nonce_verified) {
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // Récupérer les données
    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
    
    // Valider le type de post
    $allowed_types = array('fan-art', 'audio', 'post', 'event');
    if (!in_array($post_type, $allowed_types)) {
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    // Vérifier que seuls les admins et artistes peuvent créer des posts "event"
    if ($post_type === 'event') {
        $user_type_meta = get_user_meta($current_user->ID, 'bebeats_user_type', true);
        $can_create_event = ($user_type_meta === 'artiste' || current_user_can('administrator'));
        
        if (!$can_create_event) {
            wp_redirect(home_url('/contribuer?error=1'));
            exit;
        }
    }
    
    // Récupérer le contenu selon le type de post
    $content = '';
    $event_title = '';
    if ($post_type === 'post' || $post_type === 'event') {
        $content = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';
        // Pour les évènements, on peut avoir un titre d'artiste dédié
        if ($post_type === 'event') {
            $event_title = isset($_POST['event_title']) ? sanitize_text_field($_POST['event_title']) : '';
        }
    } elseif ($post_type === 'fan-art') {
        // Pour fan-art, le contenu peut venir de 'content' (description)
        $content = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';
    } elseif ($post_type === 'audio') {
        // Pour audio, le contenu peut venir de 'content' (description)
        $content = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';
    }
    
    $media_type = '';
    $media_url = '';
    
    // Gérer l'upload du média selon le type
    if (($post_type === 'fan-art' || $post_type === 'event') && !empty($_FILES['media']['name'])) {
        $upload = bebeats_handle_validated_upload($_FILES['media'], bebeats_allowed_media_mimes());
        if ($upload !== null) {
            $media_url = $upload['url'];
            // Déterminer le type de média
            $file_type = wp_check_filetype($upload['file']);
            if (strpos($file_type['type'], 'image') !== false) {
                $media_type = 'image';
            } elseif (strpos($file_type['type'], 'video') !== false) {
                $media_type = 'video';
            } else {
                $media_type = 'image'; // Par défaut
            }
        }
    } elseif ($post_type === 'audio' && !empty($_FILES['media']['name'])) {
        $upload = bebeats_handle_validated_upload($_FILES['media'], bebeats_allowed_audio_mimes());
        if ($upload !== null) {
            $media_url = $upload['url'];
            $media_type = 'audio';
        }
    }
    
    // Validation : au moins du contenu, un média, ou un titre d'évènement
    if (empty($content) && empty($media_url) && empty($event_title)) {
        wp_redirect(home_url('/contribuer?error=2'));
        exit;
    }
    
    // Récupérer les options
    $allow_comments = isset($_POST['allow_comments']) ? intval($_POST['allow_comments']) : 1;
    $allow_repost = isset($_POST['allow_repost']) ? intval($_POST['allow_repost']) : 1;
    $show_likes = isset($_POST['show_likes']) ? intval($_POST['show_likes']) : 1;
    
    // Personnes identifiées sur le post (max 10, utilisateurs valides, pas soi-même)
    $tagged_users = array();
    if (isset($_POST['tagged_users'])) {
        $raw = is_array($_POST['tagged_users']) ? $_POST['tagged_users'] : explode(',', $_POST['tagged_users']);
        $tagged_users = array_map('intval', $raw);
        $tagged_users = array_filter(array_unique($tagged_users));
        $tagged_users = array_slice($tagged_users, 0, 10);
        
        foreach ($tagged_users as $tagged_id) {
            if (!get_userdata($tagged_id) || (int) $tagged_id === (int) $user_id) {
                $tagged_users = array_diff($tagged_users, array($tagged_id));
            }
        }
    }
    $tagged_users_str = !empty($tagged_users) ? implode(',', $tagged_users) : '';
    
    // Insérer le post dans la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_posts';
    
    // Vérifier que la table existe
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if (!$table_exists) {
        // Créer la table si elle n'existe pas
        bebeats_create_posts_table();
    }
    
    // Préparer les données pour l'insertion
    $insert_data = array(
        'user_id' => $user_id,
        'post_type' => $post_type,
        'content' => $content,
        'media_url' => $media_url,
        'media_type' => $media_type,
        'allow_comments' => $allow_comments,
        'allow_repost' => $allow_repost,
        'show_likes' => $show_likes,
        'tagged_users' => $tagged_users_str
    );

    $insert_formats = array('%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s');

    // Enregistrer le titre d'évènement séparément si fourni
    if (!empty($event_title)) {
        $insert_data['event_title'] = $event_title;
        $insert_formats[] = '%s';
    }
    
    
    $result = $wpdb->insert(
        $table_name,
        $insert_data,
        $insert_formats
    );
    
    if ($result === false) {
        wp_redirect(home_url('/contribuer?error=1'));
        exit;
    }
    
    // Notifier les followers si l'auteur est un artiste
    if (get_user_meta($user_id, 'bebeats_user_type', true) === 'artiste') {
        bebeats_notify_followers($user_id, $wpdb->insert_id);
    }
    
    // Gamification : +35 points par publication
    bebeats_award_points($user_id, 35);
    
    // Notifier les personnes identifiées sur le post
    if (!empty($tagged_users)) {
        global $wpdb;
        $notif_table = $wpdb->prefix . 'bebeats_notifications';
        $author = get_userdata($user_id);
        $author_name = $author ? ($author->display_name ?: $author->user_login) : '';
        
        foreach ($tagged_users as $tagged_id) {
            $wpdb->insert($notif_table, array(
                'user_id' => $tagged_id,
                'actor_id' => $user_id,
                'type' => 'tagged',
                'post_id' => $wpdb->insert_id
            ), array('%d', '%d', '%s', '%d'));
        }
    }
    
    // Rediriger vers la page contribuer avec un message de succès
    wp_redirect(home_url('/contribuer?success=1'));
    exit;
}
add_action('admin_post_bebeats_create_post', 'bebeats_handle_create_post');
add_action('admin_post_nopriv_bebeats_create_post', 'bebeats_handle_create_post');

/**
 * Traitement des réactions aux posts (like, commentaire, republier, favoris)
 */
function bebeats_handle_post_reaction() {
    // Vérifier que l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Vous devez être connecté'));
        exit;
    }
    
    // Vérifier le nonce
    if (!isset($_POST['bebeats_reaction_nonce']) || !wp_verify_nonce($_POST['bebeats_reaction_nonce'], 'bebeats_reaction_action')) {
        wp_send_json_error(array('message' => 'Erreur de sécurité'));
        exit;
    }
    
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $reaction_type = isset($_POST['reaction_type']) ? sanitize_text_field($_POST['reaction_type']) : '';
    $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : 'toggle'; // toggle, add, remove
    $comment_text = isset($_POST['comment_text']) ? sanitize_textarea_field($_POST['comment_text']) : '';
    $parent_comment_id = isset($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : 0;
    
    if (empty($post_id) || empty($reaction_type)) {
        wp_send_json_error(array('message' => 'Données manquantes'));
        exit;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'bebeats_post_reactions';
    
    // Vérifier que la table existe
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
    if (!$table_exists) {
        bebeats_create_post_reactions_table();
    }
    
    // Pour les likes, republier et favoris : toggle (ajouter ou supprimer)
    // IMPORTANT: Un utilisateur ne peut avoir qu'UNE SEULE réaction de chaque type par post
    if (in_array($reaction_type, array('like', 'repost', 'favorite')) && $action === 'toggle') {
        // Vérifier si la réaction existe déjà (sans parent_comment_id pour ces types)
        // Utiliser COALESCE pour gérer NULL et 0 de manière uniforme
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND COALESCE(parent_comment_id, 0) = 0",
            $post_id, $user_id, $reaction_type
        ));
        
        if ($existing) {
            // Supprimer la réaction existante
            $deleted = $wpdb->delete(
                $table_name,
                array('id' => $existing->id),
                array('%d')
            );
            
            if ($deleted !== false) {
                // Récupérer le nouveau compteur
                $new_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                    $post_id, $reaction_type
                ));
                wp_send_json_success(array(
                    'action' => 'removed', 
                    'reaction_type' => $reaction_type,
                    'count' => $new_count
                ));
            } else {
                // Même en cas d'erreur, retourner le compteur actuel
                $current_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                    $post_id, $reaction_type
                ));
                wp_send_json_error(array(
                    'message' => 'Erreur lors de la suppression',
                    'count' => $current_count
                ));
            }
        } else {
            // Ajouter la réaction (un utilisateur ne peut avoir qu'une seule réaction de ce type)
            // Utiliser 0 au lieu de NULL pour parent_comment_id pour éviter les problèmes
            // Vérifier une dernière fois qu'elle n'existe pas (race condition)
            $double_check = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND COALESCE(parent_comment_id, 0) = 0",
                $post_id, $user_id, $reaction_type
            ));
            
            if ($double_check > 0) {
                // La réaction existe déjà, la supprimer (toggle)
                $existing = $wpdb->get_row($wpdb->prepare(
                    "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND COALESCE(parent_comment_id, 0) = 0",
                    $post_id, $user_id, $reaction_type
                ));
                
                if ($existing) {
                    $deleted = $wpdb->delete(
                        $table_name,
                        array('id' => $existing->id),
                        array('%d')
                    );
                    
                    if ($deleted !== false) {
                        $new_count = (int) $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                            $post_id, $reaction_type
                        ));
                        wp_send_json_success(array(
                            'action' => 'removed', 
                            'reaction_type' => $reaction_type,
                            'count' => $new_count
                        ));
                        return;
                    }
                }
            }
            
            // Insérer la nouvelle réaction
            $inserted = $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'reaction_type' => $reaction_type,
                    'parent_comment_id' => 0
                ),
                array('%d', '%d', '%s', '%d')
            );
            
            if ($inserted !== false) {
                // Gamification : +10 points pour un like donne,
                // double (+20) si le post vient d'un artiste suivi
                if ($reaction_type === 'like') {
                    $like_points = 10;
                    if (bebeats_user_follows_author($user_id, $post_id)) {
                        $like_points *= 2;
                    }
                    bebeats_award_points($user_id, $like_points);
                }
                
                // Récupérer le nouveau compteur
                $new_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                    $post_id, $reaction_type
                ));
                wp_send_json_success(array(
                    'action' => 'added', 
                    'reaction_type' => $reaction_type,
                    'count' => $new_count
                ));
            } else {
                // Si l'insertion échoue, vérifier l'erreur et réessayer avec une vérification
                $error_message = $wpdb->last_error;
                
                // Vérifier à nouveau si la réaction existe (au cas où elle aurait été créée entre-temps)
                $existing_check = $wpdb->get_row($wpdb->prepare(
                    "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND COALESCE(parent_comment_id, 0) = 0",
                    $post_id, $user_id, $reaction_type
                ));
                
                if ($existing_check) {
                    // La réaction existe déjà, la supprimer (toggle)
                    $deleted = $wpdb->delete(
                        $table_name,
                        array('id' => $existing_check->id),
                        array('%d')
                    );
                    
                    if ($deleted !== false) {
                        $new_count = (int) $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                            $post_id, $reaction_type
                        ));
                        wp_send_json_success(array(
                            'action' => 'removed', 
                            'reaction_type' => $reaction_type,
                            'count' => $new_count
                        ));
                    } else {
                        // Récupérer le compteur actuel même en cas d'erreur
                        $current_count = (int) $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                            $post_id, $reaction_type
                        ));
                        wp_send_json_error(array(
                            'message' => 'Erreur lors de la suppression: ' . $wpdb->last_error,
                            'count' => $current_count
                        ));
                    }
                } else {
                    // Autre erreur - retourner le compteur actuel et le message d'erreur détaillé
                    $current_count = (int) $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE post_id = %d AND reaction_type = %s",
                        $post_id, $reaction_type
                    ));
                    wp_send_json_error(array(
                        'message' => 'Erreur lors de l\'ajout de la réaction: ' . ($error_message ?: 'Erreur inconnue'),
                        'count' => $current_count,
                        'debug' => array(
                            'post_id' => $post_id,
                            'user_id' => $user_id,
                            'reaction_type' => $reaction_type,
                            'last_error' => $wpdb->last_error
                        )
                    ));
                }
            }
        }
    }
    // Pour les commentaires : toujours ajouter (un utilisateur peut publier plusieurs commentaires)
    elseif ($reaction_type === 'comment' && !empty($comment_text)) {
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'user_id' => $user_id,
                'reaction_type' => 'comment',
                'comment_text' => $comment_text,
                'parent_comment_id' => $parent_comment_id > 0 ? $parent_comment_id : null
            ),
            array('%d', '%d', '%s', '%s', '%d')
        );
        
        if ($inserted !== false) {
            $comment_id = $wpdb->insert_id;
            
            // Récupérer les données complètes du commentaire créé
            $comment = $wpdb->get_row($wpdb->prepare(
                "SELECT r.*, u.display_name, u.user_login,
                        pm1.meta_value as profile_photo
                 FROM $table_name r
                 LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
                 LEFT JOIN {$wpdb->usermeta} pm1 ON u.ID = pm1.user_id AND pm1.meta_key = 'bebeats_profile_photo'
                 WHERE r.id = %d",
                $comment_id
            ));
            
            // Récupérer le nouveau compteur de commentaires
            $new_count = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE post_id = %d AND reaction_type = 'comment' AND (parent_comment_id IS NULL OR parent_comment_id = 0)",
                $post_id
            ));
            
            // Préparer les données du commentaire pour le retour
            $comment_data = null;
            if ($comment) {
                $comment_profile_photo = !empty($comment->profile_photo) ? $comment->profile_photo : get_avatar_url($comment->user_id, array('size' => 40));
                $comment_time_ago = human_time_diff(strtotime($comment->created_at), current_time('timestamp'));
                $comment_timestamp = strtotime($comment->created_at);
                
                $comment_data = array(
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'display_name' => $comment->display_name ?: $comment->user_login,
                    'user_login' => $comment->user_login,
                    'profile_photo' => $comment_profile_photo,
                    'comment_text' => $comment->comment_text,
                    'time_ago' => $comment_time_ago,
                    'timestamp' => $comment_timestamp,
                    'likes_count' => 0
                );
            }
            
            // Gamification : +25 points par commentaire publie,
            // double (+50) si le post vient d'un artiste suivi
            $comment_points = 25;
            if (bebeats_user_follows_author($user_id, $post_id)) {
                $comment_points *= 2;
            }
            bebeats_award_points($user_id, $comment_points);
            
            wp_send_json_success(array(
                'action' => 'added', 
                'reaction_type' => 'comment',
                'count' => $new_count,
                'comment' => $comment_data
            ));
        } else {
            wp_send_json_error(array('message' => 'Erreur lors de l\'ajout du commentaire'));
        }
    }
    // Pour liker un commentaire : un utilisateur ne peut liker qu'une fois chaque commentaire
    elseif ($reaction_type === 'comment_like' && $parent_comment_id > 0) {
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE post_id = %d AND user_id = %d AND reaction_type = %s AND parent_comment_id = %d",
            $post_id, $user_id, 'comment_like', $parent_comment_id
        ));
        
        if ($existing) {
            $deleted = $wpdb->delete(
                $table_name,
                array('id' => $existing->id),
                array('%d')
            );
            
            if ($deleted !== false) {
                // Récupérer le nouveau compteur
                $new_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE parent_comment_id = %d AND reaction_type = 'comment_like'",
                    $parent_comment_id
                ));
                wp_send_json_success(array(
                    'action' => 'removed', 
                    'reaction_type' => 'comment_like',
                    'count' => $new_count
                ));
            } else {
                wp_send_json_error(array('message' => 'Erreur lors de la suppression'));
            }
        } else {
            $inserted = $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'reaction_type' => 'comment_like',
                    'parent_comment_id' => $parent_comment_id
                ),
                array('%d', '%d', '%s', '%d')
            );
            
            if ($inserted !== false) {
                // Récupérer le nouveau compteur
                $new_count = (int) $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE parent_comment_id = %d AND reaction_type = 'comment_like'",
                    $parent_comment_id
                ));
                wp_send_json_success(array(
                    'action' => 'added', 
                    'reaction_type' => 'comment_like',
                    'count' => $new_count
                ));
            } else {
                wp_send_json_error(array('message' => 'Erreur lors de l\'ajout du like'));
            }
        }
    } else {
        wp_send_json_error(array('message' => 'Action invalide'));
    }
}
add_action('wp_ajax_bebeats_post_reaction', 'bebeats_handle_post_reaction');
add_action('wp_ajax_nopriv_bebeats_post_reaction', 'bebeats_handle_post_reaction');

// Fonction pour rechercher des utilisateurs (autocomplete)
function bebeats_search_users() {
    check_ajax_referer('bebeats_search_users_action', 'nonce');
    
    $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    if (strlen($search_term) < 2) {
        wp_send_json_success(array('users' => array()));
    }
    
    $users = get_users(array(
        'search' => '*' . $search_term . '*',
        'search_columns' => array('user_login', 'user_nicename', 'display_name'),
        'number' => 10
    ));
    
    $results = array();
    foreach ($users as $user) {
        $profile_photo = get_user_meta($user->ID, 'bebeats_profile_photo', true);
        if (empty($profile_photo)) {
            $profile_photo = get_avatar_url($user->ID, array('size' => 50));
        }
        
        $results[] = array(
            'id' => $user->ID,
            'username' => $user->user_login,
            'display_name' => $user->display_name ?: $user->user_login,
            'profile_photo' => $profile_photo
        );
    }
    
    wp_send_json_success(array('users' => $results));
}
add_action('wp_ajax_bebeats_search_users', 'bebeats_search_users');
add_action('wp_ajax_nopriv_bebeats_search_users', 'bebeats_search_users');

/**
 * Fonction AJAX pour récupérer un post aléatoire selon la catégorie (Roll)
 */
function bebeats_roll_random_post() {
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    
    if (empty($category)) {
        wp_send_json_error(array('message' => 'Catégorie manquante'));
        exit;
    }
    
    global $wpdb;
    $posts_table = $wpdb->prefix . 'bebeats_posts';
    $users_table = $wpdb->users;
    
    $post = null;
    
    // Déterminer le post_type et les conditions selon la catégorie
    switch ($category) {
        case 'Musique':
            // Récupérer un audio d'un utilisateur de type "artiste"
            $query = "
                SELECT p.*, u.ID as user_id, u.display_name, u.user_login
                FROM $posts_table p
                INNER JOIN $users_table u ON p.user_id = u.ID
                INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
                WHERE p.post_type = 'audio'
                AND p.media_type = 'audio'
                AND um.meta_key = 'bebeats_user_type'
                AND um.meta_value = 'artiste'
                ORDER BY RAND()
                LIMIT 1
            ";
            $post = $wpdb->get_row($query);
            break;
            
        case 'Fan Arts':
            // Récupérer un fan-art
            $query = "
                SELECT p.*, u.ID as user_id, u.display_name, u.user_login
                FROM $posts_table p
                INNER JOIN $users_table u ON p.user_id = u.ID
                WHERE p.post_type = 'fan-art'
                ORDER BY RAND()
                LIMIT 1
            ";
            $post = $wpdb->get_row($query);
            break;
            
        case 'Evènement':
        case 'Événement':
            // Récupérer un événement (post_type = 'event')
            $query = "
                SELECT p.*, u.ID as user_id, u.display_name, u.user_login
                FROM $posts_table p
                INNER JOIN $users_table u ON p.user_id = u.ID
                WHERE p.post_type = 'event'
                ORDER BY RAND()
                LIMIT 1
            ";
            $post = $wpdb->get_row($query);
            break;
            
        case 'Article':
            // Récupérer un article (post_type = 'post')
            $query = "
                SELECT p.*, u.ID as user_id, u.display_name, u.user_login
                FROM $posts_table p
                INNER JOIN $users_table u ON p.user_id = u.ID
                WHERE p.post_type = 'post'
                ORDER BY RAND()
                LIMIT 1
            ";
            $post = $wpdb->get_row($query);
            break;
            
        default:
            wp_send_json_error(array('message' => 'Catégorie invalide'));
            exit;
    }
    
    if (!$post) {
        wp_send_json_error(array('message' => 'Aucun contenu trouvé pour cette catégorie'));
        exit;
    }
    
    // Récupérer les informations de l'utilisateur
    $user_id = $post->user_id;
    $profile_photo = get_user_meta($user_id, 'bebeats_profile_photo', true);
    if (empty($profile_photo)) {
        $profile_photo = get_avatar_url($user_id, array('size' => 60));
    }
    
    // Récupérer les statistiques du post
    $reactions_table = $wpdb->prefix . 'bebeats_post_reactions';
    $likes_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'like'",
        $post->id
    ));
    $comments_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'comment'",
        $post->id
    ));
    $reposts_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'repost'",
        $post->id
    ));
    $favorites_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $reactions_table WHERE post_id = %d AND reaction_type = 'favorite'",
        $post->id
    ));
    
    // Formater la date
    $created_at = new DateTime($post->created_at);
    $now = new DateTime();
    $diff = $now->diff($created_at);
    
    $time_ago = '';
    if ($diff->y > 0) {
        $time_ago = $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
    } elseif ($diff->m > 0) {
        $time_ago = $diff->m . ' mois';
    } elseif ($diff->d > 0) {
        $time_ago = $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
    } elseif ($diff->h > 0) {
        $time_ago = $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
    } elseif ($diff->i > 0) {
        $time_ago = $diff->i . ' min';
    } else {
        $time_ago = 'À l\'instant';
    }
    
    $result = array(
        'id' => $post->id,
        'user_id' => $user_id,
        'username' => $post->display_name ?: $post->user_login,
        'profile_photo' => $profile_photo,
        'content' => $post->content,
        'media_url' => $post->media_url,
        'media_type' => $post->media_type,
        'post_type' => $post->post_type,
        'time_ago' => $time_ago,
        'likes' => intval($likes_count),
        'comments' => intval($comments_count),
        'reposts' => intval($reposts_count),
        'favorites' => intval($favorites_count)
    );
    
    wp_send_json_success($result);
}
add_action('wp_ajax_bebeats_roll_random_post', 'bebeats_roll_random_post');
add_action('wp_ajax_nopriv_bebeats_roll_random_post', 'bebeats_roll_random_post');

