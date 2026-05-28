<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Slimani\MediaManager\Form\MediaPicker;
use Slimani\MediaManager\Models\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Disable media conversions on Slimani File model to prevent WebP/Avif conversion errors
        File::registerMediaConversionsUsing(function () {
            // No conversions - prevents thumb.webp and preview.webp generation
        });

        // MediaPicker::getFile() is called in Slimani's blade template to show the
        // current selected file preview. The package does not define this method, so
        // we register it as a macro here.
        MediaPicker::macro('getFile', function () {
            /** @var MediaPicker $this */
            try {
                $raw = $this->getRawState();

                // State could be a plain string ('86'), UUID-keyed array ({uuid=>'86'}),
                // or a numeric-keyed array (['86']).
                if (is_array($raw)) {
                    $id = array_values(array_filter($raw))[0] ?? null;
                } else {
                    $id = $raw;
                }

                if (blank($id)) {
                    return null;
                }

                return File::find((int) $id);
            } catch (\Throwable) {
                return null;
            }
        });

        // Register observer for MediaFile to trigger video conversion
        File::observe(\App\Observers\MediaFileObserver::class);
    }
}
