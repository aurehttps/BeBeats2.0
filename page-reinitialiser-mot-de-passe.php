<?php
/**
 * Template Name: Reinitialiser mot de passe
 * Formulaire de definition d'un nouveau mot de passe (via lien mail)
 */

get_header();

$reset_login = isset($_GET['login']) ? sanitize_user($_GET['login']) : '';
$reset_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
$reset_valid = false;

if (!empty($reset_login) && !empty($reset_key)) {
    $user = get_user_by('login', $reset_login);
    if ($user) {
        $key_hash = get_user_meta($user->ID, 'bebeats_reset_key_hash', true);
        $key_time = (int) get_user_meta($user->ID, 'bebeats_reset_key_time', true);
        if (!empty($key_hash) && (time() - $key_time) <= 3600 && wp_check_password($reset_key, $key_hash, $user->ID)) {
            $reset_valid = true;
        }
    }
}
?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 75%;"></div>
                </div>
                <h1 class="auth-title">Nouveau mot de passe</h1>
                
                <?php if (!$reset_valid): ?>
                    <div class="auth-error-message">
                        Ce lien de reinitialisation est invalide ou a expire.
                    </div>
                    <p class="auth-alt-link">
                        <a href="<?php echo esc_url(home_url('/mot-de-passe-oublie')); ?>">Demander un nouveau lien</a>
                    </p>
                <?php else: ?>
                
                    <?php if (isset($_GET['reset_error'])): ?>
                        <div class="auth-error-message">
                            <?php
                            $reset_error = $_GET['reset_error'];
                            if ($reset_error === 'length') {
                                echo 'Le mot de passe doit contenir au moins 8 caracteres.';
                            } elseif ($reset_error === 'confirm') {
                                echo 'Les deux mots de passe ne correspondent pas.';
                            } else {
                                echo 'Une erreur est survenue. Reessaie.';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <form class="auth-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="bebeats_reset_password">
                        <?php wp_nonce_field('bebeats_reset_password_action', 'bebeats_reset_password_nonce'); ?>
                        <input type="hidden" name="login" value="<?php echo esc_attr($reset_login); ?>">
                        <input type="hidden" name="key" value="<?php echo esc_attr($reset_key); ?>">
                        
                        <div class="auth-input-group">
                            <input 
                                type="password" 
                                name="new_password" 
                                id="new_password"
                                class="auth-input" 
                                placeholder="Nouveau mot de passe (8 caracteres min.)"
                                required
                                minlength="8"
                                autocomplete="new-password"
                            />
                        </div>
                        
                        <div class="auth-input-group">
                            <input 
                                type="password" 
                                name="confirm_password" 
                                id="confirm_password"
                                class="auth-input" 
                                placeholder="Confirme le nouveau mot de passe"
                                required
                                minlength="8"
                                autocomplete="new-password"
                            />
                        </div>
                        
                        <button type="submit" class="auth-submit-btn">Modifier mon mot de passe</button>
                    </form>
                    
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php get_footer(); ?>
