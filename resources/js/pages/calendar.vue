<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'
import { Modal } from 'bootstrap'
const calendarRef = ref(null)

const currentYear = new Date().getFullYear()
const currentMonth = new Date().getMonth() + 1;

const filterType = ref('');
const filterYear = ref(currentYear);
const filterMonth = ref(currentMonth.toString().padStart(2, '0'));

const yearOptions = ref([])
for (let y = currentYear - 5; y <= currentYear + 5; y++) {
    yearOptions.value.push(y)
}

let modalInstance = null;
const showModal = async () => {
    const modalEl = document.getElementById('eventModal')
    if (!modalInstance) {
        modalInstance = new Modal(modalEl)
    }
    modalInstance.show()
}

const hideModal = () => {
    modalInstance?.hide()
}


const editingEvent = ref(null)
const form = ref({ title: '', description: '', type: 'task', start_time: '', end_time: '' })

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    events: [],
    dateClick: ({ dateStr }) => openCreateModal(dateStr),
    eventClick: ({ event }) => openEditModal(event),
    eventClassNames: (event) => getEventClass(event),
})

const fetchEvents = async () => {
    const { data } = await axios.get('/events')

    const events = data
        .filter(e => !filterType.value || e.type === filterType.value)
        .filter(e => {
            if (!filterYear.value) return true
            const eventYear = new Date(e.start_time).getFullYear()
            return eventYear === parseInt(filterYear.value)
        })
        .map(e => ({
            ...e,
            title: `${e.title} (${e.type})`,
            type: e.type,
            start: e.start_time,
            end: e.end_time,
            id: e.id
        }))

    calendarOptions.value = {
        ...calendarOptions.value,
        events
    }

    if (calendarRef.value && filterYear.value) {
        const calendarApi = calendarRef.value.getApi()
        calendarApi.gotoDate(`${filterYear.value}-${filterMonth.value}-01`)
    }

    /*calendarOptions.value.events = data
        .filter(e => !filterType.value || e.type === filterType.value)
        .filter(e => {
            if (!filterYear.value) return true
            const eventYear = new Date(e.start_time).getFullYear()
            return eventYear === parseInt(filterYear.value)
        })
        .map(e => ({
        ...e,
        title: `${e.title} (${e.type})`,
        type: e.type,
        start: e.start_time,
        end: e.end_time,
        id: e.id
    }))*/
}

const openBlankCreateModal = (dateStr) => {
    editingEvent.value = null
    form.value = { title: '', description: '', type: 'task', start_time: dateStr, end_time: dateStr }
    showModal()
}

const openCreateModal = (dateStr) => {
    editingEvent.value = null

    // Get current time
    const now = new Date()
    const hours = now.getHours().toString().padStart(2, '0')
    const minutes = now.getMinutes().toString().padStart(2, '0')

    // Merge dateStr from calendar with current time
    const start = `${dateStr}T${hours}:${minutes}`

    // Create Date object to calculate +1 hour end time
    const startDateObj = new Date(`${dateStr}T${hours}:${minutes}`)
    const endDateObj = new Date(startDateObj.getTime() + 60 * 60 * 1000)

    const endDateStr = `${endDateObj.getFullYear()}-${(endDateObj.getMonth() + 1).toString().padStart(2, '0')}-${endDateObj.getDate().toString().padStart(2, '0')}T${endDateObj.getHours().toString().padStart(2, '0')}:${endDateObj.getMinutes().toString().padStart(2, '0')}`


    form.value = { title: '', description: '', type: 'task', start_time: start, end_time: endDateStr }
    showModal()
}

const openEditModal = (event) => {
    editingEvent.value = event.id
    const found = calendarOptions.value.events.find(e => e.id == event.id)

    if (found) {
        const cleanTitle = found.title.replace(/\s*\(task\)|\(event\)|\(appointment\)/gi, '').trim()
        form.value = {
            ...found,
            title: cleanTitle, // set the cleaned title
        }
    }

    showModal()
}

const saveEvent = async () => {
    if (editingEvent.value) {
        await axios.put(`/events/${editingEvent.value}`, form.value)
    } else {
        await axios.post('/events', form.value)
    }
    hideModal()
    fetchEvents()
}

const deleteEvent = async () => {
    await axios.delete(`/events/${editingEvent.value}`)
    hideModal()
    fetchEvents()
}

const getEventClass = (event) => {
    if(event.event.extendedProps.type == 'event' || event.event.extendedProps.type == 'task'){
        const now = new Date();
        return event.event.start < now ? 'bg-red-500 text-white' : 'bg-green-500 text-white';
    }else{
        return '';
    }

};

onMounted(fetchEvents)
</script>


<template>
    <div>
        <div class="row mb-3">
            <div class="col-md-8">
                <button type="button" class="btn btn-primary" @click="openBlankCreateModal()">Add Event</button>
            </div>
            <div class="col-md-4 text-end">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control form-select" v-model="filterType" @change="fetchEvents">
                            <option value="">All</option>
                            <option value="task">Task</option>
                            <option value="event">Event</option>
                            <option value="appointment">Appointment</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-control form-select" v-model="filterYear" @change="fetchEvents">
                            <option v-for="year in yearOptions" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <FullCalendar ref="calendarRef" :options="calendarOptions" />

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editingEvent ? 'Edit Event' : 'Create Event' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <input v-model="form.title" class="form-control mb-2" placeholder="Title" />
                        <textarea v-model="form.description" class="form-control mb-2" placeholder="Description"></textarea>
                        <select v-model="form.type" class="form-select mb-2">
                            <option value="task">Task</option>
                            <option value="event">Event</option>
                            <option value="appointment">Appointment</option>
                        </select>
                        <input type="datetime-local" v-model="form.start_time" class="form-control mb-2" />
                        <input type="datetime-local" v-model="form.end_time" class="form-control mb-2" />
                    </div>
                    <div class="modal-footer">
                        <button @click="saveEvent" class="btn btn-success">Save</button>
                        <button v-if="editingEvent" @click="deleteEvent" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
