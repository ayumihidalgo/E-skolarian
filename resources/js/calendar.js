const initCalendar = () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const handleSubmit = async (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        try {
            const response = await fetch('/events', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            if (response.ok) {
                const event = await response.json();
                calendar.addEvent(event);
                closeEventModal();
                form.reset();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };

    const openEventModal = (event = null) => {
        const modal = document.getElementById('eventModal');
        const form = document.getElementById('eventForm');
        
        if (event) {
            form.title.value = event.title;
            form.description.value = event.description || '';
            form.start_date.value = event.start.toISOString().slice(0, 16);
            form.end_date.value = event.end ? event.end.toISOString().slice(0, 16) : '';
        }
        
        modal.classList.remove('hidden');
    };

    const closeEventModal = () => {
        const modal = document.getElementById('eventModal');
        const form = document.getElementById('eventForm');
        form.reset();
        modal.classList.add('hidden');
    };

    window.openEventModal = openEventModal;
    window.closeEventModal = closeEventModal;
    document.getElementById('eventForm').addEventListener('submit', handleSubmit);
};

document.addEventListener('DOMContentLoaded', initCalendar);