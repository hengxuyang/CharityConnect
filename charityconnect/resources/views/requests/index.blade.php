<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Donation Requests') }}
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

                    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
                        <h3 class="text-lg font-semibold mb-4 md:mb-0">Active Donation Requests</h3>
                        
                        @auth
                            @if (auth()->user()->isCharity())
                                <a href="{{ route('requests.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Create New Request
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium mb-3">Filter Requests</h4>
                        <form action="{{ route('requests.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select id="category" name="category" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Request Type</label>
                                <select id="type" name="type" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full">
                                    <option value="">All Types</option>
                                    <option value="drop-off" {{ request('type') == 'drop-off' ? 'selected' : '' }}>Drop-off</option>
                                    <option value="pickup" {{ request('type') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    @if ($requests->isEmpty())
                        <p class="text-gray-500">No donation requests found.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($requests as $request)
                                <div class="border rounded-lg overflow-hidden shadow-md">
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="text-xl font-semibold">{{ $request->item->name }}</h4>
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                {{ $request->request_type }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-gray-600 mb-2">
                                            <span class="font-semibold">Charity:</span> {{ $request->charity->name }}
                                        </p>
                                        <p class="text-gray-600 mb-2">
                                            <span class="font-semibold">Category:</span> {{ $request->item->category->name }}
                                        </p>
                                        <p class="text-gray-600 mb-2">
                                            <span class="font-semibold">Quantity Needed:</span> {{ $request->quantity }}
                                        </p>
                                        <p class="text-gray-600 mb-4">
                                            <span class="font-semibold">Available Times:</span> {{ \Carbon\Carbon::parse($request->available_times)->format('M d, Y') }}
                                        </p>
                                        
                                        <div class="flex justify-between items-center">
                                            <a href="{{ route('requests.show', $request) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                View Details
                                            </a>
                                            
                                            @auth
                                                @if (!auth()->user()->isCharity() || auth()->user()->charity_id === $request->charity_id)
                                                    <a href="{{ route('donations.create', ['request_id' => $request->id]) }}" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                        Donate
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    Login to Donate
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
