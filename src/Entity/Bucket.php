<?php

namespace AntonAm\Selectel\Cloud\Entity;

use AntonAm\Selectel\Cloud\EntityInterface;
use AntonAm\Selectel\Cloud\Manager;
use AntonAm\Selectel\Cloud\SelectelCloudException;

/**
 * Class Bucket
 *
 * @package AntonAm\Selectel\Cloud\Entity
 */
class Bucket implements EntityInterface
{
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function set($bucketName): self
    {
        $this->manager->setClient(null, null, $bucketName);
        return $this;
    }

    public function create(): void
    {
        //Selectel API can't create buckets
    }

    public function list(): array
    {
        $request = $this->manager
            ->getClient()
            ->getIterator('ListBuckets', [
                'Bucket' => $this->manager->getBucket(),
            ]);

        $buckets = [];
        foreach ($request as $bucket) {
            $buckets[] = $bucket;
        }

        return $buckets;
    }

    public function download($path): bool
    {
        // Method doesn't work in case of handmade directories
        /*$this->manager
            ->getClient()
            ->downloadBucket($path, $this->manager->getBucket());*/

        $files = $this->files();

        $result = true;

        if (count($files) > 0) {
            foreach ($files as $file) {
                if ((int)$file['Size'] > 0) {
                    $filePath = $path . '/' . $file['Key'];
                    $filePathDir = dirname($filePath);

                    if (!is_dir($filePathDir) && !mkdir($filePathDir, 0777, true) && !is_dir($filePathDir)) {
                        throw new SelectelCloudException(sprintf('Directory "%s" was not created', $filePath));
                    }

                    if (!$this->manager->file($file['Key'])->download($filePath)) {
                        $result = false;
                    }
                }
            }
        }

        return $result;
    }

    public function delete(): bool
    {

        $this->manager
            ->getClient()
            ->deleteBucket(['Bucket' => $this->manager->getBucket()]);

        $this->manager
            ->getClient()
            ->waitUntil('BucketNotExists', ['Bucket' => $this->manager->getBucket()]);

        return true;
    }

    public function exist(): bool
    {
        return $this->manager
            ->getClient()
            ->doesBucketExist($this->manager->getBucket());
    }

    public function files(): array
    {
        $request = $this->manager
            ->getClient()
            ->getIterator('ListObjects', [
                'Bucket' => $this->manager->getBucket(),
            ]);
        $files = [];
        foreach ($request as $object) {
            $files[] = $object;
        }

        return $files;
    }

    public function location(): string
    {
        return $this->manager
            ->getClient()
            ->getBucketLocation(['Bucket' => $this->manager->getBucket()])
            ->get('LocationConstraint');
    }
}