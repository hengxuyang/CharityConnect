<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Make a Donation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="mb-6">
                        <a href="{{ route('requests.show', $donationRequest) }}" class="text-blue-500 hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Request Details
                        </a>
                    </div>

                    <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Donation Information</h3>
                        
                        <div class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Item</p>
                                    <p class="text-md font-medium">{{ $donationRequest->item->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Charity</p>
                                    <p class="text-md font-medium">{{ $donationRequest->charity->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Requested Quantity</p>
                                    <p class="text-md font-medium">{{ $donationRequest->quantity }} {{ $donationRequest->item->unit }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Request Status</p>
                                    <p class="text-md font-medium">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                            Pending
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Description</p>
                                <p class="text-md">{{ $donationRequest->description }}</p>
                            </div>
                        </div>
                        
                        <form action="{{ route('donations.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="request_id" value="{{ $donationRequest->id }}">
                            
                            <div class="mb-6">
                                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Donation Quantity</label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" max="{{ $donationRequest->quantity }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full sm:w-1/3" required>
                                @error('quantity')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Maximum: {{ $donationRequest->quantity }} {{ $donationRequest->item->unit }}</p>
                            </div>
                            
                            <div class="flex items-center">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Submit Donation
                                </button>
                                <a href="{{ route('requests.show', $donationRequest) }}" class="ml-4 text-gray-500 hover:text-gray-700">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Donation Guidelines</h3>
                        <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <ul class="list-disc pl-5 space-y-2 text-gray-600 dark:text-gray-400">
                                <li>Ensure that the items you donate are in good condition and meet the charity's requirements.</li>
                                <li>Once you submit a donation, the charity will be notified and will contact you to arrange for delivery or pickup.</li>
                                <li>You can track the status of your donation in your donation history.</li>
                                <li>If you need to cancel or modify your donation, you can do so as long as the status is still "Pending".</li>
                                <li>For any questions or concerns, please contact the charity directly.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
