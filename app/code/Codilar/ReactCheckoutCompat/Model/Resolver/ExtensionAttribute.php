<?php
declare(strict_types=1);

namespace Codilar\ReactCheckoutCompat\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Replacement for FriendsOfHyva\ReactCheckout\Model\Resolver\ExtensionAttribute.
 *
 * The original declares `array $value = null` (implicitly nullable), which is
 * deprecated since PHP 8.4. Magento turns that deprecation into an exception in
 * developer mode, so every checkout GraphQL query failed with
 * "Internal server error". It does not extend the original, because loading the
 * original class file is what triggers the deprecation.
 */
class ExtensionAttribute implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        return $value['extension_attributes'][$field->getName()] ?? null;
    }
}
