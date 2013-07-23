<?php

/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Common class for HTML_QuickForm elements to display a CAPTCHA
 *
 * The HTML_QuickForm_CAPTCHA package adds an element to the
 * HTML_QuickForm package to display a CAPTCHA question (image, riddle, etc...)
 *
 * This package requires the use of a PHP session.
 *
 * PHP versions 4 and 5
 *
 * @category   HTML
 * @package    HTML_QuickForm_CAPTCHA
 * @author     Philippe Jausions <Philippe.Jausions@11abacus.com>
 * @copyright  2006 by 11abacus
 * @license    LGPL
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_QuickForm_CAPTCHA
 */

/**
 * Required packages
 */

require_once 'HTML/QuickForm/input.php';
require_once 'Text/CAPTCHA.php';




/**
 * Common class for HTML_QuickForm elements to display a CAPTCHA
 *
 * The HTML_QuickForm_CAPTCHA package adds an element to the
 * HTML_QuickForm package to display a CAPTCHA question (image, riddle, etc...)
 *
 * This package requires the use of a PHP session.
 *
 * Because the CAPTCHA element is serialized in the PHP session,
 * you need to include the class declaration BEFORE the session starts.
 * So BEWARE if you have php.ini session.auto_start enabled, you won't be
 * able to use this element (unless you're also using PHP 5's __autoload()
 *
 * PHP versions 4 and 5
 *
 * @category   HTML
 * @package    HTML_QuickForm_CAPTCHA
 * @author     Philippe Jausions <Philippe.Jausions@11abacus.com>
 * @copyright  2006 by 11abacus
 * @license    LGPL
 * @version    Release: 0.2.1
 * @link       http://pear.php.net/package/HTML_QuickForm_CAPTCHA
 */
class HTML_QuickForm_CAPTCHA extends HTML_QuickForm_input
{
	

    /**
     * Default options
     *
     * @var array
     * @access protected
     */
    var $_options = array(
            'sessionVar' => '_HTML_QuickForm_CAPTCHA',
            'phrase'     => null,
            );

    /**
     * CAPTCHA driver
     *
     * @var string
     * @access protected
     */
    var $_CAPTCHA_driver;

    /**
     * Class constructor
     *
     * @param      string    Name
     * @param      mixed     Label for the CAPTCHA
     * @param      array     Options for the Text_CAPTCHA package
     * <ul>
     *  <li>'sessionVar'   (string)  name of session variable containing
     *                               the Text_CAPTCHA instance (defaults to
     *                               _HTML_QuickForm_CAPTCHA.)</li>
     *  <li>Other options depend on the driver used</li>
     * </ul>
     * @param      mixed     HTML Attributes for the <a> tag surrounding the
     *                       image. Can be a string or array.
     * @access     public
     */
    function HTML_QuickForm_CAPTCHA($elementName = null, $elementLabel = null,
                                    $options = null, $attributes = null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, $elementLabel,
                                                   $attributes);
        $this->setType('CAPTCHA_'.$this->_CAPTCHA_driver);

        if (is_array($options)) {
            $this->_options = array_merge($this->_options, $options);
        }
    }



    /**
     * Initializes the CAPTCHA instance (if needed)
     *
     * @access     protected
     * @return     boolean  TRUE or PEAR_Error on error
     */
    function _initCAPTCHA()
    {
        $sessionVar = $this->_options['sessionVar'];

        if (empty($_SESSION[$sessionVar])) {
            $_SESSION[$sessionVar] =& Text_CAPTCHA::factory($this->_CAPTCHA_driver);
            if (PEAR::isError($_SESSION[$sessionVar])) {
                return $_SESSION[$sessionVar];
            }
            $result = $_SESSION[$sessionVar]->init($this->_options);
            if (PEAR::isError($result)) {
                return $result;
            }
        }

        return true;
    }

    /**
     * Returns the answer/phrase of the CAPTCHA
     *
     * @return    string
     * @access    private
     */
    function _findValue(&$values)
    {
        return $this->getValue();
    }

    /**
     * Returns the answer/phrase of the CAPTCHA
     *
     * @return    string
     * @access    public
     */
    function getValue()
    {
        $sessionVar = $this->_options['sessionVar'];
// var_dump($this);
 //var_dump($_SESSION[$sessionVar]);
        return (!empty($_SESSION[$sessionVar]))
                 ? $_SESSION[$sessionVar]->getPhrase()
                 : null;
    }

    /**
     * Returns the answer/phrase of the CAPTCHA
     *
     * @return    string
     * @access    public
     */
    function exportValue(&$submitValues, $assoc = false)
    {
        return ($assoc)
               ? array($this->getName() => $this->getValue())
               : $this->getValue();
    }

    /**
     * Sets the CAPTCHA question/phrase
     *
     * Pass NULL or no argument for a random question/phrase to be generated
     *
     * @param string $phrase
     * @access public
     */
    function setPhrase($phrase = null)
    {
        $this->_options['phrase'] = $phrase;

        if (!empty($_SESSION[$this->_options['sessionVar']])) {
            $_SESSION[$this->_options['sessionVar']]->setPhrase($phrase);
        }
    }

    /**
     * Destroys the CAPTCHA instance to prevent reuse
     *
     * @access public
     */
    function destroy()
    {
        unset($_SESSION[$this->_options['sessionVar']]);
    }

    /**
     * Returns the HTML for the CAPTCHA
     *
     * This can be overwritten by sub-classes for specific output behavior
     * (for instance the Image CAPTCHA displays an image)
     *
     * @access     public
     * @return     string
     */
    function toHtml()
    {
        $result = $this->_initCAPTCHA();
        if (PEAR::isError($result)) {
            return $result;
        }

        $captcha = $_SESSION[$this->_options['sessionVar']]->getCAPTCHA();

        $attr = $this->_attributes;
        unset($attr['type']);
        unset($attr['value']);
        unset($attr['name']);

        $html = $this->_getTabs()
                . '<span' . $this->_getAttrString($attr) . '>'
                . htmlspecialchars($captcha)
                . '</span>';
        return $html;
    }
}

/**
 * Register the rule with QuickForm
 */
require_once 'HTML/QuickForm/Rule/CAPTCHA.php';

?>