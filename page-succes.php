<?php
/**
 * Template Name: Succes Page
 * Titres epingles, badges epingles et progression d'experience
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/auth-start'));
    exit;
}

get_header();

$current_user_id = get_current_user_id();
$activity = bebeats_get_user_activity($current_user_id);
$titles = bebeats_get_user_titles($current_user_id);
$badges = bebeats_get_user_badges($current_user_id);

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

// Icones disponibles (titres et badges)
$icons = array(
    'footsteps' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5-5m-4.5 2L15 2m-8 8l-3 3a1.5 1.5 0 003 3l3-3M9 10l-3 3a1.5 1.5 0 003 3l3-3m-1-9l2-2m-4 4l2-2"></path>',
    'megaphone' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l12-6v14l-12-6v-2zm12 2h3a2 2 0 002-2v-2a2 2 0 00-2-2h-3M7 14v5a1 1 0 001 1h2a1 1 0 001-1v-4"></path>',
    'chat' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>',
    'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>',
    'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.539 1.118l-3.975-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.98 9.101c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.518-4.674z"></path>',
    'trophy' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4M7 4h10v6a5 5 0 01-10 0V4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6H5a2 2 0 00-2 2c0 1.5 1 3 3.5 3.5M17 6h2a2 2 0 012 2c0 1.5-1 3-3.5 3.5"></path>'
);

$pinned_badges = array();
foreach ($badges as $key => $badge) {
    if ($badge['pinned']) {
        $pinned_badges[$key] = $badge;
    }
}

$pinned_title = null;
foreach ($titles as $key => $title) {
    if ($title['selected']) {
        $pinned_title = $title;
        $pinned_title['key'] = $key;
    }
}
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
            <p class="succes-xp-bonus">Les points de like et commentaire sont doublés sur les posts des artistes que tu suis.</p>
        </section>
        
        <!-- Filtres -->
        <div class="succes-filters" role="group" aria-label="Filtres des succès">
            <button type="button" class="succes-filter-btn is-active" data-filter="profil">Sur mon profil</button>
            <button type="button" class="succes-filter-btn" data-filter="titres">Titres</button>
            <button type="button" class="succes-filter-btn" data-filter="badges">Badges</button>
        </div>
        
        <!-- Vue par defaut : ce qui est affiche sur le profil -->
        <section class="succes-view succes-view-profil glassmorphism" data-view="profil">
            <h2 class="succes-badges-title">Titre affiché</h2>
            <?php if ($pinned_title): ?>
                <div class="succes-profil-item is-selected">
                    <span class="succes-badge-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <?php echo $icons[$pinned_title['icon']]; ?>
                        </svg>
                    </span>
                    <span class="succes-badge-title"><?php echo esc_html($pinned_title['display']); ?></span>
                    <span class="succes-badge-selected-label">Titre épinglé</span>
                </div>
            <?php else: ?>
                <p class="succes-empty-hint">Aucun titre épinglé : le type de compte (Artiste / Fan) s'affiche par défaut. Choisis-en un dans le filtre « Titres ».</p>
            <?php endif; ?>
            
            <h2 class="succes-badges-title succes-badges-title-badges">Badges épinglés (<?php echo count($pinned_badges); ?>/5)</h2>
            <?php if (!empty($pinned_badges)): ?>
                <div class="succes-profil-badges-row">
                    <?php foreach ($pinned_badges as $key => $badge): ?>
                        <div class="succes-profil-item is-selected" data-badge="<?php echo esc_attr($key); ?>">
                            <span class="succes-badge-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <?php echo $icons[$badge['icon']]; ?>
                                </svg>
                            </span>
                            <span class="succes-badge-title"><?php echo esc_html($badge['title']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="succes-empty-hint">Aucun badge épinglé. Choisis-en jusqu'à 5 dans le filtre « Badges ».</p>
            <?php endif; ?>
            
            <p class="succes-empty-hint">Clique sur un titre ou un badge épinglé pour le retirer, ou utilise les filtres ci-dessus pour en changer.</p>
        </section>
        
        <!-- Vue Titres -->
        <section class="succes-view succes-view-titres glassmorphism" data-view="titres" style="display: none;">
            <h2 class="succes-badges-title">Titres remportés</h2>
            <p class="succes-badges-hint">Clique sur un titre débloqué pour l'épingler sur ton profil (un seul à la fois). Re-clic pour le retirer.</p>
            
            <div class="succes-badges-grid">
                <?php foreach ($titles as $key => $title): ?>
                    <button 
                        type="button"
                        class="succes-badge succes-title-card <?php echo $title['unlocked'] ? 'is-unlocked' : 'is-locked'; ?> <?php echo $title['selected'] ? 'is-selected' : ''; ?>"
                        data-badge="<?php echo esc_attr($key); ?>"
                        data-kind="title"
                        <?php echo $title['unlocked'] ? '' : 'disabled'; ?>
                    >
                        <span class="succes-badge-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <?php echo $icons[$title['icon']]; ?>
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
        
        <!-- Vue Badges -->
        <section class="succes-view succes-view-badges glassmorphism" data-view="badges" style="display: none;">
            <h2 class="succes-badges-title">Badges remportés</h2>
            <p class="succes-badges-hint">Clique sur un badge débloqué pour l'épingler sur ton profil (jusqu'à 5). Re-clic pour le retirer.</p>
            
            <div class="succes-badges-grid">
                <?php foreach ($badges as $key => $badge): ?>
                    <button 
                        type="button"
                        class="succes-badge <?php echo $badge['unlocked'] ? 'is-unlocked' : 'is-locked'; ?> <?php echo $badge['pinned'] ? 'is-selected' : ''; ?>"
                        data-badge="<?php echo esc_attr($key); ?>"
                        data-kind="badge"
                        <?php echo $badge['unlocked'] ? '' : 'disabled'; ?>
                    >
                        <span class="succes-badge-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <?php echo $icons[$badge['icon']]; ?>
                            </svg>
                        </span>
                        <span class="succes-badge-title"><?php echo esc_html($badge['title']); ?></span>
                        <span class="succes-badge-description"><?php echo esc_html($badge['description']); ?></span>
                        <span class="succes-badge-progress">
                            <?php echo esc_html(min($badge['current'], $badge['target'])); ?>/<?php echo esc_html($badge['target']); ?>
                        </span>
                        <?php if ($badge['pinned']): ?>
                            <span class="succes-badge-selected-label">Épinglé</span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
