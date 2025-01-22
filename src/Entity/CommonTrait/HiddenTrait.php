<?php
namespace  App\Entity\CommonTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Show/hide options for main entities for management.
 */
trait HiddenTrait
{
    #[ORM\Column(name: 'hidden', type: 'boolean', nullable: false, options: ['default' => 1])]
    private bool $hidden=true;

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden):self
    {
        $this->hidden = $hidden;
        return $this;
    }
}
