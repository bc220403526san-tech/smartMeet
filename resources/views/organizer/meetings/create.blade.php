<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen overflow-hidden">

    <label for="sidebar-toggle" class="sidebar-overlay"></label>
    <x-sidebar.organizer-menu />

    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">

        <x-header.page-title title="Organizer Dashboard" />

        {{-- Content Wrapper --}}
        <div class="flex-1 flex items-start justify-center
                    p-4 sm:p-6 bg-gray-50 rounded-lg mt-3 overflow-y-auto">

            <x-success />
            <x-error />

            <div class="mt-10 w-full max-w-4xl bg-white rounded-xl shadow p-4 sm:p-6
                        border border-l-6 border-gray-400 hover:border-blue-500
                        transition-all duration-300 ease-in-out hover:shadow-lg">

                <div class="mb-5">
                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Create New Meeting</h1>
                    <p class="text-sm text-gray-400 mt-1">Set up a new collaboration session with your team.</p>
                </div>

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('organizer.meetings.store') }}" method="POST" id="meeting-form">
                    @csrf

                    <!-- MEETING TITLE -->
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                            Meeting Title <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}"
                               placeholder="e.g. Q4 Strategy Sync"
                               class="w-full mt-1 px-4 py-2.5 bg-gray-100 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-300 focus:bg-white transition">
                    </div>

                    <!-- DATE + TIME + DURATION -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Date <span class="text-red-400">*</span>
                            </label>
                            <input type="date" name="date" value="{{ old('date') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full mt-1 px-4 py-2.5 bg-gray-100 rounded-lg text-sm
                                          focus:outline-none focus:ring-2 focus:ring-blue-300 focus:bg-white transition">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Time <span class="text-red-400">*</span>
                            </label>
                            <input type="time" name="time" value="{{ old('time') }}"
                                   class="w-full mt-1 px-4 py-2.5 bg-gray-100 rounded-lg text-sm
                                          focus:outline-none focus:ring-2 focus:ring-blue-300 focus:bg-white transition">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Duration <span class="text-red-400">*</span>
                            </label>
                            <select name="duration"
                                    class="w-full mt-1 px-4 py-2.5 bg-gray-100 rounded-lg text-sm
                                           focus:outline-none focus:ring-2 focus:ring-blue-300 focus:bg-white transition">
                                <option value="15"  {{ old('duration') == 15  ? 'selected' : '' }}>15 mins</option>
                                <option value="30"  {{ old('duration') == 30  ? 'selected' : '' }}>30 mins</option>
                                <option value="45"  {{ old('duration') == 45  ? 'selected' : '' }}>45 mins</option>
                                <option value="60"  {{ old('duration', 60) == 60 ? 'selected' : '' }}>1 hour</option>
                                <option value="90"  {{ old('duration') == 90  ? 'selected' : '' }}>1.5 hours</option>
                                <option value="120" {{ old('duration') == 120 ? 'selected' : '' }}>2 hours</option>
                            </select>
                        </div>
                    </div>
                    <!-- TIMEZONE -->
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                            Timezone <span class="text-red-400">*</span>
                        </label>
                        <select name="timezone"
                                class="w-full mt-1 px-4 py-2.5 bg-gray-100 rounded-lg text-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-300 focus:bg-white transition">
                            @foreach(\DateTimeZone::listIdentifiers() as $timezone)
                                <option value="{{ $timezone }}"
                                    {{ old('timezone', 'Asia/Karachi') == $timezone ? 'selected' : '' }}>
                                    {{ $timezone }} (UTC {{ now($timezone)->format('P') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PARTICIPANTS -->
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Participants</label>

                        {{-- Selected Chips Area --}}
                        <div id="chips-area"
                             class="mt-1 flex items-center gap-2 flex-wrap bg-gray-100 p-2 rounded-lg min-h-[44px]">
                            <button type="button" id="add-member-btn"
                                    class="text-blue-500 text-xs flex items-center gap-1 hover:text-blue-700 transition">
                                <i class="fa fa-plus text-xs"></i> Add Member
                            </button>
                        </div>

                        {{-- Dropdown --}}
                        <div id="participant-dropdown"
                             class="hidden mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-20 relative">

                            {{-- Header --}}
                            <div class="px-3 py-2 border-b border-gray-100 bg-gray-50
                                        flex justify-between items-center sticky top-0">
                                <span class="text-xs font-medium text-gray-500">Select Participants</span>
                                <button type="button" id="close-dropdown"
                                        class="text-xs text-gray-400 hover:text-gray-600">
                                    <i class="fa fa-times"></i> Close
                                </button>
                            </div>

                            {{-- Search --}}
                            <div class="px-3 py-2 border-b border-gray-100">
                                <input type="text" id="participant-search"
                                       placeholder="Search..."
                                       class="w-full px-3 py-2 bg-gray-100 rounded-lg text-xs
                                              focus:outline-none focus:ring-2 focus:ring-blue-300">
                            </div>

                            {{-- List --}}
                            <div id="participant-list" class="max-h-44 overflow-y-auto">
                                @foreach($participants as $participant)
                                    <div class="participant-item flex items-center gap-3 px-4 py-2.5
                                                hover:bg-blue-50 cursor-pointer transition border-b
                                                border-gray-50 last:border-0"
                                         data-id="{{ $participant->id }}"
                                         data-name="{{ $participant->name }}"
                                         data-email="{{ $participant->email }}">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700
                                                    flex items-center justify-center text-xs font-medium shrink-0">
                                            {{ strtoupper(substr($participant->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">{{ $participant->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $participant->email }}</p>
                                        </div>
                                        <i class="fa fa-check text-blue-500 ml-auto text-xs hidden check-icon"></i>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Done --}}
                            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                                <button type="button" id="done-btn"
                                        class="w-full text-center px-4 py-2 bg-blue-600 text-white
                                               text-xs rounded-lg hover:bg-blue-700 transition">
                                    Done
                                </button>
                            </div>

                        </div>

                        {{-- Hidden inputs container --}}
                        <div id="hidden-inputs"></div>

                    </div>

                    <!-- AGENDA -->
                    <div class="mb-5 mt-4">

                        <div class="flex items-center justify-between mb-2">

                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Meeting Agenda
                            </label>

                            <button type="button"
                                    id="addAgendaBtn"
                                    class="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">

                                + Add Agenda

                            </button>

                        </div>

                        <div id="agendaWrapper" class="space-y-3">

                            <!-- Agenda Item -->
                            <div class="agenda-item p-3 bg-gray-50 rounded-lg border border-gray-200 relative">

                                <!-- Remove Button -->
                                <button type="button"
                                        class="removeAgenda hidden absolute top-3 right-3 text-red-500 hover:text-red-700 text-sm">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                         stroke="currentColor" class="size-4 m-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>


                                </button>

                                <!-- Title -->
                                <input type="text"
                                       name="agenda_title[]"
                                       value="{{ old('agenda_title.0') }}"
                                       placeholder="Agenda Title (e.g. Product Discussion)"
                                       class="w-full px-3 py-2 bg-white rounded-md text-sm border border-gray-200
                   focus:outline-none focus:ring-2 focus:ring-blue-300 mb-2">

                                @error('agenda_title.0')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                                <!-- Description -->
                                <textarea rows="2"
                                          name="agenda_description[]"
                                          placeholder="Agenda Description..."
                                          class="w-full px-3 py-2 bg-white rounded-md text-sm border border-gray-200
                      focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none">{{ old('agenda_description.0') }}</textarea>

                                @error('agenda_description.0')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>

                        </div>

                    </div>

                    <script>

                        const addAgendaBtn = document.getElementById('addAgendaBtn');
                        const agendaWrapper = document.getElementById('agendaWrapper');

                        addAgendaBtn.addEventListener('click', () => {

                            const agendaItem = document.createElement('div');

                            agendaItem.className =
                                'agenda-item p-3 bg-gray-50 rounded-lg border border-gray-200 relative';

                            agendaItem.innerHTML = `

            <button type="button"
                    class="removeAgenda absolute top-3 right-3 text-red-500 hover:text-red-700 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
               stroke="currentColor" class="size-4 m-2">
               <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
              </svg>

            </button>

            <input type="text"
                   name="agenda_title[]"
                   placeholder="Agenda Title (e.g. Product Discussion)"
                   class="w-full px-3 py-2 bg-white rounded-md text-sm border border-gray-200
                   focus:outline-none focus:ring-2 focus:ring-blue-300 mb-2">

            <textarea rows="2"
                      name="agenda_description[]"
                      placeholder="Agenda Description..."
                      class="w-full px-3 py-2 bg-white rounded-md text-sm border border-gray-200
                      focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none"></textarea>
        `;

                            agendaWrapper.appendChild(agendaItem);

                            updateRemoveButtons();

                        });

                        function updateRemoveButtons() {

                            const items = document.querySelectorAll('.agenda-item');

                            items.forEach((item, index) => {

                                const removeBtn = item.querySelector('.removeAgenda');

                                if (items.length === 1) {
                                    removeBtn.classList.add('hidden');
                                } else {
                                    removeBtn.classList.remove('hidden');
                                }

                                removeBtn.onclick = () => {
                                    item.remove();
                                    updateRemoveButtons();
                                };

                            });

                        }

                        updateRemoveButtons();

                    </script>

                    <!-- DESCRIPTION -->
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Description</label>
                        <textarea rows="3" name="description"
                                  placeholder="Outline the meeting objectives..."
                                  class="w-full mt-1 px-4 py-2.5 bg-gray-100 rounded-lg text-sm
                                         focus:outline-none focus:ring-2 focus:ring-blue-300
                                         focus:bg-white transition resize-none">{{ old('description') }}</textarea>
                    </div>

                    <!-- BUTTONS -->
                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                        <a href="{{ route('organizer.meetings.index') }}"
                           class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800
                                  border border-gray-200 rounded-lg hover:bg-gray-50
                                  transition w-full sm:w-auto text-center">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm
                                       hover:bg-blue-700 transition w-full sm:w-auto font-medium">
                            Create Meeting
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- JS --}}
<script>
    const addBtn       = document.getElementById('add-member-btn');
    const dropdown     = document.getElementById('participant-dropdown');
    const closeBtn     = document.getElementById('close-dropdown');
    const doneBtn      = document.getElementById('done-btn');
    const searchInput  = document.getElementById('participant-search');
    const chipsArea    = document.getElementById('chips-area');
    const hiddenInputs = document.getElementById('hidden-inputs');
    const items        = document.querySelectorAll('.participant-item');

    let selected = {};

    // Dropdown open
    addBtn.addEventListener('click', () => {
        dropdown.classList.remove('hidden');
    });

    // Dropdown close
    closeBtn.addEventListener('click', () => dropdown.classList.add('hidden'));
    doneBtn.addEventListener('click',  () => dropdown.classList.add('hidden'));

    // Search filter
    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        items.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = name.includes(q) ? '' : 'none';
        });
    });

    // Participant select/deselect
    items.forEach(item => {
        item.addEventListener('click', () => {
            const id    = item.dataset.id;
            const name  = item.dataset.name;
            const check = item.querySelector('.check-icon');

            if (selected[id]) {
                // Deselect
                delete selected[id];
                check.classList.add('hidden');
                removeChip(id);
            } else {
                // Select
                selected[id] = name;
                check.classList.remove('hidden');
                addChip(id, name);
            }
        });
    });

    // Chip add karo
    function addChip(id, name) {
        const chip = document.createElement('span');
        chip.id = 'chip-' + id;
        chip.className = 'bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs flex items-center gap-2';
        chip.innerHTML = `${name} <button type="button" onclick="removeParticipant('${id}')" class="hover:text-red-500 transition"><i class="fa-solid fa-xmark text-xs"></i></button>`;

        // Add button se pehle insert karo
        chipsArea.insertBefore(chip, addBtn);

        // Hidden input add karo
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'participants[]';
        input.value = id;
        input.id    = 'input-' + id;
        hiddenInputs.appendChild(input);
    }

    // Chip remove karo
    function removeChip(id) {
        const chip  = document.getElementById('chip-' + id);
        const input = document.getElementById('input-' + id);
        if (chip)  chip.remove();
        if (input) input.remove();
    }

    // Cross se remove
    window.removeParticipant = function(id) {
        delete selected[id];
        removeChip(id);

        // Check icon bhi hide karo
        items.forEach(item => {
            if (item.dataset.id === id) {
                item.querySelector('.check-icon').classList.add('hidden');
            }
        });
    };
</script>

</body>
</html>
