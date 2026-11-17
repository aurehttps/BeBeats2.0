<?php
/**
 * Template Name: Réglages Page
 * Template pour la page Réglages
 */

get_header(); 

// Récupérer les données de l'utilisateur connecté
$current_user = null;
$profile_photo = '';
$banner = '';
$description = '';

if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $profile_photo = get_user_meta($current_user->ID, 'bebeats_profile_photo', true);
    $banner = get_user_meta($current_user->ID, 'bebeats_banner', true);
    $description = get_user_meta($current_user->ID, 'description', true);
    
    // Si pas de photo de profil, utiliser l'avatar WordPress par défaut
    if (empty($profile_photo)) {
        $profile_photo = get_avatar_url($current_user->ID, array('size' => 200));
    }
}
?>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (is_user_logged_in()): ?>
        <!-- Section Profil -->
        <section class="settings-panel glassmorphism profile-settings-section">
            <h2 class="settings-section-title">Profil</h2>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                <div class="settings-success-message">
                    Profil mis à jour avec succès !
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                <div class="settings-error-message">
                    Une erreur s'est produite. Veuillez réessayer.
                </div>
            <?php endif; ?>
            
            <form class="profile-settings-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="bebeats_update_profile">
                <?php wp_nonce_field('bebeats_update_profile_action', 'bebeats_update_profile_nonce'); ?>
                
                <div class="profile-settings-grid">
                    <!-- Photo de profil -->
                    <div class="profile-setting-item">
                        <label class="profile-setting-label">Photo de profil</label>
                        <div class="profile-photo-preview-container">
                            <img src="<?php echo esc_url($profile_photo); ?>" alt="Photo de profil" class="profile-photo-preview" id="profile-photo-preview">
                            <label class="profile-file-btn">
                                <input type="file" name="profile_photo" accept="image/*" class="profile-file-input" id="profile-photo-input">
                                <span>Changer</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Bannière -->
                    <div class="profile-setting-item">
                        <label class="profile-setting-label">Bannière</label>
                        <div class="profile-banner-preview-container">
                            <?php if (!empty($banner)): ?>
                                <img src="<?php echo esc_url($banner); ?>" alt="Bannière" class="profile-banner-preview" id="banner-preview">
                            <?php else: ?>
                                <div class="profile-banner-placeholder" id="banner-preview">
                                    <span>Aucune bannière</span>
                                </div>
                            <?php endif; ?>
                            <label class="profile-file-btn">
                                <input type="file" name="banner" accept="image/*" class="profile-file-input" id="banner-input">
                                <span><?php echo !empty($banner) ? 'Changer' : 'Ajouter'; ?></span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="profile-setting-item profile-setting-item-full">
                        <label class="profile-setting-label" for="description">Description</label>
                        <textarea 
                            name="description" 
                            id="description"
                            class="profile-description-input" 
                            placeholder="Écris une description"
                            rows="4"
                        ><?php echo esc_textarea($description); ?></textarea>
                    </div>
                </div>
                
                <button type="submit" class="profile-save-btn">Enregistrer les modifications</button>
            </form>
        </section>
        <?php endif; ?>

        <!-- Section Réglages généraux -->
        <section class="settings-panel glassmorphism">
            <h2 class="settings-section-title">Réglages</h2>
            <div class="settings-grid">
                <div class="setting-item">
                    <label class="setting-label">Cookies</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-cookies" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">✓</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label class="setting-label">Langues</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-langues" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">✓</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label class="setting-label">Thème</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-theme" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">&#10003;</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label class="setting-label">Mode</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-mode" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">✓</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <label class="setting-label">Repost</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-repost" class="toggle-input">
                        <span class="toggle-slider">
                            <span class="toggle-checkmark">✓</span>
                            <span class="toggle-knob"></span>
                        </span>
                    </label>
                </div>
            </div>
        </section>

        <?php if (is_user_logged_in()): ?>
        <!-- Section Compte -->
        <section class="settings-panel glassmorphism account-settings-section">
            <h2 class="settings-section-title">Compte</h2>
            
            <?php if (isset($_GET['account_success']) && $_GET['account_success'] == '1'): ?>
                <div class="settings-success-message">Compte mis à jour avec succès !</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['account_error'])): ?>
                <div class="settings-error-message">
                    <?php
                    $account_error = sanitize_text_field($_GET['account_error']);
                    if ($account_error === 'pseudo') {
                        echo 'Le pseudo visible doit contenir entre 2 et 40 caractères.';
                    } elseif ($account_error === 'current') {
                        echo 'Mot de passe actuel incorrect.';
                    } elseif ($account_error === 'length') {
                        echo 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
                    } elseif ($account_error === 'confirm') {
                        echo 'Les deux mots de passe ne correspondent pas.';
                    } else {
                        echo 'Une erreur est survenue. Veuillez réessayer.';
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form class="account-settings-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="bebeats_update_account">
                <?php wp_nonce_field('bebeats_update_account_action', 'bebeats_update_account_nonce'); ?>
                
                <div class="account-setting-item">
                    <label class="profile-setting-label">Pseudo visible par les autres utilisateurs</label>
                    <input 
                        type="text" 
                        name="display_name" 
                        class="auth-input account-input" 
                        value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>"
                        minlength="2"
                        maxlength="40"
                    />
                </div>
                
                <div class="account-setting-item">
                    <label class="profile-setting-label">Changer mon mot de passe</label>
                    <input 
                        type="password" 
                        name="current_password" 
                        class="auth-input account-input" 
                        placeholder="Mot de passe actuel"
                        autocomplete="current-password"
                    />
                    <input 
                        type="password" 
                        name="new_password" 
                        class="auth-input account-input" 
                        placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)"
                        minlength="8"
                        autocomplete="new-password"
                    />
                    <input 
                        type="password" 
                        name="confirm_password" 
                        class="auth-input account-input" 
                        placeholder="Confirmer le nouveau mot de passe"
                        minlength="8"
                        autocomplete="new-password"
                    />
                </div>
                
                <button type="submit" class="profile-save-btn">Enregistrer</button>
            </form>
            
            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="bebeats_logout">
                <?php wp_nonce_field('bebeats_logout_action', 'bebeats_logout_nonce'); ?>
                <button type="submit" class="logout-btn">Se déconnecter</button>
            </form>
        </section>
        <?php endif; ?>
    </main>

<?php get_footer(); ?>

