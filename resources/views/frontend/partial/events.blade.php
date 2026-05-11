@php
    $today = \Carbon\Carbon::now();
@endphp

<div class="events-section">

    <h5 class="section-heading mb-3">
        <i class="fa fa-calendar me-1"></i> Event Calendar
    </h5>

    <div class="calendar-box">

        <div class="calendar-header">
            <button type="button" class="cal-nav" id="prevMonth">&#8249;</button>

            <select id="calMonth" class="cal-select"></select>
            <select id="calYear" class="cal-select"></select>

            <button type="button" class="cal-nav" id="nextMonth">&#8250;</button>
        </div>

        <div class="calendar-grid cal-days-header">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>

        <div class="calendar-grid cal-dates" id="calendarDates"></div>

    </div>
</div>

<style>
.section-heading {
    color: #1a3a6b;
    font-size: 18px;
    font-weight: 700;
}

.calendar-box {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
    background: #fff;
}

.calendar-header {
    background: #1a3a6b;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px;
}

.cal-nav {
    background: #fff;
    color: #1a3a6b;
    border: none;
    border-radius: 4px;
    width: 28px;
    height: 28px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
}

.cal-select {
    flex: 1;
    height: 28px;
    border: none;
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 13px;
    font-weight: 600;
    color: #1a3a6b;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.cal-days-header {
    background: #f0f4f8;
    border-bottom: 1px solid #dee2e6;
}

.cal-days-header div {
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    color: #666;
    padding: 6px 0;
}

.cal-dates {
    padding: 4px;
    gap: 2px;
}

.cal-date-cell {
    text-align: center;
    font-size: 13px;
    min-height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    cursor: pointer;
    border-radius: 10%;
}

 

.cal-date-cell.empty {
    cursor: default;
}

.cal-today {
    background: #1a3a6b;
    color: #fff;
    font-weight: 700;
}

.cal-selected {
    outline: 2px solid #c0392b;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const monthSelect = document.getElementById('calMonth');
    const yearSelect = document.getElementById('calYear');
    const datesBox = document.getElementById('calendarDates');

    const today = new Date();

    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    months.forEach((month, index) => {
        monthSelect.innerHTML += `<option value="${index}">${month}</option>`;
    });

    for (let y = 1900; y <= 2100; y++) {
        yearSelect.innerHTML += `<option value="${y}">${y}</option>`;
    }

    function renderCalendar(month, year) {
        datesBox.innerHTML = '';

        monthSelect.value = month;
        yearSelect.value = year;

        const firstDay = new Date(year, month, 1).getDay();
        const totalDays = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            datesBox.innerHTML += `<div class="cal-date-cell empty"></div>`;
        }

        for (let day = 1; day <= totalDays; day++) {
            const isToday =
                day === today.getDate() &&
                month === today.getMonth() &&
                year === today.getFullYear();

            datesBox.innerHTML += `
                <div class="cal-date-cell ${isToday ? 'cal-today' : ''}"
                     data-date="${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}">
                    ${day}
                </div>
            `;
        }

        document.querySelectorAll('.cal-date-cell[data-date]').forEach(cell => {
            cell.addEventListener('click', function () {
                document.querySelectorAll('.cal-selected').forEach(el => {
                    el.classList.remove('cal-selected');
                });

                this.classList.add('cal-selected');

                console.log('Selected date:', this.dataset.date);
            });
        });
    }

    document.getElementById('prevMonth').addEventListener('click', function () {
        currentMonth--;

        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }

        renderCalendar(currentMonth, currentYear);
    });

    document.getElementById('nextMonth').addEventListener('click', function () {
        currentMonth++;

        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }

        renderCalendar(currentMonth, currentYear);
    });

    monthSelect.addEventListener('change', function () {
        currentMonth = parseInt(this.value);
        renderCalendar(currentMonth, currentYear);
    });

    yearSelect.addEventListener('change', function () {
        currentYear = parseInt(this.value);
        renderCalendar(currentMonth, currentYear);
    });

    renderCalendar(currentMonth, currentYear);
});
</script>