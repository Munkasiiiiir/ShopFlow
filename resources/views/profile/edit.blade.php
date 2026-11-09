<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="sf-page-title">{{ __('Profile') }}</h2>
            <p class="sf-page-subtitle">Manage your account details</p>
        </div>
    </x-slot>

    <div class="space-y-4 p-4 md:p-6">
        <div class="sf-card p-5 md:p-6">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="sf-card p-5 md:p-6">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="sf-card p-5 md:p-6">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
