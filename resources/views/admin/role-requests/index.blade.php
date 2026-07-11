<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Admin Dashboard" />
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        {{-- Page heading --}}
        <div class="flex items-center justify-between mb-5 sm:mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Role Change Requests</h1>
                <p class="text-gray-400 text-xs sm:text-sm mt-0.5">
                    Review and manage participant requests to become organizers.
                </p>
            </div>

            @if($requests->where('status', 'pending')->count() > 0)
                <span class="hidden sm:inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 text-xs font-medium px-3 py-1.5 rounded-full border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $requests->where('status', 'pending')->count() }} pending review
                </span>
            @endif
        </div>

        {{-- Table card --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[720px]">
                    <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/80">
                        <th class="py-3.5 px-4 font-medium">User</th>
                        <th class="px-4 font-medium">Subject</th>
                        <th class="px-4 font-medium">Requested Role</th>
                        <th class="px-4 font-medium">Status</th>
                        <th class="px-4 font-medium text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50/60 transition-colors duration-150">
                            {{-- User --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-semibold shrink-0">
                                        {{ strtoupper(substr($req->user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-gray-800 font-medium truncate">{{ $req->user->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $req->user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Subject --}}
                            <td class="px-4 text-gray-600">{{ $req->subject }}</td>

                            {{-- Requested role --}}
                            <td class="px-4">
                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                        {{ ucfirst($req->requested_role) }}
                                    </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4">
                                @php
                                    $statusStyles = [
                                        'pending'  => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
                                        'approved' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
                                        'rejected' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
                                    ];
                                    $dotStyles = [
                                        'pending'  => 'bg-amber-500',
                                        'approved' => 'bg-emerald-500',
                                        'rejected' => 'bg-rose-500',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusStyles[$req->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotStyles[$req->status] ?? 'bg-gray-400' }}"></span>
                                        {{ ucfirst($req->status) }}
                                    </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4">
                                @if($req->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.role-requests.approve', $req) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.role-requests.reject', $req) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" />
                                                </svg>
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="flex items-center justify-end gap-3">
                                        <p class="text-xs text-gray-400 italic">Already reviewed</p>
                                        <form method="POST" action="{{ route('admin.role-requests.destroy', $req) }}"
                                              onsubmit="return confirm('Remove this request permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-red-600 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-14 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M9 8h6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-400 text-sm">No role change requests yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($requests->hasPages())
            <div class="pt-1">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
