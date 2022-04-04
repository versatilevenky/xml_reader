<?php

namespace Tests\Feature;

use App\Exceptions\FTPHostConnectionFailedException;
use App\Exceptions\InvalidFTPLoginCredentialsException;
use App\Exceptions\InvalidOptionException;
use App\Exceptions\XMLLoadException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaveXMLDataTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_invalid_path_option()
    {
        $this->expectException(InvalidOptionException::class);
        $this->artisan('data:saveXmlData --path=')->expectsOutputToContain('Path to xml file cannot be empty');
    }
    public function test_empty_storage_type_option()
    {
        $this->expectException(InvalidOptionException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=')->expectsOutputToContain('Storage should not be empty and should be a valid Storage type');
    }
    public function test_invalid_storage_type_option()
    {
        $this->expectException(InvalidOptionException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=def')->expectsOutputToContain('Storage should not be empty and should be a valid Storage type');
    }

    public function test_empty_connection_type_option()
    {
        $this->expectException(InvalidOptionException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=')->expectsOutputToContain('Connection Type should not be empty and should be a valid Connection Type (Remote or Local)');
    }
    public function test_invalid_connection_type_option()
    {
        $this->expectException(InvalidOptionException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=abc')->expectsOutputToContain('Connection Type should not be empty and should be a valid Connection Type (Remote or Local)');
    }
    public function test_empty_host_for_remote()
    {
        $this->expectException(InvalidOptionException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Remote')->expectsOutputToContain('Host cannot be empty');
    }
    public function test_empty_username_for_remote()
    {
        $this->expectException(InvalidOptionException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Remote --host="def"')->expectsOutputToContain('FTP Username and Password cannot be empty');
    }
    public function test_empty_password_for_remote()
    {
        $this->expectException(InvalidOptionException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Remote --host="def" --username="ghi"')->expectsOutputToContain('FTP Username and Password cannot be empty');
    }

    public function test_host_connection_failed_for_remote()
    {
        $this->expectException(FTPHostConnectionFailedException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Remote --host="def" --username="ghi" --password=jkl')->expectsOutputToContain('FTP Username and Password cannot be empty');
    }
    public function test_connection_failed_with_invalid_username_for_remote()
    {
        $this->expectException(InvalidFTPLoginCredentialsException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Remote --host="transport.productsup.io" --username="ghi" --password="asdf"')->expectsOutputToContain('Unable to Login to FTP Server with given Username and Password');
    }
    public function test_connection_failed_with_invalid_password_for_remote()
    {
        $this->expectException(InvalidFTPLoginCredentialsException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Remote --host="transport.productsup.io" --username="pupDev" --password="jkl"')->expectsOutputToContain('Unable to Login to FTP Server with given Username and Password');
    }
    public function test_connection_failed_with_invalid_username_and_password_for_remote()
    {
        $this->expectException(InvalidFTPLoginCredentialsException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Remote --host="transport.productsup.io" --username="ghi" --password=jkl')->expectsOutputToContain('Unable to Login to FTP Server with given Username and Password');
    }
    public function test_file_not_found_for_remote()
    {
        $this->expectException(FileNotFoundException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Remote --host="transport.productsup.io" --username="pupDev" --password=pupDev2018')->expectsOutputToContain('File not available at abc.xml');
    }
    public function test_file_not_found_for_local()
    {
        $this->expectException(FileNotFoundException::class);
        $this->artisan('data:saveXmlData --path="abc.xml" --storage-type=CSV --connection-type=Local')->expectsOutputToContain('Connection Type should not be empty and should be a valid Connection Type (Remote or Local)');
    }
    public function test_file_load_failed()
    {
        $this->expectException(XMLLoadException::class);
        $this->artisan('data:saveXmlData --path="2.xml" --storage-type=CSV --connection-type=Local')->expectsOutputToContain('Opening and ending tag mismatch');
    }
    public function test_file_load_with_empty_data_failed()
    {
        $this->expectException(XMLLoadException::class);
        $this->artisan('data:saveXmlData --path="1.xml" --storage-type=CSV --connection-type=Local')->expectsOutputToContain('No Data in XML');
    }
    public function test_local_file_saved()
    {
        $this->artisan("data:saveXmlData --path='/Users/dasarivenkata/Desktop/coffee_feed_trimmed.xml' --storage-type=CSV");
        $filePath = $this->getActualOutput();
        $this->assertFileExists($filePath);
    }
    public function test_remote_file_saved()
    {
        $this->artisan('data:saveXmlData --path="coffee_feed.xml" --storage-type=CSV --connection-type=Remote --host="transport.productsup.io" --username="pupDev" --password=pupDev2018');
        $filePath = $this->getActualOutput();
        $this->assertFileExists($filePath);
    }
}
