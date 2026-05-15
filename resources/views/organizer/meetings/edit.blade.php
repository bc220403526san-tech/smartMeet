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

<body class="bg-[#f4f6f9] overflow-hidden">

<input type="checkbox" id="sidebar-toggle">

<div class="flex h-screen overflow-hidden">

    <label for="sidebar-toggle" class="fixed inset-0 bg-black/40 z-30 hidden"></label>

    <x-sidebar.organizer-menu />

    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">

        <x-header.page-title title="Edit Meeting" />

        <div class="flex-1 overflow-y-auto mt-3 rounded-md">

            <x-success />
            <x-error />

            <div class="min-h-screen flex flex-col bg-gray-50">

                <div class="max-w-4xl mx-auto">

                <!-- TOP BAR -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 mt-8">

                    <div class="flex items-center gap-2 text-xs">
                        <a href="{{ route('organizer.meetings.index') }}"
                           class="text-gray-400 font-medium hover:text-blue-600 transition">
                            Manage Meetings
                        </a>
                        <i class="fa-solid fa-angle-right text-[10px] text-gray-300"></i>
                        <span class="font-semibold text-blue-600 uppercase tracking-wider">Edit Meeting</span>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                                bg-orange-50 border border-orange-100 text-orange-600 text-xs font-semibold w-fit">
                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        {{ ucfirst($meeting->status) }} Meeting
                    </div>

                </div>

                    <!-- MAIN CARD -->
                    <div class="bg-white rounded-[28px] border border-gray-200 shadow-sm overflow-hidden">

                    <!-- HEADER -->
                    <div class="relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-cyan-500 to-sky-500"></div>
                        <div class="relative p-6">
                            <div class="flex items-start justify-between gap-4 flex-wrap">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md
                                                flex items-center justify-center border border-white/20">
                                        <i class="fa-solid fa-pen-to-square text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <h1 class="text-2xl font-bold text-white">Edit Meeting Session</h1>
                                        <p class="text-sm text-blue-100 mt-1">Update meeting details, agendas & participants.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md
                                            border border-white/20 px-4 py-2 rounded-2xl">
                                    <i class="fa-regular fa-calendar text-white text-sm"></i>
                                    <span class="text-white text-xs font-medium">
                                        Last Updated {{ $meeting->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FORM -->
                    <form
                        action="{{ route('organizer.meetings.update', $meeting) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        <div class="p-5 lg:p-6 space-y-6">

                            {{-- ERRORS --}}
                            @if($errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-2xl">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- BASIC INFO -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                                <!-- TITLE -->
                                <div class="lg:col-span-2">
                                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                        Meeting Title
                                    </label>
                                    <input type="text" name="title"
                                           value="{{ old('title', $meeting->title) }}"
                                           class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-2xl
                                                  text-sm text-gray-800 focus:outline-none focus:ring-4
                                                  focus:ring-blue-100 focus:border-blue-400 focus:bg-white transition">
                                </div>

                                <!-- DESCRIPTION -->
                                <div class="lg:col-span-2">
                                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                        Description
                                    </label>
                                    <textarea rows="4" name="description"
                                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl
                                                     text-sm text-gray-700 focus:outline-none focus:ring-4
                                                     focus:ring-blue-100 focus:border-blue-400 focus:bg-white
                                                     transition resize-none">{{ old('description', $meeting->description) }}</textarea>
                                </div>

                            </div>

                            <!-- DATE TIME -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                                <!-- DATE -->
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                        Date
                                    </label>
                                    <div class="relative">
                                        <input type="date" name="date"
                                               value="{{ old('date', $meeting->date) }}"
                                               min="{{ date('Y-m-d') }}"
                                               class="w-full h-12 px-4 pr-10 bg-gray-50 border border-gray-200 rounded-2xl
                                                      text-sm text-gray-700 focus:outline-none focus:ring-4
                                                      focus:ring-blue-100 focus:border-blue-400 focus:bg-white transition">
                                    </div>
                                </div>

                                <!-- TIME -->
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                        Start Time
                                    </label>
                                    <div class="relative">
                                        <input type="time" name="time"
                                               value="{{ old('time', \Carbon\Carbon::parse($meeting->time)->format('H:i')) }}"
                                               class="w-full h-12 px-4 pr-10 bg-gray-50 border border-gray-200 rounded-2xl
                                                      text-sm text-gray-700 focus:outline-none focus:ring-4
                                                      focus:ring-blue-100 focus:border-blue-400 focus:bg-white transition">
                                    </div>
                                </div>

                                <!-- DURATION -->
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                        Duration
                                    </label>
                                    <div class="relative">
                                        <select name="duration"
                                                class="w-full h-12 px-4 pr-10 bg-gray-50 border border-gray-200 rounded-2xl
                                                       text-sm text-gray-700 focus:outline-none focus:ring-4
                                                       focus:ring-blue-100 focus:border-blue-400 focus:bg-white transition appearance-none">
                                            <option value="15"  {{ old('duration', $meeting->duration) == 15  ? 'selected' : '' }}>15 mins</option>
                                            <option value="30"  {{ old('duration', $meeting->duration) == 30  ? 'selected' : '' }}>30 mins</option>
                                            <option value="45"  {{ old('duration', $meeting->duration) == 45  ? 'selected' : '' }}>45 mins</option>
                                            <option value="60"  {{ old('duration', $meeting->duration) == 60  ? 'selected' : '' }}>1 hour</option>
                                            <option value="90"  {{ old('duration', $meeting->duration) == 90  ? 'selected' : '' }}>1.5 hours</option>
                                            <option value="120" {{ old('duration', $meeting->duration) == 120 ? 'selected' : '' }}>2 hours</option>
                                        </select>
                                            <i class="fa-regular fa-hourglass-half text-orange-500 absolute right-4 top-1/2 -translate-y-1/2 text-sm"></i>
                                    </div>
                                </div>

                                <!-- TIMEZONE -->
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                        Timezone
                                    </label>
                                    <div class="relative">
                                        <select name="timezone"
                                                class="w-full h-12 px-4 pr-10 bg-gray-50 border border-gray-200 rounded-2xl
                                                       text-sm text-gray-700 focus:outline-none focus:ring-4
                                                       focus:ring-blue-100 focus:border-blue-400 focus:bg-white transition appearance-none">
                                            @foreach(\DateTimeZone::listIdentifiers() as $tz)
                                                <option value="{{ $tz }}"
                                                    {{ old('timezone', $meeting->timezone) == $tz ? 'selected' : '' }}>
                                                    {{ $tz }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-earth-asia absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    </div>
                                </div>

                            </div>

                            <!-- PARTICIPANTS -->
                            <div class="bg-gray-50 border border-gray-100 rounded-3xl p-5">

                                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                            <i class="fa-solid fa-user-group"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-sm font-bold text-gray-800">Participants</h2>
                                            <p class="text-xs text-gray-400">Manage invited members</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Participants -->
                                <div id="chips-area"
                                     class="flex items-center gap-2 flex-wrap bg-white border border-gray-200
                                            p-3 rounded-2xl min-h-[50px] mb-3">

                                    @foreach($participants as $participant)
                                        @php
                                            $isSelected = in_array($participant->id, $selectedParticipants);
                                        @endphp
                                        @if($isSelected)
                                            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs
                                                         flex items-center gap-2" id="chip-{{ $participant->id }}">
                                                {{ $participant->name }}
                                                <input type="hidden" name="participants[]"
                                                       value="{{ $participant->id }}"
                                                       id="input-{{ $participant->id }}">
                                                <button type="button"
                                                        onclick="removeParticipant('{{ $participant->id }}')"
                                                        class="hover:text-red-500 transition">
                                                    <i class="fa-solid fa-xmark text-xs"></i>
                                                </button>
                                            </span>
                                        @endif
                                    @endforeach

                                    <button type="button" id="add-member-btn"
                                            class="text-blue-500 text-xs flex items-center gap-1 hover:text-blue-700 transition">
                                        <i class="fa fa-plus text-xs"></i> Add Member
                                    </button>

                                </div>

                                <!-- Dropdown -->
                                <div id="participant-dropdown"
                                     class="hidden bg-white border border-gray-200 rounded-2xl shadow-lg z-20 relative">

                                    <div class="px-3 py-2 border-b border-gray-100 bg-gray-50
                                                flex justify-between items-center sticky top-0 rounded-t-2xl">
                                        <span class="text-xs font-medium text-gray-500">Select Participants</span>
                                        <button type="button" id="close-dropdown"
                                                class="text-xs text-gray-400 hover:text-gray-600">
                                            <i class="fa fa-times"></i> Close
                                        </button>
                                    </div>

                                    <div class="px-3 py-2 border-b border-gray-100">
                                        <input type="text" id="participant-search"
                                               placeholder="Search..."
                                               class="w-full px-3 py-2 bg-gray-100 rounded-xl text-xs
                                                      focus:outline-none focus:ring-2 focus:ring-blue-300">
                                    </div>

                                    <div id="participant-list" class="max-h-44 overflow-y-auto">
                                        @foreach($participants as $participant)
                                            <div class="participant-item flex items-center gap-3 px-4 py-2.5
                                                        hover:bg-blue-50 cursor-pointer transition border-b
                                                        border-gray-50 last:border-0"
                                                 data-id="{{ $participant->id }}"
                                                 data-name="{{ $participant->name }}">
                                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700
                                                            flex items-center justify-center text-xs font-medium shrink-0">
                                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700">{{ $participant->name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $participant->email }}</p>
                                                </div>
                                                <i class="fa fa-check text-blue-500 ml-auto text-xs
                                                          {{ in_array($participant->id, $selectedParticipants) ? '' : 'hidden' }}
                                                          check-icon"></i>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                                        <button type="button" id="done-btn"
                                                class="w-full text-center px-4 py-2 bg-blue-600 text-white
                                                       text-xs rounded-xl hover:bg-blue-700 transition">
                                            Done
                                        </button>
                                    </div>

                                </div>

                                <div id="hidden-inputs"></div>

                            </div>

                            <!-- AGENDA -->
                            <div class="bg-gray-50 border border-gray-100 rounded-3xl p-5">

                                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center">
                                            <i class="fa-solid fa-list-check"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-sm font-bold text-gray-800">Meeting Agenda</h2>
                                            <p class="text-xs text-gray-400">Add discussion topics</p>
                                        </div>
                                    </div>
                                    <button type="button" id="addAgendaBtn"
                                            class="h-10 px-4 rounded-xl bg-blue-600 hover:bg-blue-700
                                                   text-white text-sm font-semibold transition">
                                        + Add Agenda
                                    </button>
                                </div>

                                <div id="agendaWrapper" class="space-y-4">

                                    @php
                                        $agendaItems = json_decode($meeting->agenda, true) ?? [];
                                        if(empty($agendaItems)) $agendaItems = [['title' => '', 'description' => '']];
                                    @endphp

                                    @foreach($agendaItems as $index => $item)
                                        <div class="agenda-item bg-white border border-gray-200 rounded-3xl p-5 relative shadow-sm">

                                            <button type="button"
                                                    class="removeAgenda {{ count($agendaItems) === 1 ? 'hidden' : '' }}
                                                           absolute top-2 right-4 w-7 h-7 rounded-xl
                                                           bg-red-50 hover:bg-red-100 text-red-500 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 ml-1">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                </svg>

                                            </button>

                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                                        Agenda Title
                                                    </label>
                                                    <input type="text"
                                                           name="agenda_title[]"
                                                           value="{{ old('agenda_title.'.$index, $item['title'] ?? '') }}"
                                                           placeholder="e.g. Marketing Strategy"
                                                           class="w-full h-12 px-4 bg-gray-50 border border-gray-200
                                                                  rounded-2xl text-sm focus:outline-none focus:ring-4
                                                                  focus:ring-blue-100 focus:border-blue-400 focus:bg-white transition">
                                                </div>
                                                <div>
                                                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                                        Description
                                                    </label>
                                                    <textarea rows="3"
                                                              name="agenda_description[]"
                                                              placeholder="Agenda description..."
                                                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200
                                                                     rounded-2xl text-sm focus:outline-none focus:ring-4
                                                                     focus:ring-blue-100 focus:border-blue-400 focus:bg-white
                                                                     transition resize-none">{{ old('agenda_description.'.$index, $item['description'] ?? '') }}</textarea>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach

                                </div>

                            </div>

                            <!-- BUTTONS -->
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <a href="{{ route('organizer.meetings.index') }}"
                                   class="h-11 px-5 rounded-2xl bg-white border border-gray-200
                                          text-gray-600 hover:bg-gray-50 transition text-sm font-medium
                                          flex items-center">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="h-11 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700
                                               text-white transition text-sm font-semibold
                                               flex items-center gap-2 shadow-sm">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Save Changes
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // ── PARTICIPANTS ──
    const addBtn       = document.getElementById('add-member-btn');
    const dropdown     = document.getElementById('participant-dropdown');
    const closeBtn     = document.getElementById('close-dropdown');
    const doneBtn      = document.getElementById('done-btn');
    const searchInput  = document.getElementById('participant-search');
    const chipsArea    = document.getElementById('chips-area');
    const hiddenInputs = document.getElementById('hidden-inputs');
    const items        = document.querySelectorAll('.participant-item');

    // Already selected participants
    let selected = {};
    @foreach($selectedParticipants as $id)
        selected['{{ $id }}'] = true;
    @endforeach

    addBtn.addEventListener('click', () => dropdown.classList.remove('hidden'));
    closeBtn.addEventListener('click', () => dropdown.classList.add('hidden'));
    doneBtn.addEventListener('click',  () => dropdown.classList.add('hidden'));

    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        items.forEach(item => {
            item.style.display = item.dataset.name.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    items.forEach(item => {
        item.addEventListener('click', () => {
            const id   = item.dataset.id;
            const name = item.dataset.name;
            const check = item.querySelector('.check-icon');

            if (selected[id]) {
                delete selected[id];
                check.classList.add('hidden');
                removeChip(id);
            } else {
                selected[id] = true;
                check.classList.remove('hidden');
                addChip(id, name);
            }
        });
    });

    function addChip(id, name) {
        // Purana chip remove karo agar already hai
        removeChip(id);

        const chip = document.createElement('span');
        chip.id = 'chip-' + id;
        chip.className = 'bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs flex items-center gap-2';
        chip.innerHTML = `${name}
            <input type="hidden" name="participants[]" value="${id}" id="input-${id}">
            <button type="button" onclick="removeParticipant('${id}')" class="hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>`;
        chipsArea.insertBefore(chip, addBtn);
    }

    function removeChip(id) {
        const chip  = document.getElementById('chip-' + id);
        const input = document.getElementById('input-' + id);
        if (chip)  chip.remove();
        if (input) input.remove();
    }

    window.removeParticipant = function(id) {
        delete selected[id];
        removeChip(id);
        items.forEach(item => {
            if (item.dataset.id == id) {
                item.querySelector('.check-icon').classList.add('hidden');
            }
        });
    };

    // ── AGENDA ──
    const addAgendaBtn  = document.getElementById('addAgendaBtn');
    const agendaWrapper = document.getElementById('agendaWrapper');

    addAgendaBtn.addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'agenda-item bg-white border border-gray-200 rounded-3xl p-5 relative shadow-sm';
        div.innerHTML = `
            <button type="button" class="removeAgenda absolute top-4 right-4 w-9 h-9
                                         rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Agenda Title
                    </label>
                    <input type="text" name="agenda_title[]" placeholder="e.g. Product Discussion"
                           class="w-full h-12 px-4 bg-gray-50 border border-gray-200 rounded-2xl text-sm
                                  focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400
                                  focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Description
                    </label>
                    <textarea rows="3" name="agenda_description[]" placeholder="Agenda description..."
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm
                                     focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400
                                     focus:bg-white transition resize-none"></textarea>
                </div>
            </div>`;
        agendaWrapper.appendChild(div);
        updateAgendaButtons();
    });

    function updateAgendaButtons() {
        const agendaItems = document.querySelectorAll('.agenda-item');
        agendaItems.forEach(item => {
            const btn = item.querySelector('.removeAgenda');
            if (agendaItems.length === 1) {
                btn.classList.add('hidden');
            } else {
                btn.classList.remove('hidden');
            }
            btn.onclick = () => {
                item.remove();
                updateAgendaButtons();
            };
        });
    }

    updateAgendaButtons();
</script>

</body>
</html>
