<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $charity->name }}
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

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">{{ $charity->name }}</h3>
                            <p class="text-gray-600">{{ $charity->status === 'approved' ? 'Approved Charity' : 'Pending Approval' }}</p>
                        </div>
                        
                        @auth
                            @if (auth()->user()->charity_id === $charity->id || auth()->user()->isAdmin())
                                <div class="flex space-x-2">
                                    <a href="{{ route('charities.edit', $charity) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Edit Charity
                                    </a>
                                    
                                    @if (auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('charities.destroy', $charity) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this charity?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        @endauth
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-lg mb-3">Contact Information</h4>
                            <p class="mb-2">
                                <span class="font-medium">Email:</span> {{ $charity->email }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Phone:</span> {{ $charity->phone }}
                            </p>
                            <p>
                                <span class="font-medium">Address:</span> {{ $charity->address }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t pt-6">
                        <h4 class="font-semibold text-lg mb-4">Current Donation Requests</h4>
                        
                        @if ($charity->requests->where('status', 'Pending')->isEmpty())
                            <p class="text-gray-500">No active donation requests at this time.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($charity->requests->where('status', 'Pending') as $request)
                                    <div class="border rounded-lg p-4 shadow-sm">
                                        <h5 class="font-semibold mb-2">{{ $request->item->name }}</h5>
                                        <p class="text-sm text-gray-600 mb-1">
                                            <span class="font-medium">Quantity Needed:</span> {{ $request->quantity }}
                                        </p>
                                        <p class="text-sm text-gray-600 mb-1">
                                            <span class="font-medium">Type:</span> {{ $request->request_type }}
                                        </p>
                                        <p class="text-sm text-gray-600 mb-3">
                                            <span class="font-medium">Available Times:</span> {{ \Carbon\Carbon::parse($request->available_times)->format('M d, Y') }}
                                        </p>
                                        <a href="{{ route('requests.show', $request) }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            View Details
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
