import './bootstrap';

import Chart from 'chart.js/auto';
window.Chart = Chart;

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import trLocale from '@fullcalendar/core/locales/tr';

window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    interactionPlugin,
    listPlugin,
    trLocale
};

import Gantt from 'frappe-gantt';
window.Gantt = Gantt;
