/**
 * Reglages > Compte : panneau "Informations de connexion" repliable
 * et affichage/masquage des champs mot de passe (oeil)
 */

document.addEventListener('DOMContentLoaded', function () {
    // Panneau repliable
    var toggle = document.getElementById('account-info-toggle');
    var panel = document.getElementById('account-info-panel');

    if (toggle && panel) {
        toggle.addEventListener('click', function () {
            var isHidden = panel.style.display === 'none';
            panel.style.display = isHidden ? 'block' : 'none';
            toggle.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            toggle.classList.toggle('is-open', isHidden);
        });
    }

    // Boutons oeil : basculer type password <-> text
    document.querySelectorAll('.password-eye-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;

            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.classList.toggle('is-visible', show);
        });
    });
});
