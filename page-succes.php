<?php
/**
 * Template Name: Succes Page
 * Badges, titres et progression d'experience de l'utilisateur
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth-start'));
    exit;
}

get_header();

$current_user_id = get_current_user_id();
$activity = bebeats_get_user_activity($current_user_id);
$badges = bebeats_get_user_badges($current_user_id);
$selected_badge = get_user_meta($current_user_id, 'bebeats_selected_badge', true);

// Progression d'XP : palier en cours (100 puis 500 points)
$xp = $activity['points'];
if ($xp < 100) {
    $xp_target = 100;
    $next_title = 'Super Fan';
} elseif ($xp < 500) {
    $xp_target = 500;
    $next_title = 'Veteran BeBeats';
} else {
    $xp_target = $xp;
    $next_title = '';
}
$xp_progress = $xp_target > 0 ? min(100, round(($xp / $xp_target) * 100)) : 100;

// Icones des badges
$badge_icons = array(
    'footsteps' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5-5m-4.5 2L15 2m-8 8l-3 3a1.5 1.5 0 003 3l3-3M9 10l-3 3a1.5 1.5 0 003 3l3-3m-1-9l2-2m-4 4l2-2"></path>',
    'megaphone' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l12-6v14l-12-6v-2zm12 2h3a2 2 0 002-2v-2a2 2 0 00-2-2h-3M7 14v5a1 1 0 001 1h2a1 1 0 001-1v-4"></path>',
    'chat' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>',
    'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>',
    'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.539 1.118l-3.975-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.98 9.101c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path>',
    'trophy' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4M7 4h10v6a5 5 0 01-10 0V4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6H5a2 2 0 00-2 2c0 1.5 1 3 3.5 3.5M17 6h2a2 2 0 012 2c0 1.5-1 3-3.5 3.5"></path>'
);
?>

<main class="succes-page">
    <div class="succes-container">
        <h1 class="succes-title">Tes succès</h1>
        
        <!-- Progression d'XP -->
        <section class="succes-xp glassmorphism">
            <div class="succes-xp-header">
                <span class="succes-xp-points"><?php echo esc_html($xp); ?> points d'expérience</span>
                <?php if (!empty($next_title)): ?>
                    <span class="succes-xp-next"><?php echo esc_html($xp_target - $xp); ?> points avant « <?php echo esc_html($next_title); ?> »</span>
                <?php else: ?>
                    <span class="succes-xp-next">Palier maximum atteint !</span>
                <?php endif; ?>
            </div>
            <div class="succes-xp-bar">
                <div class="succes-xp-bar-fill" style="width: <?php echo esc_attr($xp_progress); ?>%;"></div>
            </div>
            <div class="succes-xp-activities">
                <span class="succes-xp-activity"><?php echo esc_html($activity['posts']); ?> publication<?php echo $activity['posts'] > 1 ? 's' : ''; ?></span>
                <span class="succes-xp-activity"><?php echo esc_html($activity['comments']); ?> commentaire<?php echo $activity['comments'] > 1 ? 's' : ''; ?></span>
                <span class="succes-xp-activity"><?php echo esc_html($activity['following']); ?> abonnement<?php echo $activity['following'] > 1 ? 's' : ''; ?></span>
            </div>
        </section>
        
        <!-- Grille des badges -->
        <section class="succes-badges">
            <h2 class="succes-badges-title">Badges et titres</h2>
            <p class="succes-badges-hint">Clique sur un badge débloqué pour l'afficher comme titre sur ton profil.</p>
            
            <div class="succes-badges-grid">
                <?php foreach ($badges as $key => $badge): ?>
                    <button 
                        type="button"
                        class="succes-badge <?php echo $badge['unlocked'] ? 'is-unlocked' : 'is-locked'; ?> <?php echo $badge['selected'] ? 'is-selected' : ''; ?>"
                        data-badge="<?php echo esc_attr($key); ?>"
                        <?php echo $badge['unlocked'] ? '' : 'disabled'; ?>
                    >
                        <span class="succes-badge-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <?php echo $badge_icons[$badge['icon']]; ?>
                            </svg>
                        </span>
                        <span class="succes-badge-title"><?php echo esc_html($badge['title']); ?></span>
                        <span class="succes-badge-description"><?php echo esc_html($badge['description']); ?></span>
                        <span class="succes-badge-progress">
                            <?php echo esc_html(min($badge['current'], $badge['target'])); ?>/<?php echo esc_html($badge['target']); ?>
                        </span>
                        <?php if ($badge['selected']): ?>
                            <span class="succes-badge-selected-label">Titre affiché</span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
