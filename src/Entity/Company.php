<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 */
class Company
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Cleaner::class, mappedBy="company", orphanRemoval=true)
     */
    private $cleaners;

    public function __construct()
    {
        $this->cleaners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Cleaner[]
     */
    public function getCleaners(): Collection
    {
        return $this->cleaners;
    }

    public function addCleaner(Cleaner $cleaner): self
    {
        if (!$this->cleaners->contains($cleaner)) {
            $this->cleaners[] = $cleaner;
            $cleaner->setCompany($this);
        }

        return $this;
    }

    public function removeCleaner(Cleaner $cleaner): self
    {
        if ($this->cleaners->contains($cleaner)) {
            $this->cleaners->removeElement($cleaner);
            // set the owning side to null (unless already changed)
            if ($cleaner->getCompany() === $this) {
                $cleaner->setCompany(null);
            }
        }

        return $this;
    }
}
