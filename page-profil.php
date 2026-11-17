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

// Déterminer le badge selon le type d'utilisateur,
// complete par le titre gamification selectionne le cas echeant
$badge = ($user_type === 'artiste') ? 'Artiste' : 'Fan';
$selected_badge_key = get_user_meta($viewed_user_id, 'bebeats_selected_badge', true);
if (!empty($selected_badge_key)) {
    $badge_catalog = bebeats_get_badge_catalog();
    if (isset($badge_catalog[$selected_badge_key])) {
        $badge = $badge_catalog[$selected_badge_key]['title'];
    }
}

// Suivi : compteurs et état
global $wpdb;
$follows_table = $wpdb->prefix . 'bebeats_follows';
$posts_table = $wpdb->prefix . 'bebeats_posts';

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
            
            <!-- Colonne d'infos utilisateur : pseudo, stats, actions, description -->
            <div class="profile-user-info">
                <h1 class="profile-username"><?php echo esc_html($profile_user->display_name ?: $profile_user->user_login); ?></h1>
                
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

            <!-- Section Posts de l'utilisateur -->
            <?php
            global $wpdb;
            $posts_table = $wpdb->prefix . 'bebeats_posts';
            $reactions_table = $wpdb->prefix . 'bebeats_post_reactions';
            
            // Récupérer les posts de l'utilisateur
            $user_posts = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $posts_table WHERE user_id = %d ORDER BY created_at DESC LIMIT 20",
                $viewed_user_id
            ));
            
            $stats_map = bebeats_get_reactions_stats(wp_list_pluck($user_posts, 'id'), $current_user_id);
            
            if (!empty($user_posts)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title"><?php echo $is_own_profile ? 'Mes publications' : 'Publications'; ?></h2>
                    <div class="profile-posts-list">
                        <?php foreach ($user_posts as $post): 
                            $stats = isset($stats_map[$post->id]) ? $stats_map[$post->id] : array('likes_count' => 0, 'reposts_count' => 0, 'comments_count' => 0, 'user_liked' => false, 'user_favorited' => false, 'user_reposted' => false);
                            $likes_count = $stats['likes_count'];
                            $comments_count = $stats['comments_count'];
                            $reposts_count = $stats['reposts_count'];
                            $user_liked = $stats['user_liked'];
                            $user_favorited = $stats['user_favorited'];
                            $user_reposted = $stats['user_reposted'];
                            
                            $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
                            $post_timestamp = strtotime($post->created_at);
                        ?>
                            <a href="<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>" class="profile-post-link">
                                <article class="profile-post-item">
                                    <div class="profile-post-header">
                                        <span class="profile-post-time" data-timestamp="<?php echo esc_attr($post_timestamp); ?>">Il y a <?php echo esc_html($time_ago); ?></span>
                                        <span class="profile-post-type"><?php 
                                            echo esc_html(ucfirst(str_replace('-', ' ', $post->post_type))); 
                                        ?></span>
                                    </div>
                                    
                                    <div class="profile-post-content">
                                        <?php if (!empty($post->content)): ?>
                                            <p class="profile-post-text"><?php echo nl2br(esc_html($post->content)); ?></p>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($post->media_url)): ?>
                                            <div class="profile-post-media">
                                                <?php if ($post->media_type === 'image'): ?>
                                                    <img src="<?php echo esc_url($post->media_url); ?>" alt="Post media" class="profile-post-image">
                                                <?php elseif ($post->media_type === 'video'): ?>
                                                    <video controls class="profile-post-video">
                                                        <source src="<?php echo esc_url($post->media_url); ?>" type="video/mp4">
                                                        Votre navigateur ne supporte pas l'élément vidéo.
                                                    </video>
                                                <?php elseif ($post->media_type === 'audio'): ?>
                                                    <audio controls class="profile-post-audio">
                                                        <source src="<?php echo esc_url($post->media_url); ?>" type="audio/mpeg">
                                                        Votre navigateur ne supporte pas l'élément audio.
                                                    </audio>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="profile-post-actions">
                                        <?php if ($post->show_likes): ?>
                                            <button class="profile-post-action-btn like-btn <?php echo $user_liked ? 'active' : ''; ?>" 
                                                    data-post-id="<?php echo esc_attr($post->id); ?>" 
                                                    data-reaction-type="like"
                                                    onclick="event.preventDefault(); event.stopPropagation(); handleProfileReaction(<?php echo esc_js($post->id); ?>, 'like', this);">
                                                <svg class="profile-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                                <span class="profile-action-count"><?php echo esc_html($likes_count); ?></span>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($post->allow_comments): ?>
                                            <button class="profile-post-action-btn comment-btn" 
                                                    data-post-id="<?php echo esc_attr($post->id); ?>"
                                                    onclick="event.preventDefault(); event.stopPropagation(); window.location.href='<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>';">
                                                <svg class="profile-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                <span class="profile-action-count"><?php echo esc_html($comments_count); ?></span>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($post->allow_repost): ?>
                                            <button class="profile-post-action-btn repost-btn <?php echo $user_reposted ? 'active' : ''; ?>" 
                                                    data-post-id="<?php echo esc_attr($post->id); ?>" 
                                                    data-reaction-type="repost"
                                                    onclick="event.preventDefault(); event.stopPropagation(); handleProfileReaction(<?php echo esc_js($post->id); ?>, 'repost', this);">
                                                <svg class="profile-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                <span class="profile-action-count"><?php echo esc_html($reposts_count); ?></span>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button class="profile-post-action-btn favorite-btn <?php echo $user_favorited ? 'active' : ''; ?>" 
                                                data-post-id="<?php echo esc_attr($post->id); ?>" 
                                                data-reaction-type="favorite"
                                                onclick="event.preventDefault(); event.stopPropagation(); handleProfileReaction(<?php echo esc_js($post->id); ?>, 'favorite', this);">
                                            <svg class="profile-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </article>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            // Fonction helper pour afficher un post dans un carrousel (format compact)
            function bebeats_render_carousel_post($post, $stats, $current_user_id) {
                $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
                $post_timestamp = strtotime($post->created_at);
                ?>
                <a href="<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>" class="carousel-post-link">
                    <article class="carousel-post-item">
                        <div class="carousel-post-header">
                            <span class="carousel-post-time" data-timestamp="<?php echo esc_attr($post_timestamp); ?>">Il y a <?php echo esc_html($time_ago); ?></span>
                            <span class="carousel-post-type"><?php 
                                echo esc_html(ucfirst(str_replace('-', ' ', $post->post_type))); 
                            ?></span>
                        </div>
                        
                        <div class="carousel-post-content">
                            <?php if (!empty($post->content)): ?>
                                <p class="carousel-post-text"><?php echo nl2br(esc_html($post->content)); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($post->media_url)): ?>
                                <div class="carousel-post-media">
                                    <?php if ($post->media_type === 'image'): ?>
                                        <img src="<?php echo esc_url($post->media_url); ?>" alt="Post media" class="carousel-post-image">
                                    <?php elseif ($post->media_type === 'video'): ?>
                                        <video controls class="carousel-post-video">
                                            <source src="<?php echo esc_url($post->media_url); ?>" type="video/mp4">
                                            Votre navigateur ne supporte pas l'élément vidéo.
                                        </video>
                                    <?php elseif ($post->media_type === 'audio'): ?>
                                        <audio controls class="carousel-post-audio">
                                            <source src="<?php echo esc_url($post->media_url); ?>" type="audio/mpeg">
                                            Votre navigateur ne supporte pas l'élément audio.
                                        </audio>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="carousel-post-actions">
                            <?php 
                            // Pour le carrousel, on ne montre que les likes et commentaires pour garder compact
                            ?>
                            <button class="carousel-post-action-btn like-btn <?php echo $stats['user_liked'] ? 'active' : ''; ?>" 
                                    data-post-id="<?php echo esc_attr($post->id); ?>" 
                                    data-reaction-type="like"
                                    onclick="event.preventDefault(); event.stopPropagation(); handleProfileReaction(<?php echo esc_js($post->id); ?>, 'like', this);">
                                <svg class="carousel-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="carousel-action-count"><?php echo esc_html($stats['likes_count']); ?></span>
                            </button>
                            
                            <button class="carousel-post-action-btn comment-btn" 
                                    data-post-id="<?php echo esc_attr($post->id); ?>"
                                    onclick="event.preventDefault(); event.stopPropagation(); window.location.href='<?php echo esc_url(home_url('/feed-page#post-' . $post->id)); ?>';">
                                <svg class="carousel-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="carousel-action-count"><?php echo esc_html($stats['comments_count']); ?></span>
                            </button>
                        </div>
                    </article>
                </a>
                <?php
            }
            
            // Fonction helper pour afficher un post dans le format feed (format complet)
            function bebeats_render_feed_carousel_post($post, $stats, $current_user_id, $wpdb) {
                $time_ago = human_time_diff(strtotime($post->created_at), current_time('timestamp'));
                $post_timestamp = strtotime($post->created_at);
                
                // Récupérer les infos utilisateur
                $user_info = $wpdb->get_row($wpdb->prepare(
                    "SELECT u.display_name, u.user_login,
                            (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
                     FROM {$wpdb->users} u
                     WHERE u.ID = %d",
                    $post->user_id
                ));
                
                $profile_photo = !empty($user_info->profile_photo) ? $user_info->profile_photo : get_avatar_url($post->user_id, array('size' => 60));
                ?>
                <article class="carousel-feed-post glassmorphism" data-post-id="<?php echo esc_attr($post->id); ?>">
                    <div class="post-header">
                        <img src="<?php echo esc_url($profile_photo); ?>" alt="<?php echo esc_attr($user_info->display_name ?: $user_info->user_login); ?>" class="post-author-avatar">
                        <div class="post-author-info">
                            <h3 class="post-author-name"><?php echo esc_html($user_info->display_name ?: $user_info->user_login); ?></h3>
                            <span class="post-time" data-timestamp="<?php echo esc_attr($post_timestamp); ?>">Il y a <?php echo esc_html($time_ago); ?></span>
                        </div>
                    </div>
                    
                    <div class="post-content">
                        <?php if (!empty($post->content)): ?>
                            <p class="post-text"><?php echo nl2br(esc_html($post->content)); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($post->media_url)): ?>
                            <div class="post-media">
                                <?php if ($post->media_type === 'image'): ?>
                                    <img src="<?php echo esc_url($post->media_url); ?>" alt="Post media" class="post-media-image">
                                <?php elseif ($post->media_type === 'video'): ?>
                                    <video controls class="post-media-video">
                                        <source src="<?php echo esc_url($post->media_url); ?>" type="video/mp4">
                                        Votre navigateur ne supporte pas l'élément vidéo.
                                    </video>
                                <?php elseif ($post->media_type === 'audio'): ?>
                                    <audio controls class="post-media-audio">
                                        <source src="<?php echo esc_url($post->media_url); ?>" type="audio/mpeg">
                                        Votre navigateur ne supporte pas l'élément audio.
                                    </audio>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-actions">
                        <div class="post-actions-left">
                            <button class="post-action-btn like-btn <?php echo $stats['user_liked'] ? 'active' : ''; ?>" 
                                    data-post-id="<?php echo esc_attr($post->id); ?>" 
                                    data-reaction-type="like"
                                    <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="action-count"><?php echo esc_html($stats['likes_count']); ?></span>
                            </button>
                            
                            <button class="post-action-btn comment-btn" 
                                    data-post-id="<?php echo esc_attr($post->id); ?>"
                                    <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="action-count"><?php echo esc_html($stats['comments_count']); ?></span>
                            </button>
                            
                            <button class="post-action-btn repost-btn <?php echo $stats['user_reposted'] ? 'active' : ''; ?>" 
                                    data-post-id="<?php echo esc_attr($post->id); ?>" 
                                    data-reaction-type="repost"
                                    <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                                <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="action-count"><?php echo esc_html($stats['reposts_count']); ?></span>
                            </button>
                        </div>
                        
                        <button class="post-action-btn favorite-btn <?php echo $stats['user_favorited'] ? 'active' : ''; ?>" 
                                data-post-id="<?php echo esc_attr($post->id); ?>" 
                                data-reaction-type="favorite"
                                <?php echo !is_user_logged_in() ? 'disabled' : ''; ?>>
                            <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                        </button>
                    </div>
                </article>
                <?php
            }
            ?>
            
            <!-- Carrousel: Mes posts Fan-Art -->
            <?php
            $user_fanart_posts = $wpdb->get_results($wpdb->prepare(
                "SELECT p.*, u.display_name, u.user_login,
                        (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
                 FROM $posts_table p
                 LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
                 WHERE p.user_id = %d AND p.post_type = 'fan-art'
                 ORDER BY p.created_at DESC
                 LIMIT 20",
                $viewed_user_id
            ));
            
            if (!empty($user_fanart_posts)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Fan-Art</h2>
                    <div class="carousel-container">
                        <button class="carousel-nav-btn carousel-prev" aria-label="Précédent">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="carousel-wrapper">
                            <div class="carousel-track" data-carousel="fanart-user">
                                <?php foreach ($user_fanart_posts as $post): 
                                    $stats = isset($carousel_stats[$post->id]) ? $carousel_stats[$post->id] : array('likes_count' => 0, 'reposts_count' => 0, 'comments_count' => 0, 'user_liked' => false, 'user_favorited' => false, 'user_reposted' => false);
                                    bebeats_render_carousel_post($post, $stats, $current_user_id);
                                endforeach; ?>
                            </div>
                        </div>
                        <button class="carousel-nav-btn carousel-next" aria-label="Suivant">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Carrousel: Mes posts Audio -->
            <?php
            $user_audio_posts = $wpdb->get_results($wpdb->prepare(
                "SELECT p.*, u.display_name, u.user_login,
                        (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
                 FROM $posts_table p
                 LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
                 WHERE p.user_id = %d AND p.post_type = 'audio'
                 ORDER BY p.created_at DESC
                 LIMIT 20",
                $viewed_user_id
            ));
            
            if (!empty($user_audio_posts)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Audio</h2>
                    <div class="carousel-container">
                        <button class="carousel-nav-btn carousel-prev" aria-label="Précédent">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="carousel-wrapper">
                            <div class="carousel-track" data-carousel="audio-user">
                                <?php foreach ($user_audio_posts as $post): 
                                    $stats = isset($carousel_stats[$post->id]) ? $carousel_stats[$post->id] : array('likes_count' => 0, 'reposts_count' => 0, 'comments_count' => 0, 'user_liked' => false, 'user_favorited' => false, 'user_reposted' => false);
                                    bebeats_render_carousel_post($post, $stats, $current_user_id);
                                endforeach; ?>
                            </div>
                        </div>
                        <button class="carousel-nav-btn carousel-next" aria-label="Suivant">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Carrousel: Mes posts Articles -->
            <?php
            $user_article_posts = $wpdb->get_results($wpdb->prepare(
                "SELECT p.*, u.display_name, u.user_login,
                        (SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id = u.ID AND meta_key = 'bebeats_profile_photo' LIMIT 1) as profile_photo
                 FROM $posts_table p
                 LEFT JOIN {$wpdb->users} u ON p.user_id = u.ID
                 WHERE p.user_id = %d AND p.post_type = 'post'
                 ORDER BY p.created_at DESC
                 LIMIT 20",
                $viewed_user_id
            ));
            $carousel_stats = bebeats_get_reactions_stats(array_merge(
                wp_list_pluck($user_fanart_posts, 'id'),
                wp_list_pluck($user_audio_posts, 'id'),
                wp_list_pluck($user_article_posts, 'id')
            ), $current_user_id);
            
            if (!empty($user_article_posts)): ?>
                <div class="profile-section glassmorphism">
                    <h2 class="section-title">Articles</h2>
                    <div class="carousel-container">
                        <button class="carousel-nav-btn carousel-prev" aria-label="Précédent">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="carousel-wrapper">
                            <div class="carousel-track" data-carousel="articles-user">
                                <?php foreach ($user_article_posts as $post): 
                                    $stats = isset($carousel_stats[$post->id]) ? $carousel_stats[$post->id] : array('likes_count' => 0, 'reposts_count' => 0, 'comments_count' => 0, 'user_liked' => false, 'user_favorited' => false, 'user_reposted' => false);
                                    bebeats_render_carousel_post($post, $stats, $current_user_id);
                                endforeach; ?>
                            </div>
                        </div>
                        <button class="carousel-nav-btn carousel-next" aria-label="Suivant">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
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
