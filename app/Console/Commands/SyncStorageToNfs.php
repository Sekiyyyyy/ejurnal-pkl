<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncStorageToNfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:nfs-sync 
                            {--target=/mnt/nfs/data-jurnal : Path direktori tujuan NFS} 
                            {--source= : Path direktori sumber penyimpanan publik (default: storage/app/public)} 
                            {--dry-run : Menampilkan rencana pemindahan file tanpa menyalin fisik file}
                            {--link : Perbarui symlink public/storage ke direktori NFS secara otomatis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi dan migrasi file upload jurnal (foto live, tanda tangan, template Word) ke penyimpanan NFS secara aman tanpa menghapus data asli';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('================================================================');
        $this->info('  E-Jurnal SMKN 1 Beringin - Storage NFS Migration Utility      ');
        $this->info('================================================================');

        $source = $this->option('source') ?: storage_path('app/public');
        $target = rtrim($this->option('target') ?: '/mnt/nfs/data-jurnal', '/');
        $dryRun = $this->option('dry-run');
        $createLink = $this->option('link');

        $this->line("Direktori Sumber: <comment>{$source}</comment>");
        $this->line("Direktori Target: <comment>{$target}</comment>");

        if (!File::exists($source)) {
            $this->error("Direktori sumber [{$source}] tidak ditemukan.");
            return Command::FAILURE;
        }

        // Cek direktori target
        if (!File::exists($target)) {
            if ($dryRun) {
                $this->warn("[DRY-RUN] Direktori target belum ada. Sistem akan mencoba membuat direktori: {$target}");
            } else {
                $this->info("Membuat direktori target [{$target}]...");
                try {
                    File::makeDirectory($target, 0775, true, true);
                } catch (\Throwable $e) {
                    $this->error("Gagal membuat direktori target [{$target}]: " . $e->getMessage());
                    $this->warn("Pastikan NFS sudah di-mount dan user web/server memiliki izin tulis (write permission).");
                    return Command::FAILURE;
                }
            }
        }

        // Cek izin tulis (write permission)
        if (!$dryRun && !is_writable($target)) {
            $this->error("Direktori target [{$target}] tidak dapat ditulis (Permission Denied).");
            $this->warn("Periksa hak akses folder NFS: chmod 775 {$target} atau chown ke user web server.");
            return Command::FAILURE;
        }

        // Buat subfolder standar
        $subfolders = ['signatures', 'live_photos', 'templates'];
        if (!$dryRun) {
            foreach ($subfolders as $sub) {
                $path = "{$target}/{$sub}";
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0775, true, true);
                }
            }
        }

        // Pindai semua file di folder sumber
        $allFiles = File::allFiles($source);
        $totalFiles = count($allFiles);

        $this->info("Ditemukan {$totalFiles} file di direktori sumber.");

        if ($totalFiles === 0) {
            $this->line("Tidak ada file yang perlu dipindahkan.");
            return Command::SUCCESS;
        }

        $copiedCount = 0;
        $skippedCount = 0;
        $totalBytes = 0;
        $errors = [];

        $this->output->progressStart($totalFiles);

        foreach ($allFiles as $file) {
            $relativePath = $file->getRelativePathname();
            $destPath = "{$target}/{$relativePath}";
            $destDir = dirname($destPath);

            $fileSize = $file->getSize();

            if ($dryRun) {
                $this->output->progressAdvance();
                $copiedCount++;
                $totalBytes += $fileSize;
                continue;
            }

            try {
                if (!File::exists($destDir)) {
                    File::makeDirectory($destDir, 0775, true, true);
                }

                // Jika file sudah ada di target dengan ukuran yang sama persis, lewati
                if (File::exists($destPath) && File::size($destPath) === $fileSize) {
                    $skippedCount++;
                } else {
                    File::copy($file->getRealPath(), $destPath);
                    $copiedCount++;
                    $totalBytes += $fileSize;
                }
            } catch (\Throwable $e) {
                $errors[] = "Gagal menyalin [{$relativePath}]: " . $e->getMessage();
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $mbTransferred = round($totalBytes / (1024 * 1024), 2);

        $this->newLine();
        $this->info("------------------- Ringkasan Proses -------------------");
        if ($dryRun) {
            $this->info("[DRY-RUN] File yang akan disalin : {$copiedCount} file ({$mbTransferred} MB)");
        } else {
            $this->info("File baru disalin             : {$copiedCount} file ({$mbTransferred} MB)");
            $this->line("File identik (dilewati)        : {$skippedCount} file");
            $this->info("Data asli di [{$source}] TETAP AMAN & TIDAK DIHAPUS.");
        }

        if (!empty($errors)) {
            $this->warn("Terdapat " . count($errors) . " kendala saat penyalinan:");
            foreach (array_slice($errors, 0, 5) as $err) {
                $this->error(" - {$err}");
            }
            if (count($errors) > 5) {
                $this->line(" ... dan " . (count($errors) - 5) . " kendala lainnya.");
            }
        }

        // Perbarui Symbolic Link jika diminta
        if ($createLink && !$dryRun) {
            $this->newLine();
            $publicStorageLink = public_path('storage');
            $this->info("Memperbarui symbolic link [{$publicStorageLink}]...");

            if (is_link($publicStorageLink) || File::exists($publicStorageLink)) {
                @unlink($publicStorageLink);
            }

            try {
                symlink($target, $publicStorageLink);
                $this->info("Symbolic link berhasil diarahkan ke [{$target}].");
            } catch (\Throwable $e) {
                $this->warn("Gagal membuat symlink otomatis: " . $e->getMessage());
                $this->line("Anda dapat menjalankan manual di terminal: ln -sfn {$target} {$publicStorageLink}");
            }
        }

        $this->newLine();
        $this->info("---------------- Langkah Selanjutnya ----------------");
        $this->line("1. Pastikan baris berikut sudah ada di file <comment>.env</comment>:");
        $this->line("   <info>FILESYSTEM_PUBLIC_ROOT={$target}</info>");
        $this->line("2. Jalankan perintah optimasi cache:");
        $this->line("   <info>php artisan config:cache</info>");
        $this->line("3. Jika belum membuat symlink publik:");
        $this->line("   <info>php artisan storage:link</info>");
        $this->info("================================================================");

        return Command::SUCCESS;
    }
}
