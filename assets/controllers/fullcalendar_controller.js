// assets/controllers/fullcalendar_controller.js

import { Controller } from '@hotwired/stimulus';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction'; // pour permettre l'interaction

export default class extends Controller {
    connect() {
        let calendarEl = document.getElementById('calendar');

        new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            events: '/path/to/your/api/for/events', // ici tu dois indiquer la source des événements
        }).render();
    }
}
