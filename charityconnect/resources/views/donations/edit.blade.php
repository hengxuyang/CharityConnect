<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Donation') }}
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
                        <a href="{{ route('donations.show', $donation) }}" class="text-blue-500 hover:text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Donation Details
                        </a>
                    </div>

                    <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Edit Donation</h3>
                        
                        <div class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Item</p>
                                    <p class="text-md font-medium">{{ $donation->request->item->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Charity</p>
                                    <p class="text-md font-medium">{{ $donation->request->charity->name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Requested Quantity</p>
                                    <p class="text-md font-medium">{{ $donation->request->quantity }} {{ $donation->request->item->unit }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Current Donation</p>
                                    <p class="text-md font-medium">{{ $donation->quantity }} {{ $donation->request->item->unit }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('donations.update', $donation) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-6">
                                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity</label>
                                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $donation->quantity) }}" min="1" max="{{ $donation->request->quantity }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full sm:w-1/3" required>
                                @error('quantity')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Maximum: {{ $donation->request->quantity }} {{ $donation->request->item->unit }}</p>
                            </div>
                            
                            <div class="mb-6">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select name="status" id="status" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full sm:w-1/3">
                                    <option value="Pending" {{ old('status', $donation->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Cancelled" {{ old('status', $donation->status) == 'Cancelled' ? 'selected' : '' }}>Cancel Donation</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="flex items-center">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Update Donation
                                </button>
                                <a href="{{ route('donations.show', $donation) }}" class="ml-4 text-gray-500 hover:text-gray-700">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
