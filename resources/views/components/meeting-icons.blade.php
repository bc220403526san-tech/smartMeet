@props(['meeting'])

@php
    $closed = in_array(
        $meeting->status,
        ['completed', 'ended', 'cancelled'],
        true
    );
@endphp

<div class="flex items-center gap-1.5">

    {{-- View --}}
    <a href="{{ route('organizer.meetings.show', $meeting) }}"
       title="View meeting"
       class="w-8 h-8 rounded-lg
              bg-gray-100 hover:bg-blue-100
              text-gray-500 hover:text-blue-600
              transition flex items-center justify-center">
        <i class="fa-regular fa-eye text-xs"></i>
    </a>


    @unless($closed)

        {{-- Email Invite --}}
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
                   transition flex items-center justify-center">

            <i class="fa-regular fa-envelope text-xs"></i>
        </button>


        {{-- Copy Invite Link --}}
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
                   transition flex items-center justify-center">

            <i class="fa-solid fa-link text-xs"></i>
        </button>

    @endunless


    {{-- Edit --}}
    @if($meeting->status === 'upcoming')

        <a href="{{ route('organizer.meetings.edit', $meeting) }}"
           title="Edit meeting"
           class="w-8 h-8 rounded-lg
                  bg-gray-100 hover:bg-blue-100
                  text-gray-500 hover:text-blue-600
                  transition flex items-center justify-center">

            <i class="fa-regular fa-pen-to-square text-xs"></i>
        </a>

    @endif


    {{-- Cancel --}}
    @if(in_array($meeting->status, ['upcoming', 'active'], true))

        <form
            method="POST"
            action="{{ route('organizer.meetings.cancel', $meeting) }}"
            onsubmit="
                return confirm(
                    'Cancel this meeting? Participants will no longer be able to join.'
                );
            ">

            @csrf
            @method('PATCH')

            <button
                type="submit"
                title="Cancel meeting"
                class="w-8 h-8 rounded-lg
                       bg-red-50 hover:bg-red-100
                       text-red-500 hover:text-red-700
                       transition flex items-center justify-center
                       border border-red-100">

                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

        </form>

    @elseif($closed)

        <span
            title="{{ ucfirst($meeting->status) }}"
            class="w-8 h-8 rounded-lg
                   border border-gray-200
                   bg-gray-50 text-gray-400
                   flex items-center justify-center">

            <i class="fa-solid fa-lock text-[10px]"></i>
        </span>

    @endif

</div>


