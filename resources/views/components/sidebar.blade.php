<ul class="mt-10 space-y-1">

    @if(auth()->user()->role === 'admin')
        @include('components.sidebar.admin-menu')
    @endif

    @if(auth()->user()->role === 'organizer')
        @include('components.sidebar.organizer-menu')
    @endif

    @if(auth()->user()->role === 'participant')
        @include('components.sidebar.participant-menu')
    @endif

</ul>
