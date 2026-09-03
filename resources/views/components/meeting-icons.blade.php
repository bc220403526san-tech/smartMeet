@props(['meeting'])

@php
    $closed = in_array(
        $meeting->status,
        ['completed', 'ended', 'cancelled'],
        true
    );
@endphp


<div class="flex items-center gap-1.5">

    {{-- VIEW --}}
    <a
        href="{{ route('organizer.meetings.show', $meeting) }}"
        title="View meeting"
        class="w-8 h-8 rounded-lg
               bg-gray-100 hover:bg-blue-100
               text-gray-500 hover:text-blue-600
               transition flex items-center justify-center"
    >
        <i class="fa-regular fa-eye text-xs"></i>
    </a>


    @unless($closed)

        {{-- EMAIL INVITE --}}
        <button
            type="button"
            title="Send email invite"
            onclick="openMeetingEmailInvite(
                '{{ $meeting->id }}',
                @js($meeting->title),
                @js(route('organizer.meetings.sendInvite', $meeting))
            )"
            class="w-8 h-8 rounded-lg
                   bg-gray-100 hover:bg-blue-100
                   text-gray-500 hover:text-blue-600
                   transition flex items-center justify-center"
        >
            <i class="fa-regular fa-envelope text-xs"></i>
        </button>


        {{-- COPY INVITE LINK --}}
        <button
            type="button"
            title="Copy invite link"
            onclick="copyLinkFromTable(
                @js(route('meetings.join.link', $meeting->unique_code)),
                this
            )"
            class="w-8 h-8 rounded-lg
                   bg-gray-100 hover:bg-blue-100
                   text-gray-500 hover:text-blue-600
                   transition flex items-center justify-center"
        >
            <i class="fa-solid fa-link text-xs"></i>
        </button>

    @endunless


    {{-- EDIT --}}
    @if($meeting->status === 'upcoming')

        <a
            href="{{ route('organizer.meetings.edit', $meeting) }}"
            title="Edit meeting"
            class="w-8 h-8 rounded-lg
                   bg-gray-100 hover:bg-blue-100
                   text-gray-500 hover:text-blue-600
                   transition flex items-center justify-center"
        >
            <i class="fa-regular fa-pen-to-square text-xs"></i>
        </a>

    @endif


    {{-- CANCEL --}}
    @if(in_array($meeting->status, ['upcoming', 'active'], true))

        <form
            method="POST"
            action="{{ route('organizer.meetings.cancel', $meeting) }}"
            onsubmit="
                return confirm(
                    'Cancel this meeting? Participants will no longer be able to join.'
                );
            "
        >
            @csrf
            @method('PATCH')

            <button
                type="submit"
                title="Cancel meeting"
                class="w-8 h-8 rounded-lg
                       bg-red-50 hover:bg-red-100
                       text-red-500 hover:text-red-700
                       border border-red-100
                       transition flex items-center justify-center"
            >
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </form>

    @elseif($closed)

        <span
            title="{{ ucfirst($meeting->status) }}"
            class="w-8 h-8 rounded-lg
                   bg-gray-50 text-gray-400
                   border border-gray-200
                   flex items-center justify-center"
        >
            <i class="fa-solid fa-lock text-[10px]"></i>
        </span>

    @endif

</div>


