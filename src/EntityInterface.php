<?php

namespace AntonAm\Selectel\Cloud;

interface EntityInterface
{
    public function create();

    public function download($path);

    public function delete(): bool;

    public function exist(): bool;
}