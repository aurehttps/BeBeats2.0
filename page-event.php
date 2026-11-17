<?php
/**
 * Template Name: Event Page
 * Template pour la page Event
 */

get_header();

global $wpdb;
$posts_table = $wpdb->prefix . 'bebeats_posts';
$events = $wpdb->get_results("
    SELECT p.*, u.display_name, u.user_login
    FROM {$posts_table} p
    INNER JOIN {$wpdb->users} u ON p.user_id = u.ID
    WHERE p.post_type = 'event'
    ORDER BY p.created_at DESC
    LIMIT 50
");

$known_locations = $wpdb->get_col("
    SELECT DISTINCT event_location FROM {$posts_table}
    WHERE post_type = 'event' AND event_location IS NOT NULL AND event_location != ''
    ORDER BY event_location ASC
    LIMIT 50
");

$known_genres = $wpdb->get_col("
    SELECT DISTINCT event_genre FROM {$posts_table}
    WHERE post_type = 'event' AND event_genre IS NOT NULL AND event_genre != ''
    ORDER BY event_genre ASC
    LIMIT 50
");

$max_price_row = $wpdb->get_var("
    SELECT MAX(event_price) FROM {$posts_table}
    WHERE post_type = 'event' AND event_price IS NOT NULL
");
$max_price = $max_price_row !== null ? max(10, (float) $max_price_row) : 10;
$max_price = ceil($max_price);
?>

<main class="event-content">
    <!-- Filter Menu -->
    <aside class="event-filters">
        <div class="event-filter-group" data-filter-group="date">
            <button type="button" class="filter-btn" data-filter-toggle="date">Date</button>
            <div class="filter-popover">
                <label class="filter-popover-label" for="filter-date-input">Choisis une date</label>
                <input type="date" id="filter-date-input" class="filter-input" data-filter-role="date" />
                <button type="button" class="filter-clear-btn" data-filter-clear="date">Effacer</button>
            </div>
        </div>

        <div class="event-filter-group" data-filter-group="lieu">
            <button type="button" class="filter-btn" data-filter-toggle="lieu">Lieu</button>
            <div class="filter-popover">
                <label class="filter-popover-label" for="filter-lieu-input">Tape un lieu</label>
                <input type="text" id="filter-lieu-input" class="filter-input" data-filter-role="lieu" list="filter-lieu-suggestions" autocomplete="off" placeholder="Ex : Bruxelles..." />
                <datalist id="filter-lieu-suggestions">
                    <?php foreach ((array) $known_locations as $loc): ?>
                        <option value="<?php echo esc_attr($loc); ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <button type="button" class="filter-clear-btn" data-filter-clear="lieu">Effacer</button>
            </div>
        </div>

        <div class="event-filter-group" data-filter-group="prix">
            <button type="button" class="filter-btn" data-filter-toggle="prix">Prix</button>
            <div class="filter-popover">
                <label class="filter-popover-label">Budget (max : <?php echo esc_html($max_price); ?> €)</label>
                <div class="filter-price-row">
                    <input type="range" id="filter-price-min" class="filter-range" data-filter-role="price-min" min="0" max="<?php echo esc_attr($max_price); ?>" value="0" step="1" />
                    <span class="filter-price-sep">→</span>
                    <input type="range" id="filter-price-max" class="filter-range" data-filter-role="price-max" min="0" max="<?php echo esc_attr($max_price); ?>" value="<?php echo esc_attr($max_price); ?>" step="1" />
                </div>
                <div class="filter-price-values">
                    <span id="filter-price-min-value">0 €</span>
                    <span id="filter-price-max-value"><?php echo esc_html($max_price); ?> €</span>
                </div>
                <button type="button" class="filter-clear-btn" data-filter-clear="prix">Effacer</button>
            </div>
        </div>

        <div class="event-filter-group" data-filter-group="musique">
            <button type="button" class="filter-btn filter-btn-active" data-filter-toggle="musique">Musique</button>
            <div class="filter-popover">
                <label class="filter-popover-label" for="filter-genre-input">Type de musique</label>
                <select id="filter-genre-input" class="filter-input" data-filter-role="genre">
                    <option value="">Tous</option>
                    <?php foreach ((array) $known_genres as $genre): ?>
                        <option value="<?php echo esc_attr($genre); ?>"><?php echo esc_html($genre); ?></option>
                    <?php endforeach; ?>
                    <?php if (empty($known_genres)): ?>
                        <option value="Pop">Pop</option>
                        <option value="Rock">Rock</option>
                        <option value="Hip-hop">Hip-hop</option>
                        <option value="Rap">Rap</option>
                        <option value="Electro">Electro</option>
                        <option value="Jazz">Jazz</option>
                        <option value="Classique">Classique</option>
                        <option value="Folk">Folk</option>
                        <option value="Metal">Metal</option>
                        <option value="Reggae">Reggae</option>
                        <option value="R&B">R&B</option>
                        <option value="Autre">Autre</option>
                    <?php endif; ?>
                </select>
                <button type="button" class="filter-clear-btn" data-filter-clear="musique">Effacer</button>
            </div>
        </div>
    </aside>

    <!-- Events List -->
    <section class="events-list">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): 
                $event_date_sql = !empty($event->event_date) ? $event->event_date : date('Y-m-d', strtotime($event->created_at));
                $event_date = date_i18n('d/m/Y', strtotime($event_date_sql));
                $event_title = !empty($event->event_title)
                    ? $event->event_title
                    : (!empty($event->content) ? wp_trim_words($event->content, 6, '...') : 'Évènement BeBeats');
                $event_price_val = ($event->event_price !== null && $event->event_price !== '') ? (float) $event->event_price : 0;
                $event_price_label = $event_price_val > 0 ? number_format_i18n($event_price_val, 2) . ' €' : 'Entrée libre';
                $event_location = !empty($event->event_location) ? $event->event_location : 'Belgique';
            ?>
                <article
                    class="event-card"
                    id="event-<?php echo esc_attr($event->id); ?>"
                    data-date="<?php echo esc_attr($event_date_sql); ?>"
                    data-lieu="<?php echo esc_attr($event_location); ?>"
                    data-price="<?php echo esc_attr($event_price_val); ?>"
                    data-genre="<?php echo esc_attr($event->event_genre); ?>"
                    data-title="<?php echo esc_attr($event_title); ?>"
                    data-artist="<?php echo esc_attr($event->display_name); ?>"
                    data-description="<?php echo esc_attr($event->content); ?>"
                    data-image="<?php echo esc_url($event->media_url); ?>"
                    data-date-label="<?php echo esc_attr($event_date); ?>"
                    data-price-label="<?php echo esc_attr($event_price_label); ?>"
                >
                    <?php if (!empty($event->media_url) && $event->media_type === 'image'): ?>
                        <div class="event-image">
                            <img src="<?php echo esc_url($event->media_url); ?>" alt="<?php echo esc_attr($event_title); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="event-details">
                        <h2 class="event-title">
                            <?php echo esc_html($event_title); ?>
                        </h2>
                        <p class="event-artist">par <?php echo esc_html($event->display_name); ?></p>
                        <?php if (!empty($event->content)): ?>
                            <p class="event-description">
                                <?php echo esc_html(wp_trim_words($event->content, 20, '...')); ?>
                            </p>
                        <?php endif; ?>
                        <div class="event-info">
                            <span class="event-info-item">
                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <?php echo esc_html($event_date); ?>
                            </span>
                            <span class="event-info-item">
                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <?php echo esc_html($event_location); ?>
                            </span>
                            <span class="event-info-item">
                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php echo esc_html($event_price_label); ?>
                            </span>
                            <?php if (!empty($event->event_genre)): ?>
                                <span class="event-info-item event-genre-item"><?php echo esc_html($event->event_genre); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Événements éditoriaux fixes -->
        <article class="event-card"
            data-date="2025-12-23" data-lieu="Ciney" data-price="0" data-genre="Rock"
            data-title="Firew00d" data-artist="Firew00d"
            data-description="Ce groupe venant tout droit de la maison de jeunesse de Ciney a déjà fait ces preuves sur la scène de plusieurs festival. Avec des reprises incroyables."
            data-image="<?php echo esc_url(get_template_directory_uri()); ?>/Images/firewood logo.jpg"
            data-date-label="23/12/2025" data-price-label="Prix libre"
        >
            <div class="event-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/firewood logo.jpg" alt="Firew00d">
            </div>
            <div class="event-details">
                <h2 class="event-title">Firew00d</h2>
                <p class="event-description">Ce groupe venant tout droit de la maison de jeunesse de Ciney a déjà fait ces preuves sur la scène de plusieurs festival. Avec des reprises incroyables.</p>
                <div class="event-info">
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        23/12/2025
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ciney
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prix libre
                    </span>
                </div>
            </div>
        </article>

        <article class="event-card"
            data-date="2025-12-23" data-lieu="Ciney" data-price="0" data-genre="Pop"
            data-title="Bobby Watson" data-artist="Bobby Watson"
            data-description="Engagée dans le travail collaboratif, Bobbi Watson s'associe à des artistes d'horizons divers, comme la drag queer Tiny Beast Prince."
            data-image="<?php echo esc_url(get_template_directory_uri()); ?>/Images/bobby watson.jpg"
            data-date-label="23/12/2025" data-price-label="Prix libre"
        >
            <div class="event-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/bobby watson.jpg" alt="Bobby Watson">
            </div>
            <div class="event-details">
                <h2 class="event-title">Bobby Watson</h2>
                <p class="event-description">Engagée dans le travail collaboratif, Bobbi Watson s'associe à des artistes d'horizons divers, comme la drag queer Tiny Beast Prince.</p>
                <div class="event-info">
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        23/12/2025
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ciney
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prix libre
                    </span>
                </div>
            </div>
        </article>

        <article class="event-card"
            data-date="2025-12-23" data-lieu="Ciney" data-price="0" data-genre="Rock"
            data-title="Pretty Much Average" data-artist="Pretty Much Average"
            data-description="Avec la sortie de leur nouvel EP &quot;Warm Ankle&quot;, Pretty Much Average se sont réveillés à la bourre pour le taf, avec une vilaine gueule de bois."
            data-image="<?php echo esc_url(get_template_directory_uri()); ?>/Images/pretty much average.jpg"
            data-date-label="23/12/2025" data-price-label="Prix libre"
        >
            <div class="event-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/pretty much average.jpg" alt="Pretty Much Average">
            </div>
            <div class="event-details">
                <h2 class="event-title">Pretty Much Average</h2>
                <p class="event-description">Avec la sortie de leur nouvel EP "Warm Ankle", Pretty Much Average se sont réveillés à la bourre pour le taf, avec une vilaine gueule de bois.</p>
                <div class="event-info">
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        23/12/2025
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ciney
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prix libre
                    </span>
                </div>
            </div>
        </article>

        <article class="event-card"
            data-date="2025-12-23" data-lieu="Ciney" data-price="0" data-genre="Electro"
            data-title="IAMWILL" data-artist="IAMWILL"
            data-description="Après deux premiers singles remarqués, « OCEANS BLOOM » et « GRASS IS GREEN », l'artiste s'apprête à franchir une nouvelle étape."
            data-image="<?php echo esc_url(get_template_directory_uri()); ?>/Images/iamwill.jpg"
            data-date-label="23/12/2025" data-price-label="Prix libre"
        >
            <div class="event-image">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/Images/iamwill.jpg" alt="IAMWILL">
            </div>
            <div class="event-details">
                <h2 class="event-title">IAMWILL</h2>
                <p class="event-description">Après deux premiers singles remarqués, « OCEANS BLOOM » et « GRASS IS GREEN », l'artiste s'apprête à franchir une nouvelle étape.</p>
                <div class="event-info">
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        23/12/2025
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ciney
                    </span>
                    <span class="event-info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Prix libre
                    </span>
                </div>
            </div>
        </article>

        <p class="event-empty-hint" id="event-empty-hint" style="display: none;">Aucun événement ne correspond à ces filtres.</p>
    </section>
</main>

<!-- Modale détail d'un événement -->
<div class="event-detail-modal" id="event-detail-modal" style="display: none;" role="dialog" aria-modal="true" aria-label="Détail de l'événement">
    <div class="event-detail-backdrop" id="event-detail-backdrop"></div>
    <div class="event-detail-panel">
        <button type="button" class="event-detail-close" id="event-detail-close" aria-label="Fermer">&times;</button>
        <div class="event-detail-image" id="event-detail-image"></div>
        <div class="event-detail-body">
            <h2 class="event-detail-title" id="event-detail-title"></h2>
            <p class="event-artist" id="event-detail-artist"></p>
            <p class="event-detail-description" id="event-detail-description"></p>
            <div class="event-detail-info">
                <span class="event-info-item" id="event-detail-date"></span>
                <span class="event-info-item" id="event-detail-lieu"></span>
                <span class="event-info-item" id="event-detail-prix"></span>
                <span class="event-info-item" id="event-detail-genre"></span>
            </div>
            <button type="button" class="event-detail-calendar-btn" id="event-detail-calendar-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Ajouter au calendrier
            </button>
        </div>
    </div>
</div>

<?php get_footer(); ?>
