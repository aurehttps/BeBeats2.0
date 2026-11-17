<?php
/**
 * Template Name: Profil Page
 * Template pour la page de profil utilisateur
 */

// Rediriger vers la connexion si l'utilisateur n'est pas connecté
if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth-start'));
    exit;
}

get_header(); 

// Passer le timestamp actuel du serveur à JavaScript pour synchronisation
$server_timestamp = current_time('timestamp');
$current_user = wp_get_current_user();
$current_user_id = is_user_logged_in() ? get_current_user_id() : 0;

// Profil consulté : l'utilisateur courant par défaut, ?user=ID pour un autre profil
$viewed_user_id = $current_user_id;
if (isset($_GET['user'])) {
    $requested_id = intval($_GET['user']);
    if ($requested_id > 0 && get_userdata($requested_id)) {
        $viewed_user_id = $requested_id;
    }
}
$is_own_profile = ($viewed_user_id === $current_user_id);

$profile_user = get_userdata($viewed_user_id);
$user_type = get_user_meta($viewed_user_id, 'bebeats_user_type', true);
$profile_photo = get_user_meta($viewed_user_id, 'bebeats_profile_photo', true);
$banner = get_user_meta($viewed_user_id, 'bebeats_banner', true);
$description = get_user_meta($viewed_user_id, 'description', true);
$genres = get_user_meta($viewed_user_id, 'bebeats_genres', true);
$sounds = get_user_meta($viewed_user_id, 'bebeats_sounds', true);

// Si pas de photo de profil, utiliser l'avatar WordPress par défaut
if (empty($profile_photo)) {
    $profile_photo = get_avatar_url($viewed_user_id, array('size' => 200));
}

// Titre epingle au-dessus de la photo (sans le niveau),
// sinon type de compte par defaut
$badge = ($user_type === 'artiste') ? 'Artiste' : 'Fan';
$selected_badge_key = get_user_meta($viewed_user_id, 'bebeats_selected_badge', true);
if (!empty($selected_badge_key)) {
    $title_catalog = bebeats_get_title_catalog();
    if (isset($title_catalog[$selected_badge_key])) {
        $badge = $title_catalog[$selected_badge_key]['title'];
    }
}

// Niveau gamification de l'utilisateur consulte (affiche a cote du pseudo)
$viewed_user_level = bebeats_get_level_from_points(bebeats_get_user_points($viewed_user_id));

// Suivi : compteurs et état
global $wpdb;
$follows_table = $wpdb->prefix . 'bebeats_follows';
$posts_table = $wpdb->prefix . 'bebeats_posts';
$reactions_table = $wpdb->prefix . 'bebeats_post_reactions';

$followers_count = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $follows_table WHERE artist_id = %d",
    $viewed_user_id
));
$following_count = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $follows_table WHERE follower_id = %d",
    $viewed_user_id
));
$posts_count = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $posts_table WHERE user_id = %d",
    $viewed_user_id
));

$is_following = false;
$is_notify_on = false;
if (!$is_own_profile && $user_type === 'artiste') {
    $follow_row = $wpdb->get_row($wpdb->prepare(
        "SELECT notify FROM $follows_table WHERE artist_id = %d AND follower_id = %d",
        $viewed_user_id, $current_user_id
    ));
    if ($follow_row) {
        $is_following = true;
        $is_notify_on = (bool) $follow_row->notify;
    }
}

// Formater les genres pour l'affichage
$genres_display = '';
if (!empty($genres) && is_array($genres)) {
    $genres_display = implode(', ', array_map('ucfirst', $genres));
}
?>

