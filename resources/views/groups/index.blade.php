<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Patient Groups (Families)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                        <!-- Search Form -->
                        <form method="GET" action="{{ route('groups.index') }}" class="w-full md:w-auto flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Search groups..." 
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm w-full md:w-80 focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-md px-4 py-2 hover:bg-gray-700 dark:hover:bg-gray-100 font-semibold text-sm">
                                Search
                            </button>
                            @if(request('search'))
                                <a href="{{ route('groups.index') }}" class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 font-semibold text-sm">
                                    Clear
                                </a>
                            @endif
                        </form>

                        <!-- Create Group Button -->
                        <a href="{{ route('groups.create') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded shadow-md text-sm transition duration-150">
                            Create New Group
                        </a>
                    </div>

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-300 p-3 rounded-md mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Groups Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Group Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Members
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider w-24">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                @forelse ($groups as $group)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $group->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                            {{ $group->description ?: 'No description' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                            @if($group->members->count() > 0)
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($group->members as $member)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300">
                                                            {{ $member->name }} ({{ $member->patient_id }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-xs italic text-gray-400">No members</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center items-center gap-3">
                                                <a href="{{ route('groups.edit', $group->id) }}"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Edit">
                                                    <i class="fas fa-edit text-lg"></i>
                                                </a>
                                                <form method="POST" action="{{ route('groups.destroy', $group->id) }}" onsubmit="return confirm('Are you sure you want to delete this group? Patients will be removed from the group but not deleted.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Delete">
                                                        <i class="fas fa-trash text-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                            No groups found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $groups->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
