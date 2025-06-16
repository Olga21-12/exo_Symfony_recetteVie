<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use App\Validator\InappropriateWords;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // добавляется чтобы не сохранялось с пустой строкой
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; // добавляется чтобы не сохранялось с названием, которое уже существует

use App\Entity\Traits\Timestampable;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\Table(name: "recipes")]
#[UniqueEntity('title', message: "Ce titre existe déjà")] // добавляется чтобы не сохранялось с названием, которое уже существует
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Titre obligatoire")]  // добавляется чтобы не сохранялось с пустой строкой
    #[Assert\Length(min: 10, minMessage: "Valeur trop petite, Vous devez avoir un titre de minimum 10 caractéres")] // добавляется чтобы не сохранялось с названием менее 10 символов
    #[Assert\Length(max: 50, maxMessage: "Valeur trop grande, 50 maximum")] // добавляется чтобы не сохранялось с названием более 255 символов
    // #[Assert\Length(max: 50, min: 10)] // добавляется чтобы не сохранялось с названием более 50 символов и менее 10
    //#[Assert\NotEqualTo("Merde", message: "Le mot 'Merde' n'est pas autorisé dans le titre.")] // добавляется чтобы не сохранялось с названием содержащим слово "Merde"
    #[InappropriateWords()]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Content obligatoire")] // добавляется чтобы не сохранялось с пустой строкой
    #[Assert\Length(min: 20, minMessage: "Valeur trop petite, 20 minimum")] // добавляется чтобы не сохранялось с описанием менее 20 символов
    private ?string $content = null;

    use Timestampable;

    

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "Valeur trop petite, 1 minimum")] // добавляется чтобы не сохранялось с значением менее 1 минуты
    #[Assert\LessThan(1440, message: "Valeur trop grande, 1440 maximum")] // добавляется чтобы не сохранялось с значением более 1440 минут (24 часа)
    private ?int $duration = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageName = "https://cdn-icons-png.flaticon.com/512/4054/4054617.png";

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

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

}
