<?php
declare(strict_types=1);

namespace Codilar\ReactCheckoutCompat\Block;

use FriendsOfHyva\ReactCheckout\Block\CheckoutTranslator as BaseCheckoutTranslator;

/**
 * Passes str_getcsv()'s $escape argument explicitly; omitting it is deprecated
 * since PHP 8.4 and breaks the checkout page in developer mode.
 */
class CheckoutTranslator extends BaseCheckoutTranslator
{
    public function getTranslations(): string
    {
        $translations = $this->getCheckoutTranslations();

        if (!isset($translations)) {
            return json_encode([]);
        }

        $stringsTranslated = [];

        foreach ($translations as $translatableItem) {
            foreach (str_getcsv((string) $translatableItem, ',', '"', '\\') as $stringToBeTranslated) {
                $stringsTranslated[$stringToBeTranslated] = __($stringToBeTranslated);
            }
        }

        return json_encode($stringsTranslated);
    }
}
