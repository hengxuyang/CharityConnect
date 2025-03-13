<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- User Type -->
        <div class="mt-4">
            <x-input-label for="user_type" :value="__('User Type')" />
            <div class="items-center">
                <div class="flex items-center">
                    <input type="radio" id="normal_user" name="user_type" value="normal" class="mr-2">
                    <x-input-label for="normal_user" :value="__('Normal User')" />
                </div>
                <div class="flex items-center">
                    <input type="radio" id="charity_organization" name="user_type" value="charity" class="mr-2">
                    <x-input-label for="charity_organization" :value="__('Charity Organization')" />
                </div>
            </div>
            <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
        </div>



        <!-- Charity File (only shown if charity organization is selected) -->
        <div id="charity_file_div" class="mt-4 hidden">
            <x-input-label for="charity_file" :value="__('Charity Organization Proof (File Upload)')" />
            <x-text-input id="charity_file" class="block mt-1 w-full" type="file" name="charity_file" accept="image/*,application/pdf" />
            <x-input-error :messages="$errors->get('charity_file')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        // Show or hide the charity file input based on user type selection
        const normalUserRadio = document.getElementById('normal_user');
        const charityRadio = document.getElementById('charity_organization');
        const charityFileDiv = document.getElementById('charity_file_div');

        normalUserRadio.addEventListener('change', function() {
            if (normalUserRadio.checked) {
                charityFileDiv.classList.add('hidden');
            }
        });

        charityRadio.addEventListener('change', function() {
            if (charityRadio.checked) {
                charityFileDiv.classList.remove('hidden');
            }
        });
    </script>
</x-guest-layout>
