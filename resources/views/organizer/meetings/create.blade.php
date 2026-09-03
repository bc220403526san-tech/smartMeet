<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>
    <x-success />
    <x-error />
    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">
        <div class="max-w-4xl mx-auto w-full">
            <div class="bg-white rounded-3xl shadow-xl p-6 sm:p-8 border border-gray-100 hover:shadow-2xl transition-all duration-500 ease-in-out relative overflow-hidden">
                <!-- Decorative -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-blue-50/60 via-indigo-50/30 to-purple-50/20 rounded-full blur-2xl -mr-24 -mt-24"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-emerald-50/40 via-teal-50/20 to-cyan-50/10 rounded-full blur-2xl -ml-24 -mb-24"></div>
                <div class="relative z-10">
                    <!-- HEADER -->
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
                    <!-- ERRORS -->
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 text-sm px-5 py-3.5 rounded-2xl flex items-start gap-3">
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
                    <form action="{{ route('organizers.meetings.store') }}" method="POST" id="meeting-form">
                        @csrf
                        <!-- TITLE -->
                        <div class="mb-5">
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
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
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Date <span class="text-red-400">*</span>
                                </label>
                                <input type="date" name="date" value="{{ old('date') }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white transition-all duration-200">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Time <span class="text-red-400">*</span>
                                </label>
                                <input type="time" name="time" value="{{ old('time') }}"
                                       class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white transition-all duration-200">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Duration <span class="text-red-400">*</span>
                                </label>
                                <select name="duration"
                                        class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                               focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white transition-all duration-200 cursor-pointer">
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
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Timezone <span class="text-red-400">*</span>
                            </label>
                            <select name="timezone"
                                    class="w-full mt-1.5 px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                           focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white transition-all duration-200 cursor-pointer">
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
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Participants
                            </label>
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
                            <!-- DROPDOWN -->
                            <div id="participant-dropdown"
                                 class="hidden mt-2 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 relative overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-blue-50/50 flex justify-between items-center">
                                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Select Participants</span>
                                    <button type="button" id="close-dropdown"
                                            class="text-xs text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <input type="text" id="participant-search"
                                           placeholder="Search participants..."
                                           class="w-full px-3 py-2 bg-gray-50 rounded-lg text-sm border border-gray-200
                                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white transition">
                                </div>
                                <div id="participant-list" class="max-h-48 overflow-y-auto">
                                    @foreach($participants as $participant)
                                        <div class="participant-item flex items-center gap-3 px-4 py-3
                                                    hover:bg-blue-50 cursor-pointer transition border-b border-gray-50 last:border-0"
                                             data-id="{{ $participant->id }}"
                                             data-name="{{ $participant->name }}"
                                             data-email="{{ $participant->email }}">
                                            <img src="{{ $participant->image_url }}"
                                                 class="w-8 h-8 rounded-full object-cover">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-700">{{ $participant->name }}</p>
                                                <p class="text-xs text-gray-400 truncate">{{ $participant->email }}</p>
                                            </div>
                                            <i class="fa fa-check-circle text-blue-500 text-base hidden check-icon"></i>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                                    <button type="button" id="done-btn"
                                            class="w-full text-center px-4 py-2.5 bg-blue-600 text-white
                                                   text-sm font-semibold rounded-xl hover:bg-blue-700 transition shadow-md">
                                        Done
                                    </button>
                                </div>
                            </div>
                            <div id="hidden-inputs"></div>
                        </div>

                        <!-- EMAIL INVITES -->
                        <div class="mb-5">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <label for="invite_emails" class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0-8.69 5.793a2 2 0 0 1-2.12 0L2.25 6.75" />
                                        </svg>
                                        Invite by Email
                                        <span class="normal-case tracking-normal font-medium text-gray-400">(Optional)</span>
                                    </label>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Invite registered users or new guests when this meeting is created.
                                    </p>
                                </div>
                                <span id="invite-email-count"
                                      class="hidden shrink-0 px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-[11px] font-semibold">
                                    0 emails
                                </span>
                            </div>

                            <textarea id="invite_emails"
                                      name="invite_emails"
                                      rows="2"
                                      placeholder="e.g. ali@example.com, sara@example.com"
                                      class="w-full px-4 py-3 bg-gray-50 rounded-xl text-sm border border-gray-200
                                             focus:outline-none focus:ring-2 focus:ring-blue-400 focus:bg-white
                                             transition-all duration-200 resize-none placeholder:text-gray-400">{{ old('invite_emails') }}</textarea>

                            <div class="mt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <p class="text-[11px] text-gray-400">
                                    Separate multiple email addresses with commas, semicolons, or new lines.
                                </p>
                                <button type="button" id="toggle-invite-options"
                                        class="text-xs font-medium text-blue-600 hover:text-blue-700 transition self-start sm:self-auto">
                                    + Add custom message
                                </button>
                            </div>

                            <div id="invite-options"
                                 class="hidden mt-3 p-4 rounded-xl border border-gray-200 bg-gray-50/70 space-y-3">
                                <div>
                                    <label for="invite_subject" class="text-xs font-semibold text-gray-600">
                                        Email Subject <span class="font-normal text-gray-400">(Optional)</span>
                                    </label>
                                    <input id="invite_subject"
                                           type="text"
                                           name="invite_subject"
                                           maxlength="255"
                                           value="{{ old('invite_subject') }}"
                                           placeholder="Meeting invitation"
                                           class="w-full mt-1.5 px-3.5 py-2.5 bg-white rounded-lg text-sm border border-gray-200
                                                  focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                                </div>

                                <div>
                                    <label for="invite_message" class="text-xs font-semibold text-gray-600">
                                        Personal Message <span class="font-normal text-gray-400">(Optional)</span>
                                    </label>
                                    <textarea id="invite_message"
                                              name="invite_message"
                                              rows="2"
                                              maxlength="1500"
                                              placeholder="Add a short note for your invitees..."
                                              class="w-full mt-1.5 px-3.5 py-2.5 bg-white rounded-lg text-sm border border-gray-200
                                                     focus:outline-none focus:ring-2 focus:ring-blue-400 transition resize-none">{{ old('invite_message') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- AGENDA — Sirf Title -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Meeting Agenda
                                </label>
                                <button type="button" id="addAgendaBtn"
                                        class="px-4 py-2 text-xs font-semibold bg-gradient-to-r from-blue-600 to-indigo-600
                                               text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition
                                               shadow-md flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Add Agenda
                                </button>
                            </div>
                            <div id="agendaWrapper" class="space-y-2">
                                <!-- First Item -->
                                <div class="agenda-item flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                                    <span class="agenda-number text-gray-400 text-xs font-bold w-5 text-center shrink-0">1</span>
                                    <input type="text"
                                           name="agenda_title[]"
                                           value="{{ old('agenda_title.0') }}"
                                           placeholder="e.g. Welcome & Introduction"
                                           class="flex-1 px-3 py-2.5 bg-white rounded-lg text-sm border border-gray-200
                                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200">
                                    <button type="button"
                                            class="removeAgenda hidden text-gray-400 hover:text-red-500 transition p-1 rounded-lg hover:bg-red-50 shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- MEETING DESCRIPTION (wapas add ki gayi) -->
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
                            <a href="{{ route('organizers.meetings.index') }}"
                               class="px-6 py-2.5 text-sm text-gray-600 border border-gray-200 rounded-xl
                                      hover:bg-gray-50 transition w-full sm:w-auto text-center font-medium">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm
                                           hover:from-blue-700 hover:to-indigo-700 transition w-full sm:w-auto font-semibold
                                           shadow-md flex items-center justify-center gap-2">
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
    <!-- SCRIPTS -->
    <script>
        // ── Participants ──────────────────────────────────────────────
        const addBtn       = document.getElementById('add-member-btn');
        const dropdown     = document.getElementById('participant-dropdown');
        const closeBtn     = document.getElementById('close-dropdown');
        const doneBtn      = document.getElementById('done-btn');
        const searchInput  = document.getElementById('participant-search');
        const chipsArea    = document.getElementById('chips-area');
        const hiddenInputs = document.getElementById('hidden-inputs');
        const items        = document.querySelectorAll('.participant-item');
        let selected = {};
        addBtn.addEventListener('click', e => { e.preventDefault(); dropdown.classList.remove('hidden'); });
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
                    selected[id] = name;
                    check.classList.remove('hidden');
                    addChip(id, name);
                }
            });
        });
        function addChip(id, name) {
            const chip = document.createElement('span');
            chip.id = 'chip-' + id;
            chip.className = 'bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs flex items-center gap-2 font-medium';
            chip.innerHTML = `${name} <button type="button" onclick="removeParticipant('${id}')" class="hover:text-red-500 transition">✕</button>`;
            chipsArea.insertBefore(chip, addBtn);
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'participants[]';
            input.value = id;
            input.id    = 'input-' + id;
            hiddenInputs.appendChild(input);
        }
        function removeChip(id) {
            document.getElementById('chip-' + id)?.remove();
            document.getElementById('input-' + id)?.remove();
        }
        window.removeParticipant = function(id) {
            delete selected[id];
            removeChip(id);
            items.forEach(item => {
                if (item.dataset.id === id) item.querySelector('.check-icon').classList.add('hidden');
            });
        };
        document.addEventListener('click', function(e) {
            if (!dropdown.classList.contains('hidden')) {
                if (!dropdown.contains(e.target) && !addBtn.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });
        // ── Email Invites ────────────────────────────────────────────
        const inviteEmailsInput  = document.getElementById('invite_emails');
        const inviteEmailCount   = document.getElementById('invite-email-count');
        const inviteOptions      = document.getElementById('invite-options');
        const toggleInviteButton = document.getElementById('toggle-invite-options');

        function parsedInviteEmails() {
            if (!inviteEmailsInput) return [];

            return [...new Set(
                inviteEmailsInput.value
                    .split(/[;,\n]+/)
                    .map(email => email.trim())
                    .filter(Boolean)
            )];
        }

        function refreshInviteEmailCount() {
            if (!inviteEmailCount) return;

            const count = parsedInviteEmails().length;
            inviteEmailCount.textContent = `${count} email${count === 1 ? '' : 's'}`;
            inviteEmailCount.classList.toggle('hidden', count === 0);
        }

        inviteEmailsInput?.addEventListener('input', refreshInviteEmailCount);

        toggleInviteButton?.addEventListener('click', () => {
            const willOpen = inviteOptions.classList.contains('hidden');
            inviteOptions.classList.toggle('hidden');
            toggleInviteButton.textContent = willOpen
                ? '− Hide custom message'
                : '+ Add custom message';
        });

        refreshInviteEmailCount();

        // ── Agenda ───────────────────────────────────────────────────
        const addAgendaBtn  = document.getElementById('addAgendaBtn');
        const agendaWrapper = document.getElementById('agendaWrapper');
        addAgendaBtn.addEventListener('click', () => {
            const count = document.querySelectorAll('.agenda-item').length + 1;
            const item  = document.createElement('div');
            item.className = 'agenda-item flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200';
            item.innerHTML = `
                <span class="agenda-number text-gray-400 text-xs font-bold w-5 text-center shrink-0">${count}</span>
                <input type="text"
                       name="agenda_title[]"
                       placeholder="e.g. Action Items Review"
                       class="flex-1 px-3 py-2.5 bg-white rounded-lg text-sm border border-gray-200
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200">
                <button type="button"
                        class="removeAgenda text-gray-400 hover:text-red-500 transition p-1 rounded-lg hover:bg-red-50 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            agendaWrapper.appendChild(item);
            updateRemoveButtons();
            updateNumbers();
        });
        function updateRemoveButtons() {
            const items = document.querySelectorAll('.agenda-item');
            items.forEach(item => {
                const btn = item.querySelector('.removeAgenda');
                btn.classList.toggle('hidden', items.length === 1);
                btn.onclick = () => { item.remove(); updateRemoveButtons(); updateNumbers(); };
            });
        }
        function updateNumbers() {
            document.querySelectorAll('.agenda-number').forEach((el, i) => el.textContent = i + 1);
        }
        updateRemoveButtons();
    </script>
</x-layouts.app>
