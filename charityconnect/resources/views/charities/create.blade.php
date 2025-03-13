<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Register a Charity') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('charities.store') }}" class="space-y-6">
                        @csrf

                        <div class="border-b pb-4 mb-6">
                            <h3 class="text-lg font-semibold mb-4">Charity Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Charity Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Charity Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Charity Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Charity Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- Charity Phone -->
                                <div>
                                    <x-input-label for="phone" :value="__('Charity Phone')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <!-- Charity Address -->
                                <div class="md:col-span-2">
                                    <x-input-label for="address" :value="__('Charity Address')" />
                                    <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Administrator Account</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Admin Name -->
                                <div>
                                    <x-input-label for="user_name" :value="__('Your Name')" />
                                    <x-text-input id="user_name" class="block mt-1 w-full" type="text" name="user_name" :value="old('user_name')" required />
                                    <x-input-error :messages="$errors->get('user_name')" class="mt-2" />
                                </div>

                                <!-- Admin Email -->
                                <div>
                                    <x-input-label for="user_email" :value="__('Your Email')" />
                                    <x-text-input id="user_email" class="block mt-1 w-full" type="email" name="user_email" :value="old('user_email')" required />
                                    <x-input-error :messages="$errors->get('user_email')" class="mt-2" />
                                </div>

                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('charities.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Register Charity') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
