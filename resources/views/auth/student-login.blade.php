<x-app-background-layout>
    <x-authentication-card-plain>
        <div class="rounded-full bg-contain bg-no-repeat bg-center w-36 h-36 mx-auto" style="background-image: url('{{asset('images/pages/logo-app-new.png')}}')"></div>
            <div class="w-full my-5">
                <h5 class="text-3xl text-center font-bold">LOGIN</h5>
            </div>
        <div class="w-full mb-4">
            <hr>
        </div>
        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.login.create') }}">
            @csrf

            <div>
                <x-label for="username" value="Username" />
                <x-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4 mb-4">

                <button type="submit" class="ml-4 bg-sky-500 hover:bg-sky-700 focus:bg-sky-700 shadow-lg inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-sky-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sky-500/50 hover:shadow-sky-700/50">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </x-authentication-card-plain>
</x-app-background-layout>
