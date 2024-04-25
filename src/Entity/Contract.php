<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[Groups(['contract'])]
#[ORM\Entity(repositoryClass: ContractRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Contract
{
    #[Groups(['contractLinked','contractLinkedId'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['contractLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100)]
    #[Assert\ExpressionSyntax(
        allowedVariables: ['quotation', 'order','invoice'],
        message : 'You should provide a valid type of contract ! '
    )]
    // TODO : wait till it's ok to create in db
    private ?string $type = 'quotation';

    #[Groups(['contractLinked'])]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $orderedAt = null;

    #[Groups(['contractLinked'])]
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $invoicedAt = null;
    
    // TODO : put some regex constraint to validate french address
    #[Groups(['contractLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]  
    #[ORM\Column(length: 500)]
    private ?string $deliveryAddress = null;

    #[Groups(['contractLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100)]
    #[Assert\ExpressionSyntax(
        allowedVariables: ['created','archived','obsolete','deleted'],
        message : 'You should provide a valid status for the contract ! '
    )]
    private ?string $status = 'created';

    #[Groups(['contractLinked'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[Groups(['contractLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\type(Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['contractLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'],nullable: true)]
    #[Assert\type(Types::DATE_MUTABLE ,nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'contracts', cascade: ['persist'],fetch : 'EAGER')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'contracts', cascade: ['persist'],fetch : 'EAGER')]
    // #[ORM\ManyToOne(inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    // #[Groups(['contractproductLinked'])]
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'contract',  orphanRemoval: true, cascade: ['persist'])]
    // #[ORM\OneToMany(targetEntity: Product::class, mappedBy:  orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOrderedAt(): ?\DateTimeInterface
    {
        return $this->orderedAt;
    }

    public function setOrderedAt(?\DateTimeInterface $orderedAt): static
    {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    public function getInvoicedAt(): ?\DateTimeInterface
    {
        return $this->invoicedAt;
    }

    public function setInvoicedAt(?\DateTimeInterface $invoicedAt): static
    {
        $this->invoicedAt = $invoicedAt;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setContract($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getContract() === $this) {
                $product->setContract(null);
            }
        }

        return $this;
    }
}
