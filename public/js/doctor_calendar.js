(() => {
  const STORAGE_KEY = 'doctor_calendar_events_v1';

  const state = {
    currentDate: new Date(),
    view: 'month', // month | week | day
    events: loadEvents(),
  };

  const calendarTitle = document.getElementById('calendarTitle');
  const calendarGrid = document.getElementById('calendarGrid');

  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const todayBtn = document.getElementById('todayBtn');
  const openModalBtn = document.getElementById('openModalBtn');
  const viewBtns = document.querySelectorAll('.view-btn');

  const eventModal = document.getElementById('eventModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const eventForm = document.getElementById('eventForm');
  const modalTitle = document.getElementById('modalTitle');
  const deleteEventBtn = document.getElementById('deleteEventBtn');
  const formError = document.getElementById('formError');

  const fId = document.getElementById('eventId');
  const fTitle = document.getElementById('eventTitle');
  const fType = document.getElementById('eventType');
  const fStatus = document.getElementById('eventStatus');
  const fDate = document.getElementById('eventDate');
  const fStart = document.getElementById('eventStart');
  const fEnd = document.getElementById('eventEnd');
  const fPatient = document.getElementById('eventPatient');
  const fNotes = document.getElementById('eventNotes');
  const patientRow = document.getElementById('patientRow');

  function loadEvents() {
    const stored = localStorage.getItem(STORAGE_KEY);
    const localEvents = stored ? JSON.parse(stored) : [];

    const manualEvents = localEvents.filter((e) => !e.id.toString().startsWith('db_'));

    const dbAppointments = window.DB_EVENTS || [];

    return [...manualEvents, ...dbAppointments];
  }

  function saveEvents() {
    const manualEvents = state.events.filter((e) => !e.id.toString().startsWith('db_'));
    localStorage.setItem(STORAGE_KEY, JSON.stringify(manualEvents));
  }
  function pad(n) {
    return String(n).padStart(2, '0');
  }

  function toDateKey(date) {
    const d = new Date(date);
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
  }

  function formatMonthTitle(date) {
    return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
  }

  function getWeekRange(date) {
    const d = new Date(date);
    const day = d.getDay();
    const diffToMonday = (day + 6) % 7;
    const start = new Date(d);
    start.setDate(d.getDate() - diffToMonday);
    const end = new Date(start);
    end.setDate(start.getDate() + 6);
    return { start, end };
  }

  function formatTimeRange(start, end) {
    return `${start} - ${end}`;
  }

  function getEventsByDate(dateKey) {
    return state.events
      .filter((e) => e.date === dateKey)
      .sort((a, b) => a.start.localeCompare(b.start));
  }

  function eventClass(event) {
    if (event.status === 'completed') return 'completed';
    return event.type === 'consultation' ? 'consultation' : 'task';
  }

  function togglePatientField() {
    const isConsultation = fType.value === 'consultation';
    patientRow.classList.toggle('hidden', !isConsultation);

    if (!isConsultation) {
      fPatient.value = '';
    }
  }

  function render() {
    viewBtns.forEach((btn) => btn.classList.toggle('active', btn.dataset.view === state.view));

    if (state.view === 'month') renderMonthView();
    if (state.view === 'week') renderWeekView();
    if (state.view === 'day') renderDayView();
  }

  function renderMonthView() {
    const date = new Date(state.currentDate);
    calendarTitle.textContent = formatMonthTitle(date);

    const year = date.getFullYear();
    const month = date.getMonth();

    const firstDay = new Date(year, month, 1);
    const startWeekDay = (firstDay.getDay() + 6) % 7; // Monday first
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const prevMonthDays = new Date(year, month, 0).getDate();
    const cells = [];

    for (let i = 0; i < startWeekDay; i++) {
      const day = prevMonthDays - startWeekDay + i + 1;
      cells.push({ date: new Date(year, month - 1, day), otherMonth: true });
    }

    for (let d = 1; d <= daysInMonth; d++) {
      cells.push({ date: new Date(year, month, d), otherMonth: false });
    }

    while (cells.length % 7 !== 0) {
      const day = cells.length - (startWeekDay + daysInMonth) + 1;
      cells.push({ date: new Date(year, month + 1, day), otherMonth: true });
    }

    const weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const wrapper = document.createElement('div');
    wrapper.className = 'month-grid';

    weekdays.forEach((w) => {
      const head = document.createElement('div');
      head.className = 'weekday';
      head.textContent = w;
      wrapper.appendChild(head);
    });

    cells.forEach((cell) => {
      const cellDiv = document.createElement('div');
      cellDiv.className = `day-cell ${cell.otherMonth ? 'other-month' : ''}`;
      const dateKey = toDateKey(cell.date);

      const num = document.createElement('div');
      num.className = 'day-number';
      num.textContent = cell.date.getDate();
      cellDiv.appendChild(num);

      const allDayEvents = getEventsByDate(dateKey);
      const dayEvents = allDayEvents.slice(0, 3);

      dayEvents.forEach((ev) => {
        const evBtn = document.createElement('button');
        evBtn.className = `event-pill ${eventClass(ev)}`;
        evBtn.textContent = `${ev.start} ${ev.title}`;
        evBtn.onclick = (e) => {
          e.stopPropagation();
          openEditModal(ev.id);
        };
        cellDiv.appendChild(evBtn);
      });

      if (allDayEvents.length > 3) {
        const more = document.createElement('small');
        more.textContent = `+${allDayEvents.length - 3} more`;
        more.style.color = '#6b7280';
        cellDiv.appendChild(more);
      }

      cellDiv.onclick = () => openAddModal(dateKey);
      wrapper.appendChild(cellDiv);
    });

    calendarGrid.innerHTML = '';
    calendarGrid.appendChild(wrapper);
  }

  function renderWeekView() {
    const { start, end } = getWeekRange(state.currentDate);
    calendarTitle.textContent =
      `${start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ` +
      `${end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;

    const container = document.createElement('div');
    container.className = 'week-columns';

    for (let i = 0; i < 7; i++) {
      const d = new Date(start);
      d.setDate(start.getDate() + i);
      const dateKey = toDateKey(d);

      const col = document.createElement('div');
      col.className = 'week-col';

      const head = document.createElement('div');
      head.className = 'week-col-header';
      head.textContent = d.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric' });
      col.appendChild(head);

      const events = getEventsByDate(dateKey);

      if (!events.length) {
        const empty = document.createElement('div');
        empty.className = 'slot-area';
        empty.style.color = '#9ca3af';
        empty.textContent = 'No events';
        empty.onclick = () => openAddModal(dateKey);
        col.appendChild(empty);
      } else {
        events.forEach((ev) => {
          const pill = document.createElement('button');
          pill.className = `event-pill ${eventClass(ev)}`;
          pill.style.margin = '6px';
          pill.textContent = `${formatTimeRange(ev.start, ev.end)} • ${ev.title}`;
          pill.onclick = () => openEditModal(ev.id);
          col.appendChild(pill);
        });

        const add = document.createElement('div');
        add.className = 'slot-area';
        add.style.fontSize = '0.85rem';
        add.style.color = '#2563eb';
        add.textContent = '+ Add event';
        add.onclick = () => openAddModal(dateKey);
        col.appendChild(add);
      }

      container.appendChild(col);
    }

    calendarGrid.innerHTML = '';
    calendarGrid.appendChild(container);
  }

  function renderDayView() {
    const d = new Date(state.currentDate);
    const dateKey = toDateKey(d);

    calendarTitle.textContent = d.toLocaleDateString('en-US', {
      weekday: 'long',
      month: 'long',
      day: 'numeric',
      year: 'numeric',
    });

    const wrapper = document.createElement('div');
    wrapper.className = 'card';
    wrapper.style.padding = '12px';

    const events = getEventsByDate(dateKey);

    const top = document.createElement('div');
    top.style.display = 'flex';
    top.style.justifyContent = 'space-between';
    top.style.alignItems = 'center';
    top.style.marginBottom = '8px';

    const count = document.createElement('strong');
    count.textContent = `${events.length} event(s)`;
    top.appendChild(count);

    const add = document.createElement('button');
    add.className = 'btn btn-success';
    add.textContent = '+ Add event';
    add.onclick = () => openAddModal(dateKey);
    top.appendChild(add);

    wrapper.appendChild(top);

    if (!events.length) {
      const empty = document.createElement('p');
      empty.textContent = 'No events for this day.';
      empty.style.color = '#6b7280';
      wrapper.appendChild(empty);
    } else {
      events.forEach((ev) => {
        const row = document.createElement('button');
        row.className = `event-pill ${eventClass(ev)}`;
        row.style.display = 'block';
        row.style.width = '100%';
        row.style.marginBottom = '8px';
        row.style.textAlign = 'left';
        row.textContent = `${ev.start} - ${ev.end} | ${ev.title}${ev.patient ? ` (Patient: ${ev.patient})` : ''}`;
        row.onclick = () => openEditModal(ev.id);
        wrapper.appendChild(row);
      });
    }

    calendarGrid.innerHTML = '';
    calendarGrid.appendChild(wrapper);
  }

  function openModal() {
    eventModal.classList.remove('hidden');
  }

  function closeModal() {
    eventModal.classList.add('hidden');
    formError.textContent = '';
    eventForm.reset();
    fId.value = '';
    deleteEventBtn.classList.add('hidden');
    fType.value = 'consultation';
    togglePatientField();
  }

  function openAddModal(dateKey) {
    modalTitle.textContent = 'Add Event';
    eventForm.reset();
    fId.value = '';
    fDate.value = dateKey || toDateKey(state.currentDate);
    fType.value = 'consultation';
    fStatus.value = 'planned';
    togglePatientField();
    deleteEventBtn.classList.add('hidden');
    openModal();
  }

  function openEditModal(id) {
    const ev = state.events.find((e) => e.id === id);
    if (!ev) return;

    modalTitle.textContent = 'Edit Event';
    fId.value = ev.id;
    fTitle.value = ev.title;
    fType.value = ev.type;
    fStatus.value = ev.status;
    fDate.value = ev.date;
    fStart.value = ev.start;
    fEnd.value = ev.end;
    fPatient.value = ev.patient || '';
    fNotes.value = ev.notes || '';

    togglePatientField();
    deleteEventBtn.classList.remove('hidden');
    openModal();
  }

  function toMinutes(timeStr) {
    const [h, m] = timeStr.split(':').map(Number);
    return h * 60 + m;
  }

  function hasConsultationConflict(candidate, ignoreId = null) {
    if (candidate.type !== 'consultation') return false;

    return state.events.some((ev) => {
      if (ignoreId && ev.id === ignoreId) return false;
      if (ev.type !== 'consultation') return false;
      if (ev.date !== candidate.date) return false;
      if (ev.status === 'cancelled') return false;

      const aStart = toMinutes(candidate.start);
      const aEnd = toMinutes(candidate.end);
      const bStart = toMinutes(ev.start);
      const bEnd = toMinutes(ev.end);

      return aStart < bEnd && bStart < aEnd;
    });
  }

  function validateEvent(data, editingId = null) {
    if (!data.title.trim()) return 'Title is required.';
    if (!data.date) return 'Date is required.';
    if (!data.start || !data.end) return 'Start and end times are required.';
    if (toMinutes(data.end) <= toMinutes(data.start)) return 'End time must be after start time.';
    if (data.type === 'consultation' && !data.patient.trim()) {
      return 'Patient name is required for consultations.';
    }
    if (hasConsultationConflict(data, editingId)) {
      return 'Consultation conflict detected: overlapping consultation at this time.';
    }
    return '';
  }

  eventForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const data = {
      id: fId.value || crypto.randomUUID(),
      title: fTitle.value.trim(),
      type: fType.value,
      status: fStatus.value,
      date: fDate.value,
      start: fStart.value,
      end: fEnd.value,
      patient: fPatient.value.trim(),
      notes: fNotes.value.trim(),
    };

    const err = validateEvent(data, fId.value || null);
    if (err) {
      formError.textContent = err;
      return;
    }

    if (fId.value) {
      const idx = state.events.findIndex((x) => x.id === fId.value);
      if (idx !== -1) state.events[idx] = data;
    } else {
      state.events.push(data);
    }

    saveEvents();
    closeModal();
    render();
  });

  deleteEventBtn.addEventListener('click', () => {
    if (!fId.value) return;
    if (!confirm('Delete this event?')) return;

    state.events = state.events.filter((e) => e.id !== fId.value);
    saveEvents();
    closeModal();
    render();
  });

  openModalBtn.addEventListener('click', () => openAddModal(toDateKey(state.currentDate)));
  closeModalBtn.addEventListener('click', closeModal);

  eventModal.addEventListener('click', (e) => {
    if (e.target === eventModal) closeModal();
  });

  prevBtn.addEventListener('click', () => {
    if (state.view === 'month') state.currentDate.setMonth(state.currentDate.getMonth() - 1);
    if (state.view === 'week') state.currentDate.setDate(state.currentDate.getDate() - 7);
    if (state.view === 'day') state.currentDate.setDate(state.currentDate.getDate() - 1);
    state.currentDate = new Date(state.currentDate);
    render();
  });

  nextBtn.addEventListener('click', () => {
    if (state.view === 'month') state.currentDate.setMonth(state.currentDate.getMonth() + 1);
    if (state.view === 'week') state.currentDate.setDate(state.currentDate.getDate() + 7);
    if (state.view === 'day') state.currentDate.setDate(state.currentDate.getDate() + 1);
    state.currentDate = new Date(state.currentDate);
    render();
  });

  todayBtn.addEventListener('click', () => {
    state.currentDate = new Date();
    render();
  });

  viewBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      state.view = btn.dataset.view;
      render();
    });
  });

  fType.addEventListener('change', togglePatientField);

  // initial
  togglePatientField();
  render();
})();
// Add inside renderMonthView(), just before cells.forEach(...)
const todayKey = toDateKey(new Date());

// Inside cells.forEach(...) after dateKey is created:
if (dateKey === todayKey) {
  cellDiv.style.border = '2px solid #3b82f6';
  cellDiv.style.animation = 'pulseToday 1.8s ease-in-out infinite';
}
