<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 04.01.18
 * Time: 17:04
 */

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Config
 * @package App\Entity
 * @ORM\Entity()
 */
class Config
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", name="`key`")
     */
    private $key;
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $value;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;


    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function updateValue(string $newValue): void
    {
        $this->value = $newValue;
        $this->updatedAt = new \DateTime();
    }
}