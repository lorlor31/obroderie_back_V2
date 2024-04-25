<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TextileRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[Groups(['textile'])]
#[ORM\Entity(repositoryClass: TextileRepository::class)]
#[ORM\HasLifecycleCallbacks]

    
class Textile
{
    // public const $date = time();

    #[Groups(['contract','textileLinked','textileLinkedId'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['contract','textileLinked'])]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[Groups(['contract','textileLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100)]
    private ?string $type = null;

    #[Groups(['contract','textileLinked'])]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $size = null;

    #[Groups(['contract','textileLinked'])]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $color = null;

    #[Groups(['contract','textileLinked'])]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $brand = null;

    #[Groups(['contract','textileLinked'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[Groups(['contract','textileLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\type(Types::DATE_IMMUTABLE)]
/*   #[ORM\Column(type: Types::DATETIME_IMMUTABLE )]
 */  private ?\DateTimeImmutable $createdAt = null;

    //TODO
    
    #[Groups(['contract','textileLinked'])]
/*     #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
 */ #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'],nullable: true)]
    #[Assert\type(Types::DATE_MUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'textile')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

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
            $product->setTextile($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getTextile() === $this) {
                $product->setTextile(null);
            }
        }

        return $this;
    }
}
