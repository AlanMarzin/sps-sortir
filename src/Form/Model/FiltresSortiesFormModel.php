<?php

namespace App\Form\Model;

use App\Entity\Campus;
use Symfony\Component\Validator\Constraints as Assert;

class FiltresSortiesFormModel
{

    private ?Campus $campus;
    private ?string $nomRecherche;
    private ?\DateTimeInterface $dateDebut;

//    #[Assert\Expression(
//        "this.getDateFin() > this.getDateDebut()",
//        message: 'La date de fin doit être postérieure à la date de début'
//    )]
    private ?\DateTimeInterface $dateFin;
    private ?bool $isOrganisateur;
    private ?bool $isInscrit;
    private ?bool $isNotInscrit;
    private ?bool $isPassee;

    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     * @return FiltresSortiesFormModel
     */
    public function setCampus(?Campus $campus): FiltresSortiesFormModel
    {
        $this->campus = $campus;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomRecherche(): ?string
    {
        return $this->nomRecherche;
    }

    /**
     * @param string|null $nomRecherche
     * @return FiltresSortiesFormModel
     */
    public function setNomRecherche(?string $nomRecherche): FiltresSortiesFormModel
    {
        $this->nomRecherche = $nomRecherche;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    /**
     * @param \DateTimeInterface|null $dateDebut
     * @return FiltresSortiesFormModel
     */
    public function setDateDebut(?\DateTimeInterface $dateDebut): FiltresSortiesFormModel
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    /**
     * @param \DateTimeInterface|null $dateFin
     * @return FiltresSortiesFormModel
     */
    public function setDateFin(?\DateTimeInterface $dateFin): FiltresSortiesFormModel
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsOrganisateur(): ?bool
    {
        return $this->isOrganisateur;
    }

    /**
     * @param bool|null $isOrganisateur
     * @return FiltresSortiesFormModel
     */
    public function setIsOrganisateur(?bool $isOrganisateur): FiltresSortiesFormModel
    {
        $this->isOrganisateur = $isOrganisateur;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsInscrit(): ?bool
    {
        return $this->isInscrit;
    }

    /**
     * @param bool|null $isInscrit
     * @return FiltresSortiesFormModel
     */
    public function setIsInscrit(?bool $isInscrit): FiltresSortiesFormModel
    {
        $this->isInscrit = $isInscrit;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsNotInscrit(): ?bool
    {
        return $this->isNotInscrit;
    }

    /**
     * @param bool|null $isNotInscrit
     * @return FiltresSortiesFormModel
     */
    public function setIsNotInscrit(?bool $isNotInscrit): FiltresSortiesFormModel
    {
        $this->isNotInscrit = $isNotInscrit;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPassee(): ?bool
    {
        return $this->isPassee;
    }

    /**
     * @param bool|null $isPassee
     * @return FiltresSortiesFormModel
     */
    public function setIsPassee(?bool $isPassee): FiltresSortiesFormModel
    {
        $this->isPassee = $isPassee;
        return $this;
    }

}