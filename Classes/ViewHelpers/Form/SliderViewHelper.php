<?php
namespace PunktDe\PtExtbase\ViewHelpers\Form;

/***************************************************************
 *  Copyright (C) 2014 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Fluid\ViewHelpers\Form\TextfieldViewHelper;

/**
 * Class SliderViewHelper
 *
 * @package PunktDe\PtMalBase\ViewHelpers
 */
class SliderViewHelper extends TextfieldViewHelper {

	/**
	 * @param boolean $required If the field is required or not
	 * @param string $type The field type, e.g. "text", "email", "url" etc.
	 * @param integer $sliderMin
	 * @param integer $sliderMax
	 * @param integer $sliderStep
	 *
	 * @return string
	 */
	public function render($required = NULL, $type = 'text', $sliderMin = 0, $sliderMax = 10, $sliderStep = 1) {
		$inputString = parent::render($required, $type);

		$id = $this->arguments['id'];

		if (empty($id)) {
			return 'Please add an id to the slider';
		}


		$value = $sliderMin;
		if ($this->getValue() > $value) {
			$value = $this->getValue();
		}

		$sliderElementTemplate = '<div class="slider-element-wrap">%s<div class="slider-bar-wrap"><div class="slider-element" id="slider-' . $id . '"></div></div><div class="slider-input-wrap">%s</div></div>';

		$javascriptCode = '<script type="text/javascript">
$(function() {
	if (jQuery.ui) {
		jQuery("#slider-' . $id . '").slider({
			range: "min",
			min: ' . $sliderMin . ',
			max: ' . $sliderMax . ',
			step: ' . $sliderStep . ',
			value: ' . $value . ',
			slide: function(event, ui) {
				jQuery(ui.handle).closest(".slider-element-wrap").find("input").val(ui.value);
			}
		});

		jQuery("#' . $id . '").val(' . $value . ').live("keyup", function() {
			if (' . $sliderMin . ' <= this.value && this.value <= ' . $sliderMax . ') {
				jQuery(this).closest(".slider-element-wrap").find(".slider-element").slider("value", this.value);
			}
		});
	}
});
</script>';

		return sprintf($sliderElementTemplate, $javascriptCode, $inputString);

	}

}
