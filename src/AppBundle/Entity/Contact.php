<?php

namespace AppBundle\Entity;

use AppBundle\Validator\Constraints as AppBundleAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @AppBundleAssert\ValidPhoneNumbers
     */
    private $phoneNumbers;

    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="contact")
     */
    private $notes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inRecycleBin;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (!$this->getPhoneNumbers() && !$this->getAddress()) {
            $context->buildViolation("You must enter more data.")
                    ->addViolation();

            return;
        }
    }

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->inRecycleBin = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setAddress(?string $address)
    {
        $this->address = $address;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setPhoneNumbers(array $phoneNumbers)
    {
        $this->phoneNumbers = $phoneNumbers;
    }

    public function getPhoneNumbers(): ?array
    {
        return $this->phoneNumbers;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function addNote(Note $note)
    {
        $this->notes->add($note);
    }

    public function removeNote(Note $note)
    {
        $this->notes->removeElement($note);
    }

    public function setInRecycleBin(bool $inRecycleBin)
    {
        $this->inRecycleBin = $inRecycleBin;
    }

    public function isInRecycleBin(): bool
    {
        return $this->inRecycleBin;
    }

    public function __toString() {
        return $this->name;
    }
}
