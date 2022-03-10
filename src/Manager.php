<?php

namespace AntonAm\Selectel\Cloud;

use AntonAm\Selectel\Cloud\Entity\Bucket;
use AntonAm\Selectel\Cloud\Entity\Directory;
use AntonAm\Selectel\Cloud\Entity\File;
use Aws\S3\S3Client;

/**
 * Class Manager
 *
 * @package AntonAm\Selectel\Cloud
 */
class Manager
{
    private $client;
    private $bucketName;
    private $accessKey;
    private $secretKey;

    public function __construct($accessKey, $secretKey, $bucket, $region = 'ru-1', $host = 'https://s3.storage.selcloud.ru')
    {
        if (!class_exists(S3Client::class)) {
            throw new SelectelCloudException('There is no AWS client. Please update composer.');
        }

        $this->setClient($accessKey, $secretKey, $bucket, $region, $host);
    }

    public function setClient($accessKey, $secretKey, $bucket, $region = 'ru-1', $host = 'https://s3.storage.selcloud.ru'): self
    {
        if (!empty($accessKey)) {
            $this->accessKey = $accessKey;
        }

        if (!empty($secretKey)) {
            $this->secretKey = $secretKey;
        }

        $this->client = new S3Client([
            'region'                  => $region,
            'version'                 => 'latest',
            'endpoint'                => $host,
            'credentials'             => [
                'key'    => $this->accessKey,
                'secret' => $this->secretKey,
            ],
            'use_path_style_endpoint' => true,
        ]);

        $this->bucketName = $bucket;

        return $this;
    }


    /**
     * @return mixed|S3Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function getBucket()
    {
        return $this->bucketName;
    }

    public function bucket(): Bucket
    {
        return new Bucket($this);
    }

    public function directory($directory): Directory
    {
        return new Directory($this, ltrim($directory, '/'));
    }

    public function file($file): File
    {
        return new File($this, trim($file, '/'));
    }
}