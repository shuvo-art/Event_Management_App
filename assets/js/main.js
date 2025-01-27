// Event search functionality
document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const eventsList = document.getElementById('eventsList');
    const globalSearchInput = document.getElementById('globalSearch');
    const searchResultsDiv = document.getElementById('searchResults');

    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            searchEvents(searchInput.value);
        });
    }

    if (searchInput) {
        // Real-time search functionality
        searchInput.addEventListener('keyup', function () {
            const searchTerm = searchInput.value.trim();
            fetch(`${SITE_URL}/api/events.php?search=${encodeURIComponent(searchTerm)}`)
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 'success') {
                        renderEvents(data.data);
                    } else {
                        console.error('Error fetching events:', data.message);
                    }
                })
                .catch((error) => console.error('Error:', error));
        });
    }

    if (globalSearchInput) {
        globalSearchInput.addEventListener('input', function () {
            const searchTerm = globalSearchInput.value.trim();
            if (searchTerm.length > 2) {
                fetch(`${SITE_URL}/api/search.php?term=${encodeURIComponent(searchTerm)}`)
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.status === 'success') {
                            renderSearchResults(data.events, data.attendees);
                        } else {
                            searchResultsDiv.innerHTML = '<p>No results found.</p>';
                        }
                    })
                    .catch((error) => console.error('Error:', error));
            } else {
                searchResultsDiv.innerHTML = '';
            }
        });
    }

    document.body.addEventListener('click', function (e) {
        if (e.target.classList.contains('register-btn')) {
            const eventId = e.target.getAttribute('data-event-id');
            if (eventId) {
                fetch(`${SITE_URL}/api/register.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        event_id: eventId,
                        csrf_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.status === 'success') {
                            Swal.fire('Success', 'Registered successfully!', 'success').then(() => {
                                location.reload(); // Reload the page to update the capacity
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch((error) => Swal.fire('Error', error.message, 'error'));
            }
        }
    });

    function searchEvents(term) {
        fetch(`${SITE_URL}/api/events.php?search=${encodeURIComponent(term)}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.status === 'success') {
                    renderEvents(data.data);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to search events', 'error');
            });
    }

    function renderEvents(events) {
        if (!eventsList) return;

        const eventsHtml = events
            .map(
                (event) => `
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${event.title}</h5>
                        <p class="card-text">${event.description}</p>
                        <p class="card-text">
                            <small class="text-muted">
                                ${new Date(event.date).toLocaleDateString()} at ${event.location}
                            </small>
                        </p>
                        <button class="btn btn-primary register-btn" 
                                data-event-id="${event.id}"
                                ${event.registration_count >= event.capacity ? 'disabled' : ''}>
                            ${event.registration_count >= event.capacity ? 'Full' : 'Register'}
                        </button>
                    </div>
                </div>
            </div>
        `
            )
            .join('');

        eventsList.innerHTML = eventsHtml;

        // Add event listeners for registration buttons
        document.querySelectorAll('.register-btn').forEach((button) => {
            button.addEventListener('click', function () {
                registerForEvent(this.dataset.eventId);
            });
        });
    }

    function renderSearchResults(events, attendees) {
        let html = '';

        if (events.length > 0) {
            html += '<h4>Events</h4><ul>';
            events.forEach((event) => {
                html += `<li>${event.title} (${event.date})</li>`;
            });
            html += '</ul>';
        }

        if (attendees.length > 0) {
            html += '<h4>Attendees</h4><ul>';
            attendees.forEach((attendee) => {
                html += `<li>${attendee.name} - ${attendee.email} (${attendee.event_title})</li>`;
            });
            html += '</ul>';
        }

        searchResultsDiv.innerHTML = html || '<p>No results found.</p>';
    }
});

// Event registration functionality
function registerForEvent(eventId) {
    fetch(`${SITE_URL}/api/register.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            event_id: eventId,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status === 'success') {
                Swal.fire('Success', 'Successfully registered for the event', 'success').then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Failed to register for event', 'error');
        });
}

// Form validation
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach((form) => {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    });
});
