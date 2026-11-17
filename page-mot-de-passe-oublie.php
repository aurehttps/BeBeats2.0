<?php
/**
 * Template Name: Mot de passe oublie
 * Formulaire de demande de reinitialisation de mot de passe
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 50%;"></div>
                </div>
                <h1 class="auth-title">Mot de passe oublie</h1>
                <p class="auth-subtitle">Indique l'adresse mail de ton compte BeBeats, on t'envoie un lien pour choisir un nouveau mot de passe.</p>
                
                <?php if (isset($_GET['forgot_sent']) && $_GET['forgot_sent'] == '1'): ?>
                    <div class="auth-success-message">
                        Si un compte existe avec cette adresse, un mail de reinitialisation vient d'etre envoye. Pense a verifier tes spams.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['forgot_error'])): ?>
                    <div class="auth-error-message">
                        <?php
                        $forgot_error = $_GET['forgot_error'];
                        if ($forgot_error === 'empty') {
                            echo 'Entre une adresse mail.';
                        } elseif ($forgot_error === 'expired') {
                            echo 'Ce lien de reinitialisation a expire. Demande-en un nouveau.';
                        } elseif ($forgot_error === 'invalid') {
                            echo 'Ce lien de reinitialisation est invalide. Demande-en un nouveau.';
                        } else {
                            echo 'Une erreur est survenue. Reessaie.';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <form class="auth-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="bebeats_forgot_password">
                    <?php wp_nonce_field('bebeats_forgot_password_action', 'bebeats_forgot_password_nonce'); ?>
                    
                    <div class="auth-input-group">
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            class="auth-input" 
                            placeholder="Adresse mail du compte"
                            required
                            autocomplete="email"
                        />
                    </div>
                    
                    <button type="submit" class="auth-submit-btn">Envoyer le lien</button>
                </form>
                
                <p class="auth-alt-link">
                    <a href="<?php echo esc_url(home_url('/connexion')); ?>">Retour a la connexion</a>
                </p>
            </div>
        </div>
    </main>

<?php get_footer(); ?>
