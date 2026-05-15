<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<!-- Hidden checkbox for sidebar toggle -->
<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen overflow-hidden">

    <!-- OVERLAY (mobile) -->
    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    <!-- SIDEBAR -->
    <x-sidebar.organizer-menu />

    <!-- ══════════════════════════════
         MAIN
    ══════════════════════════════ -->
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">


        <!-- HEADER -->
        <x-header.page-title
            title="Organizer Dashboard"
        />

        <!-- CONTENT -->
        <div class="p-4 sm:p-6 bg-gray-50 rounded-xl mt-3 overflow-y-auto flex-1">

            <div class="mt-6 flex justify-center">

                <div class="w-full max-w-3xl bg-white shadow-md border-transparent rounded-xl p-8 relative border-l-4
                hover:border-blue-600 hover:shadow-lg transition-all duration-300 ease-in-out">

                    <!-- TITLE -->
                    <h2 class="text-2xl font-semibold text-gray-800">Add New Participant</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Invite a new member to join the meeting ecosystem.
                    </p>

                    <!-- PROFILE -->
                    <div class="flex gap-5 mt-6 items-center">

                        <!-- IMAGE BOX -->
                        <div class="w-20 h-20 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center bg-gray-200 relative overflow-hidden">

                            <!-- Preview Image -->
                            <img id="preview" class="hidden w-full h-full object-cover">

                            <!-- Default Icon -->
                            <i id="icon" class="fa fa-user text-gray-400 text-xl"></i>

                            <!-- Camera -->
                            <label for="avatar" class="absolute -bottom-1 -right-1 bg-white p-1.5 rounded-full shadow cursor-pointer">
                                <i class="fa fa-camera text-xs text-gray-500"></i>
                            </label>

                            <!-- FILE INPUT -->
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">
                        </div>

                        <!-- TEXT -->
                        <div>
                            <p class="text-sm font-medium text-gray-700">Profile Identity</p>
                            <p class="text-xs text-gray-400">JPG, PNG or GIF. Max size 2MB.</p>

                            <!-- Upload Button -->
                            <label for="avatar" class="text-blue-600 text-sm mt-1 font-medium cursor-pointer">
                                Upload Avatar
                            </label>
                        </div>

                    </div>

                    <!-- FORM -->
                    <div class="grid grid-cols-2 gap-5 mt-6">

                        <!-- FULL NAME -->
                        <div>
                            <label class="text-xs font-semibold text-gray-500">FULL NAME</label>
                            <input type="text" placeholder="Name"
                                   class="mt-1 w-full px-4 py-2.5 bg-gray-100 rounded-lg text-sm outline-none">
                        </div>

                        <!-- EMAIL -->
                        <div>
                            <label class="text-xs font-semibold text-gray-500">EMAIL ADDRESS</label>
                            <input type="email" placeholder="Email"
                                   class="mt-1 w-full px-4 py-2.5 bg-gray-100 rounded-lg text-sm outline-none">
                        </div>

                        <!-- ROLE -->
                        <div>
                            <label class="text-xs font-semibold text-gray-500">ROLE TYPE</label>
                            <div class="relative mt-1">
                                <select class="w-full px-4 py-2.5 bg-gray-100 rounded-lg text-sm appearance-none outline-none">
                                    <option>Participant</option>
                                    <option>Organizer</option>
                                </select>

                                <!-- dropdown icon -->
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                                    <i class="fa fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ACCESS STATUS -->
                        <div>
                            <label class="text-xs font-semibold text-gray-500">ACCESS STATUS</label>

                            <div class="mt-1 flex items-center justify-between bg-gray-100 px-4 py-2.5 rounded-xl">
                                <span class="text-sm text-gray-700">Active Account</span>

                                <!-- TOGGLE -->
                                <label class="cursor-pointer relative inline-flex items-center">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-10 h-5 rounded-full bg-gray-300 peer-checked:bg-blue-600 transition-colors duration-300 relative">
                                        <div class="absolute top-[3px] left-[3px] w-[14px] h-[14px] bg-white rounded-full shadow
                            transition-transform duration-300"></div>
                                    </div>
                                </label>

                            </div>
                        </div>

                    </div>

                    <!-- DIVIDER -->
                    <div class="border-t mt-8 pt-6 flex justify-end gap-6 items-center">

                        <button class="text-gray-500 text-sm">Cancel</button>

                        <button class="bg-blue-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-blue-700 text-sm font-medium">
                            Add Participant
                        </button>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<div class="fixed inset-0 bg-black/40 hidden peer-checked:flex items-center justify-center z-50">

    <div class="bg-white w-full max-w-2xl rounded-2xl p-6 relative">

        <!-- CLOSE -->
        <label for="add-modal"
               class="absolute top-4 right-4 text-gray-400 text-xl cursor-pointer">&times;</label>

        <h2 class="text-xl font-semibold">Add New Participant</h2>
        <p class="text-sm text-gray-400 mt-1">
            Invite a new member to join the meeting ecosystem.
        </p>

        <!-- PROFILE -->
        <div class="flex gap-4 mt-6">
            <div class="w-20 h-20 border-2 border-dashed rounded-xl flex items-center justify-center bg-gray-50 relative">
                <i class="fa fa-user text-gray-300 text-xl"></i>

                <div class="absolute bottom-1 right-1 bg-white p-1 rounded-full shadow">
                    <i class="fa fa-camera text-xs text-gray-500"></i>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium">Profile Identity</p>
                <p class="text-xs text-gray-400">Max 2MB</p>
                <button class="text-blue-600 text-sm mt-1">Upload Avatar</button>
            </div>
        </div>

        <!-- FORM -->
        <div class="grid grid-cols-2 gap-4 mt-6">

            <input type="text" placeholder="Full Name"
                   class="px-3 py-2 bg-gray-100 rounded-lg text-sm outline-none">

            <input type="email" placeholder="Email"
                   class="px-3 py-2 bg-gray-100 rounded-lg text-sm outline-none">

            <select class="px-3 py-2 bg-gray-100 rounded-lg text-sm">
                <option>Participant</option>
                <option>Organizer</option>
            </select>

            <!-- ACTIVE -->
            <div class="flex items-center justify-between bg-gray-100 px-3 py-2 rounded-lg">
                <span class="text-sm">Active</span>
                <input type="checkbox" checked>
            </div>

        </div>

        <div class="border-t my-6"></div>

        <!-- ACTIONS -->
        <div class="flex justify-end gap-4">
            <label for="add-modal" class="text-gray-500 cursor-pointer">Cancel</label>

            <button class="bg-blue-600 text-white px-5 py-2 rounded-lg">
                Add Participant
            </button>
        </div>

    </div>

    </div>

</body>
</html>
