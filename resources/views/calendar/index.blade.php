@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-2 sm:py-4 lg:py-6">
    <div class="mb-3 sm:mb-4 lg:mb-6 flex items-center justify-between">
        <h1 class="text-lg sm:text-xl lg:text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Calendar') }}
        </h1>
        <a href="{{ route('calendar.missed-events') }}" class="flex items-center gap-2 px-4 py-2 {{ $missedEventsCount > 0 ? 'bg-red-500 hover:bg-red-600' : 'bg-gray-500 hover:bg-gray-600' }} text-white rounded-lg transition-colors text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ __('Missed Events') }}</span>
            @if ($missedEventsCount > 0)
            <span class="bg-white/20 px-2 py-0.5 rounded-full text-xs">{{ $missedEventsCount }}</span>
            @endif
        </a>
    </div>

    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-2 sm:p-3 lg:p-6 overflow-x-auto -mx-2 sm:mx-0" style="scrollbar-width: thin; -webkit-overflow-scrolling: touch;">
        <div id="calendar" class="calendar-container" style="min-width: 600px;"></div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-2 sm:p-4" style="display: none; overflow-y: auto;">
    <div class="bg-white dark:bg-[#161615] rounded-xl shadow-2xl max-w-lg w-full border border-[#e3e3e0] dark:border-[#3E3E3A] transform transition-all my-auto">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                    <div id="eventIcon" class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center text-white shadow-lg flex-shrink-0">
                        <!-- Icon will be populated here -->
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] truncate">
                        {{ __('Event Details') }}</h3>
                </div>
                <button onclick="closeEventModal()" class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:bg-gray-100 dark:hover:bg-[#3E3E3A] rounded-lg transition-colors flex-shrink-0 ml-2">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="eventDetails" class="space-y-3 sm:space-y-4 text-[#1b1b18] dark:text-[#EDEDEC] max-h-[60vh] overflow-y-auto">
                <!-- Event details will be populated here -->
            </div>
            <div id="eventActions" class="mt-4 sm:mt-6 flex flex-col sm:flex-row gap-2 sm:gap-3">
                <!-- Action buttons will be populated here -->
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
    .calendar-container {
        min-height: 400px;
        width: 100%;
    }

    @media (max-width: 639px) {
        .calendar-container {
            min-width: 600px !important;
            width: 600px !important;
        }
    }

    @media (min-width: 640px) {
        .calendar-container {
            min-height: 500px;
            min-width: auto !important;
            width: 100% !important;
        }
    }

    @media (min-width: 1024px) {
        .calendar-container {
            min-height: 600px;
        }
    }

    /* Fix FullCalendar on mobile */
    .fc {
        width: 100% !important;
    }

    @media (max-width: 639px) {
        .fc {
            min-width: 600px !important;
            width: 600px !important;
        }
    }

    .fc-scroller {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    @media (max-width: 639px) {
        .fc-scroller {
            overflow-x: scroll !important;
        }
    }

    .fc-scroller::-webkit-scrollbar {
        height: 8px;
    }

    .fc-scroller::-webkit-scrollbar-track {
        background: var(--app-surface);
    }

    .fc-scroller::-webkit-scrollbar-thumb {
        background: var(--app-border);
        border-radius: 4px;
    }

    /* Custom scrollbar for mobile container */
    @media (max-width: 639px) {
        .overflow-x-auto::-webkit-scrollbar {
            height: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: var(--app-surface);
            border-radius: 5px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #9ca3af;
            border-radius: 5px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    }

    .fc {
        font-family: inherit;
    }

    .fc-header-toolbar {
        margin-bottom: 1rem !important;
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
        padding: 0.5rem 0 !important;
    }

    @media (min-width: 640px) {
        .fc-header-toolbar {
            margin-bottom: 1.5rem !important;
            padding: 0 !important;
        }
    }

    .fc-toolbar-chunk {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 0.25rem !important;
        align-items: center !important;
    }

    .fc-toolbar-title {
        font-size: 1rem !important;
        margin: 0 0.5rem !important;
    }

    @media (min-width: 640px) {
        .fc-toolbar-title {
            font-size: 1.5rem !important;
        }
    }

    .fc-button {
        background-color: var(--app-surface) !important;
        border-color: var(--app-border) !important;
        color: var(--app-text) !important;
        padding: 0.375rem 0.5rem !important;
        border-radius: 0.5rem !important;
        font-size: 0.75rem !important;
        white-space: nowrap !important;
        min-width: auto !important;
    }

    @media (min-width: 640px) {
        .fc-button {
            padding: 0.5rem 1rem !important;
            font-size: 0.875rem !important;
        }
    }

    .fc-button-group {
        display: flex !important;
        gap: 0.25rem !important;
    }

    .fc-button:hover {
        background-color: var(--app-surface-hover) !important;
    }

    .fc-button-primary:not(:disabled):active,
    .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: white !important;
    }

    .fc-daygrid-day {
        background-color: var(--app-surface) !important;
    }

    .fc-daygrid-day-top {
        color: var(--app-text) !important;
    }

    .fc-col-header-cell {
        background-color: var(--app-surface) !important;
        color: var(--app-text) !important;
        padding: 0.5rem 0.25rem !important;
        font-size: 0.75rem !important;
    }

    @media (min-width: 640px) {
        .fc-col-header-cell {
            padding: 0.75rem 0.5rem !important;
            font-size: 0.875rem !important;
        }
    }

    .fc-daygrid-day-number {
        color: var(--app-text) !important;
        padding: 0.25rem !important;
        font-size: 0.75rem !important;
    }

    @media (min-width: 640px) {
        .fc-daygrid-day-number {
            padding: 0.5rem !important;
            font-size: 0.875rem !important;
        }
    }

    @media (min-width: 1024px) {
        .fc-daygrid-day-number {
            font-size: 1rem !important;
        }
    }

    /* Fix table cells on mobile */
    .fc-daygrid-day {
        min-height: 60px !important;
    }

    @media (min-width: 640px) {
        .fc-daygrid-day {
            min-height: 80px !important;
        }
    }

    @media (min-width: 1024px) {
        .fc-daygrid-day {
            min-height: 100px !important;
        }
    }

    .fc-col-header-cell-cushion {
        padding: 0.25rem 0.125rem !important;
        font-size: 0.75rem !important;
    }

    @media (min-width: 640px) {
        .fc-col-header-cell-cushion {
            padding: 0.5rem 0.25rem !important;
            font-size: 0.875rem !important;
        }
    }

    .fc-daygrid-event {
        border-radius: 0.375rem !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.75rem !important;
        border: none !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s ease !important;
        margin: 1px 0 !important;
    }

    @media (min-width: 640px) {
        .fc-daygrid-event {
            border-radius: 0.5rem !important;
            padding: 0.375rem 0.625rem !important;
            font-size: 0.875rem !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
            margin: 2px 0 !important;
        }
    }

    .fc-daygrid-event:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
    }

    @media (min-width: 640px) {
        .fc-daygrid-event:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
        }
    }

    .fc-event-title {
        font-weight: 600 !important;
        line-height: 1.3 !important;
        font-size: 0.75rem !important;
    }

    @media (min-width: 640px) {
        .fc-event-title {
            line-height: 1.4 !important;
            font-size: 0.875rem !important;
        }
    }

    .fc-event-time {
        font-weight: 500 !important;
        opacity: 0.9 !important;
        font-size: 0.625rem !important;
    }

    @media (min-width: 640px) {
        .fc-event-time {
            font-size: 0.75rem !important;
        }
    }

    /* Custom event styles based on type */
    .fc-event[data-event-type="appointment"] {
        background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%) !important;
        color: white !important;
    }

    .fc-event[data-event-type="activity"] {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%) !important;
        color: white !important;
    }

    .fc-event[data-event-type="communication"] {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%) !important;
        color: white !important;
    }

    .fc-timegrid-event {
        border-radius: 0.5rem !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    .fc-list-event {
        border-radius: 0.5rem !important;
        margin: 0.25rem 0 !important;
        padding: 0.5rem !important;
    }

    .fc-today-button {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: white !important;
    }

    .fc-today-button:hover {
        background-color: #2563eb !important;
    }

    .fc-day-today {
        background-color: rgba(59, 130, 246, 0.1) !important;
    }

    .fc-daygrid-day-number {
        color: var(--app-text) !important;
    }

    .fc-scrollgrid {
        border-color: var(--app-border) !important;
    }

    .fc-scrollgrid-section-header td {
        border-color: var(--app-border) !important;
    }

    .fc-scrollgrid-section-body td {
        border-color: var(--app-border) !important;
    }

    .fc-event {
        cursor: pointer;
    }

</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
@if (app()->getLocale() === 'ar')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/ar.js"></script>
@endif
<script>
    // Ensure FullCalendar is loaded before using it
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar not loaded');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) {
            console.error('Calendar element not found');
            return;
        }

        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar library not loaded');
            return;
        }

        // Detect mobile
        const isMobile = window.innerWidth < 640;
        const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
        const moreText = 'more';

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridDay'
            , initialDate: new Date()
            , headerToolbar: {
                left: isMobile ? 'prev,next' : 'prev,next today'
                , center: 'title'
                , right: isMobile ? '' : 'dayGridMonth,timeGridWeek,timeGridDay'
            }
            , locale: '{{ app()->getLocale() }}'
            , direction: '{{ app()->getLocale() === '
            ar ' ? '
            rtl ' : '
            ltr ' }}'
            , height: 'auto'
            , aspectRatio: isMobile ? 1.1 : (isTablet ? 1.5 : 1.8)
            , firstDay: 1
            , weekNumbers: false
            , dayMaxEvents: isMobile ? 2 : 3
            , moreLinkClick: 'popover'
            , moreLinkContent: function(arg) {
                return '+' + arg.num + ' ' + moreText;
            }
            , events: function(fetchInfo, successCallback, failureCallback) {
                fetch('{{ route("
                        calendar.events ") }}?start=' + fetchInfo.startStr + '&end=' +
                        fetchInfo.endStr, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                                , 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            }
                        })
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        failureCallback(error);
                    });
            }
            , eventClick: function(info) {
                showEventDetails(info.event);
            }
            , eventContent: function(arg) {
                const props = arg.event.extendedProps;
                const type = props.type || 'appointment';
                const time = arg.timeText || '';
                const isMobile = window.innerWidth < 640;

                let icon = '';

                if (type === 'appointment') {
                    icon = '📅';
                } else if (type === 'activity') {
                    icon = '📋';
                } else if (type === 'communication') {
                    icon = '💬';
                }

                // On mobile, show only icon and truncated title
                if (isMobile) {
                    return {
                        html: `
                            <div class="fc-event-main-frame flex items-center gap-1">
                                <span class="text-xs">${icon}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="fc-event-title-container truncate">
                                        <div class="fc-event-title">${arg.event.title}</div>
                                    </div>
                                </div>
                            </div>
                        `
                    };
                }

                return {
                    html: `
                        <div class="fc-event-main-frame flex items-center gap-2">
                            <span class="text-sm">${icon}</span>
                            <div class="flex-1 min-w-0">
                                <div class="fc-event-title-container truncate">
                                    <div class="fc-event-title">${arg.event.title}</div>
                                </div>
                                ${time ? `<div class="fc-event-time text-xs opacity-90">${time}</div>` : ''}
                            </div>
                        </div>
                    `
                };
            }
            , eventDisplay: 'block'
            , height: 'auto'
            , contentHeight: 'auto'
        , });

        calendar.render();
        window.calendar = calendar;

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                calendar.updateSize();
                // Force re-render on mobile orientation change
                if (window.innerWidth < 640) {
                    calendar.render();
                }
            }, 250);
        });

        // Fix touch events on mobile
        if (isMobile) {
            calendarEl.addEventListener('touchstart', function(e) {
                // Allow touch scrolling
            }, {
                passive: true
            });
        }
    });

    function showEventDetails(event) {
        const props = event.extendedProps;
        const modal = document.getElementById('eventModal');
        const detailsEl = document.getElementById('eventDetails');
        const actionsEl = document.getElementById('eventActions');
        const iconEl = document.getElementById('eventIcon');

        const type = props.type || 'appointment';
        let icon = '';
        let iconBg = '';
        let iconSvg = '';

        if (type === 'appointment') {
            icon = '📅';
            iconBg = 'bg-gradient-to-br from-pink-500 to-pink-600';
            iconSvg =
                `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>`;
        } else if (type === 'activity') {
            icon = '📋';
            iconBg = 'bg-gradient-to-br from-purple-500 to-purple-600';
            iconSvg =
                `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>`;
        } else if (type === 'communication') {
            icon = '💬';
            iconBg = 'bg-gradient-to-br from-green-500 to-green-600';
            iconSvg =
                `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>`;
        }

        if (iconEl) {
            iconEl.className = `w-12 h-12 rounded-lg flex items-center justify-center text-white shadow-lg ${iconBg}`;
            iconEl.innerHTML = iconSvg;
        }

        const eventDate = new Date(event.start);
        const formattedDate = eventDate.toLocaleDateString('{{ app()->getLocale() }}', {
            weekday: 'long'
            , year: 'numeric'
            , month: 'long'
            , day: 'numeric'
        });
        const formattedTime = eventDate.toLocaleTimeString('{{ app()->getLocale() }}', {
            hour: '2-digit'
            , minute: '2-digit'
        });

        let html = `
            <div class="space-y-3 sm:space-y-4">
                <div class="pb-3 sm:pb-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h4 class="text-base sm:text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-2 break-words">${event.title}</h4>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-xs sm:text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="break-words">${formattedDate}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>${formattedTime}</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2 sm:space-y-3">
        `;

        if (props.customer) {
            html += `
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Customer') }}</div>
                        <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] truncate">${props.customer}</div>
                    </div>
                </div>
            `;
        }
        if (props.lead) {
            html += `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Lead') }}</div>
                        <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">${props.lead}</div>
                    </div>
                </div>
            `;
        }
        if (props.unit) {
            html += `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Unit') }}</div>
                        <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">${props.unit}</div>
                    </div>
                </div>
            `;
        }
        if (props.user) {
            html += `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Assigned To') }}</div>
                        <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">${props.user}</div>
                    </div>
                </div>
            `;
        }
        if (props.status) {
            const statusColors = {
                'scheduled': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                , 'completed': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'
                , 'cancelled': 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
            };
            const statusColor = statusColors[props.status] ||
                'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400';
            html += `
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${statusColor}">
                        ${props.status.charAt(0).toUpperCase() + props.status.slice(1)}
                    </span>
                </div>
            `;
        }
        if (props.activity_type || props.type) {
            const activityType = props.activity_type || props.type;
            html += `
                <div class="flex items-center gap-3">
                    <div class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Type') }}:</div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/30 text-[#1b1b18] dark:text-[#EDEDEC]">
                        ${activityType}
                    </span>
                </div>
            `;
        }
        if (props.notes || props.details) {
            html += `
                <div class="pt-3 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Notes') }}</div>
                    <div class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] bg-gray-50 dark:bg-[#0a0a0a] p-3 rounded-lg">
                        ${props.notes || props.details || '-'}
                    </div>
                </div>
            `;
        }

        html += `</div></div>`;

        detailsEl.innerHTML = html;

        actionsEl.innerHTML = '';
        if (props.canEdit) {
            let editUrl = '';
            if (event.extendedProps.type === 'appointment') {
                editUrl = `{{ url('/appointments') }}/${event.extendedProps.resourceId}/edit`;
            } else if (event.extendedProps.type === 'activity') {
                editUrl = `{{ url('/leads') }}/${event.extendedProps.lead_id}/edit`;
            } else if (event.extendedProps.type === 'communication') {
                editUrl = `{{ url('/customer-communications') }}/${event.extendedProps.resourceId}/edit`;
            }

            if (editUrl) {
                actionsEl.innerHTML = `
                    <a href="${editUrl}" class="w-full sm:flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2.5 rounded-lg text-center transition-all shadow-lg shadow-blue-500/30 font-medium flex items-center justify-center gap-2 text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Edit') }}
                    </a>
                `;
            }
        }

        actionsEl.innerHTML += `
            <button onclick="closeEventModal()" class="w-full sm:flex-1 bg-gray-100 dark:bg-[#3E3E3A] hover:bg-gray-200 dark:hover:bg-[#4E4E4A] text-[#1b1b18] dark:text-[#EDEDEC] px-4 py-2.5 rounded-lg transition-colors font-medium text-sm sm:text-base">
                {{ __('Close') }}
            </button>
        `;

        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    }

    function closeEventModal() {
        const modal = document.getElementById('eventModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    // Close modal on outside click
    document.getElementById('eventModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEventModal();
        }
    });

</script>
@endpush
@endsection
