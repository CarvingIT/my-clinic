<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <i class="fas fa-users-cog text-blue-600"></i>
                    {{ __('messages.Staff') }} & {{ __('messages.Branch') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ __('messages.Manage Staff and Branches') }}</p>
            </div>
            
            <!-- Dynamic Header Action Buttons -->
            <div id="quick-action-container">
                <a id="btn-create-staff" href="{{ route('users.create') }}"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                    <i class="fas fa-user-plus text-xs"></i>
                    <span>{{ __('messages.Create Staff') }}</span>
                </a>

                <button id="btn-add-branch" onclick="openAddBranchModal()" type="button" style="display: none;"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold py-2.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus text-xs"></i>
                    <span>{{ __('messages.Add Branch') }}</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stat Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Total Staff Card -->
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('messages.Staff Users') }}</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 mt-1">{{ count($users) }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>

                <!-- Total Branches Card -->
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('messages.Clinic Branches') }}</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 mt-1">{{ count($branches) }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                        <i class="fas fa-clinic-medical"></i>
                    </div>
                </div>

                <!-- System Status Card -->
                <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow sm:col-span-2 lg:col-span-1">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">System Status</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Fully Operational
                            </span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Flash Notifications -->
            @if (session('success'))
                <div id="flash-success" class="flex items-center justify-between bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                    <button onclick="document.getElementById('flash-success').remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div id="flash-errors" class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center text-sm font-bold shadow-sm shrink-0 mt-0.5">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold">Please check errors:</h4>
                            <ul class="list-disc pl-4 text-xs mt-1 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="document.getElementById('flash-errors').remove()" class="text-rose-400 hover:text-rose-600 p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Main Content Container -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                
                <!-- Pill Navigation Header -->
                <div class="p-4 sm:p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="inline-flex p-1.5 bg-slate-200/60 rounded-2xl gap-1">
                        <button id="tab-users-btn" onclick="switchTab('users')"
                            class="tab-btn px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 flex items-center gap-2 bg-white text-blue-600 shadow-sm">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ __('messages.Staff Users') }}</span>
                            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 font-semibold">{{ count($users) }}</span>
                        </button>
                        
                        <button id="tab-branches-btn" onclick="switchTab('branches')"
                            class="tab-btn px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-200 flex items-center gap-2 text-slate-600 hover:text-slate-900">
                            <i class="fas fa-building"></i>
                            <span>{{ __('messages.Clinic Branches') }}</span>
                            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-slate-300/60 text-slate-700 font-semibold">{{ count($branches) }}</span>
                        </button>
                    </div>

                    <!-- Live Filter Search Box -->
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="{{ __('messages.search') }}..."
                            class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-xs">
                    </div>
                </div>

                <div class="p-6">
                    <!-- Tab Content 1: Staff Users -->
                    <div id="tab-users" class="tab-content">
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="min-w-full divide-y divide-slate-100" id="usersTable">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            {{ __('messages.Name') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            {{ __('messages.Email') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            {{ __('messages.Roles') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            {{ __('messages.Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @foreach ($users as $user)
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-bold text-slate-800 search-target">{{ $user->name }}</div>
                                                        <div class="text-xs text-slate-400">ID: #{{ $user->id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 search-target">
                                                <span class="inline-flex items-center gap-1.5">
                                                    <i class="far fa-envelope text-slate-400"></i>
                                                    {{ $user->email }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-wrap gap-1.5">
                                                    @forelse ($user->roles as $role)
                                                        @php
                                                            $roleLower = strtolower($role);
                                                            $badgeStyle = match(true) {
                                                                str_contains($roleLower, 'admin') => 'bg-rose-50 text-rose-700 border-rose-200',
                                                                str_contains($roleLower, 'doctor') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                                default => 'bg-blue-50 text-blue-700 border-blue-200',
                                                            };
                                                        @endphp
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $badgeStyle }}">
                                                            {{ ucfirst($role) }}
                                                        </span>
                                                    @empty
                                                        <span class="text-xs text-slate-400 italic">No role assigned</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-colors"
                                                        title="{{ __('messages.Edit') }}">
                                                        <i class="fas fa-pen text-xs"></i>
                                                    </a>
                                                    
                                                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" onsubmit="return confirmDelete('staff user')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 flex items-center justify-center transition-colors"
                                                            title="{{ __('messages.Delete') }}">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Content 2: Branches -->
                    <div id="tab-branches" class="tab-content hidden space-y-4">
                        <div class="flex justify-between items-center bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">{{ __('messages.Clinic Branches') }}</h4>
                                <p class="text-xs text-slate-500">Manage all registered clinic locations</p>
                            </div>
                            <button onclick="openAddBranchModal()" type="button"
                                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-xs transition-all">
                                <i class="fas fa-plus"></i> {{ __('messages.Add Branch') }}
                            </button>
                        </div>

                        <!-- Branches Table -->
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="min-w-full divide-y divide-slate-100" id="branchesTable">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            #
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            {{ __('messages.Branch Name') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            {{ __('messages.Created At') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            {{ __('messages.Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @forelse ($branches as $index => $branch)
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-400">
                                                {{ sprintf('%02d', $index + 1) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-sm shadow-xs">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                    <span class="text-sm font-bold text-slate-800 search-target">{{ $branch->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-medium">
                                                    <i class="far fa-calendar-alt text-slate-400"></i>
                                                    {{ $branch->created_at ? $branch->created_at->format('M d, Y') : 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button onclick="openEditBranchModal({{ $branch->id }}, '{{ addslashes($branch->name) }}')"
                                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 flex items-center justify-center transition-colors"
                                                        title="{{ __('messages.Edit') }}">
                                                        <i class="fas fa-pen text-xs"></i>
                                                    </button>

                                                    <form method="POST" action="{{ route('branches.destroy', $branch->id) }}" onsubmit="return confirmDelete('branch')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 flex items-center justify-center transition-colors"
                                                            title="{{ __('messages.Delete') }}">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                                <div class="flex flex-col items-center gap-2">
                                                    <i class="fas fa-clinic-medical text-3xl text-slate-300"></i>
                                                    <p class="text-sm font-medium">No branches added yet.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Add Branch Popup Modal -->
    <div id="addBranchModal" class="fixed inset-0 backdrop-blur-sm bg-slate-900/40 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-blue-600"></i> {{ __('messages.Add Branch') }}
                </h3>
                <button type="button" onclick="closeAddBranchModal()" class="w-8 h-8 rounded-full hover:bg-slate-200/60 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            
            <form action="{{ route('branches.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('messages.Branch Name') }}</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" name="name" required placeholder="{{ __('messages.Enter Branch Name') }}"
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeAddBranchModal()"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-colors">
                        {{ __('messages.Close') }}
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all">
                        {{ __('messages.Save Branch') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Branch Popup Modal -->
    <div id="editBranchModal" class="fixed inset-0 backdrop-blur-sm bg-slate-900/40 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-edit text-blue-600"></i> {{ __('messages.Edit Branch') }}
                </h3>
                <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-full hover:bg-slate-200/60 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            
            <form id="editBranchForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('messages.Branch Name') }}</label>
                    <div class="relative">
                        <i class="fas fa-building absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" id="editBranchName" name="name" required placeholder="{{ __('messages.Enter Branch Name') }}"
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-colors">
                        {{ __('messages.Close') }}
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all">
                        {{ __('messages.Update Branch') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    let activeTab = 'users';

    function confirmDelete(type) {
        return confirm(`Are you sure you want to delete this ${type}?`);
    }

    function switchTab(tab) {
        activeTab = tab;
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
            el.classList.add('text-slate-600');
        });

        document.getElementById(`tab-${tab}`).classList.remove('hidden');
        const btn = document.getElementById(`tab-${tab}-btn`);
        btn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
        btn.classList.remove('text-slate-600');

        // Toggle Header Action Buttons
        const btnStaff = document.getElementById('btn-create-staff');
        const btnBranch = document.getElementById('btn-add-branch');
        if (tab === 'branches') {
            if (btnStaff) btnStaff.style.display = 'none';
            if (btnBranch) btnBranch.style.display = 'inline-flex';
        } else {
            if (btnStaff) btnStaff.style.display = 'inline-flex';
            if (btnBranch) btnBranch.style.display = 'none';
        }

        window.location.hash = tab;
        filterTable();
    }

    function openAddBranchModal() {
        document.getElementById('addBranchModal').classList.remove('hidden');
    }

    function closeAddBranchModal() {
        document.getElementById('addBranchModal').classList.add('hidden');
    }

    function openEditBranchModal(id, name) {
        const form = document.getElementById('editBranchForm');
        form.action = `/branches/${id}`;
        document.getElementById('editBranchName').value = name;
        document.getElementById('editBranchModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editBranchModal').classList.add('hidden');
    }

    function filterTable() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const activeTableId = activeTab === 'users' ? 'usersTable' : 'branchesTable';
        const rows = document.querySelectorAll(`#${activeTableId} tbody tr`);

        rows.forEach(row => {
            const targets = row.querySelectorAll('.search-target');
            if (!targets.length) return;
            
            let match = false;
            targets.forEach(t => {
                if (t.textContent.toLowerCase().includes(query)) {
                    match = true;
                }
            });

            row.style.display = match ? '' : 'none';
        });
    }

    // Auto select tab on load if hash present or on validation error
    document.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash === '#branches' || {{ $errors->has('name') ? 'true' : 'false' }}) {
            switchTab('branches');
        }
    });
</script>
