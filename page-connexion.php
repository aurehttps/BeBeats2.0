<?php
/**
 * Template Name: Connexion Page
 * Template pour la page Connexion
 */

get_header(); ?>

    <!-- Main Content -->
    <main class="auth-content">
        <div class="auth-container">
            <div class="auth-frame">
                <div class="auth-progress-bar">
                    <div class="auth-progress-bar-fill" style="width: 50%;"></div>
                </div>
                <h1 class="auth-title">Connectes-toi à ton profil BeBeats</h1>
                
                <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
                    <div class="auth-error-message">
                        Identifiants incorrects : nous ne trouvons pas de compte lié à cette adresse mail/pseudo, ou le mot de passe ne correspond pas.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['reset_success']) && $_GET['reset_success'] == '1'): ?>
                    <div class="auth-success-message">
                        Ton mot de passe a bien été modifié. Connecte-toi avec le nouveau.
                    </div>
                <?php endif; ?>
                
                <form class="auth-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="bebeats_login">
                    <?php wp_nonce_field('bebeats_login_action', 'bebeats_login_nonce'); ?>
                    
                    <div class="auth-input-group">
                        <input 
                            type="text" 
                            name="username" 
                            id="username"
                            class="auth-input" 
                            placeholder="Entre une adresse mail ou une pseudo"
                            required
                            autocomplete="username"
                        />
                    </div>
                    
                    <div class="auth-input-group">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="auth-input" 
                            placeholder="Mot de passe"
                            required
                            autocomplete="current-password"
                        />
                    </div>
                    
                    <button type="submit" class="auth-submit-btn">Connexion</button>
                </form>
                
                <p class="auth-alt-link">
                    <a href="<?php echo esc_url(home_url('/mot-de-passe-oublie')); ?>">Mot de passe oublié ?</a>
                </p>
            </div>
        </div>
    </main>

<?php get_footer(); ?>

