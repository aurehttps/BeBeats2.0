document.addEventListener('DOMContentLoaded', function() {
    const cards = Array.from(document.querySelectorAll('.event-card'));
    const emptyHint = document.getElementById('event-empty-hint');
    const filterGroups = Array.from(document.querySelectorAll('.event-filter-group'));

    const state = {
        date: '',
        lieu: '',
        priceMin: 0,
        priceMax: null,
        genre: ''
    };

    function applyFilters() {
        let visibleCount = 0;

        cards.forEach(function(card) {
            const cardDate = card.getAttribute('data-date') || '';
            const cardLieu = (card.getAttribute('data-lieu') || '').toLowerCase();
            const cardPrice = parseFloat(card.getAttribute('data-price') || '0');
            const cardGenre = card.getAttribute('data-genre') || '';

            let visible = true;

            if (state.date && cardDate !== state.date) {
                visible = false;
            }

            if (state.lieu && cardLieu.indexOf(state.lieu.toLowerCase()) === -1) {
                visible = false;
            }

            if (state.priceMax !== null && cardPrice > state.priceMax) {
                visible = false;
            }

            if (cardPrice < state.priceMin) {
                visible = false;
            }

            if (state.genre && cardGenre !== state.genre) {
                visible = false;
            }

            card.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (emptyHint) {
            emptyHint.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    filterGroups.forEach(function(group) {
        const toggle = group.querySelector('[data-filter-toggle]');
        const popover = group.querySelector('.filter-popover');

        if (toggle && popover) {
            toggle.addEventListener('click', function() {
                const isOpen = popover.classList.contains('is-open');
                filterGroups.forEach(function(other) {
                    const otherPopover = other.querySelector('.filter-popover');
                    if (otherPopover) otherPopover.classList.remove('is-open');
                });
                popover.classList.toggle('is-open', !isOpen);
            });
        }

        const clearBtn = group.querySelector('[data-filter-clear]');
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const kind = clearBtn.getAttribute('data-filter-clear');
                if (kind === 'date') {
                    state.date = '';
                    const input = group.querySelector('[data-filter-role="date"]');
                    if (input) input.value = '';
                } else if (kind === 'lieu') {
                    state.lieu = '';
                    const input = group.querySelector('[data-filter-role="lieu"]');
                    if (input) input.value = '';
                } else if (kind === 'prix') {
                    state.priceMin = 0;
                    state.priceMax = null;
                    const minInput = group.querySelector('[data-filter-role="price-min"]');
                    const maxInput = group.querySelector('[data-filter-role="price-max"]');
                    if (minInput) {
                        minInput.value = minInput.min;
                        updatePriceLabels(group);
                    }
                    if (maxInput) {
                        maxInput.value = maxInput.max;
                        updatePriceLabels(group);
                    }
                } else if (kind === 'musique') {
                    state.genre = '';
                    const select = group.querySelector('[data-filter-role="genre"]');
                    if (select) select.value = '';
                }
                applyFilters();
            });
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.event-filter-group')) {
            filterGroups.forEach(function(group) {
                const popover = group.querySelector('.filter-popover');
                if (popover) popover.classList.remove('is-open');
            });
        }
    });

    const dateInput = document.querySelector('[data-filter-role="date"]');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            state.date = dateInput.value;
            applyFilters();
        });
    }

    const lieuInput = document.querySelector('[data-filter-role="lieu"]');
    if (lieuInput) {
        lieuInput.addEventListener('input', function() {
            state.lieu = lieuInput.value.trim();
            applyFilters();
        });
    }

    function updatePriceLabels(group) {
        const minInput = group.querySelector('[data-filter-role="price-min"]');
        const maxInput = group.querySelector('[data-filter-role="price-max"]');
        const minValueLabel = document.getElementById('filter-price-min-value');
        const maxValueLabel = document.getElementById('filter-price-max-value');

        let minVal = parseInt(minInput.value, 10) || 0;
        let maxVal = parseInt(maxInput.value, 10) || 0;
        if (minVal > maxVal) {
            const tmp = minVal;
            minVal = maxVal;
            maxVal = tmp;
            minInput.value = minVal;
            maxInput.value = maxVal;
        }

        if (minValueLabel) minValueLabel.textContent = minVal + ' \u20ac';
        if (maxValueLabel) maxValueLabel.textContent = maxVal + ' \u20ac';

        state.priceMin = minVal;
        const maxLimit = parseInt(maxInput.max, 10);
        state.priceMax = maxVal >= maxLimit ? null : maxVal;
    }

    const priceGroup = document.querySelector('[data-filter-group="prix"]');
    if (priceGroup) {
        priceGroup.querySelectorAll('.filter-range').forEach(function(range) {
            range.addEventListener('input', function() {
                updatePriceLabels(priceGroup);
                applyFilters();
            });
        });
    }

    const genreSelect = document.querySelector('[data-filter-role="genre"]');
    if (genreSelect) {
        genreSelect.addEventListener('change', function() {
            state.genre = genreSelect.value;
            applyFilters();
        });
    }

    const modal = document.getElementById('event-detail-modal');
    const modalBackdrop = document.getElementById('event-detail-backdrop');
    const modalClose = document.getElementById('event-detail-close');
    const calendarBtn = document.getElementById('event-detail-calendar-btn');
    let currentEvent = null;

    function closeModal() {
        if (modal) modal.style.display = 'none';
        currentEvent = null;
        document.body.style.overflow = '';
    }

    if (modalBackdrop) modalBackdrop.addEventListener('click', closeModal);
    if (modalClose) modalClose.addEventListener('click', closeModal);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display !== 'none') {
            closeModal();
        }
    });

    cards.forEach(function(card) {
        card.addEventListener('click', function() {
            currentEvent = {
                title: card.getAttribute('data-title') || '',
                artist: card.getAttribute('data-artist') || '',
                description: card.getAttribute('data-description') || '',
                image: card.getAttribute('data-image') || '',
                date: card.getAttribute('data-date') || '',
                dateLabel: card.getAttribute('data-date-label') || '',
                lieu: card.getAttribute('data-lieu') || '',
                priceLabel: card.getAttribute('data-price-label') || '',
                genre: card.getAttribute('data-genre') || ''
            };

            const imageBox = document.getElementById('event-detail-image');
            if (imageBox) {
                imageBox.textContent = '';
                if (currentEvent.image) {
                    const img = document.createElement('img');
                    img.src = currentEvent.image;
                    img.alt = currentEvent.title;
                    imageBox.appendChild(img);
                }
            }

            document.getElementById('event-detail-title').textContent = currentEvent.title;
            document.getElementById('event-detail-artist').textContent = currentEvent.artist ? 'par ' + currentEvent.artist : '';
            document.getElementById('event-detail-description').textContent = currentEvent.description;
            document.getElementById('event-detail-date').textContent = currentEvent.dateLabel;
            document.getElementById('event-detail-lieu').textContent = currentEvent.lieu;
            document.getElementById('event-detail-prix').textContent = currentEvent.priceLabel;
            document.getElementById('event-detail-genre').textContent = currentEvent.genre;

            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });

    function pad(number) {
        return number < 10 ? '0' + number : '' + number;
    }

    function buildIcsDate(dateStr) {
        return dateStr.replace(/-/g, '');
    }

    if (calendarBtn) {
        calendarBtn.addEventListener('click', function() {
            if (!currentEvent) return;

            const dateStr = currentEvent.date || '';
            const icsLines = [
                'BEGIN:VCALENDAR',
                'VERSION:2.0',
                'PRODID:-//BeBeats//Event//FR',
                'BEGIN:VEVENT',
                'UID:' + Date.now() + '-bebeats@bebeats',
                'DTSTAMP:' + buildIcsDate(new Date().toISOString().slice(0, 10)) + 'T' + pad(new Date().getUTCHours()) + pad(new Date().getUTCMinutes()) + pad(new Date().getUTCSeconds()) + 'Z'
            ];

            if (dateStr) {
                icsLines.push('DTSTART;VALUE=DATE:' + buildIcsDate(dateStr));
                const endDate = new Date(dateStr + 'T00:00:00');
                endDate.setDate(endDate.getDate() + 1);
                const endStr = endDate.toISOString().slice(0, 10);
                icsLines.push('DTEND;VALUE=DATE:' + buildIcsDate(endStr));
            }

            icsLines.push('SUMMARY:' + (currentEvent.title || 'Événement BeBeats'));
            if (currentEvent.description) {
                icsLines.push('DESCRIPTION:' + currentEvent.description.replace(/\r?\n/g, '\\n'));
            }
            if (currentEvent.lieu) {
                icsLines.push('LOCATION:' + currentEvent.lieu);
            }
            icsLines.push('BEGIN:VALARM');
            icsLines.push('TRIGGER:-PT1H');
            icsLines.push('ACTION:DISPLAY');
            icsLines.push('DESCRIPTION:Rappel BeBeats : ' + (currentEvent.title || 'Événement'));
            icsLines.push('END:VALARM');
            icsLines.push('END:VEVENT');
            icsLines.push('END:VCALENDAR');

            const blob = new Blob([icsLines.join('\r\n')], { type: 'text/calendar;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = (currentEvent.title || 'evenement-bebeats').replace(/[^a-z0-9]+/gi, '-').toLowerCase() + '.ics';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        });
    }
});
