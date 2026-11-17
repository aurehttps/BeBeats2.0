/**
 * Suivi d'artistes + centre de notifications
 */

document.addEventListener('DOMContentLoaded', function () {
    initFollowButtons();
    initNotificationsBell();
});

/* ---------- Suivi d'artistes ---------- */

function initFollowButtons() {
    document.querySelectorAll('.bebeats-follow-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            toggleFollow(btn);
        });
    });

    document.querySelectorAll('.bebeats-notify-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            toggleFollowNotifications(btn);
        });
    });
}

function toggleFollow(btn) {
    if (btn.disabled) return;
    btn.disabled = true;

    var artistId = parseInt(btn.getAttribute('data-artist-id'), 10);
    if (!artistId || typeof bebeatsSocial === 'undefined') {
        btn.disabled = false;
        return;
    }

    jQuery.post(bebeatsSocial.ajaxUrl, {
        action: 'bebeats_toggle_follow',
        nonce: bebeatsSocial.followNonce,
        artist_id: artistId
    }).done(function (response) {
        if (response && response.success) {
            var following = response.data.following;
            btn.classList.toggle('is-following', following);
            btn.querySelector('.follow-label').textContent = following ? 'Suivi' : 'Suivre';

            var statsRow = document.querySelector('.profile-stats-row');
            if (statsRow && response.data.followers_count !== undefined) {
                var followerStat = statsRow.querySelector('.profile-stat');
                if (followerStat) {
                    followerStat.querySelector('.profile-stat-value').textContent = response.data.followers_count;
                    followerStat.querySelector('.profile-stat-label').textContent = response.data.followers_count > 1 ? 'abonnés' : 'abonné';
                }
            }

            var notifyBtn = document.querySelector('.bebeats-notify-btn');
            if (notifyBtn) {
                notifyBtn.classList.toggle('is-hidden', !following);
                notifyBtn.classList.toggle('is-active', following);
            }

            if (following) {
                var bell = document.querySelector('.notification-btn');
                if (bell) {
                    bell.classList.remove('is-hidden');
                    refreshUnreadBadge();
                }
            }
        }
    }).always(function () {
        btn.disabled = false;
    });
}

function toggleFollowNotifications(btn) {
    if (btn.disabled) return;
    btn.disabled = true;

    var artistId = parseInt(btn.getAttribute('data-artist-id'), 10);
    if (!artistId || typeof bebeatsSocial === 'undefined') {
        btn.disabled = false;
        return;
    }

    jQuery.post(bebeatsSocial.ajaxUrl, {
        action: 'bebeats_toggle_follow_notifications',
        nonce: bebeatsSocial.followNonce,
        artist_id: artistId
    }).done(function (response) {
        if (response && response.success) {
            btn.classList.toggle('is-active', response.data.notify);
        }
    }).always(function () {
        btn.disabled = false;
    });
}

/* ---------- Notifications ---------- */

function initNotificationsBell() {
    var bell = document.querySelector('.notification-btn');
    if (!bell) return;

    var dropdown = document.createElement('div');
    dropdown.className = 'bebeats-notifications-dropdown glassmorphism';
    dropdown.style.display = 'none';
    bell.parentNode.appendChild(dropdown);

    bell.addEventListener('click', function (e) {
        e.preventDefault();
        var isVisible = dropdown.style.display !== 'none';
        if (isVisible) {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'block';
            loadNotifications(dropdown);
        }
    });

    document.addEventListener('click', function (e) {
        if (dropdown.style.display === 'none') return;
        if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    if (typeof bebeatsSocial !== 'undefined' && bebeatsSocial.isLoggedIn) {
        refreshUnreadBadge();
    }
}

function refreshUnreadBadge() {
    jQuery.post(bebeatsSocial.ajaxUrl, {
        action: 'bebeats_get_notifications',
        nonce: bebeatsSocial.notificationsNonce
    }).done(function (response) {
        if (!response || !response.success) return;
        updateBadge(response.data.unread);
    });
}

function updateBadge(unread) {
    var bell = document.querySelector('.notification-btn');
    if (!bell) return;

    var badge = bell.querySelector('.notification-badge');
    if (unread > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'notification-badge';
            bell.appendChild(badge);
        }
        badge.textContent = unread > 99 ? '99+' : unread;
    } else if (badge) {
        badge.remove();
    }
}

function loadNotifications(dropdown) {
    dropdown.innerHTML = '<p class="notifications-loading">Chargement…</p>';

    jQuery.post(bebeatsSocial.ajaxUrl, {
        action: 'bebeats_get_notifications',
        nonce: bebeatsSocial.notificationsNonce
    }).done(function (response) {
        if (!response || !response.success) {
            dropdown.innerHTML = '<p class="notifications-empty">Impossible de charger les notifications.</p>';
            return;
        }

        var items = response.data.notifications || [];
        if (!items.length) {
            dropdown.innerHTML = '<p class="notifications-empty">Aucune notification pour le moment.</p>';
        } else {
            var list = document.createElement('ul');
            list.className = 'notifications-list';
            items.forEach(function (item) {
                var li = document.createElement('li');
                li.className = 'notification-item' + (item.is_read ? '' : ' is-unread');
                var a = document.createElement('a');
                a.href = item.link;
                a.innerHTML = '<span class="notification-message"></span><span class="notification-time"></span>';
                a.querySelector('.notification-message').textContent = item.message;
                a.querySelector('.notification-time').textContent = 'il y a ' + item.time;
                li.appendChild(a);
                list.appendChild(li);
            });
            dropdown.innerHTML = '';
            dropdown.appendChild(list);
        }

        updateBadge(response.data.unread);

        // Marquer comme lues une fois affichées
        if (response.data.unread > 0) {
            jQuery.post(bebeatsSocial.ajaxUrl, {
                action: 'bebeats_mark_notifications_read',
                nonce: bebeatsSocial.notificationsNonce
            }).done(function (markResponse) {
                if (markResponse && markResponse.success) {
                    updateBadge(0);
                }
            });
        }
    });
}
