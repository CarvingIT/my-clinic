<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ isset($template) ? 'Edit Template' : 'New Template' }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ isset($template) ? $template->name : 'Create a new document template' }}
                </p>
            </div>
            <a href="{{ route('admin.templates.index') }}"
               class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Back to Templates
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium">Please fix the following errors:</p>
                            <ul class="mt-1 text-sm list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ isset($template) ? route('admin.templates.update', $template) : route('admin.templates.store') }}"
                  method="POST">
                @csrf
                @if(isset($template))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name"
                                           value="{{ old('name', $template->name ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="e.g., Medical Certificate"
                                           required>
                                </div>
                                <div>
                                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                                        Slug <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="slug" id="slug"
                                           value="{{ old('slug', $template->slug ?? '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="e.g., medical_certificate"
                                           required>
                                    <p class="mt-1 text-xs text-gray-500">Auto-generated from name</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                        Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="type" id="type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="certificate" {{ old('type', $template->type ?? '') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                                        <option value="form" {{ old('type', $template->type ?? '') == 'form' ? 'selected' : '' }}>Form</option>
                                        <option value="document" {{ old('type', $template->type ?? '') == 'document' ? 'selected' : '' }}>Document</option>
                                        <option value="letter" {{ old('type', $template->type ?? '') == 'letter' ? 'selected' : '' }}>Letter</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="placeholders" class="block text-sm font-medium text-gray-700 mb-1">
                                        Placeholders
                                    </label>
                                    <input type="text" name="placeholders" id="placeholders"
                                           value="{{ old('placeholders', isset($template) ? implode(', ', $template->placeholders ?? []) : '') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="patient_name, patient_age, current_date">
                                    <p class="mt-1 text-xs text-gray-500">Comma-separated list</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                       {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 text-sm text-gray-700">
                                    Template is active
                                </label>
                            </div>
                        </div>

                        <!-- Template Content -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Template Content (HTML)</h3>
                            <textarea name="content" id="content" rows="20"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Enter HTML content here..."
                                      required>{{ old('content', $template->content ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Actions -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                            <div class="space-y-3">
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-save mr-2"></i>
                                    {{ isset($template) ? 'Save Changes' : 'Create Template' }}
                                </button>
                                <a href="{{ route('admin.templates.index') }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    Cancel
                                </a>
                            </div>
                        </div>

                        @if(isset($template))
                            <!-- Template Info -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Info</h3>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Created</dt>
                                        <dd class="text-gray-900">{{ $template->created_at->format('d M Y') }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Updated</dt>
                                        <dd class="text-gray-900">{{ $template->updated_at->format('d M Y') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        @endif

                        <!-- Help -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-sm font-medium text-blue-900 mb-2">
                                <i class="fas fa-lightbulb mr-1"></i>
                                Placeholders
                            </h3>
                            <p class="text-xs text-blue-800 mb-3">
                                Use curly braces for placeholders <br> For eg:
                            </p>

                            <div class="space-y-1.5">
                                <code class="block text-xs bg-white px-2 py-1 rounded text-blue-700">{patient_name}</code>
                                <code class="block text-xs bg-white px-2 py-1 rounded text-blue-700">{patient_age}</code>
                                <code class="block text-xs bg-white px-2 py-1 rounded text-blue-700">{current_date}</code>
                                <code class="block text-xs bg-white px-2 py-1 rounded text-blue-700">{branch_name}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @if(isset($template))
                <div class="mt-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Danger Zone</h3>
                        <p class="text-sm text-gray-600 mb-4">Once you delete this template, there is no going back. Please be certain.</p>
                        <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this template? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fas fa-trash-alt mr-2"></i>
                                Delete Template
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('name').addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '_')
                .replace(/-+/g, '_');
            document.getElementById('slug').value = slug;
        });
    </script>
</x-app-layout>
