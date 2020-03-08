<?php

declare(strict_types=1);

namespace App\Visits\Entities;

use Carbon\Carbon;
use DateTimeInterface;
use App\Entities\Contracts\Entity;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

abstract class Visit implements Entity
{
    use SerializesAttributes;

    protected UuidInterface $id;
    protected DateTimeInterface $date;

    protected function __construct(?DateTimeInterface $date = null)
    {
        $this->id = Uuid::uuid1();
        $this->date = $date ?? Carbon::now();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
