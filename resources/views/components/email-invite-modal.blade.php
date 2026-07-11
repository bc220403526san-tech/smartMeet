@props(['meeting' => null])
{{-- $meeting prop is optional — modal is fully generic, meetingId is passed via JS --}}

{{-- ============================================================
     SHARED EMAIL MODAL
     Used by both show.blade.php and meeting-setting.blade.php.
     To trigger from any button, call:
     openEmailModal({{ $meeting->id }}, '{{ addslashes($meeting->title) }}')
============================================================ --}}
<div id="emailModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-gray-800">Send Email</h3>
            <button type="button" onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <p class="text-xs text-gray-500 mb-3">
            Send email invitation for <strong id="email-meeting-title"></strong> —
            new emails will be auto-invited, existing participants will receive an update.
        </p>

        <form id="email-form" onsubmit="sendEmail(event)">
            <input type="hidden" id="email-meeting-id" value="">

            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Emails</label>
                <textarea id="email-emails-input" rows="2"
                          class="h-20 w-full border border-gray-200 rounded-lg p-2 text-sm"
                          placeholder="email1@example.com, email2@example.com"></textarea>
                <p class="text-[11px] text-gray-400 mt-1">Separate emails with commas. Existing participants will be auto-filled.</p>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                <input type="text" id="email-subject"
                       class="w-full border border-gray-200 rounded-lg p-2 text-sm"
                       placeholder="You're invited: meeting title">
            </div>

            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Message (optional)</label>
                <textarea id="email-message" rows="3"
                          class="w-full border border-gray-200 rounded-lg p-2 text-sm"
                          placeholder="Hello, please join our meeting..."></textarea>
            </div>

            <div id="email-msg" class="text-xs mb-3 hidden"></div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEmailModal()"
                        class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition flex items-center gap-2">
                    <i class="fa-regular fa-envelope"></i>
                    Send
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // ============================================================
    // SHARED EMAIL MODAL LOGIC — Single place for both pages
    // ============================================================
    function openEmailModal(meetingId, meetingTitle, participantEmails = '') {
        document.getElementById('email-meeting-id').value = meetingId;
        document.getElementById('email-meeting-title').textContent = meetingTitle;
        document.getElementById('email-subject').value = `You're invited: ${meetingTitle}`;
        document.getElementById('email-message').value = '';
        document.getElementById('email-emails-input').value = participantEmails;
        document.getElementById('email-msg').classList.add('hidden');
        document.getElementById('emailModal').classList.remove('hidden');
    }

    function closeEmailModal() {
        document.getElementById('emailModal').classList.add('hidden');
        document.getElementById('email-form').reset();
        document.getElementById('email-msg').classList.add('hidden');
    }

    function sendEmail(event) {
        event.preventDefault();

        const meetingId = document.getElementById('email-meeting-id').value;
        const emails = document.getElementById('email-emails-input').value.trim();
        const subject = document.getElementById('email-subject').value.trim();
        const message = document.getElementById('email-message').value.trim();
        const msgBox = document.getElementById('email-msg');

        if (!emails) {
            msgBox.textContent = 'At least one email is required.';
            msgBox.className = 'text-xs mb-3 text-red-600';
            msgBox.classList.remove('hidden');
            return;
        }

        const tokenTag = document.querySelector('meta[name="csrf-token"]');
        if (!tokenTag) {
            msgBox.textContent = 'CSRF token missing from page head.';
            msgBox.className = 'text-xs mb-3 text-red-600';
            msgBox.classList.remove('hidden');
            return;
        }

        fetch(`/organizer/meetings/${meetingId}/send-invite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': tokenTag.content
            },
            body: JSON.stringify({ emails, subject, message })
        })
            .then(res => res.json())
            .then(data => {
                msgBox.textContent = data.message || 'Sent successfully!';
                msgBox.className = 'text-xs mb-3 text-green-600';
                msgBox.classList.remove('hidden');
                setTimeout(closeEmailModal, 1500);
            })
            .catch(() => {
                msgBox.textContent = 'Failed to send. Please try again.';
                msgBox.className = 'text-xs mb-3 text-red-600';
                msgBox.classList.remove('hidden');
            });
    }
</script>
