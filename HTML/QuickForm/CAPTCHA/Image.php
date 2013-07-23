<?php

/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Element for HTML_QuickForm to display a CAPTCHA image
 *
 * The HTML_QuickForm_CAPTCHA package adds an element to the
 * HTML_QuickForm package to display a CAPTCHA image.
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
require_once 'HTML/QuickForm/CAPTCHA.php';
require_once 'Text/CAPTCHA/Driver/Image.php';

/**
 * Element for HTML_QuickForm to display a CAPTCHA image
 *
 * The HTML_QuickForm_CAPTCHA package adds an element to the
 * HTML_QuickForm package to display a CAPTCHA image.
 *
 * Options for the element
 * <ul>
 *  <li>'width'        (integer) width of the image,</li>
 *  <li>'height'       (integer) height of the image,</li>
 *  <li>'imageOptions' (array)   options passed to the Image_Text
 *                               constructor,</li>
 *  <li>'callback'     (string)  URL of callback script that will generate
 *                               and output the image itself,</li>
 *  <li>'alt'          (string)  the alt text for the image,</li>
 *  <li>'sessionVar'   (string)  name of session variable containing
 *                               the Text_CAPTCHA instance (defaults to
 *                               _HTML_QuickForm_CAPTCHA.)</li>
 * </ul>
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
 * @version    Release: 0.2.1
 * @link       http://pear.php.net/package/HTML_QuickForm_CAPTCHA
 * @see        Text_CAPTCHA_Driver_Image
 */
class HTML_QuickForm_CAPTCHA_Image extends HTML_QuickForm_CAPTCHA
{
    /**
     * Default options
     *
     * @var array
     * @access protected
     */
    var $_options = array(
            'sessionVar'   => '_HTML_QuickForm_CAPTCHA',
            'width'        => '200',
            'height'       => '80',
            'alt'          => 'Click to view another image',
            'callback'     => '',
            'imageOptions' => null,
            'phrase'       => null,
            'output'       => 'jpeg',
            );

    /**
     * CAPTCHA driver
     *
     * @var string
     * @access protected
     */
    var $_CAPTCHA_driver = 'Image';

    /**
     * Returns the HTML for the CAPTCHA image
     *
     * @access     public
     * @return     string
     */
    function toHtml()
    {
        if ($this->_flagFrozen) {
            return '';
        }

        $result = parent::_initCAPTCHA();
        if (PEAR::isError($result)) {
            return $result;
        }

        $html      = '';
        $tabs      = $this->_getTabs();
        $inputName = $this->getName();
        $imgName   = 'QF_CAPTCHA_' . $inputName;

        if ($this->getComment() != '') {
            $html .= $tabs . '<!-- ' . $this->getComment() . ' // -->';
        }

        $html = $tabs . '<a href="' . $this->_options['callback']
                . '" target="_blank" '
                . $this->_getAttrString($this->_attributes)
                . ' onclick="var cancelClick = false; '
                . $this->getOnclickJs($imgName)
                . ' return !cancelClick;"><img src="'
                . $this->_options['callback'] . '" name="' . $imgName
                . '" id="' . $imgName . '" width="' . $this->_options['width']
                . '" height="' . $this->_options['height'] . '" title="'
                . htmlspecialchars($this->_options['alt']) . '" /></a>';

        return $html;
    }

    /**
     * Create the javascript for the onclick event which will
     * reload a new CAPTCHA image
     *
     * @param     string    $imageName    The image name/id
     *
     * @access public
     * @return string
     */
    function getOnclickJs($imageName)
    {
        $onclickJs = 'if (document.images) {var img = new Image(); var d = new Date(); img.src = this.href + ((this.href.indexOf(\'?\') == -1) ? \'?\' : \'&\') + d.getTime(); document.images[\'' . addslashes($imageName) . '\'].src = img.src; cancelClick = true;}';
        return $onclickJs;
    }
}

/**
 * Register the class with QuickForm
 */
if (class_exists('HTML_QuickForm')) {
    HTML_QuickForm::registerElementType('CAPTCHA_Image',
            'HTML/QuickForm/CAPTCHA/Image.php', 'HTML_QuickForm_CAPTCHA_Image');
}

?>