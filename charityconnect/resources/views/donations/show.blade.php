<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Donation Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="mb-6 flex justify-between items-center">
                        <a href="{{ route('donations.index') }}" class="text-blue-500 hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Donations
                        </a>
                        
                        <div>
                            @if($donation->status == 'Pending' && (auth()->id() == $donation->donor_id || auth()->user()->isAdmin()))
                                <a href="{{ route('donations.edit', $donation) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                                    Edit Donation
                                </a>
                                <form action="{{ route('donations.destroy', $donation) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to cancel this donation?')">
                                        Cancel Donation
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Donation Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Donation ID</p>
                                <p class="text-md font-medium">{{ $donation->id }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                                <p class="text-md font-medium">
                                    @if($donation->status == 'Pending')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                            Pending
                                        </span>
                                    @elseif($donation->status == 'Delivered')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Delivered
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            Cancelled
                                        </span>
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Item</p>
                                <p class="text-md font-medium">{{ $donation->request->item->name }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Quantity</p>
                                <p class="text-md font-medium">{{ $donation->quantity }} {{ $donation->request->item->unit }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Charity</p>
                                <p class="text-md font-medium">{{ $donation->request->charity->name }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Donation Date</p>
                                <p class="text-md font-medium">{{ $donation->created_at->format('M d, Y') }}</p>
                            </div>
                            
                            @if($donation->status == 'Delivered')
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivery Date</p>
                                    <p class="text-md font-medium">{{ $donation->updated_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Donation Request Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Request ID</p>
                                <p class="text-md font-medium">{{ $donation->request->id }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Request Status</p>
                                <p class="text-md font-medium">
                                    @if($donation->request->status == 'Pending')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                            Pending
                                        </span>
                                    @elseif($donation->request->status == 'Fulfilled')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                            Fulfilled
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                            Cancelled
                                        </span>
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Requested Quantity</p>
                                <p class="text-md font-medium">{{ $donation->request->quantity }} {{ $donation->request->item->unit }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Request Date</p>
                                <p class="text-md font-medium">{{ $donation->request->created_at->format('M d, Y') }}</p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Description</p>
                                <p class="text-md">{{ $donation->request->description }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('requests.show', $donation->request) }}" class="text-blue-500 hover:text-blue-700">
                                View full request details
                            </a>
                        </div>
                    </div>

                    @if(auth()->user()->charity_id == $donation->request->charity_id)
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold mb-4">Donor Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Name</p>
                                    <p class="text-md font-medium">{{ $donation->donor->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Email</p>
                                    <p class="text-md font-medium">{{ $donation->donor->email }}</p>
                                </div>
                            </div>
                            
                            @if($donation->status == 'Delivered')
                                <div class="mt-6">
                                    <h4 class="font-medium mb-2">Send Thank You Note</h4>
                                    <form action="{{ route('donations.thank-you', $donation) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <textarea name="message" rows="3" class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Write a thank you message to the donor..." required></textarea>
                                        </div>
                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Send Thank You
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        
                        @if($donation->status == 'Pending')
                            <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h3 class="text-lg font-semibold mb-4">Update Donation Status</h3>
                                
                                <form action="{{ route('donations.update', $donation) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-4">
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                        <select name="status" id="status" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full sm:w-1/2">
                                            <option value="Pending" {{ $donation->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Delivered">Delivered</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Update Status
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
