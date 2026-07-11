<x-layouts.app>

    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>

    <x-success />
    <x-error />

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        <div class="max-w-4xl mx-auto w-full">
            <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8 border border-gray-100 hover:shadow-2xl transition-all duration-500 ease-in-out relative overflow-hidden">

                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-blue-50/60 via-indigo-50/30 to-purple-50/20 rounded-full blur-2xl -mr-24 -mt-24"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-emerald-50/40 via-teal-50/20 to-cyan-50/10 rounded-full blur-2xl -ml-24 -mb-24"></div>

                <div class="relative z-10">
                    <!-- Header with icon -->
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Create New Meeting</h1>
                            <p class="text-sm text-gray-400 mt-0.5">Set up a new collaboration session with your team.</p>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-600 text-sm px-5 py-3.5 rounded-2xl flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
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
                        <div class="mb-5">
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                Meeting Title <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                   placeholder="e.g. Q4 Strategy Sync"
                                   class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                          focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white
                                          transition-all duration-200 placeholder:text-gray-400">
                        </div>

                        <!-- DATE + TIME + DURATION -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                        <rect x="3" y="4" width="18" height="18" rx="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    Date <span class="text-red-400">*</span>
                                </label>
                                <input type="date" name="date" value="{{ old('date') }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white
                                              transition-all duration-200">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                    Time <span class="text-red-400">*</span>
                                </label>
                                <input type="time" name="time" value="{{ old('time') }}"
                                       class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white
                                              transition-all duration-200">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    Duration <span class="text-red-400">*</span>
                                </label>
                                <select name="duration"
                                        class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                               focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white
                                               transition-all duration-200 cursor-pointer">
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
                        <div class="mb-5">
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 0 1-1.161.886l-.143.048a1.107 1.107 0 0 0-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 0 1-1.652.928l-.679-.906a1.125 1.125 0 0 0-1.906.172L4.5 15.75l-.612.153M12.75 3.03a9 9 0 0 0-8.862 11.239M12.75 3.03a9 9 0 0 1 6.69 14.036m0 0-.177-.529A2.25 2.25 0 0 0 17.128 15H16.5l-.324-.324a1.453 1.453 0 0 0-2.328.377l-.036.073a1.586 1.586 0 0 1-.982.816l-.99.282c-.857.245-1.614.721-2.223 1.303" />
                                </svg>
                                Timezone <span class="text-red-400">*</span>
                            </label>
                            <select name="timezone"
                                    class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                           focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white
                                           transition-all duration-200 cursor-pointer">
                                @foreach(\DateTimeZone::listIdentifiers() as $timezone)
                                    <option value="{{ $timezone }}"
                                        {{ old('timezone', 'Asia/Karachi') == $timezone ? 'selected' : '' }}>
                                        {{ $timezone }} (UTC {{ now($timezone)->format('P') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- PARTICIPANTS -->
                        <div class="mb-5">
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106M12 12.75a4.125 4.125 0 1 0 0-8.25 4.125 4.125 0 0 0 0 8.25Zm0 0v1.5m0-1.5h-7.5a1.5 1.5 0 0 0-1.5 1.5v.5m0 0v.57m0 0a3.75 3.75 0 0 0 2.25 3.442m-2.25-3.442a3.75 3.75 0 0 0 2.25 3.442m4.5-1.5v1.5m0 0h-1.5m1.5 0h1.5" />
                                </svg>
                                Participants
                            </label>

                            {{-- Selected Chips Area --}}
                            <div id="chips-area"
                                 class="mt-1.5 flex items-center gap-2 flex-wrap bg-gray-50 p-3 rounded-xl min-h-[52px] border border-gray-200">
                                <button type="button" id="add-member-btn"
                                        class="text-blue-600 text-xs flex items-center gap-1.5 hover:text-blue-700 transition font-medium px-2 py-1 rounded-lg hover:bg-blue-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Add Member
                                </button>
                            </div>

                            {{-- Dropdown - positioned below with proper z-index --}}
                            <div id="participant-dropdown"
                                 class="hidden mt-2 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 relative overflow-hidden">

                                {{-- Header --}}
                                <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-blue-50/50
                                            flex justify-between items-center sticky top-0">
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Select Participants</span>
                                    <button type="button" id="close-dropdown"
                                            class="text-xs text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Search --}}
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <input type="text" id="participant-search"
                                           placeholder="Search participants..."
                                           class="w-full px-3 py-2 bg-gray-50 rounded-lg text-sm border border-gray-200
                                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white transition">
                                </div>

                                {{-- List --}}
                                <div id="participant-list" class="max-h-48 overflow-y-auto">
                                    @foreach($participants as $participant)
                                        <div class="participant-item flex items-center gap-3 px-4 py-3
                                                    hover:bg-blue-50 cursor-pointer transition border-b
                                                    border-gray-50 last:border-0 group"
                                             data-id="{{ $participant->id }}"
                                             data-name="{{ $participant->name }}"
                                             data-email="{{ $participant->email }}">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700
                                                        flex items-center justify-center text-xs font-semibold shrink-0">
                                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-700">{{ $participant->name }}</p>
                                                <p class="text-xs text-gray-400 truncate">{{ $participant->email }}</p>
                                            </div>
                                            <i class="fa fa-check-circle text-blue-500 text-base hidden check-icon"></i>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Done --}}
                                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                                    <button type="button" id="done-btn"
                                            class="w-full text-center px-4 py-2.5 bg-blue-600 text-white
                                                   text-sm font-semibold rounded-xl hover:bg-blue-700 transition shadow-md hover:shadow-lg">
                                        Done
                                    </button>
                                </div>

                            </div>

                            {{-- Hidden inputs container --}}
                            <div id="hidden-inputs"></div>

                        </div>

                        <!-- AGENDA -->
                        <div class="mb-5">

                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12M8.25 17.25h12M4.5 7.5h.008v.008H4.5V7.5Zm0 5.25h.008v.008H4.5v-.008Zm0 5.25h.008v.008H4.5V18Z" />
                                    </svg>
                                    Meeting Agenda
                                </label>

                                <button type="button"
                                        id="addAgendaBtn"
                                        class="px-4 py-2 text-xs font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition shadow-md hover:shadow-lg flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Add Agenda
                                </button>

                            </div>

                            <div id="agendaWrapper" class="space-y-3">

                                <!-- Agenda Item -->
                                <div class="agenda-item p-4 bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-xl border border-gray-200 relative">

                                    <!-- Remove Button - Properly aligned -->
                                    <button type="button"
                                            class="removeAgenda hidden absolute top-3 right-3 text-gray-400 hover:text-red-500 transition p-1 rounded-lg hover:bg-red-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <!-- Title with right padding to avoid overlap with close button -->
                                    <input type="text"
                                           name="agenda_title[]"
                                           value="{{ old('agenda_title.0') }}"
                                           placeholder="Agenda Title (e.g. Product Discussion)"
                                           class="w-full px-3 py-2.5 bg-white rounded-lg text-sm border border-gray-200
                                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                                                  transition-all duration-200 mb-2 pr-12">

                                    @error('agenda_title.0')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                    <!-- Description -->
                                    <textarea rows="2"
                                              name="agenda_description[]"
                                              placeholder="Agenda Description..."
                                              class="w-full px-3 py-2.5 bg-white rounded-lg text-sm border border-gray-200
                                                     focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                                                     transition-all duration-200 resize-none">{{ old('agenda_description.0') }}</textarea>

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
                                agendaItem.className = 'agenda-item p-4 bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-xl border border-gray-200 relative';
                                agendaItem.innerHTML = `
                                    <button type="button"
                                            class="removeAgenda absolute top-3 right-3 text-gray-400 hover:text-red-500 transition p-1 rounded-lg hover:bg-red-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <input type="text"
                                           name="agenda_title[]"
                                           placeholder="Agenda Title (e.g. Product Discussion)"
                                           class="w-full px-3 py-2.5 bg-white rounded-lg text-sm border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 mb-2 pr-12">
                                    <textarea rows="2"
                                              name="agenda_description[]"
                                              placeholder="Agenda Description..."
                                              class="w-full px-3 py-2.5 bg-white rounded-lg text-sm border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                                `;
                                agendaWrapper.appendChild(agendaItem);
                                updateRemoveButtons();
                            });

                            function updateRemoveButtons() {
                                const items = document.querySelectorAll('.agenda-item');
                                items.forEach((item) => {
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
                        <div class="mb-6">
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                Description
                            </label>
                            <textarea rows="3" name="description"
                                      placeholder="Outline the meeting objectives..."
                                      class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                             focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white
                                             transition-all duration-200 resize-none placeholder:text-gray-400">{{ old('description') }}</textarea>
                        </div>

                        <!-- BUTTONS -->
                        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-2 border-t border-gray-100">
                            <a href="{{ route('organizer.meetings.index') }}"
                               class="px-6 py-2.5 text-sm text-gray-600 hover:text-gray-800
                                      border border-gray-200 rounded-xl hover:bg-gray-50
                                      transition w-full sm:w-auto text-center font-medium">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm
                                           hover:from-blue-700 hover:to-indigo-700 transition w-full sm:w-auto font-semibold
                                           shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Create Meeting
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>

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

    addBtn.addEventListener('click', (e) => {
        e.preventDefault();
        dropdown.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', () => dropdown.classList.add('hidden'));
    doneBtn.addEventListener('click',  () => dropdown.classList.add('hidden'));

    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        items.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = name.includes(q) ? '' : 'none';
        });
    });

    items.forEach(item => {
        item.addEventListener('click', () => {
            const id    = item.dataset.id;
            const name  = item.dataset.name;
            const check = item.querySelector('.check-icon');

            if (selected[id]) {
                delete selected[id];
                check.classList.add('hidden');
                removeChip(id);
            } else {
                selected[id] = name;
                check.classList.remove('hidden');
                addChip(id, name);
            }
        });
    });

    function addChip(id, name) {
        const chip = document.createElement('span');
        chip.id = 'chip-' + id;
        chip.className = 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-700 px-3 py-1 rounded-full text-xs flex items-center gap-2 font-medium shadow-sm';
        chip.innerHTML = `${name} <button type="button" onclick="removeParticipant('${id}')" class="hover:text-red-500 transition"><i class="fa-solid fa-xmark text-xs"></i></button>`;
        chipsArea.insertBefore(chip, addBtn);
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'participants[]';
        input.value = id;
        input.id    = 'input-' + id;
        hiddenInputs.appendChild(input);
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
            if (item.dataset.id === id) {
                item.querySelector('.check-icon').classList.add('hidden');
            }
        });
    };

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dropdown.classList.contains('hidden')) {
            const isClickInside = dropdown.contains(e.target) || addBtn.contains(e.target);
            if (!isClickInside) {
                dropdown.classList.add('hidden');
            }
        }
    });
</script>