@once

    {{-- =========================================================
         EMAIL INVITE MODAL
    ========================================================= --}}
    <div
        id="meeting-email-overlay"
        class="fixed inset-0 z-[9999] hidden
           items-center justify-center
           bg-black/45 backdrop-blur-[2px]
           p-4"
    >

        <div
            class="w-full max-w-md
               bg-white rounded-2xl
               border border-gray-200
               shadow-2xl overflow-hidden"
        >

            {{-- HEADER --}}
            <div
                class="flex items-center justify-between
                   px-5 py-4
                   border-b border-gray-100"
            >

                <div class="min-w-0">
                    <h3 class="font-bold text-gray-800 text-base">
                        Send Email Invitation
                    </h3>

                    <p
                        id="meeting-email-title"
                        class="text-xs text-gray-400 mt-0.5
                           truncate max-w-[300px]"
                    ></p>
                </div>


                <button
                    type="button"
                    onclick="closeMeetingEmailInvite()"
                    class="w-8 h-8 rounded-lg
                       bg-gray-100 hover:bg-gray-200
                       text-gray-500
                       flex items-center justify-center"
                >
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>

            </div>


            {{-- FORM --}}
            <form
                id="meeting-email-form"
                class="p-5 space-y-4"
                onsubmit="sendMeetingEmailInvite(event)"
            >

                <input
                    type="hidden"
                    id="meeting-email-url"
                >


                {{-- EMAILS --}}
                <div>

                    <label
                        for="meeting-email-address"
                        class="block text-xs
                           font-semibold text-gray-600
                           mb-1.5"
                    >
                        Email Address
                    </label>


                    <textarea
                        id="meeting-email-address"
                        rows="2"
                        required
                        placeholder="email@example.com"
                        class="w-full px-3.5 py-2.5
                           rounded-xl
                           border border-gray-200
                           text-sm text-gray-700
                           resize-none
                           focus:outline-none
                           focus:border-blue-400
                           focus:ring-2
                           focus:ring-blue-100"
                    ></textarea>

                    <p class="text-[10px] text-gray-400 mt-1">
                        Multiple emails can be separated with commas.
                    </p>

                </div>


                {{-- SUBJECT --}}
                <div>

                    <label
                        for="meeting-email-subject"
                        class="block text-xs
                           font-semibold text-gray-600
                           mb-1.5"
                    >
                        Subject
                    </label>


                    <input
                        id="meeting-email-subject"
                        type="text"
                        maxlength="255"
                        class="w-full px-3.5 py-2.5
                           rounded-xl
                           border border-gray-200
                           text-sm text-gray-700
                           focus:outline-none
                           focus:border-blue-400
                           focus:ring-2
                           focus:ring-blue-100"
                    >

                </div>


                {{-- MESSAGE --}}
                <div>

                    <label
                        for="meeting-email-message"
                        class="block text-xs
                           font-semibold text-gray-600
                           mb-1.5"
                    >
                        Message
                        <span class="font-normal text-gray-400">
                        (optional)
                    </span>
                    </label>


                    <textarea
                        id="meeting-email-message"
                        rows="3"
                        maxlength="1500"
                        placeholder="Hello, please join our meeting..."
                        class="w-full px-3.5 py-2.5
                           rounded-xl
                           border border-gray-200
                           text-sm text-gray-700
                           resize-none
                           focus:outline-none
                           focus:border-blue-400
                           focus:ring-2
                           focus:ring-blue-100"
                    ></textarea>

                </div>


                {{-- RESPONSE MESSAGE --}}
                <div
                    id="meeting-email-msg"
                    class="hidden
                       text-xs
                       rounded-xl
                       px-3 py-2"
                ></div>


                {{-- ACTIONS --}}
                <div
                    class="flex items-center
                       justify-end gap-2 pt-1"
                >

                    <button
                        type="button"
                        onclick="closeMeetingEmailInvite()"
                        class="px-4 py-2
                           rounded-xl
                           bg-gray-100 hover:bg-gray-200
                           text-gray-600
                           text-xs font-semibold"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        id="meeting-email-send-btn"
                        class="px-4 py-2
                           rounded-xl
                           bg-blue-600 hover:bg-blue-700
                           text-white
                           text-xs font-semibold
                           flex items-center gap-2"
                    >
                        <i class="fa-regular fa-envelope"></i>
                        <span>Send</span>
                    </button>

                </div>

            </form>

        </div>

    </div>


    <script>

        /*
        |--------------------------------------------------------------------------
        | SmartMeet Email Invite
        |--------------------------------------------------------------------------
        |
        | Previous working logic restored:
        | - same organizer sendInvite route
        | - multiple emails
        | - same subject/message payload
        | - CSRF
        | - duplicate-click protection
        |
        */

        const MEETING_EMAIL_CSRF = @json(csrf_token());

        let meetingEmailInviteSending = false;


        function setMeetingEmailMessage(
            message = '',
            type = ''
        ) {

            const box =
                document.getElementById(
                    'meeting-email-msg'
                );


            if (!box) {
                return;
            }


            box.textContent = message;


            box.className =
                'hidden text-xs rounded-xl px-3 py-2';


            if (!message) {
                return;
            }


            box.classList.remove('hidden');


            if (type === 'success') {

                box.classList.add(
                    'bg-green-50',
                    'text-green-700',
                    'border',
                    'border-green-100'
                );

            } else {

                box.classList.add(
                    'bg-red-50',
                    'text-red-600',
                    'border',
                    'border-red-100'
                );
            }
        }



        function openMeetingEmailInvite(
            meetingId,
            meetingTitle,
            url
        ) {

            /*
             * Request already running ho to modal
             * dobara manipulate nahi karna.
             */
            if (meetingEmailInviteSending) {
                return;
            }


            const overlay =
                document.getElementById(
                    'meeting-email-overlay'
                );


            if (!overlay) {
                return;
            }


            document.getElementById(
                'meeting-email-url'
            ).value = url;


            document.getElementById(
                'meeting-email-title'
            ).textContent =
                meetingTitle || 'Meeting';


            const emails =
                document.getElementById(
                    'meeting-email-address'
                );


            const subject =
                document.getElementById(
                    'meeting-email-subject'
                );


            const message =
                document.getElementById(
                    'meeting-email-message'
                );


            if (emails) {
                emails.value = '';
            }


            if (subject) {

                subject.value =
                    `You're invited: ${meetingTitle}`;

            }


            if (message) {
                message.value = '';
            }


            setMeetingEmailMessage();


            overlay.classList.remove(
                'hidden'
            );

            overlay.classList.add(
                'flex'
            );


            setTimeout(() => {

                emails?.focus();

            }, 50);
        }



        function closeMeetingEmailInvite()
        {
            /*
             * Sending ke darmiyan modal close na ho.
             * Is se accidental second request / state issue nahi hoga.
             */
            if (meetingEmailInviteSending) {
                return;
            }


            const overlay =
                document.getElementById(
                    'meeting-email-overlay'
                );


            if (!overlay) {
                return;
            }


            overlay.classList.add(
                'hidden'
            );

            overlay.classList.remove(
                'flex'
            );


            document.getElementById(
                'meeting-email-form'
            )?.reset();


            setMeetingEmailMessage();
        }



        async function sendMeetingEmailInvite(event)
        {
            event.preventDefault();


            /*
             * IMPORTANT:
             * Double click / repeated click protection.
             *
             * Jab pehli request chal rahi ho gi,
             * doosra click bilkul request nahi bheje ga.
             */
            if (meetingEmailInviteSending) {
                return;
            }


            const url =
                document.getElementById(
                    'meeting-email-url'
                )?.value || '';


            const emails =
                document.getElementById(
                    'meeting-email-address'
                )?.value.trim() || '';


            const subject =
                document.getElementById(
                    'meeting-email-subject'
                )?.value.trim() || '';


            const message =
                document.getElementById(
                    'meeting-email-message'
                )?.value.trim() || '';


            const sendBtn =
                document.getElementById(
                    'meeting-email-send-btn'
                );


            if (!url) {

                setMeetingEmailMessage(
                    'Meeting invite URL is unavailable.',
                    'error'
                );

                return;
            }


            if (!emails) {

                setMeetingEmailMessage(
                    'At least one email address is required.',
                    'error'
                );

                return;
            }


            /*
             * LOCK immediately BEFORE fetch.
             */
            meetingEmailInviteSending = true;


            const originalHtml =
                sendBtn?.innerHTML || 'Send';


            if (sendBtn) {

                sendBtn.disabled = true;

                sendBtn.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );


                sendBtn.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin"></i>' +
                    '<span>Sending...</span>';
            }


            setMeetingEmailMessage(
                'Sending invitation...',
                'success'
            );


            try {

                /*
                 * SAME payload as your existing
                 * Organizer MeetingController::sendInvite()
                 */
                const response =
                    await fetch(
                        url,
                        {
                            method: 'POST',

                            credentials:
                                'same-origin',

                            headers: {

                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                MEETING_EMAIL_CSRF
                            },

                            body: JSON.stringify({
                                emails: emails,
                                subject: subject,
                                message: message
                            })
                        }
                    );


                let data = {};


                try {

                    data =
                        await response.json();

                } catch (error) {
                    data = {};
                }


                if (!response.ok) {

                    let errorMessage =
                        data.message ||
                        `Email invite failed (HTTP ${response.status})`;


                    /*
                     * Laravel validation error
                     */
                    if (data.errors) {

                        const firstError =
                            Object
                                .values(data.errors)
                                .flat()[0];


                        if (firstError) {
                            errorMessage =
                                firstError;
                        }
                    }


                    throw new Error(
                        errorMessage
                    );
                }


                /*
                 * SUCCESS
                 */
                setMeetingEmailMessage(
                    data.message ||
                    'Invitation sent successfully.',
                    'success'
                );


                /*
                 * Email field empty kar dein taa-ke
                 * accidentally same email dobara send na ho.
                 */
                const emailField =
                    document.getElementById(
                        'meeting-email-address'
                    );


                if (emailField) {
                    emailField.value = '';
                }


                /*
                 * Short delay then close.
                 *
                 * IMPORTANT:
                 * Lock abhi bhi active hai.
                 * User multiple Send clicks nahi kar sakta.
                 */
                await new Promise(
                    resolve =>
                        setTimeout(
                            resolve,
                            1300
                        )
                );


                const overlay =
                    document.getElementById(
                        'meeting-email-overlay'
                    );


                overlay?.classList.add(
                    'hidden'
                );


                overlay?.classList.remove(
                    'flex'
                );


            } catch (error) {

                console.error(
                    '[SmartMeet] email invite failed:',
                    error
                );


                setMeetingEmailMessage(
                    error?.message ||
                    'Failed to send invitation. Please try again.',
                    'error'
                );


            } finally {

                /*
                 * Release only when request is COMPLETELY finished.
                 */
                meetingEmailInviteSending = false;


                if (sendBtn) {

                    sendBtn.disabled = false;


                    sendBtn.classList.remove(
                        'opacity-60',
                        'cursor-not-allowed'
                    );


                    sendBtn.innerHTML =
                        originalHtml;
                }
            }
        }



        /*
        |--------------------------------------------------------------------------
        | COPY LINK
        |--------------------------------------------------------------------------
        */

        async function copyLinkFromTable(
            url,
            button
        ) {

            try {

                if (
                    navigator.clipboard &&
                    navigator.clipboard.writeText
                ) {

                    await navigator.clipboard.writeText(
                        url
                    );

                } else {

                    const textarea =
                        document.createElement(
                            'textarea'
                        );


                    textarea.value = url;

                    textarea.style.position =
                        'fixed';

                    textarea.style.opacity =
                        '0';


                    document.body.appendChild(
                        textarea
                    );


                    textarea.select();


                    document.execCommand(
                        'copy'
                    );


                    textarea.remove();
                }


                const icon =
                    button?.querySelector('i');


                if (icon) {

                    const oldClass =
                        icon.className;


                    icon.className =
                        'fa-solid fa-check text-green-600 text-xs';


                    setTimeout(() => {

                        icon.className =
                            oldClass;

                    }, 1500);
                }


            } catch (error) {

                window.prompt(
                    'Copy meeting link:',
                    url
                );
            }
        }



        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'meeting-email-overlay'
            )
            ?.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target === this &&
                        !meetingEmailInviteSending
                    ) {

                        closeMeetingEmailInvite();
                    }
                }
            );


        document.addEventListener(
            'keydown',
            function(event) {

                if (
                    event.key === 'Escape' &&
                    !meetingEmailInviteSending
                ) {

                    closeMeetingEmailInvite();
                }
            }
        );

    </script>

@endonce
