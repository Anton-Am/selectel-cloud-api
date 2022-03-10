<?php

namespace AntonAm\Selectel\Cloud\Entity;


/**
 * Class File
 *
 * @package AntonAm\Selectel\Cloud\Entity
 */
class File extends BucketObject
{
    private $file;
    private $mimeType = 'application/octet-stream';

    public function setFileData($data): self
    {
        if (@file_exists($data)) {
            $this->file = file_get_contents($data);
            if (!empty($mime = (string)mb_strtolower(pathinfo($data)['extension']) === 'svg' ? 'image/svg+xml' : mime_content_type($data))) {
                $this->mimeType = $mime;
            }
        } else {
            $this->file = $data;
        }

        return $this;
    }

    public function setMimeType($type): self
    {
        $this->mimeType = $type;
        return $this;
    }

    public function create(): array
    {
        $result = $this->manager
            ->getClient()
            ->putObject([
                'Bucket'      => $this->manager->getBucket(),
                'Key'         => $this->object,
                'Body'        => $this->file,
                'ContentType' => $this->mimeType
            ]);

        $this->manager
            ->getClient()
            ->waitUntil('ObjectExists', [
                'Bucket' => $this->manager->getBucket(),
                'Key'    => $this->object
            ]);

        $this->mimeType = null;

        return $result->toArray();
    }
}