<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email déjà associé à un compte.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo existe déjà ')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

//    #[Assert\Regex('/^[\w\.=-]+@[\w\.-]+\.[\w]{2,3}$/', message: 'L\'adresse mail n\'est pas au bon
//                 format ex: adresses.exemple@mail.com')]
    #[Assert\Email]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\Regex(pattern:'/^[A-Z]+$/', message: 'Votre nom ne  peut contenir que des majuscules')]
    #[Assert\Length(min:3, minMessage: 'Indiquez un nom 3 caractères minimum')]
    #[Assert\Length(max:20, maxMessage: 'Indiquez un nom de 10 caractères maximum')]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Assert\Regex(pattern:'/^[A-zÀ-ú-]+$/', message: 'Votre prénom ne  peut contenir que des lettres entre a & b')]
    #[Assert\Length(min:3, minMessage: 'Indiquez un nom 3 caractères minimum')]
    #[Assert\Length(max:30, maxMessage: 'Indiquez un nom de 30 caractères maximum')]
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[Assert\Regex('/^[A-z0-9]+$/', message: 'Votre pseudo ne peut contenir que des caractères alphanumérique pas 
    d\'accents ou de caractères spéciaux' )]
    #[Assert\Length(max: 10, maxMessage: 'Votre pseudo ne peut faire plus de {{value}} caractères')]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(pattern: '/(0|\+33)[1-9]( *[0-9]{2}){4}/', message: 'Le numéro de téléphone saisie est incorrecte 
                            Format : +33000000000 ou 0000000000')]
    #[Assert\Length(min:10, minMessage: 'Le numéro doit être composé de 10 numéros ex: 0601020304')]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class)]
    private Collection $sortiesOrganisees;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'inscrits')]
    private Collection $sortiesInscrit;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    public function __construct()
    {
        $this->sortiesOrganisees = new ArrayCollection();
        $this->sortiesInscrit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortieOrganisee(Sortie $sortieOrganisee): self
    {
        if (!$this->sortiesOrganisees->contains($sortieOrganisee)) {
            $this->sortiesOrganisees->add($sortieOrganisee);
        }

        return $this;
    }

    public function removeSortieOrganisee(Sortie $sortieOrganisee): self
    {
        if ($this->sortiesOrganisees->removeElement($sortieOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortieOrganisee->getOrganisateur() === $this) {
                $sortieOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesInscrit(): Collection
    {
        return $this->sortiesInscrit;
    }

    public function addSortiesInscrit(Sortie $sortieInscrit): self
    {
        if (!$this->sortiesInscrit->contains($sortieInscrit)) {
            $this->sortiesInscrit->add($sortieInscrit);
            $sortieInscrit->addInscrit($this);
        }

        return $this;
    }

    public function removeSortiesInscrit(Sortie $sortieInscrit): self
    {
        if ($this->sortiesInscrit->removeElement($sortieInscrit)) {
            $sortieInscrit->removeInscrit($this);
        }

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
