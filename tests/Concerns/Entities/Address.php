<?php

namespace Tests\Concerns\Entities;

use SimpleOnlineHealthcare\Contracts\Doctrine\Entity;
use SimpleOnlineHealthcare\JsonApi\Contracts\Renderable;

class Address implements Entity, Renderable
{
    use Timestamps;

    protected int $id;
    protected string $lineOne;
    protected string $lineTwo;
    protected string $postcode;
    protected User $user;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLineOne(): string
    {
        return $this->lineOne;
    }

    /**
     * @param string $lineOne
     * @return Address
     */
    public function setLineOne(string $lineOne): Address
    {
        $this->lineOne = $lineOne;

        return $this;
    }

    /**
     * @return string
     */
    public function getLineTwo(): string
    {
        return $this->lineTwo;
    }

    /**
     * @param string $lineTwo
     * @return Address
     */
    public function setLineTwo(string $lineTwo): Address
    {
        $this->lineTwo = $lineTwo;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     * @return Address
     */
    public function setPostcode(string $postcode): Address
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Address
     */
    public function setUser(User $user): Address
    {
        $this->user = $user;

        return $this;
    }
}