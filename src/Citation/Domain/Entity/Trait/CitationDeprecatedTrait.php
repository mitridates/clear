<?php
namespace  App\Citation\Domain\Entity\Trait;

/**
 * Citation date
 */
trait   CitationDeprecatedTrait
{

//    /**
    //     * Type of publication. FD 322 (Not yet ready... No hay FVC asociados)
    //     * @var ?string
    //     * @Assert\Length(
    //     *      max = 1,
    //     *      maxMessage= "cave.validator.max.length"
    //     * )
    //     *
    //     * @ORM\Column(name="type", type="string", length=1, nullable=true, options={"fixed" = true})
    //     */
    //    private $publicationtype;
    //
    //
    //     * @var ?bool Flag if surname overflow. FD 329 Not yet ready ????
    //     * @ORM\Column(name="flag_if_surname_overflow", type="boolean", nullable=true, options={"fixed" = true})
    //     */
    //    private $surnameoverflow;
    //    /**
    //     * Referencia al recurso digital. FD 3072
    //     * @Assert\Length(max=200)
    //     * @ORM\Column(name="map_image_reference", type="string", length=200, nullable=true)
    //     */
    //    private ?string $reference;
    //
    // Outdated
    //    /**
    //     * @return ?string
    //     */
    //    public function getPublicationtype(): ?string
    //    {
    //        return $this->publicationtype;
    //    }
    //
    //    /**
    //     * @param ?string $publicationtype
    //     * @return Article
    //     */
    //    public function setPublicationtype(?string $publicationtype): Article
    //    {
    //        $this->publicationtype = $publicationtype;
    //        return $this;
    //    }
}