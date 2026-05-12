<?php

namespace App\Jobs;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slimani\MediaManager\Models\File as MediaFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ConvertVideoToMp4 implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600; // 10 minutos

    /**
     * Create a new job instance.
     */
    public function __construct(
        public MediaFile $mediaFile
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Obtener el primer media de Spatie
            $spatieMedia = $this->mediaFile->getFirstMedia();
            
            if (!$spatieMedia) {
                Log::warning("No spatie media found for MediaFile {$this->mediaFile->id}");
                return;
            }

            // Verificar que sea un video
            if (!str_starts_with($spatieMedia->mime_type, 'video/')) {
                return; // No es un video, no hacer nada
            }

            // Si ya es MP4, no hacer nada
            if ($spatieMedia->mime_type === 'video/mp4') {
                return;
            }

            Log::info("Converting video {$spatieMedia->file_name} to MP4");

            $originalPath = $spatieMedia->getPath();
            $tempMp4Path = storage_path('app/temp/' . pathinfo($spatieMedia->file_name, PATHINFO_FILENAME) . '.mp4');

            // Asegurar que el directorio temp existe
            if (!File::exists(dirname($tempMp4Path))) {
                File::makeDirectory(dirname($tempMp4Path), 0755, true);
            }

            // Inicializar FFMpeg
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/bin/ffprobe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 4,
            ]);

            $video = $ffmpeg->open($originalPath);

            // Configurar formato MP4 con H.264
            $format = new X264('aac', 'libx264');
            $format->setKiloBitrate(2000) // 2Mbps
                   ->setAudioKiloBitrate(128);

            // Convertir
            $video->save($format, $tempMp4Path);

            // Reemplazar el archivo original en Spatie Media Library
            $newMp4Name = pathinfo($spatieMedia->file_name, PATHINFO_FILENAME) . '.mp4';
            
            // Eliminar el archivo original
            $spatieMedia->delete();

            // Agregar el nuevo archivo MP4
            $this->mediaFile
                ->addMedia($tempMp4Path)
                ->usingFileName($newMp4Name)
                ->toMediaCollection();

            // Actualizar el nombre del MediaFile si es necesario
            if ($this->mediaFile->name !== $newMp4Name) {
                $this->mediaFile->update([
                    'name' => $newMp4Name,
                    'mime_type' => 'video/mp4',
                ]);
            }

            // Limpiar archivo temporal
            if (File::exists($tempMp4Path)) {
                File::delete($tempMp4Path);
            }

            Log::info("Successfully converted video to MP4: {$newMp4Name}");

        } catch (\Exception $e) {
            Log::error("Failed to convert video to MP4: " . $e->getMessage(), [
                'media_file_id' => $this->mediaFile->id,
                'exception' => $e,
            ]);
            
            // Re-lanzar la excepción para que el job sea reintentado
            throw $e;
        }
    }
}
