<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Charities') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Approved Charities</h3>
                        @auth
                            @if (!auth()->user()->isCharity())
                                <a href="{{ route('charities.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Register a Charity
                                </a>
                            @endif
                        @else
                            <a href="{{ route('charities.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Register a Charity
                            </a>
                        @endauth
                    </div>

                    @if ($charities->isEmpty())
                        <p class="text-gray-500">No approved charities found.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($charities as $charity)
                                <div class="border rounded-lg overflow-hidden shadow-md">
                                    <div class="p-4">
                                        <h4 class="text-xl font-semibold mb-2">{{ $charity->name }}</h4>
                                        <p class="text-gray-600 mb-2">
                                            <span class="font-semibold">Address:</span> {{ $charity->address }}
                                        </p>
                                        <p class="text-gray-600 mb-2">
                                            <span class="font-semibold">Email:</span> {{ $charity->email }}
                                        </p>
                                        <p class="text-gray-600 mb-4">
                                            <span class="font-semibold">Phone:</span> {{ $charity->phone }}
                                        </p>
                                        <a href="{{ route('charities.show', $charity) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
