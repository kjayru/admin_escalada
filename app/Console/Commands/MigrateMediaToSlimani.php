<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Slimani\MediaManager\Models\File as MediaFile;

class MigrateMediaToSlimani extends Command
{
    protected $signature = 'media:migrate-to-slimani {--dry-run : Mostrar qué se haría sin ejecutar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra registros de legacy_media a media_files (slimani) y actualiza FKs';

    // Tablas y columnas que referencian legacy_media.id
    protected array $fkMap = [
        'blog_posts'             => ['featured_media_id'],
        'members'                => ['featured_media_id'],
        'products'               => ['featured_media_id'],
        'sponsor_placements'     => ['banner_media_id'],
        'sponsors'               => ['logo_media_id', 'slide_image_media_id', 'gallery_1_media_id', 'gallery_2_media_id', 'gallery_3_media_id', 'gallery_4_media_id', 'contact_media_id'],
        'support_methods'        => ['media_id'],
        'timelines'              => ['media_id'],
        'transparency_documents' => ['media_id'],
        'page_sections'          => ['featured_media_id'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $legacyRows = DB::table('legacy_media')->get();

        if ($legacyRows->isEmpty()) {
            $this->warn('No hay registros en legacy_media. Nada que migrar.');
            return self::SUCCESS;
        }

        $this->info("Encontrados {$legacyRows->count()} archivos en legacy_media.");

        $idMap = [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::beginTransaction();
        try {
            foreach ($legacyRows as $legacy) {
                $extension = pathinfo($legacy->file_name, PATHINFO_EXTENSION);

                // Buscar el archivo: primero en la ubicación de Spatie ({id}/filename),
                // luego en la ubicación original (path del registro)
                $spatieRelPath = $legacy->id . '/' . $legacy->file_name;
                $spatieAbsPath = storage_path('app/public/' . $spatieRelPath);
                $originalAbsPath = storage_path('app/public/' . $legacy->path);

                if (file_exists($spatieAbsPath)) {
                    $physicalRelPath = $spatieRelPath;
                    $physicalAbsPath = $spatieAbsPath;
                    $fileExists = true;
                } elseif (file_exists($originalAbsPath)) {
                    $physicalRelPath = $legacy->path;
                    $physicalAbsPath = $originalAbsPath;
                    $fileExists = true;
                } else {
                    $fileExists = false;
                }

                if ($dryRun) {
                    $status = $fileExists ? "✓ en: {$physicalRelPath}" : "✗ no encontrado";
                    $this->line("  [{$legacy->id}] {$legacy->file_name} → $status");
                    continue;
                }

                // 1. Insertar en media_files
                $newId = DB::table('media_files')->insertGetId([
                    'uploaded_by_user_id' => $legacy->created_by,
                    'folder_id'           => null,
                    'name'                => pathinfo($legacy->file_name, PATHINFO_FILENAME),
                    'caption'             => $legacy->title,
                    'alt_text'            => $legacy->alt,
                    'size'                => $legacy->size,
                    'extension'           => strtolower($extension),
                    'mime_type'           => $legacy->mime_type,
                    'width'               => $legacy->width,
                    'height'              => $legacy->height,
                    'created_at'          => $legacy->created_at,
                    'updated_at'          => $legacy->updated_at,
                ]);

                $idMap[$legacy->id] = $newId;

                // 2. Registrar en Spatie MediaLibrary
                if ($fileExists) {
                    $mediaFile = MediaFile::find($newId);
                    $mediaFile
                        ->addMediaFromDisk($physicalRelPath, 'public')
                        ->usingFileName($legacy->file_name)
                        ->preservingOriginal()
                        ->withCustomProperties(['migrated_from_legacy' => $legacy->id])
                        ->toMediaCollection('default');

                    $this->line("  ✓ [{$legacy->id}→{$newId}] {$legacy->file_name}");
                } else {
                    $this->warn("  ✗ [{$legacy->id}→{$newId}] {$legacy->file_name} — archivo no encontrado");
                }
            }

            if ($dryRun) {
                DB::rollBack();
                $this->info('[DRY RUN] Sin cambios ejecutados.');
                return self::SUCCESS;
            }

            // 3. Actualizar FKs (FK checks ya deshabilitados)
            $this->info('Actualizando claves foráneas...');
            foreach ($this->fkMap as $table => $columns) {
                foreach ($columns as $column) {
                    foreach ($idMap as $oldId => $newId) {
                        $updated = DB::table($table)->where($column, $oldId)->update([$column => $newId]);
                        if ($updated > 0) {
                            $this->line("  {$table}.{$column}: {$oldId}→{$newId} ({$updated} filas)");
                        }
                    }
                }
            }

            // 4. Migrar legacy_mediables → media_attachments
            $this->info('Migrando pivot legacy_mediables → media_attachments...');
            $pivotRows = DB::table('legacy_mediables')->get();
            $pivotCount = 0;
            foreach ($pivotRows as $pivot) {
                if (!isset($idMap[$pivot->media_id])) {
                    $this->warn("  Pivot: media_id={$pivot->media_id} sin mapping");
                    continue;
                }
                DB::table('media_attachments')->insert([
                    'media_file_id'   => $idMap[$pivot->media_id],
                    'attachable_id'   => $pivot->mediable_id,
                    'attachable_type' => $pivot->mediable_type,
                    'collection'      => $pivot->collection ?? 'gallery',
                    'sort_order'      => $pivot->sort_order ?? 0,
                ]);
                $pivotCount++;
            }
            $this->line("  {$pivotCount} registros de galería migrados.");

            DB::commit();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->info('');
            $this->info("✅ Migración completada. {$legacyRows->count()} archivos migrados.");

        } catch (\Throwable $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
