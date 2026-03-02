<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Send Bulk Email') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('bulk-email.send') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-label for="subject" value="{{ __('Subject') }}" />
                        <x-input id="subject" name="subject" type="text" class="mt-1 block w-full" value="{{ old('subject') }}" required />
                        <x-input-error for="subject" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="message" value="{{ __('Message') }}" />
                        <textarea id="message" name="message" rows="10" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('message') }}</textarea>
                        <x-input-error for="message" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="attachments" value="{{ __('Attachments (Images or PDF)') }}" />
                        <input id="attachments" name="attachments[]" type="file" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Supported formats: PDF, JPG, PNG. Max 10MB per file.</p>
                        <x-input-error for="attachments" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end">
                        <x-button>
                            {{ __('Send to All Users') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
