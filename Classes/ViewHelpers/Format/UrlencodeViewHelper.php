<?php
namespace Punktde\PtExtbase\ViewHelpers\Format;
/**
 * This script is taken from fluid > 4.6 to be used in a TYPO3 4.5 environment
 */


/*                                                                        *
 * This script is backported from the FLOW3 package "TYPO3.Fluid".        *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Encodes the given string according to http://www.faqs.org/rfcs/rfc3986.html (applying PHPs rawurlencode() function)
 * @see http://www.php.net/manual/function.rawurlencode.php
 *
 * = Examples =
 *
 * <code title="default notation">
 * <f:format.rawurlencode>foo @+%/</f:format.rawurlencode>
 * </code>
 * <output>
 * foo%20%40%2B%25%2F (rawurlencode() applied)
 * </output>
 *
 * <code title="inline notation">
 * {text -> f:format.urlencode()}
 * </code>
 * <output>
 * Url encoded text (rawurlencode() applied)
 * </output>
 *
 * @api
 */
class UrlencodeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Disable the escaping interceptor because otherwise the child nodes would be escaped before this view helper
     * can decode the text's entities.
     *
     * @var boolean
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * Escapes special characters with their escaped counterparts as needed using PHPs rawurlencode() function.
     *
     * @param string $value string to format
     * @see http://www.php.net/manual/function.rawurlencode.php
     * @api
     */
    public function render($value = null)
    {
        if ($value === null) {
            $value = $this->renderChildren();
        }
        if (!is_string($value)) {
            return $value;
        }
        return rawurlencode($value);
    }
}
