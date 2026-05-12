<?php

namespace App\Observers;

use App\Jobs\ConvertVideoToMp4;
use Slimani\MediaManager\Models\File as MediaFile;

class MediaFileObserver
{
    /**
     * Handle the File "created" event.
     */
    public function created(MediaFile $file): void
    {
        // El evento "created" se dispara antes de que el media esté adjunto
        // Usaremos el evento "saved" en su lugar
    }

    /**
     * Handle the File "updated" event.
     */
    public function updated(MediaFile $file): void
    {
        // Verificar si es un video cuando se actualiza (después de adjuntar media)
        $spatieMedia = $file->getFirstMedia();
        
        if ($spatieMedia && str_starts_with($spatieMedia->mime_type, 'video/')) {
            // Dispatch el job de conversión solo si no es MP4
            if ($spatieMedia->mime_type !== 'video/mp4') {
                ConvertVideoToMp4::dispatch($file);
            }
        }
    }

    /**
     * Handle the File "deleted" event.
     */
    public function deleted(MediaFile $file): void
    {
        //
    }

    /**
     * Handle the File "restored" event.
     */
    public function restored(MediaFile $file): void
    {
        //
    }

    /**
     * Handle the File "force deleted" event.
     */
    public function forceDeleted(MediaFile $file): void
    {
        //
    }
}
