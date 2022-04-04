<?php

namespace App\Helper;

use App\Exceptions\FTPHostConnectionFailedException;
use App\Exceptions\InvalidFTPLoginCredentialsException;

class FTPClient
{
    private mixed $ftpClient = null;
    private string $host;
    private string $username;
    private string $password;

    public function __construct($host, $username, $password)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @throws FTPHostConnectionFailedException
     * @throws InvalidFTPLoginCredentialsException
     */
    public function connectToFTP()
    {
        try {
            $this->ftpClient = ftp_connect($this->host);
        } catch (\Exception $e) {
            throw new FTPHostConnectionFailedException($e->getMessage());
        }
        try {
            ftp_login($this->ftpClient, $this->username, $this->password);
        } catch (\Exception $e) {
            throw new InvalidFTPLoginCredentialsException("Unable to Login to FTP Server with given Username and Password");
        }
    }

    public function getFtpClient()
    {
        return $this->ftpClient;
    }

    public function __destruct()
    {
        if ($this->ftpClient) {
            ftp_close($this->ftpClient);
        }
    }
}
