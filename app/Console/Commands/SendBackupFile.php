<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Drive;

class SendBackupFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Send Backup File...');
        $this->sending();
        $this->info('Backup File Sent Success.');
    }

    private function sending()
    {
        try {
            $client = new Client();
            $client->setAuthConfig(public_path('credentials.json'));
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $name = $this->fileName();
            $file = public_path('storage/'.$name);
            $fileName = basename($file);
            $mimeType = mime_content_type($file);

            $fileMetadata = new Drive\DriveFile(
                array('name' => $fileName,'parents' => ['11CjKzIs8ndfv_V6jhIDFy4y99jsUuYYN'])
                );
            $content = file_get_contents($file);
            $file = $driveService->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id'));
            return $file->id;
        } catch(Exception $e) {
            return "Error Message: ".$e;
        }
    }

    private function fileName()
    {
        $data = Storage::allFiles('public/RAS');
        $input = date('Y-m-d');
        $result = array_filter($data, function ($item) use ($input) {
            if (stripos($item, $input) !== false) {
                return true;
            }
            return false;
        });
        $file = $result[0];
        $file = str_replace('public/', '', $file);
        return $file;
    }
}
