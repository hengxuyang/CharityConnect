<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Donation Request Details') }}
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
                            <h3 class="text-2xl font-bold mb-2">{{ $request->item->name }}</h3>
                            <p class="text-gray-600">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                    {{ $request->request_type }}
                                </span>
                                <span class="inline-block bg-{{ $request->status === 'Pending' ? 'yellow' : ($request->status === 'Fulfilled' ? 'green' : 'red') }}-100 text-{{ $request->status === 'Pending' ? 'yellow' : ($request->status === 'Fulfilled' ? 'green' : 'red') }}-800 text-xs px-2 py-1 rounded ml-2">
                                    {{ $request->status }}
                                </span>
                            </p>
                        </div>
                        
                        <div class="flex space-x-2">
                            @auth
                                @if (auth()->user()->charity_id === $request->charity_id)
                                    <a href="{{ route('requests.edit', $request) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Edit Request
                                    </a>
                                    
                                    <form method="POST" action="{{ route('requests.destroy', $request) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to cancel this request?')">
                                            Cancel Request
                                        </button>
                                    </form>
                                @elseif (!auth()->user()->isCharity())
                                    <a href="{{ route('donations.create', ['request_id' => $request->id]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Donate
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Login to Donate
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-lg mb-3">Request Details</h4>
                            <p class="mb-2">
                                <span class="font-medium">Item:</span> {{ $request->item->name }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Category:</span> {{ $request->item->category->name }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Quantity Needed:</span> {{ $request->quantity }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Request Type:</span> {{ $request->request_type }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Drop-off/Pickup Address:</span> {{ $request->drop_off_address }}
                            </p>
                            <p>
                                <span class="font-medium">Available Times:</span> {{ \Carbon\Carbon::parse($request->available_times)->format('M d, Y') }}
                            </p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-lg mb-3">Charity Information</h4>
                            <p class="mb-2">
                                <span class="font-medium">Charity:</span> {{ $request->charity->name }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Email:</span> {{ $request->charity->email }}
                            </p>
                            <p class="mb-2">
                                <span class="font-medium">Phone:</span> {{ $request->charity->phone }}
                            </p>
                            <p>
                                <span class="font-medium">Address:</span> {{ $request->charity->address }}
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('charities.show', $request->charity) }}" class="text-blue-500 hover:text-blue-700">
                                    View Charity Profile
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($request->donations->isNotEmpty())
                        <div class="border-t pt-6">
                            <h4 class="font-semibold text-lg mb-4">Donations for this Request</h4>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b text-left">Donor</th>
                                            <th class="py-2 px-4 border-b text-left">Quantity</th>
                                            <th class="py-2 px-4 border-b text-left">Status</th>
                                            <th class="py-2 px-4 border-b text-left">Date</th>
                                            <th class="py-2 px-4 border-b text-left">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($request->donations as $donation)
                                            <tr>
                                                <td class="py-2 px-4 border-b">{{ $donation->user->name }}</td>
                                                <td class="py-2 px-4 border-b">{{ $donation->quantity }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="inline-block bg-{{ $donation->status === 'Pending' ? 'yellow' : ($donation->status === 'Completed' ? 'green' : 'red') }}-100 text-{{ $donation->status === 'Pending' ? 'yellow' : ($donation->status === 'Completed' ? 'green' : 'red') }}-800 text-xs px-2 py-1 rounded">
                                                        {{ $donation->status }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border-b">{{ $donation->created_at->format('M d, Y') }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <a href="{{ route('donations.show', $donation) }}" class="text-blue-500 hover:text-blue-700">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
