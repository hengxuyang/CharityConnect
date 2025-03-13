<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Charity') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('charities.update', $charity) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Charity Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Charity Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Charity Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $charity->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Charity Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Charity Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $charity->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- Charity Phone -->
                                <div>
                                    <x-input-label for="phone" :value="__('Charity Phone')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $charity->phone)" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <!-- Charity Address -->
                                <div class="md:col-span-2">
                                    <x-input-label for="address" :value="__('Charity Address')" />
                                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $charity->address)" required />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('charities.show', $charity) }}" class="text-gray-500 hover:text-gray-700 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Charity') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
