<?php

namespace App\Helper;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class DownloadRemoteFile
{

    public function saveRemoteFileToLocal($ftpClient, $serverPath, $localPath)
    {
        $fp = fopen($localPath, 'w');
        if (!@ftp_fget($ftpClient, $fp, $serverPath, FTP_BINARY)) {
            throw new FileNotFoundException("File not available at {$serverPath}.");
        }
        fclose($fp);
    }
}