@once

    {{-- EMAIL INVITE MODAL --}}
    <div
        id="meeting-email-modal"
        class="fixed inset-0 z-[9999] hidden
               items-center justify-center
               bg-black/40 backdrop-blur-[2px]
               p-4">

        <div
            class="w-full max-w-md
                   bg-white rounded-2xl
                   shadow-2xl border border-gray-200
                   overflow-hidden">

            {{-- Header --}}
            <div
                class="flex items-center justify-between
                       px-5 py-4
                       border-b border-gray-100">

                <div>
                    <h3 class="font-bold text-gray-800 text-base">
                        Send Email Invite
                    </h3>

                    <p
                        id="meeting-email-title"
                        class="text-xs text-gray-400 mt-0.5">
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeMeetingEmailInvite()"
                    class="w-8 h-8 rounded-lg
                           bg-gray-100 hover:bg-gray-200
                           text-gray-500
                           flex items-center justify-center">

                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>

            </div>


            {{-- Form --}}
            <form
                id="meeting-email-form"
                class="p-5 space-y-4">

                <input
                    type="hidden"
                    id="meeting-email-url">


                <div>
                    <label
                        class="block text-xs font-semibold
                               text-gray-600 mb-1.5">
                        Email address
                    </label>

                    <input
                        type="text"
                        id="meeting-email-address"
                        placeholder="example@gmail.com"
                        required
                        class="w-full px-3.5 py-2.5
                               rounded-xl border border-gray-200
                               text-sm text-gray-700
                               focus:outline-none
                               focus:border-blue-400
                               focus:ring-2
                               focus:ring-blue-100">
                </div>


                <div>
                    <label
                        class="block text-xs font-semibold
                               text-gray-600 mb-1.5">
                        Subject
                        <span class="text-gray-400 font-normal">
                            Optional
                        </span>
                    </label>

                    <input
                        type="text"
                        id="meeting-email-subject"
                        maxlength="255"
                        placeholder="Meeting invitation"
                        class="w-full px-3.5 py-2.5
                               rounded-xl border border-gray-200
                               text-sm text-gray-700
                               focus:outline-none
                               focus:border-blue-400
                               focus:ring-2
                               focus:ring-blue-100">
                </div>


                <div>
                    <label
                        class="block text-xs font-semibold
                               text-gray-600 mb-1.5">
                        Message
                        <span class="text-gray-400 font-normal">
                            Optional
                        </span>
                    </label>

                    <textarea
                        id="meeting-email-message"
                        rows="3"
                        maxlength="1500"
                        placeholder="Join the meeting using the invitation link."
                        class="w-full px-3.5 py-2.5
                               rounded-xl border border-gray-200
                               text-sm text-gray-700
                               resize-none
                               focus:outline-none
                               focus:border-blue-400
                               focus:ring-2
                               focus:ring-blue-100"></textarea>
                </div>


                <div
                    id="meeting-email-error"
                    class="hidden
                           text-xs text-red-600
                           bg-red-50 border border-red-100
                           rounded-xl px-3 py-2">
                </div>


                <div
                    id="meeting-email-success"
                    class="hidden
                           text-xs text-green-700
                           bg-green-50 border border-green-100
                           rounded-xl px-3 py-2">
                </div>


                <div class="flex justify-end gap-2 pt-1">

                    <button
                        type="button"
                        onclick="closeMeetingEmailInvite()"
                        class="px-4 py-2
                               rounded-xl
                               text-xs font-semibold
                               text-gray-600
                               bg-gray-100 hover:bg-gray-200">

                        Cancel
                    </button>


                    <button
                        type="submit"
                        id="meeting-email-send-btn"
                        class="px-4 py-2
                               rounded-xl
                               text-xs font-semibold
                               text-white
                               bg-blue-600 hover:bg-blue-700
                               flex items-center gap-2">

                        <i class="fa-regular fa-paper-plane"></i>

                        <span>
                            Send Invite
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>


    <script>

        function copyLinkFromTable(url, button)
        {
            if (navigator.clipboard?.writeText) {

                navigator.clipboard
                    .writeText(url)
                    .then(() => {

                        const icon =
                            button?.querySelector('i');

                        if (!icon) {
                            return;
                        }

                        const oldClass =
                            icon.className;

                        icon.className =
                            'fa-solid fa-check text-green-600 text-xs';

                        setTimeout(() => {

                            icon.className =
                                oldClass;

                        }, 1600);

                    })
                    .catch(() => {

                        window.prompt(
                            'Copy meeting link:',
                            url
                        );

                    });

                return;
            }


            window.prompt(
                'Copy meeting link:',
                url
            );
        }



        function openMeetingEmailInvite(
            meetingId,
            meetingTitle,
            url
        ) {

            const modal =
                document.getElementById(
                    'meeting-email-modal'
                );

            document.getElementById(
                'meeting-email-url'
            ).value = url;


            document.getElementById(
                'meeting-email-title'
            ).textContent =
                meetingTitle || 'Meeting';


            document.getElementById(
                'meeting-email-address'
            ).value = '';


            document.getElementById(
                'meeting-email-subject'
            ).value = '';


            document.getElementById(
                'meeting-email-message'
            ).value = '';


            document.getElementById(
                'meeting-email-error'
            ).classList.add('hidden');


            document.getElementById(
                'meeting-email-success'
            ).classList.add('hidden');


            modal.classList.remove('hidden');

            modal.classList.add('flex');


            setTimeout(() => {

                document.getElementById(
                    'meeting-email-address'
                )?.focus();

            }, 100);
        }



        function closeMeetingEmailInvite()
        {
            const modal =
                document.getElementById(
                    'meeting-email-modal'
                );

            modal.classList.add('hidden');

            modal.classList.remove('flex');
        }



        document
            .getElementById('meeting-email-modal')
            ?.addEventListener(
                'click',
                function(event) {

                    if (event.target === this) {
                        closeMeetingEmailInvite();
                    }

                }
            );



        document
            .getElementById('meeting-email-form')
            ?.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();


                    const url =
                        document.getElementById(
                            'meeting-email-url'
                        ).value;


                    const email =
                        document.getElementById(
                            'meeting-email-address'
                        ).value.trim();


                    const subject =
                        document.getElementById(
                            'meeting-email-subject'
                        ).value.trim();


                    const message =
                        document.getElementById(
                            'meeting-email-message'
                        ).value.trim();


                    const errorBox =
                        document.getElementById(
                            'meeting-email-error'
                        );


                    const successBox =
                        document.getElementById(
                            'meeting-email-success'
                        );


                    const button =
                        document.getElementById(
                            'meeting-email-send-btn'
                        );


                    errorBox.classList.add('hidden');

                    successBox.classList.add('hidden');


                    if (!email) {

                        errorBox.textContent =
                            'Please enter an email address.';

                        errorBox.classList.remove(
                            'hidden'
                        );

                        return;
                    }


                    button.disabled = true;

                    button.classList.add(
                        'opacity-60',
                        'cursor-not-allowed'
                    );


                    const originalHtml =
                        button.innerHTML;


                    button.innerHTML =
                        '<i class="fa-solid fa-spinner fa-spin"></i>' +
                        '<span>Sending...</span>';


                    try {

                        const response =
                            await fetch(
                                url,
                                {
                                    method: 'POST',

                                    credentials:
                                        'same-origin',

                                    headers: {
                                        'Accept':
                                            'application/json',

                                        'Content-Type':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                ?.getAttribute(
                                                    'content'
                                                ) || ''
                                    },

                                    body: JSON.stringify({
                                        emails: email,
                                        subject: subject,
                                        message: message
                                    })
                                }
                            );


                        const data =
                            await response
                                .json()
                                .catch(() => ({}));


                        if (!response.ok) {

                            let errorMessage =
                                data.message ||
                                'Email could not be sent.';


                            if (data.errors) {

                                const firstError =
                                    Object.values(
                                        data.errors
                                    )
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


                        successBox.textContent =
                            data.message ||
                            'Invitation sent successfully.';


                        successBox.classList.remove(
                            'hidden'
                        );


                        document.getElementById(
                            'meeting-email-address'
                        ).value = '';


                        setTimeout(() => {

                            closeMeetingEmailInvite();

                        }, 1800);


                    } catch (error) {

                        console.error(
                            '[SmartMeet] email invite failed',
                            error
                        );


                        errorBox.textContent =
                            error.message ||
                            'Email could not be sent.';


                        errorBox.classList.remove(
                            'hidden'
                        );


                    } finally {

                        button.disabled = false;

                        button.classList.remove(
                            'opacity-60',
                            'cursor-not-allowed'
                        );

                        button.innerHTML =
                            originalHtml;
                    }

                }
            );


        document
            .addEventListener(
                'keydown',
                function(event) {

                    if (event.key === 'Escape') {
                        closeMeetingEmailInvite();
                    }

                }
            );

    </script>

@endonce
