<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Donation Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('requests.update', $request) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Item Selection -->
                            <div>
                                <x-input-label for="item_id" :value="__('Item Needed')" />
                                <select id="item_id" name="item_id" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full mt-1" required>
                                    <option value="">Select an item</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}" {{ old('item_id', $request->item_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} ({{ $item->category->name }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('item_id')" class="mt-2" />
                            </div>

                            <!-- Quantity -->
                            <div>
                                <x-input-label for="quantity" :value="__('Quantity Needed')" />
                                <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" :value="old('quantity', $request->quantity)" min="1" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>

                            <!-- Request Type -->
                            <div>
                                <x-input-label for="request_type" :value="__('Request Type')" />
                                <select id="request_type" name="request_type" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full mt-1" required>
                                    <option value="drop-off" {{ old('request_type', $request->request_type) == 'drop-off' ? 'selected' : '' }}>Drop-off (Donor brings items to you)</option>
                                    <option value="pickup" {{ old('request_type', $request->request_type) == 'pickup' ? 'selected' : '' }}>Pickup (You collect items from donor)</option>
                                </select>
                                <x-input-error :messages="$errors->get('request_type')" class="mt-2" />
                            </div>

                            <!-- Drop-off Address -->
                            <div>
                                <x-input-label for="drop_off_address" :value="__('Drop-off/Pickup Address')" />
                                <x-text-input id="drop_off_address" class="block mt-1 w-full" type="text" name="drop_off_address" :value="old('drop_off_address', $request->drop_off_address)" required />
                                <x-input-error :messages="$errors->get('drop_off_address')" class="mt-2" />
                            </div>

                            <!-- Available Times -->
                            <div>
                                <x-input-label for="available_times" :value="__('Available Date')" />
                                <x-text-input id="available_times" class="block mt-1 w-full" type="date" name="available_times" :value="old('available_times', $request->available_times->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('available_times')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full mt-1" required>
                                    <option value="Pending" {{ old('status', $request->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Fulfilled" {{ old('status', $request->status) == 'Fulfilled' ? 'selected' : '' }}>Fulfilled</option>
                                    <option value="Cancelled" {{ old('status', $request->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('requests.show', $request) }}" class="text-gray-500 hover:text-gray-700 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Request') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
