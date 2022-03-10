<?php

namespace AntonAm\Selectel\Cloud\Entity;

use AntonAm\Selectel\Cloud\SelectelCloudException;

/**
 * Class Directory
 *
 * @package AntonAm\Selectel\Cloud\Entity
 */
class Directory extends BucketObject
{
    public function files(): array
    {
        $request = $this->manager
            ->getClient()
            ->getIterator('ListObjects', [
                'Bucket' => $this->manager->getBucket(),
                'Prefix' => $this->object,
            ]);
        $files = [];
        foreach ($request as $object) {
            $files[] = $object;
        }

        return $files;
    }

    public function exist(): bool
    {
        //Directory check doesn't work in Selectel API
        return false;
    }

    public function create(): bool
    {
        //Selectel need file for directory creation
        $tempFileManager = $this->manager->file($this->object . '/.dir')->setFileData('');
        $result = $tempFileManager->create();
        return $result['@metadata']['statusCode'] === 200;
    }

    public function delete(): bool
    {
        $files = $this->files();

        $result = true;

        if (count($files) > 0) {
            foreach ($files as $file) {
                if (!$this->manager->file($file['Key'])->delete()) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    public function upload($path = null): bool
    {
        if (is_dir('/' . $this->object)) {

            $this->manager
                ->getClient()
                ->uploadDirectoryAsync('/' . $this->object, $this->manager->getBucket(), $path);

            return true;
        }

        return false;
    }

    public function download($path = null): bool
    {
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
}