<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Вашето заглавие трябва да има поне {{ limit }} символа",
     *      maxMessage = "Вашето заглавие не трябва да превишава {{ limit }} символа"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "Вашето съдържание трябва да има поне 10 символа",
     *      maxMessage = "Вашето съдържание не трябва да превишава 255 символа"
     * )
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;

    /**
     * @Assert\NotNull(message="Моля, изберете рейтинг от опциите.")
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      minMessage = "Вашият рейтинг трябва да е не по - малък от 1",
     *      maxMessage = "Вашият рейтинг трябва да е не по - голям от 5"
     * )
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorName;

    /**
     * @ORM\Column(type="integer")
     */
    private $hideAuthorName;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Teacher::class, inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $teacher;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(?string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getHideAuthorName(): ?int
    {
        return $this->hideAuthorName;
    }

    public function setHideAuthorName(int $hideAuthorName): self
    {
        $this->hideAuthorName = $hideAuthorName;

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

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }
}
