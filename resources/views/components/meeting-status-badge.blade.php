@props(['meeting'])
<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium
    {{ $meeting->status === 'upcoming'  ? 'bg-blue-100 text-blue-700'     : '' }}
    {{ $meeting->status === 'active'    ? 'bg-green-100 text-green-700'   : '' }}
    {{ $meeting->status === 'completed' ? 'bg-gray-100 text-gray-600'     : '' }}
    {{ $meeting->status === 'cancelled' ? 'bg-red-100 text-red-600'       : '' }}
    {{ $meeting->status === 'flagged'   ? 'bg-yellow-100 text-yellow-700' : '' }}">
    <span class="w-1.5 h-1.5 rounded-full
        {{ $meeting->status === 'upcoming'  ? 'bg-blue-500'   : '' }}
        {{ $meeting->status === 'active'    ? 'bg-green-500'  : '' }}
        {{ $meeting->status === 'completed' ? 'bg-gray-400'   : '' }}
        {{ $meeting->status === 'cancelled' ? 'bg-red-500'    : '' }}
        {{ $meeting->status === 'flagged'   ? 'bg-yellow-500' : '' }}">
    </span>
    {{ ucfirst($meeting->status) }}
</span>
