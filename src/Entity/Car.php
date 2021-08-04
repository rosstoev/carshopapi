<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\BrandFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CarRepository::class)
 * @ApiResource (
 *      collectionOperations={
 *          "get"={"security"="is_granted('ROLE_USER')"},
 *          "post"={"security"="is_granted('ROLE_USER')"}
 *     },
 *      normalizationContext={"groups":{"car:read"}},
 *      denormalizationContext={"groups":{"car:write"}},
 *     attributes={
 *     "pagination_items_per_page"=10
 *     }
 * )
 * @ApiFilter(OrderFilter::class, properties={"color.name"})
 * @ApiFilter (BrandFilter::class, properties={"brand"})
 */
class Car
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"car:read", "car:write", "color:read"})
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"car:read", "car:write", "color:read"})
     */
    private $model;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups ({"car:read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Color::class, inversedBy="cars", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"car:read", "car:write"})
     * @Assert\Valid()
     */
    private $color;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): self
    {
        $this->color = $color;

        return $this;
    }
}
