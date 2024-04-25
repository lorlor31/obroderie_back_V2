<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[Groups(['product'])]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Product
{
    #[Groups(['contract','productLinked','productLinkedId'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['productLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[Groups(['productLinked'])]
    #[Assert\NotBlank]
    #[Assert\Positive(message: 'La quantité ne peut pas être inférieur à zéro')]
    #[ORM\Column]
    private ?int $quantity = null;

    #[Groups(['productLinked'])]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?float $price = null;

    #[Groups(['productLinked'])]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deliveryAt = null;

    #[Groups(['productLinked'])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    // TODO check with the product owner the min max delays
    #[ORM\Column]
    private ?int $manufacturingDelay = null;

    #[Groups(['productLinked'])]
    #[Assert\NotBlank]
    #[ORM\Column()]
    private ?int $productOrder = null;

    #[Groups(['productLinked'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[Groups(['productLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\type(Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['productLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'], nullable: true)]
    #[Assert\type(Types::DATE_MUTABLE )]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Groups(['contractLinkedId'])]
    // #[ORM\ManyToOne(inversedBy: 'products',cascade: [])]
    #[ORM\ManyToOne(inversedBy: 'products', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Contract $contract = null;

    // #[ORM\ManyToOne(inversedBy: 'products', cascade: ['persist'])]
    #[Groups(['textileLinkedId','contractTextile','productTextileLinkedId'])]
    #[ORM\ManyToOne(inversedBy: 'products',cascade: ['persist'],fetch : 'EAGER')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Textile $textile = null;

    // #[ORM\ManyToOne(inversedBy: 'products', cascade: ['persist'])]
    #[Groups(['productLinkedId','contractEmbroidery','productEmbroideryLinkedId'])]
    #[ORM\ManyToOne(inversedBy: 'products', cascade: ['persist'], fetch : 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Embroidery $embroidery = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDeliveryAt(): ?\DateTimeInterface
    {
        return $this->deliveryAt;
    }

    public function setDeliveryAt(?\DateTimeInterface $deliveryAt): static
    {
        $this->deliveryAt = $deliveryAt;

        return $this;
    }

    public function getManufacturingDelay(): ?int
    {
        return $this->manufacturingDelay;
    }

    public function setManufacturingDelay(int $manufacturingDelay): static
    {
        $this->manufacturingDelay = $manufacturingDelay;

        return $this;
    }

    public function getProductOrder(): ?int
    {
        return $this->productOrder;
    }

    public function setProductOrder(int $productOrder): static
    {
        $this->productOrder = $productOrder;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): static
    {
        $this->contract = $contract;

        return $this;
    }

    public function getTextile(): ?Textile
    {
        return $this->textile;
    }

    public function setTextile(?Textile $textile): static
    {
        $this->textile = $textile;

        return $this;
    }

    public function getEmbroidery(): ?Embroidery
    {
        return $this->embroidery;
    }

    public function setEmbroidery(?Embroidery $embroidery): static
    {
        $this->embroidery = $embroidery;

        return $this;
    }
}
