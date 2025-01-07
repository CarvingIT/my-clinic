<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Create User Button -->

                    <div class="flex justify-end mb-5">
                        <a id="create-user-button" href="{{ route('users.create') }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow">
                            Create User
                        </a>
                    </div>

                    {{-- To disable create button --}}
                    {{-- <script>
                        const button = document.getElementById('create-user-button');
                        button.classList.add('opacity-50', 'cursor-not-allowed');
                        button.setAttribute('onclick', 'return false;');
                    </script> --}}


                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 p-2 rounded mb-5">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Users Table -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col"
                                    class="px-2 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Edit
                                </th>
                                <th scope="col"
                                    class="px-2 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Delete
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirmDelete()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this user?');
    }
</script>
