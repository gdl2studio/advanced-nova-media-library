<?php

namespace Ebess\AdvancedNovaMediaLibrary\Fields;

use Ebess\AdvancedNovaMediaLibrary\Traits\RoundedTrait;

class Images extends Media
{
    use RoundedTrait;

    protected $defaultValidatorRules = ['image'];

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->croppable(false);
    }

    /**
     * Do we deprecate this for SingleMediaRules?
     *
     * @param $singleImageRules
     *
     * @return Images
     */
    public function singleImageRules($singleImageRules): self
    {
        $this->singleMediaRules = $singleImageRules;

        return $this;
    }

    public function croppable(bool $croppable = true): self
    {
        return $this->withMeta(compact('croppable'));
    }

    public function croppingConfigs(array $configs): self
    {
        return $this->withMeta(['croppingConfigs' => $configs]);
    }

    public function showDimensions(bool $showDimensions = true): self
    {
        return $this->withMeta(compact('showDimensions'));
    }

    public function showRatio(bool $showRatio = true): self
    {
        return $this->withMeta(compact('showRatio'));
    }

    public function showMimeType(bool $showMimeType = true): self
    {
        return $this->withMeta(compact('showMimeType'));
    }

    public function showGalleryOnIndex(int $imagesLimit = 5, int $tooltipImagesPerRow = 5): self
    {
        return $this->withMeta(compact('imagesLimit', 'tooltipImagesPerRow'));
    }
}
