<?php

namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class OptionValue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Store\ProductBundle\Entity\OptionType", inversedBy="values")
     */
    protected $option;

    /**
     * @ORM\ManyToMany(targetEntity="Variant", mappedBy="value")
     */
    protected $variants;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fileupdate;

    public function __construct()
    {
        $this->variants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return OptionValue
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set option
     *
     * @param \Store\ProductBundle\Entity\OptionType $option
     * @return OptionValue
     */
    public function setOption(\Store\ProductBundle\Entity\OptionType $option = null)
    {
        $this->option = $option;
    
        return $this;
    }

    /**
     * Get option
     *
     * @return \Store\ProductBundle\Entity\OptionType
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Add variants
     */
    public function addVariant(\Store\ProductBundle\Entity\Variant $variant)
    {
        $this->variants[] = $variant;
    }

    public function removeVariant(\Store\ProductBundle\Entity\Variant $variant)
    {
        $this->variants->removeElement($variant);
    }

    public function getVariants()
    {
        return $this->variants;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/documents/vignettes';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // faites ce que vous voulez pour générer un nom unique
            $this->path = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
            $this->fileupdate = md5(time());
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode move(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->path);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * Set path
     *
     * @param string $path
     * @return OptionValue
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set fileupdate
     *
     * @param string $fileupdate
     * @return OptionValue
     */
    public function setFileupdate($fileupdate)
    {
        $this->fileupdate = $fileupdate;
    
        return $this;
    }

    /**
     * Get fileupdate
     *
     * @return string 
     */
    public function getFileupdate()
    {
        return $this->fileupdate;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}