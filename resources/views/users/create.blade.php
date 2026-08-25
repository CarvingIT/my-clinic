<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 flex items-center justify-center transition-all shadow-xs" title="Back">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('messages.Create Staff') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Add a new staff member or doctor to the clinic system.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8">
                    
                    <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                        @csrf

                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                {{ __('messages.Name') }} <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                    placeholder="Enter full name (e.g. Dr. Jane Doe)"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-rose-600" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                {{ __('messages.Email') }} <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="far fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    placeholder="staff@clinic.com"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600" />
                        </div>

                        <!-- Passwords Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    {{ __('Password') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                    <input id="password" type="password" name="password" required autocomplete="new-password"
                                        placeholder="••••••••"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    {{ __('Confirm Password') }} <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-shield-alt absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                        placeholder="••••••••"
                                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-rose-600" />
                            </div>
                        </div>

                        <!-- Roles Section -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                {{ __('messages.Roles') }}
                            </label>
                            <p class="text-xs text-slate-400 mb-3">Select the roles and access levels to assign to this user.</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach ($roles as $role)
                                    @php
                                        $roleLower = strtolower($role->name);
                                        $iconClass = match(true) {
                                            str_contains($roleLower, 'admin') => 'fa-user-shield text-rose-600 bg-rose-50',
                                            str_contains($roleLower, 'doctor') => 'fa-user-md text-emerald-600 bg-emerald-50',
                                            default => 'fa-user-tie text-blue-600 bg-blue-50',
                                        };
                                    @endphp
                                    <label class="relative flex items-center p-3 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-slate-100/60 cursor-pointer transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/40 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                            {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 mr-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs {{ $iconClass }}">
                                                <i class="fas {{ explode(' ', $iconClass)[0] }}"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-800">{{ ucfirst($role->name) }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-1.5 text-xs text-rose-600" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <a href="{{ route('users.index') }}"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-colors">
                                {{ __('messages.Close') }}
                            </a>

                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                <i class="fas fa-check text-xs"></i>
                                {{ __('messages.Create Staff') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
