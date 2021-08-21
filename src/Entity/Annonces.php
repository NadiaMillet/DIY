<?php

namespace App\Entity;

use App\Repository\AnnoncesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=AnnoncesRepository::class)
 * @ORM\Table(name= "annonces", indexes={@ORM\Index(columns={"title", "content"}, flags={"fulltext"})})
 */
class Annonces
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
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     *  @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="annonces", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\ManyToMany(targetEntity=Users::class, inversedBy="favoris")
     */
    private $favoris;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img3;
    // // L'on précise ce qui doit se passer au moment d'un persiste de l'image. Au moment de la création de l'annonces, des données doivent également être injectées dans la table images
    // public function __construct()
    // {
    //     $this->images = new ArrayCollection();
    //     $this->favoris = new ArrayCollection();
    // }

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

    public function getSlug(): ?string
    {
        return $this->slug;
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

    public function getCreatedAt()
    {
        return $this->created_at;
    }


    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    // /**
    //  * @return Collection|Images[]
    //  */
    // public function getImages(): Collection
    // {
    //     return $this->images;
    // }

    // public function addImage(Images $image): self
    // {
    //     if (!$this->images->contains($image)) {
    //         $this->images[] = $image;
    //         $image->setAnnonces($this);
    //     }

    //     return $this;
    // }

    // public function removeImage(Images $image): self
    // {
    //     if ($this->images->removeElement($image)) {
    //         // set the owning side to null (unless already changed)
    //         if ($image->getAnnonces() === $this) {
    //             $image->setAnnonces(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection|Users[]
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Users $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
        }

        return $this;
    }

    public function removeFavori(Users $favori): self
    {
        $this->favoris->removeElement($favori);

        return $this;
    }

    public function getImg1()
    {
        return $this->img1;
    }

    public function setImg1($img1)
    {
        $this->img1 = $img1;

        return $this;
    }

    public function getImg2()
    {
        return $this->img2;
    }

    public function setImg2($img2)
    {
        $this->img2 = $img2;

        return $this;
    }

    public function getImg3()
    {
        return $this->img3;
    }

    public function setImg3($img3)
    {
        $this->img3 = $img3;

        return $this;
    }
}
