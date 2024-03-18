<?php

namespace App\Modules\Audit\Revisionable;

interface Revisionable
{
    public function getUrlPath(): string;
}
