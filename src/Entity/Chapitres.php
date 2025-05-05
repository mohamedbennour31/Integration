<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Ressources;

#[ORM\Entity]
class Chapitres
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Ressources::class, inversedBy: "chapitres")]
    #[ORM\JoinColumn(name: 'id_ressources', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Ressources $id_ressources;

    #[ORM\Column(type: "string", length: 500)]
    private string $url_fichier;

    #[ORM\Column(type: "string", length: 255)]
    private string $titre;

    #[ORM\Column(type: "text")]
    private string $contenu;

    #[ORM\Column(type: "string", length: 50)]
    private string $format_fichier;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId_ressources()
    {
        return $this->id_ressources;
    }

    public function setId_ressources($value)
    {
        $this->id_ressources = $value;
    }

    public function getUrl_fichier()
    {
        return $this->url_fichier;
    }

    public function setUrl_fichier($value)
    {
        $this->url_fichier = $value;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($value)
    {
        $this->titre = $value;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($value)
    {
        $this->contenu = $value;
    }

    public function getFormat_fichier()
    {
        return $this->format_fichier;
    }

    public function setFormat_fichier($value)
    {
        $this->format_fichier = $value;
    }
}
