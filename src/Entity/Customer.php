<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[Groups(['customer'])]
#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Customer
{
    #[Groups(['customerLinked','customerLinkedId'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['customerLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[Groups(['customerLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]  
    #[ORM\Column(length: 500)]
    private ?string $address = null;

    // TODO check email constraint
    #[Groups(['customerLinked'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[Groups(['customerLinked'])]
    #[Assert\Length(max: 100)]  
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contact = null;

    // TODO check constraints related to international phone numbers
    #[Groups(['customerLinked'])]
    #[Assert\Regex(
        pattern: '/^\d{10}$/',
        message: 'You should provide a phone number with 10 digits ! ',
    )]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $phoneNumber = null;

    #[Groups(['customerLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Assert\type(Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['customerLinked'])]
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'],nullable: true)]
    #[Assert\type(Types::DATE_MUTABLE ,nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Contract::class, mappedBy: 'customer')]
    private Collection $contracts;

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
    }

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

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
     * @return Collection<int, Contract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): static
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts->add($contract);
            $contract->setCustomer($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): static
    {
        if ($this->contracts->removeElement($contract)) {
            // set the owning side to null (unless already changed)
            if ($contract->getCustomer() === $this) {
                $contract->setCustomer(null);
            }
        }

        return $this;
    }
}