<main class="profile-page">
    <div class="profile-wrapper">
        <!-- Bannière de profil avec photo et infos -->
        <div class="profile-banner">
            <?php if (!empty($banner)): ?>
                <img src="<?php echo esc_url($banner); ?>" alt="Bannière de profil" class="banner-image">
            <?php endif; ?>
            
            <!-- Photo de profil en bas à droite -->
            <div class="profile-photo-container">
                <img src="<?php echo esc_url($profile_photo); ?>" alt="<?php echo esc_attr($profile_user->display_name); ?>" class="profile-photo">
            </div>
            
            <!-- Titre épinglé (ou type de compte) au-dessus de la photo -->
            <div class="profile-badge-title"><?php echo esc_html($badge); ?></div>
            
            <!-- Colonne d'infos utilisateur : pseudo, stats, actions, description -->
            <div class="profile-user-info">
                <h1 class="profile-username">
                    <?php echo esc_html($profile_user->display_name ?: $profile_user->user_login); ?>
                    <span class="profile-level-badge">Niveau <?php echo esc_html($viewed_user_level); ?></span>
                </h1>
                
                <div class="profile-stats-row">
                    <span class="profile-stat">
                        <span class="profile-stat-value"><?php echo esc_html($followers_count); ?></span>
                        <span class="profile-stat-label">abonné<?php echo $followers_count > 1 ? 's' : ''; ?></span>
                    </span>
                    <span class="profile-stat">
                        <span class="profile-stat-value"><?php echo esc_html($following_count); ?></span>
                        <span class="profile-stat-label">abonnement<?php echo $following_count > 1 ? 's' : ''; ?></span>
                    </span>
                    <span class="profile-stat">
                        <span class="profile-stat-value"><?php echo esc_html($posts_count); ?></span>
                        <span class="profile-stat-label">publication<?php echo $posts_count > 1 ? 's' : ''; ?></span>
                    </span>
                </div>
                
                <?php if (!$is_own_profile && $user_type === 'artiste'): ?>
                    <div class="profile-actions-row">
                        <button class="bebeats-follow-btn <?php echo $is_following ? 'is-following' : ''; ?>" data-artist-id="<?php echo esc_attr($viewed_user_id); ?>" type="button">
                            <span class="follow-label"><?php echo $is_following ? 'Suivi' : 'Suivre'; ?></span>
                        </button>
                        <button class="bebeats-notify-btn <?php echo $is_following ? '' : 'is-hidden'; ?> <?php echo $is_notify_on ? 'is-active' : ''; ?>" data-artist-id="<?php echo esc_attr($viewed_user_id); ?>" type="button" aria-label="Activer ou désactiver les notifications">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($description)): ?>
                    <p class="profile-description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="profile-main">

            <!-- Grille unifiee des publications (tous types, ordre chronologique) -->
            <?php
            // Recuperer TOUS les posts de l'utilisateur, tous types confondus
            $user_posts = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $posts_table WHERE user_id = %d ORDER BY created_at DESC LIMIT 100",
                $viewed_user_id
            ));
            
            $stats_map = bebeats_get_reactions_stats(wp_list_pluck($user_posts, 'id'), $current_user_id);
            
            if (!empty($user_posts)): ?>
                <div class="profile-section glassmorphism profile-posts-section">
                    <div class="profile-posts-header">
                        <h2 class="section-title"><?php echo $is_own_profile ? 'Mes publications' : 'Publications'; ?></h2>
                        <div class="profile-view-toggle" role="group" aria-label="Mode d'affichage">
                            <button type="button" class="profile-view-btn is-active" id="view-grid-btn" aria-pressed="true">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm1 5h14M9 4v16m6-16v16M5 15h14"></path>
                                </svg>
                                Grille
                            </button>
                            <button type="button" class="profile-view-btn" id="view-detail-btn" aria-pressed="false">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                Detail
                            </button>
                        </div>
                    </div>
                    
                    <div class="profile-unified-grid" id="profile-unified-grid">
                        <?php foreach ($user_posts as $post): 
                            $stats = isset($stats_map[$post->id]) ? $stats_map[$post->id] : array('likes_count' => 0, 'reposts_count' => 0, 'comments_count' => 0, 'user_liked' => false, 'user_favorited' => false, 'user_reposted' => false);
                            
                            $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
                            $post_timestamp = strtotime($post->created_at);
                            
                            // Commentaires du post (affiches en vue detaillee)
                            $comments = $wpdb->get_results($wpdb->prepare(
                                "SELECT r.*, u.display_name, u.user_login,
                                        pm1.meta_value as profile_photo
                                 FROM $reactions_table r
                                 LEFT JOIN {$wpdb->users} u ON r.user_id = u.ID
                                 LEFT JOIN {$wpdb->usermeta} pm1 ON u.ID = pm1.user_id AND pm1.meta_key = 'bebeats_profile_photo'
                                 WHERE r.post_id = %d AND r.reaction_type = 'comment' AND r.parent_comment_id IS NULL
                                 ORDER BY r.created_at ASC
                                 LIMIT 20",
                                $post->id
                            ));
                        ?>
                        <article class="profile-grid-card" data-post-id="<?php echo esc_attr($post->id); ?>">
                            <!-- Vue compacte (grille 4 colonnes) -->
                            <div class="profile-card-compact">
                                <?php if (!empty($post->media_url)): ?>
                                    <div class="profile-card-media">
                                        <?php if ($post->media_type === 'image'): ?>
                                            <img src="<?php echo esc_url($post->media_url); ?>" alt="Apercu du contenu" loading="lazy">
                                        <?php elseif ($post->media_type === 'video'): ?>
                                            <video src="<?php echo esc_url($post->media_url); ?>" muted preload="metadata"></video>
                                            <span class="profile-card-media-badge">Video</span>
                                        <?php elseif ($post->media_type === 'audio'): ?>
                                            <div class="profile-card-audio-placeholder">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"></path>
                                                </svg>
                                                <span>Audio</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="profile-card-media profile-card-media-text">
                                        <p><?php echo esc_html(wp_trim_words($post->content, 20, '...')); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="profile-card-meta">
                                    <span class="profile-card-type"><?php echo esc_html(ucfirst(str_replace('-', ' ', $post->post_type))); ?></span>
                                    <span class="profile-card-time" data-timestamp="<?php echo esc_attr($post_timestamp); ?>">Il y a <?php echo esc_html($time_ago); ?></span>
                                </div>
                            </div>
                            
                            <!-- Vue detaillee (1 colonne) -->
                            <div class="profile-card-full">
                                <div class="profile-card-full-header">
                                    <span class="profile-card-type"><?php echo esc_html(ucfirst(str_replace('-', ' ', $post->post_type))); ?></span>
                                    <span class="profile-card-time" data-timestamp="<?php echo esc_attr($post_timestamp); ?>">Il y a <?php echo esc_html($time_ago); ?></span>
                                </div>
                                
                                <?php if (!empty($post->content)): ?>
                                    <p class="profile-card-full-text"><?php echo nl2br(esc_html($post->content)); ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($post->media_url)): ?>
                                    <div class="profile-card-full-media">
                                        <?php if ($post->media_type === 'image'): ?>
                                            <img src="<?php echo esc_url($post->media_url); ?>" alt="Contenu du post">
                                        <?php elseif ($post->media_type === 'video'): ?>
                                            <video controls>
                                                <source src="<?php echo esc_url($post->media_url); ?>" type="video/mp4">
                                                Votre navigateur ne supporte pas l'element video.
                                            </video>
                                        <?php elseif ($post->media_type === 'audio'): ?>
                                            <audio controls>
                                                <source src="<?php echo esc_url($post->media_url); ?>" type="audio/mpeg">
                                                Votre navigateur ne supporte pas l'element audio.
                                            </audio>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($current_user_id > 0): ?>
                                    <div class="profile-card-actions">
                                        <button class="profile-post-action-btn like-btn <?php echo $stats['user_liked'] ? 'active' : ''; ?>" 
                                                data-post-id="<?php echo esc_attr($post->id); ?>" 
                                                data-reaction-type="like"
                                                onclick="event.preventDefault(); event.stopPropagation(); handleProfileReaction(<?php echo esc_js($post->id); ?>, 'like', this);">
                                            <svg class="profile-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                            <span class="profile-action-count"><?php echo esc_html($stats['likes_count']); ?></span>
                                        </button>
                                        
                                        <button class="profile-post-action-btn comment-btn" 
                                                data-post-id="<?php echo esc_attr($post->id); ?>"
                                                onclick="event.preventDefault(); event.stopPropagation(); window.location.href='<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>';">
                                            <svg class="profile-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            <span class="profile-action-count"><?php echo esc_html($stats['comments_count']); ?></span>
                                        </button>
                                        
                                        <?php if ($post->allow_repost): ?>
                                            <button class="profile-post-action-btn repost-btn <?php echo $stats['user_reposted'] ? 'active' : ''; ?>" 
                                                    data-post-id="<?php echo esc_attr($post->id); ?>" 
                                                    data-reaction-type="repost"
                                                    onclick="event.preventDefault(); event.stopPropagation(); handleProfileReaction(<?php echo esc_js($post->id); ?>, 'repost', this);">
                                                <svg class="profile-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                <span class="profile-action-count"><?php echo esc_html($stats['reposts_count']); ?></span>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button class="profile-post-action-btn favorite-btn <?php echo $stats['user_favorited'] ? 'active' : ''; ?>" 
                                                data-post-id="<?php echo esc_attr($post->id); ?>" 
                                                data-reaction-type="favorite"
                                                onclick="event.preventDefault(); event.stopPropagation(); handleProfileReaction(<?php echo esc_js($post->id); ?>, 'favorite', this);">
                                            <svg class="profile-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($comments)): ?>
                                    <div class="profile-card-comments">
                                        <?php foreach ($comments as $comment): 
                                            $comment_photo = !empty($comment->profile_photo) ? $comment->profile_photo : get_avatar_url($comment->user_id, array('size' => 40));
                                        ?>
                                            <div class="profile-comment">
                                                <img src="<?php echo esc_url($comment_photo); ?>" alt="<?php echo esc_attr($comment->display_name ?: $comment->user_login); ?>" class="profile-comment-avatar">
                                                <div class="profile-comment-body">
                                                    <span class="profile-comment-author"><?php echo esc_html($comment->display_name ?: $comment->user_login); ?></span>
                                                    <p class="profile-comment-text"><?php echo esc_html($comment->comment_text); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($user_posts) >= 100): ?>
                        <p class="profile-posts-limit">Affichage limite aux 100 publications les plus recentes.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Section genres musicaux -->
            <?php if (!empty($genres_display)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Genres musicaux</h2>
                    <div class="genres-list">
                        <?php 
                        $genres_array = is_array($genres) ? $genres : explode(', ', $genres_display);
                        foreach ($genres_array as $genre): 
                        ?>
                            <span class="genre-tag"><?php echo esc_html(ucfirst(trim($genre))); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Section sons (pour les artistes) -->
            <?php if ($user_type === 'artiste' && !empty($sounds) && is_array($sounds)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Mes sons</h2>
                    <div class="sounds-grid">
                        <?php foreach ($sounds as $sound): ?>
                            <div class="sound-card">
                                <div class="sound-icon">🎵</div>
                                <div class="sound-info">
                                    <p class="sound-name"><?php echo esc_html(basename($sound)); ?></p>
                                    <audio controls class="sound-player">
                                        <source src="<?php echo esc_url($sound); ?>" type="audio/mpeg">
                                        Votre navigateur ne supporte pas l'élément audio.
                                    </audio>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    // Passer le timestamp du serveur pour synchronisation
    window.bebeatsServerTime = <?php echo $server_timestamp; ?>;
    window.bebeatsClientTime = Math.floor(Date.now() / 1000);
    window.bebeatsTimeOffset = window.bebeatsServerTime - window.bebeatsClientTime;
</script>

<?php get_footer(); ?>
