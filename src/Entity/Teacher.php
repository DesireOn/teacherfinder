<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TeacherRepository::class)
 */
class Teacher
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Моля, въведете име")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $rating;

    /**
     * @Assert\NotBlank(message="Моля, въведете имейл")
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Моля, въведете телефонен номер")
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @Assert\NotBlank(message="Моля, качете профилна снимка")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $activeReviewsCount;

    /**
     * @Assert\NotBlank(message="Моля, въведете цена за час")
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $pricePerHour;

    /**
     * @Assert\NotBlank(message="Моля, изберете пол")
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $gender;

    /**
     * @Assert\NotBlank(message="Моля, изберете поне един начин на обучение")
     * @ORM\OneToMany(targetEntity=LessonType::class, mappedBy="teacher")
     */
    private $lessonTypes;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="teacher")
     */
    private $reviews;

    /**
     * @Assert\NotBlank(message="Моля, изберете предмет")
     * @ORM\ManyToOne(targetEntity=Subject::class)
     */
    private $subject;

    /**
     * @Assert\NotBlank(message="Моля, изберете град")
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="teachers")
     */
    private $city;

    public function __construct()
    {
        $this->lessonTypes = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getActiveReviewsCount(): ?int
    {
        return $this->activeReviewsCount;
    }

    public function setActiveReviewsCount(int $activeReviewsCount): self
    {
        $this->activeReviewsCount = $activeReviewsCount;

        return $this;
    }

    public function getPricePerHour(): ?string
    {
        return $this->pricePerHour;
    }

    public function setPricePerHour(string $pricePerHour): self
    {
        $this->pricePerHour = $pricePerHour;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, LessonType>
     */
    public function getLessonTypes(): Collection
    {
        return $this->lessonTypes;
    }

    public function addLessonType(LessonType $lessonType): self
    {
        if (!$this->lessonTypes->contains($lessonType)) {
            $this->lessonTypes[] = $lessonType;
            $lessonType->setTeacher($this);
        }

        return $this;
    }

    public function removeLessonType(LessonType $lessonType): self
    {
        if ($this->lessonTypes->removeElement($lessonType)) {
            // set the owning side to null (unless already changed)
            if ($lessonType->getTeacher() === $this) {
                $lessonType->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setTeacher($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getTeacher() === $this) {
                $review->setTeacher(null);
            }
        }

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
