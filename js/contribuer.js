// Gestion de la page Contribuer

// Variables globales pour les utilisateurs identifiés
let taggedUsers = [];

// Fonction globale pour mettre à jour l'affichage des utilisateurs identifiés
function updateTaggedUsersDisplay() {
    const container = document.getElementById('tagged-users');
    const list = document.getElementById('tagged-list');
    
    if (!container || !list) return;
    
    if (taggedUsers.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    list.innerHTML = '';
    
    taggedUsers.forEach(function(user) {
        const tagItem = document.createElement('div');
        tagItem.className = 'tagged-user-item';
        tagItem.innerHTML = `
            <img src="${user.profile_photo}" alt="${user.display_name}" class="tagged-user-avatar">
            <span class="tagged-user-name">${user.display_name}</span>
            <button type="button" class="tagged-user-remove" data-user-id="${user.id}">×</button>
        `;
        
        const removeBtn = tagItem.querySelector('.tagged-user-remove');
        removeBtn.addEventListener('click', function() {
            taggedUsers = taggedUsers.filter(u => u.id !== user.id);
            updateTaggedUsersDisplay();
        });
        
        list.appendChild(tagItem);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    
    // Faire disparaître automatiquement le message de succès après 5 secondes
    const successMessage = document.querySelector('.contribuer-success-message');
    if (successMessage) {
        
        // S'assurer que le message est visible
        successMessage.style.display = 'flex';
        successMessage.style.visibility = 'visible';
        successMessage.style.opacity = '1';
        
        // Scroller vers le message pour s'assurer qu'il est visible
        successMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // Réinitialiser le formulaire après publication réussie
        const contribuerForm = document.getElementById('contribuer-form');
        if (contribuerForm) {
            contribuerForm.reset();
            // Réinitialiser l'aperçu du média
            const mediaPreview = document.getElementById('media-preview');
            if (mediaPreview) {
                mediaPreview.innerHTML = `
                    <div class="media-placeholder">
                        <svg class="media-placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="media-placeholder-text">Ajoutez une image ou une vidéo</p>
                    </div>
                `;
                mediaPreview.classList.remove('has-media');
            }
            // Réinitialiser les utilisateurs identifiés
            taggedUsers = [];
            updateTaggedUsersDisplay();
        }
        
        // Faire disparaître le message après 5 secondes
        setTimeout(function() {
            successMessage.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
            successMessage.style.opacity = '0';
            successMessage.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                successMessage.remove();
            }, 500);
        }, 5000);
    } else {
    }
    
    const mediaInput = document.getElementById('media-input');
    const contribuerForm = document.getElementById('contribuer-form');
    const mediaPreview = document.getElementById('media-preview');
    
    
    // Vérifier aussi le textarea
    const postContentCheck = document.getElementById('post-content');
    if (postContentCheck) {
    }
    
    // Réinitialiser les utilisateurs identifiés au chargement
    taggedUsers = [];
    
    // Gestion de l'upload de média
    if (mediaInput && mediaPreview) {
        mediaInput.setAttribute('accept', 'image/*,video/*');
        mediaInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    
                    // Supprimer le placeholder
                    const placeholder = mediaPreview.querySelector('.media-placeholder');
                    if (placeholder) {
                        placeholder.remove();
                    }
                    
                    // Vider la zone (au cas où il y aurait déjà du contenu)
                    const existingMedia = mediaPreview.querySelector('img, video');
                    if (existingMedia) {
                        existingMedia.remove();
                    }
                    
                    // Ajouter une classe pour indiquer qu'un média est présent
                    mediaPreview.classList.add('has-media');
                    
                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Post media';
                        img.className = 'media-preview-image';
                        img.style.display = 'block';
                        img.style.visibility = 'visible';
                        img.style.opacity = '1';
                        mediaPreview.appendChild(img);
                    } else if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = e.target.result;
                        video.controls = true;
                        video.className = 'media-preview-video';
                        video.style.display = 'block';
                        video.style.visibility = 'visible';
                        video.style.opacity = '1';
                        mediaPreview.appendChild(video);
                    }
                };
                reader.onerror = function(error) {
                    console.error('Erreur lors de la lecture du fichier:', error);
                };
                reader.readAsDataURL(file);
            } else {
                // Si aucun fichier, réinitialiser l'aperçu
                const existingMedia = mediaPreview.querySelector('img, video');
                if (existingMedia) {
                    existingMedia.remove();
                }
                mediaPreview.classList.remove('has-media');
                // Réafficher le placeholder s'il n'existe pas
                if (!mediaPreview.querySelector('.media-placeholder')) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'media-placeholder';
                    placeholder.innerHTML = `
                        <svg class="media-placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="media-placeholder-text">Ajoutez une image ou une vidéo</p>
                    `;
                    mediaPreview.appendChild(placeholder);
                }
            }
        });
    }
    
    // Gestion du bouton "Identifier une personne"
    const tagUserBtn = document.getElementById('tag-user-btn');
    const tagUserModal = document.getElementById('tag-user-modal');
    const tagUserModalClose = document.getElementById('tag-user-modal-close');
    const tagUserInput = document.getElementById('tag-user-input');
    const userSuggestions = document.getElementById('user-suggestions');
    let searchTimeout;
    
    if (tagUserBtn && tagUserModal) {
        tagUserBtn.addEventListener('click', function(e) {
            e.preventDefault();
            tagUserModal.style.display = 'flex';
            if (tagUserInput) {
                setTimeout(() => tagUserInput.focus(), 100);
            }
        });
    }
    
    if (tagUserModalClose) {
        tagUserModalClose.addEventListener('click', function() {
            tagUserModal.style.display = 'none';
            if (tagUserInput) tagUserInput.value = '';
            if (userSuggestions) userSuggestions.style.display = 'none';
        });
    }
    
    // Fermer le modal en cliquant à l'extérieur
    if (tagUserModal) {
        tagUserModal.addEventListener('click', function(e) {
            if (e.target === tagUserModal) {
                tagUserModal.style.display = 'none';
                if (tagUserInput) tagUserInput.value = '';
                if (userSuggestions) userSuggestions.style.display = 'none';
            }
        });
    }
    
    // Gestion de l'autocomplete pour identifier des personnes
    if (tagUserInput && userSuggestions) {
        tagUserInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (searchTerm.length < 2) {
                userSuggestions.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(function() {
                if (typeof bebeatsContribuer === 'undefined') {
                    console.error('bebeatsContribuer n\'est pas défini');
                    return;
                }
                
                jQuery.ajax({
                    url: bebeatsContribuer.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'bebeats_search_users',
                        search: searchTerm,
                        nonce: bebeatsContribuer.nonce
                    },
                    success: function(response) {
                        if (response.success && response.data.users) {
                            displayUserSuggestions(response.data.users);
                        }
                    },
                    error: function() {
                        console.error('Erreur lors de la recherche d\'utilisateurs');
                    }
                });
            }, 300);
        });
    }
    
    // Fonction pour afficher les suggestions d'utilisateurs
    function displayUserSuggestions(users) {
        if (!userSuggestions) return;
        
        if (users.length === 0) {
            userSuggestions.style.display = 'none';
            return;
        }
        
        userSuggestions.innerHTML = '';
        
        users.forEach(function(user) {
            const userItem = document.createElement('div');
            userItem.className = 'user-suggestion-item';
            userItem.innerHTML = `
                <img src="${user.profile_photo}" alt="${user.display_name}" class="user-suggestion-avatar">
                <div class="user-suggestion-info">
                    <div class="user-suggestion-name">${user.display_name}</div>
                    <div class="user-suggestion-username">@${user.username}</div>
                </div>
            `;
            
            userItem.addEventListener('click', function() {
                addTaggedUser(user);
                if (tagUserInput) tagUserInput.value = '';
                userSuggestions.style.display = 'none';
            });
            
            userSuggestions.appendChild(userItem);
        });
        
        userSuggestions.style.display = 'block';
    }
    
    // Fonction pour ajouter un utilisateur identifié
    function addTaggedUser(user) {
        // Vérifier si l'utilisateur n'est pas déjà identifié
        const existing = taggedUsers.find(u => u.id === user.id);
        if (existing) return;
        
        taggedUsers.push(user);
        updateTaggedUsersDisplay();
        
        // Fermer le modal
        if (tagUserModal) tagUserModal.style.display = 'none';
        if (tagUserInput) tagUserInput.value = '';
    }
    
    // Gestion du bouton "Contenu multimédia" (label avec input file)
    const mediaBtn = document.querySelector('.menu-item-media');
    if (mediaBtn && mediaInput) {
        // S'assurer que le clic sur le label déclenche bien l'input
        mediaBtn.addEventListener('click', function(e) {
            // Si on clique sur le label (pas directement sur l'input), déclencher l'input
            if (e.target !== mediaInput && !mediaInput.contains(e.target)) {
                e.preventDefault();
                e.stopPropagation();
                mediaInput.click();
            }
        });
        
        // Également gérer le clic direct sur l'input (au cas où)
        mediaInput.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Gestion de la modal de catégorie
    const categoryModal = document.getElementById('category-modal');
    const categoryModalClose = document.getElementById('category-modal-close');
    const categoryBtn = document.getElementById('category-btn');
    const categoryRadioInputs = document.querySelectorAll('input[name="post_category"]');
    const postTypeInput = document.getElementById('post-type-input');
    const eventTitleInput = document.getElementById('event-title-input');
    
    // Fonction pour mettre à jour le champ caché avec la catégorie sélectionnée
    function updatePostType() {
        const selectedCategory = document.querySelector('input[name="post_category"]:checked');
        if (selectedCategory && postTypeInput) {
            const value = selectedCategory.value;
            postTypeInput.value = value;

            // Afficher le champ titre d'artiste uniquement pour les évènements
            if (eventTitleInput) {
                if (value === 'event') {
                    eventTitleInput.style.display = 'block';
                } else {
                    eventTitleInput.style.display = 'none';
                    eventTitleInput.value = '';
                }
            }

            // Gérer la possibilité d'ajouter du média :
            // - pour "post" (article texte), on masque le bouton et l'aperçu média
            // - pour les autres catégories, on les réaffiche
            const mediaBtn = document.querySelector('.menu-item-media');
            if (mediaBtn && mediaPreview) {
                if (value === 'post') {
                    // Masquer le bouton média
                    mediaBtn.style.display = 'none';

                    // Vider le fichier sélectionné
                    if (mediaInput) {
                        mediaInput.value = '';
                    }

                    // Réinitialiser l'aperçu média et le masquer
                    mediaPreview.innerHTML = '';
                    mediaPreview.classList.remove('has-media');
                    mediaPreview.style.display = 'none';
                } else {
                    // Réafficher le bouton média
                    mediaBtn.style.display = 'flex';

                    // Réafficher le conteneur d'aperçu (avec placeholder si vide)
                    if (mediaPreview.innerHTML.trim() === '') {
                        const placeholder = document.createElement('div');
                        placeholder.className = 'media-placeholder';
                        placeholder.innerHTML = `
                            <svg class="media-placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="media-placeholder-text">Ajoutez une image ou une vidéo</p>
                        `;
                        mediaPreview.appendChild(placeholder);
                    }
                    mediaPreview.style.display = 'block';
                }
            }
        }
    }
    
    if (categoryBtn && categoryModal) {
        categoryBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            categoryModal.style.display = 'flex';
        });
    }
    
    if (categoryModalClose) {
        categoryModalClose.addEventListener('click', function() {
            categoryModal.style.display = 'none';
        });
    }
    
    // Fermer le modal Catégorie en cliquant à l'extérieur
    if (categoryModal) {
        categoryModal.addEventListener('click', function(e) {
            if (e.target === categoryModal) {
                categoryModal.style.display = 'none';
            }
        });
    }
    
    // Écouter les changements de catégorie
    categoryRadioInputs.forEach(function(radio) {
        radio.addEventListener('change', function() {
            updatePostType();
        });
    });
    
    // Initialiser la valeur par défaut au chargement
    if (categoryRadioInputs.length > 0 && postTypeInput) {
        const defaultChecked = document.querySelector('input[name="post_category"]:checked');
        if (defaultChecked) {
            postTypeInput.value = defaultChecked.value;
        }
    }
    
    // Gestion du bouton "Options"
    const optionsBtn = document.getElementById('options-btn');
    const optionsModal = document.getElementById('options-modal');
    const optionsModalClose = document.getElementById('options-modal-close');
    
    if (optionsBtn && optionsModal) {
        optionsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            optionsModal.style.display = 'flex';
        });
    }
    
    if (optionsModalClose) {
        optionsModalClose.addEventListener('click', function() {
            optionsModal.style.display = 'none';
        });
    }
    
    // Fermer le modal Options en cliquant à l'extérieur
    if (optionsModal) {
        optionsModal.addEventListener('click', function(e) {
            if (e.target === optionsModal) {
                optionsModal.style.display = 'none';
            }
        });
    }
    
    // Gestion des toggles d'options
    const toggleComments = document.getElementById('toggle-comments');
    const toggleRepost = document.getElementById('toggle-repost');
    const toggleLikes = document.getElementById('toggle-likes');
    
    const allowCommentsInput = document.getElementById('allow-comments-input');
    const allowRepostInput = document.getElementById('allow-repost-input');
    const showLikesInput = document.getElementById('show-likes-input');
    
    // Initialiser les valeurs par défaut (tous activés)
    if (toggleComments && allowCommentsInput) {
        toggleComments.addEventListener('change', function() {
            allowCommentsInput.value = this.checked ? '1' : '0';
        });
    }
    
    if (toggleRepost && allowRepostInput) {
        toggleRepost.addEventListener('change', function() {
            allowRepostInput.value = this.checked ? '1' : '0';
        });
    }
    
    if (toggleLikes && showLikesInput) {
        toggleLikes.addEventListener('change', function() {
            showLikesInput.value = this.checked ? '1' : '0';
        });
    }
    
    // Gestion des autres actions du menu latéral (exclure les boutons submit et le bouton Publier)
    const menuItems = document.querySelectorAll('.menu-item:not(#tag-user-btn):not(#category-btn):not(#options-btn):not(.menu-item-media):not(.menu-item-publish):not([type="submit"])');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Ne pas bloquer si c'est un bouton submit
            if (this.type === 'submit' || this.classList.contains('menu-item-publish')) {
                return;
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            const label = this.querySelector('.menu-label')?.textContent || this.getAttribute('aria-label');
            if (label) {
                handleMenuAction(label);
            }
        });
    });
    
    // S'assurer que le bouton "Publier" soumet bien le formulaire
    const publishBtn = document.querySelector('.menu-item-publish[type="submit"]');
    
    // Ajouter aussi un écouteur sur le bouton pour forcer la synchronisation des options
    // Utiliser plusieurs méthodes pour s'assurer que le clic est capturé
    if (publishBtn) {
        // Méthode 1: Écouteur avec capture (phase de capture)
        publishBtn.addEventListener('click', function(e) {
            // Synchroniser les options
            syncOptions();
        }, true);
        
        // Méthode 2: Écouteur normal (phase de bubbling)
        publishBtn.addEventListener('click', function(e) {
            // Synchroniser les options
            syncOptions();
        }, false);
        
        // Fonction pour synchroniser les options
        function syncOptions() {
            const allowCommentsInput = document.getElementById('allow-comments-input');
            const allowRepostInput = document.getElementById('allow-repost-input');
            const showLikesInput = document.getElementById('show-likes-input');
            
            if (allowCommentsInput) {
                const toggleComments = document.getElementById('toggle-comments');
                if (toggleComments) {
                    allowCommentsInput.value = toggleComments.checked ? '1' : '0';
                }
            }
            
            if (allowRepostInput) {
                const toggleRepost = document.getElementById('toggle-repost');
                if (toggleRepost) {
                    allowRepostInput.value = toggleRepost.checked ? '1' : '0';
                }
            }
            
            if (showLikesInput) {
                const toggleLikes = document.getElementById('toggle-likes');
                if (toggleLikes) {
                    showLikesInput.value = toggleLikes.checked ? '1' : '0';
                }
            }
        }
    }
    
    // Gestion de la soumission du formulaire
    if (contribuerForm) {
        
        // Ajouter l'écouteur en phase de capture ET en phase de bubbling
        contribuerForm.addEventListener('submit', function(e) {
            handleFormSubmit(e);
        }, true);
        
        contribuerForm.addEventListener('submit', function(e) {
            handleFormSubmit(e);
        }, false);
        
        function handleFormSubmit(e) {
            
            const postContent = document.getElementById('post-content');
            const content = postContent ? postContent.value : '';
            
            // Vérifier qu'il y a du contenu ou un média
            const hasMedia = mediaInput && mediaInput.files && mediaInput.files.length > 0;
            
            
            if (!content.trim() && !hasMedia) {
                e.preventDefault();
                e.stopPropagation();
                alert('Veuillez ajouter du contenu ou un média.');
                return false;
            }
            
            
            // S'assurer que les champs cachés sont bien remplis
            const allowCommentsInput = document.getElementById('allow-comments-input');
            const allowRepostInput = document.getElementById('allow-repost-input');
            const showLikesInput = document.getElementById('show-likes-input');
            
            if (allowCommentsInput) {
                const toggleComments = document.getElementById('toggle-comments');
                if (toggleComments) {
                    allowCommentsInput.value = toggleComments.checked ? '1' : '0';
                }
            }
            
            if (allowRepostInput) {
                const toggleRepost = document.getElementById('toggle-repost');
                if (toggleRepost) {
                    allowRepostInput.value = toggleRepost.checked ? '1' : '0';
                }
            }
            
            if (showLikesInput) {
                const toggleLikes = document.getElementById('toggle-likes');
                if (toggleLikes) {
                    showLikesInput.value = toggleLikes.checked ? '1' : '0';
                }
            }
            
            
            // Vérifier que le nonce est présent
            const nonceInput = contribuerForm.querySelector('input[name="bebeats_create_post_nonce"]');
            if (nonceInput) {
            }
            
            // Vérifier tous les champs cachés
            const allHiddenInputs = contribuerForm.querySelectorAll('input[type="hidden"]');
            allHiddenInputs.forEach(input => {
            });
            
            
            // Ne pas empêcher la soumission si tout est OK
            // Le formulaire va se soumettre normalement
        }
    } else {
        console.error('ERREUR: Formulaire non trouvé pour ajouter l\'écouteur submit!');
    }
    
    // Vérification finale
    
});

// Fonction pour gérer les actions du menu
function handleMenuAction(action) {
    switch(action) {
        case 'Options':
            // Le modal est maintenant géré directement par l'événement click sur le bouton
            break;
        case 'Contenu multimédia':
            // Déclencher le clic sur l'input file
            const mediaInput = document.getElementById('media-input');
            if (mediaInput) {
                mediaInput.click();
            }
            break;
        case 'Publier':
            // Le formulaire sera soumis automatiquement
            break;
    }
}

