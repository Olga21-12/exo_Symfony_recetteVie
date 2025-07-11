<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use App\Validator\InappropriateWords;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // добавляется чтобы не сохранялось с пустой строкой
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; // добавляется чтобы не сохранялось с названием, которое уже существует

use App\Entity\Traits\Timestampable;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ORM\Table(name: "recipes")]
#[UniqueEntity('title')] // добавляется чтобы не сохранялось с названием, которое уже существует
#[Vich\Uploadable]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank()]  // добавляется чтобы не сохранялось с пустой строкой
    #[Assert\Length(min: 10)] // добавляется чтобы не сохранялось с названием менее 10 символов
    #[Assert\Length(max: 50)] // добавляется чтобы не сохранялось с названием более 255 символов
    // #[Assert\Length(max: 50, min: 10)] // добавляется чтобы не сохранялось с названием более 50 символов и менее 10
    //#[Assert\NotEqualTo("Merde", message: "Le mot 'Merde' n'est pas autorisé dans le titre.")] // добавляется чтобы не сохранялось с названием содержащим слово "Merde"
    #[InappropriateWords()]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()] // добавляется чтобы не сохранялось с пустой строкой
    #[Assert\Length(min: 20)] // добавляется чтобы не сохранялось с описанием менее 20 символов
    private ?string $content = null;

    use Timestampable;    

    #[ORM\Column(nullable: true)]
    #[Assert\Positive()] // добавляется чтобы не сохранялось с значением менее 1 минуты
    #[Assert\LessThan(1440)] // добавляется чтобы не сохранялось с значением более 1440 минут (24 часа)
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // 👇 Фото для рецепта (имя файла)
    #[ORM\Column(nullable: true)]
    private ?string $imageName = 'sans_photo.png';

    // 👇 Размер файла (необязательно)
    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    // 👇 Поле для загрузки файла (через Vich)
    #[Vich\UploadableField(mapping: 'recipe_image', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    // ================================
    // ✅ Геттеры и сеттеры для изображения
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }



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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function __serialize(): array
{
    return [
        'id' => $this->id,
        'email' => $this->email,
        'password' => $this->password,
    ];
}


}