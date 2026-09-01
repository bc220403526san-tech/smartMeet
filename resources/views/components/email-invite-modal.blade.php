@props(['meeting' => null])

<div id="emailModal"
     class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50"
     role="dialog"
     aria-modal="true"
     aria-labelledby="email-modal-title">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <div class="flex justify-between items-center mb-3">
            <h3 id="email-modal-title" class="font-semibold text-gray-800">Send Email</h3>
            <button type="button" onclick="closeEmailModal()"
                    class="text-gray-400 hover:text-gray-600" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p class="text-xs text-gray-500 mb-3">
            Send email invitation for <strong id="email-meeting-title"></strong>.
        </p>

        <form id="email-form" onsubmit="sendEmail(event)">
            <input type="hidden" id="email-meeting-id" value="">

            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Emails</label>
                <textarea id="email-emails-input" rows="2"
                          class="h-20 w-full border border-gray-200 rounded-lg p-2 text-sm"
                          placeholder="email1@example.com, email2@example.com"></textarea>
                <p class="text-[11px] text-gray-400 mt-1">Separate emails with commas.</p>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                <input type="text" id="email-subject"
                       class="w-full border border-gray-200 rounded-lg p-2 text-sm">
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

                <button type="submit" id="email-send-btn"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm rounded-lg transition flex items-center gap-2">
                    <i class="fa-regular fa-envelope"></i>
                    <span>Send</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (() => {
        let emailInviteSending = false;

        function setEmailMessage(message = '', type = '') {
            const box = document.getElementById('email-msg');
            if (!box) return;

            box.textContent = message;
            box.className = 'text-xs mb-3';

            if (!message) {
                box.classList.add('hidden');
                return;
            }

            if (type === 'success') box.classList.add('text-green-600');
            else if (type === 'info') box.classList.add('text-blue-600');
            else box.classList.add('text-red-600');
        }

        window.openEmailModal = function(meetingId, meetingTitle, participantEmails = '') {
            if (emailInviteSending) return;

            document.getElementById('email-meeting-id').value = meetingId;
            document.getElementById('email-meeting-title').textContent = meetingTitle;
            document.getElementById('email-subject').value = `You're invited: ${meetingTitle}`;
            document.getElementById('email-message').value = '';
            document.getElementById('email-emails-input').value = participantEmails || '';
            setEmailMessage('');
            document.getElementById('emailModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('email-emails-input')?.focus(), 30);
        };

        window.closeEmailModal = function() {
            if (emailInviteSending) return;
            document.getElementById('emailModal').classList.add('hidden');
            document.getElementById('email-form').reset();
            setEmailMessage('');
        };

        window.sendEmail = async function(event) {
            event.preventDefault();
            if (emailInviteSending) return;

            const meetingId = document.getElementById('email-meeting-id').value;
            const emails = document.getElementById('email-emails-input').value.trim();
            const subject = document.getElementById('email-subject').value.trim();
            const message = document.getElementById('email-message').value.trim();
            const sendBtn = document.getElementById('email-send-btn');
            const tokenTag = document.querySelector('meta[name="csrf-token"]');

            if (!emails) {
                setEmailMessage('At least one email is required.', 'error');
                return;
            }

            if (!tokenTag) {
                setEmailMessage('CSRF token missing from page head.', 'error');
                return;
            }

            emailInviteSending = true;

            if (sendBtn) {
                sendBtn.disabled = true;
                sendBtn.dataset.originalHtml = sendBtn.innerHTML;
                sendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Sending...</span>';
            }

            // Immediate visual feedback. Real success is shown only after Laravel confirms it.
            setEmailMessage('Sending invitation…', 'info');

            try {
                const response = await fetch(`/organizer/meetings/${encodeURIComponent(meetingId)}/send-invite`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': tokenTag.content
                    },
                    body: JSON.stringify({ emails, subject, message })
                });

                let data = {};
                try { data = await response.json(); } catch (_) {}

                if (!response.ok) {
                    throw new Error(data.message || `Failed to send invitation (HTTP ${response.status}).`);
                }

                setEmailMessage(data.message || 'Invitation sent successfully!', 'success');

                setTimeout(() => {
                    emailInviteSending = false;
                    if (sendBtn) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = sendBtn.dataset.originalHtml || '<i class="fa-regular fa-envelope"></i><span>Send</span>';
                    }
                    window.closeEmailModal();
                }, 900);

                return;
            } catch (error) {
                console.error('[SmartMeet] email invitation failed', error);
                setEmailMessage(error?.message || 'Failed to send. Please try again.', 'error');
            } finally {
                if (emailInviteSending) {
                    emailInviteSending = false;
                    if (sendBtn) {
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = sendBtn.dataset.originalHtml || '<i class="fa-regular fa-envelope"></i><span>Send</span>';
                    }
                }
            }
        };
    })();
</script>
