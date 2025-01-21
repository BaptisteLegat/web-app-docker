<?php

namespace App\Entity;

use App\Repository\PictureUserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PictureUserRepository::class)]
#[ORM\Table(name: 'picture_user')]
#[ORM\UniqueConstraint(name: 'picture_user_unique', columns: ['picture_id', 'user_id'])]
class PictureUser
{
    #[ORM\ManyToOne(targetEntity: Picture::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'picture_id')]
    #[ORM\Id]
    private ?Picture $picture = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'pictures')]
    #[ORM\JoinColumn(name: 'user_id')]
    #[ORM\Id]
    private ?User $user = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isLiked = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    public function getPicture(): ?picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isLiked(): ?bool
    {
        return $this->isLiked;
    }

    public function setIsLiked(bool $isLiked): self
    {
        $this->isLiked = $isLiked;

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
}
