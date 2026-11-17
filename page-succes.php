<?php
/**
 * Template Name: Succes Page
 * Niveaux, titres epingles et progression d'experience
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth-start'));
    exit;
}

get_header();

$current_user_id = get_current_user_id();
$activity = bebeats_get_user_activity($current_user_id);
$titles = bebeats_get_user_titles($current_user_id);

$xp = $activity['points'];
$level = bebeats_get_level_from_points($xp);
$thresholds = bebeats_get_level_thresholds();

// Progression vers le niveau suivant
if ($level < 10) {
    $next_level = $level + 1;
    $xp_next = $thresholds[$next_level];
    $xp_current = $thresholds[$level];
    $xp_progress = round((($xp - $xp_current) / max(1, $xp_next - $xp_current)) * 100);
    $xp_remaining = $xp_next - $xp;
} else {
    $next_level = null;
    $xp_progress = 100;
    $xp_remaining = 0;
}

// Icones des titres
$title_icons = array(
    'footsteps' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5-5m-4.5 2L15 2m-8 8l-3 3a1.5 1.5 0 003 3l3-3M9 10l-3 3a1.5 1.5 0 003 3l3-3m-1-9l2-2m-4 4l2-2"></path>',
    'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>',
    'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.539 1.118l-3.975-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.98 9.101c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path>',
    'trophy' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4M7 4h10v6a5 5 0 01-10 0V4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6H5a2 2 0 00-2 2c0 1.5 1 3 3.5 3.5M17 6h2a2 2 0 012 2c0 1.5-1 3-3.5 3.5"></path>'
);
?>

<main class="succes-page">
    <div class="succes-container">
        <h1 class="succes-title">Tes succès</h1>
        
        <!-- Progression de niveau -->
        <section class="succes-xp glassmorphism">
            <div class="succes-xp-header">
                <span class="succes-xp-points">Niveau <?php echo esc_html($level); ?> · <?php echo esc_html(number_format_i18n($xp)); ?> points d'expérience</span>
                <?php if ($next_level): ?>
                    <span class="succes-xp-next"><?php echo esc_html(number_format_i18n($xp_remaining)); ?> points avant le niveau <?php echo esc_html($next_level); ?></span>
                <?php else: ?>
                    <span class="succes-xp-next">Niveau maximum atteint !</span>
                <?php endif; ?>
            </div>
            <div class="succes-xp-bar">
                <div class="succes-xp-bar-fill" style="width: <?php echo esc_attr($xp_progress); ?>%;"></div>
            </div>
            <div class="succes-xp-activities">
                <span class="succes-xp-activity"><?php echo esc_html($activity['posts']); ?> publication<?php echo $activity['posts'] > 1 ? 's' : ''; ?> (+35)</span>
                <span class="succes-xp-activity"><?php echo esc_html($activity['comments']); ?> commentaire<?php echo $activity['comments'] > 1 ? 's' : ''; ?> (+25)</span>
                <span class="succes-xp-activity"><?php echo esc_html($activity['likes_given']); ?> like<?php echo $activity['likes_given'] > 1 ? 's' : ''; ?> (+10)</span>
                <span class="succes-xp-activity"><?php echo esc_html($activity['following']); ?> abonnement<?php echo $activity['following'] > 1 ? 's' : ''; ?> (+50)</span>
            </div>
            <p class="succes-xp-bonus">Les points de like et commentaire sont doublés sur les posts des artistes que tu suis.</p>
        </section>
        
        <!-- Titres epinglables -->
        <section class="succes-badges">
            <h2 class="succes-badges-title">Titres</h2>
            <p class="succes-badges-hint">Clique sur un titre débloqué pour l'épingler au-dessus de ta photo de profil.</p>
            
            <div class="succes-badges-grid">
                <?php foreach ($titles as $key => $title): ?>
                    <button 
                        type="button"
                        class="succes-badge <?php echo $title['unlocked'] ? 'is-unlocked' : 'is-locked'; ?> <?php echo $title['selected'] ? 'is-selected' : ''; ?>"
                        data-badge="<?php echo esc_attr($key); ?>"
                        <?php echo $title['unlocked'] ? '' : 'disabled'; ?>
                    >
                        <span class="succes-badge-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <?php echo $title_icons[$title['icon']]; ?>
                            </svg>
                        </span>
                        <span class="succes-badge-title"><?php echo esc_html($title['display']); ?></span>
                        <span class="succes-badge-description">
                            <?php echo $title['unlocked'] ? 'Débloqué' : 'Nécessite le niveau ' . esc_html($title['min_level']); ?>
                        </span>
                        <?php if ($title['selected']): ?>
                            <span class="succes-badge-selected-label">Titre épinglé</span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
