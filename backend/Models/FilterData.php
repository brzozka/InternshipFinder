<?php

declare(strict_types=1);

namespace Internships\Models;

use JsonSerializable;

class FilterData implements JsonSerializable
{
    protected string $specialization;
    protected array $tags;

    public function __construct(string $specialization, array $tags)
    {
        $this->specialization = $specialization;
        $this->tags = $tags;
    }

    public function getSpecialization(): string
    {
        return $this->specialization;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function jsonSerialize(): array
    {
        return [
            "specialization" => $this->specialization,
            "tags" => $this->tags,
        ];
    }
}