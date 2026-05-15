<div class="bg-gray-100 border border-blue-400 p-5 rounded-xl shadow-lg">

    <!-- Header -->
    <div class="flex justify-between mb-4">
        <h2 class="font-semibold">{{ $title ?? 'Recent Activity' }}</h2>
        <a href="#" class="text-blue-500 text-sm">View All</a>
    </div>

    <!-- Activity List -->
    <div class="space-y-4">

        @foreach($activities as $activity)
            <div class="flex items-center gap-3">

                <!-- Image -->
                <div class="w-10 h-10 bg-gray-300 rounded-full overflow-hidden">
                    <img src="{{ asset($activity['image']) }}"
                         class="w-full h-full object-cover rounded-full">
                </div>

                <!-- Content -->
                <div>
                    <p class="font-medium">{{ $activity['name'] }}</p>
                    <p class="text-sm text-gray-500">{{ $activity['message'] }}</p>
                    <p class="text-xs text-gray-400">{{ $activity['time'] }}</p>
                </div>

            </div>
        @endforeach

    </div>

</div>
