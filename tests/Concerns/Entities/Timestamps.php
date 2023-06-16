<?php

namespace Tests\Concerns\Entities;

use Carbon\Carbon;

trait Timestamps
{
    protected Carbon $createdAt;
    protected Carbon $updatedAt;

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @param Carbon $createdAt
     * @return Timestamps
     */
    public function setCreatedAt(Carbon $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    /**
     * @param Carbon $updatedAt
     * @return Timestamps
     */
    public function setUpdatedAt(Carbon $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}