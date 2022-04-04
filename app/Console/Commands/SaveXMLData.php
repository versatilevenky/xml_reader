<?php

namespace App\Console\Commands;

use App\Exceptions\FTPHostConnectionFailedException;
use App\Exceptions\InvalidOptionException;
use App\Exceptions\XMLLoadException;
use App\Helper\DownloadRemoteFile;
use App\Helper\FTPClient;
use App\Helper\GetXMLData;
use App\Helper\XMLParser;
use App\Http\Enums\ConnectionTypes;
use App\Http\Enums\StorageTypes;
use App\Repositories\SaveAsCSVRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;


class SaveXMLData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:saveXmlData {--path=} {--storage-type=} {--connection-type=LOCAL} {--host=} {--username=} {--password=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads data from the given XML and stores in it required format';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->option('path');
        if (empty($path)) {
            throw new InvalidOptionException('Path to xml file cannot be empty');
        }
        $storageType = $this->option('storage-type');
        if (empty($storageType) || !in_array($storageType, StorageTypes::$storageTypes)) {
            throw new InvalidOptionException('Storage should not be empty and should be a valid Storage type');
        }
        $connectionType = $this->option('connection-type');
        $connectionType = strtoupper($connectionType);

        if (empty($connectionType) || ($connectionType != ConnectionTypes::LOCAL && $connectionType != ConnectionTypes::REMOTE)) {
            throw new InvalidOptionException('Connection Type should not be empty and should be a valid Connection Type (Remote or Local)');
        }
        if ($connectionType == ConnectionTypes::REMOTE) {
            $host = $this->option('host');
            $ftpUsername = $this->option('username');
            $ftpPassword = $this->option('password');

            if (empty($host)) {
                throw new InvalidOptionException('Host cannot be empty');
            }

            if (empty($ftpUsername) || empty($ftpPassword)) {
                throw new InvalidOptionException('FTP Username and Password cannot be empty');
            }

            $ftpClient = new FTPClient($host, $ftpUsername, $ftpPassword);
            $ftpClient->connectToFTP();
            $ftpClientObj = $ftpClient->getFtpClient();

            $localPath = storage_path() . '/' . $this->getFilenameFromPath($path);
            $ftpFileGetter = new DownloadRemoteFile();
            $ftpFileGetter->saveRemoteFileToLocal($ftpClientObj, $path, $localPath);

            $path = $localPath;
        } else if ($connectionType == ConnectionTypes::LOCAL) {
            if (!file_exists($path)) {
                throw new FileNotFoundException(sprintf('Unable to open the file at %s', $path));
            }
        }

        $xmlParser = new XMLParser($path);
        $xmlObject = $xmlParser->getXmlObject();

        $xmlDataReader = new GetXMLData($xmlObject);
        $xmlData = $xmlDataReader->getXmlData();


        if ($storageType == 'CSV') {
            $saveAsCsvObj = new SaveAsCSVRepository();
            $savedFilePath = $saveAsCsvObj->saveXmlData($xmlData);
            echo $savedFilePath;
            return $savedFilePath;
        }
        return 0;
    }

    public function getFilenameFromPath($path)
    {
        $folders = explode('/', $path);

        return $folders[sizeof($folders) - 1];
    }
}
