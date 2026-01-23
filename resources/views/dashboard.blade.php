<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (auth()->user()->role === 'admin')
                {{-- ADMIN VIEW --}}
                <div class="grid grid-cols-12 gap-6">

                    <div class="col-span-12">
                        @include('admin.inbox-list')
                    </div>

                </div>
            @else
                {{-- CUSTOMER VIEW --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>

                <a href="{{ route('chat.index') }}"
                    class="fixed bottom-6 right-6 bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 z-50">
                    💬
                </a>
            @endif

        </div>
    </div>
</x-app-layout>
