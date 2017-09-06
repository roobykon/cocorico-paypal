<?php

namespace Cocorico\TranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CocoricoTranslationBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'JMSTranslationBundle';
    }

}
