<section>
    <header class="mb-4">
        <h2 class="h5 mb-1 text-danger">{{ __('Delete Account') }}</h2>
        <p class="text-muted mb-0">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        {{ __('Delete Account') }}
    </button>

    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h2 class="modal-title h5" id="confirmUserDeletionLabel">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Cancel') }}"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted">
                        {{ __('Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <x-input-label for="password" value="{{ __('Password') }}" />
                    <x-text-input id="password" name="password" type="password" placeholder="{{ __('Password') }}" />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <x-danger-button>{{ __('Delete Account') }}</x-danger-button>
                </div>
            </form>
        </div>
    </div>
</section>
