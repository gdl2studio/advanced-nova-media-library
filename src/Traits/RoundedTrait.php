<?php

namespace Ebess\AdvancedNovaMediaLibrary\Traits;

trait RoundedTrait
{
    /**
     * Indicates whether the image should be fully rounded or not.
     *
     * @var bool
     */
    public $rounded = false;

    /**
     * Display the image thumbnail with full-rounded edges.
     *
     * @return $this
     */
    public function rounded(): self
    {
        return $this->withMeta(['rounded' => $this->rounded = true]);
    }

    /**
     * Display the image thumbnail with square edges.
     *
     * @return $this
     */
    public function squared()
    {
        return $this->withMeta(['rounded' => $this->rounded = false]);
    }

    /**
     * Determine whether the field should have rounded corners.
     *
     * @return bool
     */
    public function isRounded()
    {
        return true == $this->rounded;
    }

    /**
     * Determine whether the field should have squared corners.
     *
     * @return bool
     */
    public function isSquared()
    {
        return false == $this->rounded;
    }
}
