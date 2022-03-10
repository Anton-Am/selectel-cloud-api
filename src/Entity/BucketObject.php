<?php

namespace AntonAm\Selectel\Cloud\Entity;

use AntonAm\Selectel\Cloud\EntityInterface;
use AntonAm\Selectel\Cloud\Manager;
use Aws\S3\Exception\S3Exception;

/**
 * Class BucketObject
 *
 * @package AntonAm\Selectel\Cloud\Entity
 */
abstract class BucketObject implements EntityInterface
{
    protected $manager;
    protected $object;

    public function __construct(Manager $manager, $object)
    {
        $this->manager = $manager;
        $this->object = $object;
    }

    public function get()
    {
        $result = $this->manager
            ->getClient()
            ->getObject([
                'Bucket' => $this->manager->getBucket(),
                'Key'    => $this->object,
            ]);

        return $result->toArray();
    }


    abstract public function create();


    public function download($path = null)
    {
        if (null === $path) {
            $result = $this->manager
                ->getClient()
                ->getObject([
                    'Bucket' => $this->manager->getBucket(),
                    'Key'    => $this->object,
                ]);

            return $result->toArray()['Body'];
        }

        $result = $this->manager
            ->getClient()
            ->getObject([
                'Bucket' => $this->manager->getBucket(),
                'Key'    => $this->object,
                'SaveAs' => $path
            ]);

        return $result->toArray();
    }


    public function delete(): bool
    {
        $this->manager
            ->getClient()
            ->deleteObject([
                'Bucket' => $this->manager->getBucket(),
                'Key'    => $this->object,
            ]);

        $this->manager
            ->getClient()
            ->waitUntil('ObjectNotExists', [
                'Bucket' => $this->manager->getBucket(),
                'Key'    => $this->object
            ]);

        return true;
    }


    public function exist(): bool
    {
        try {
            $result = $this->manager
                ->getClient()
                ->headObject([
                    'Bucket' => $this->manager->getBucket(),
                    'Key'    => $this->object
                ]);

            return $result['@metadata']['statusCode'] === 200;
        } catch (S3Exception $exception) {

        }

        return false;
    }
}