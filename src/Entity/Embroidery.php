<?php

namespace App\Entity;

use App\Repository\EmbroideryRepository;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


#[Groups(['embroidery'])]
#[ORM\Entity(repositoryClass: EmbroideryRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Embroidery
{
    #[Groups(['contract','embroideryLinked','embroideryLinkedId'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['contract','embroideryLinked'])]  
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[Groups(['contract','embroideryLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100)]
    private ?string $design = null;

    #[Groups(['contract','embroideryLinked'])]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $text = null;

    #[Groups(['contract','embroideryLinked'])]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $detail = null;


    #[Groups(['contract','embroideryLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\type(Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['contract','embroideryLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'],nullable: true)]
    #[Assert\type(Types::DATE_MUTABLE ,nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'embroidery')]
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

    public function getDesign(): ?string
    {
        return $this->design;
    }

    public function setDesign(string $design): static
    {
        $this->design = $design;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): static
    {
        $this->detail = $detail;

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
            $product->setEmbroidery($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getEmbroidery() === $this) {
                $product->setEmbroidery(null);
            }
        }

        return $this;
    }
}
