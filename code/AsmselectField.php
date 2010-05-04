<?php

/**
 * AsmselectField is a SilverStripe implementation of jquery-asmselect
 * 
 * http://code.google.com/p/jquery-asmselect/
 * 
 * @author Jeremy Shipman http://www.burnbright.co.nz
 */

class AsmselectField extends DropdownField{
	
	protected $rollbacksize = 10; //size of multiple select filed if js is broken
	protected $usejavascript = true;
	
	function __construct($name, $title = null, $source = array(), $values = array(), $form = null) {
		parent::__construct($name, $title, $source, $values , $form, null);
	}
	
	function Field() {
		
		if($this->usejavascript){
			//Requirements::css('jqueryfields/asmselect/jquery.asmselect.css'); //can be included seperately if desired
			Requirements::javascript('jsparty/jquery/jquery.js');
			Requirements::javascript('jqueryfields/asmselect/jquery.asmselect.js');
			
			$id = $this->id();
			$removelabel = 'remove'; //TODO: allow to be custom
			
			//TODO: provide more customisation options
			$script = <<<JS
					jQuery(document).ready(function($) {
					    $("select#$id").asmSelect({
					    	listType: 'ul',
					    	removeLabel: "$removelabel"
					    });
					}); 
JS;
			Requirements::customScript($script,'asmselect'.$this->name);
		}
		
		$options = '';
		
		$source = $this->getSource();
		if($source) {
			// For SQLMap sources, the empty string needs to be added specially
			if(is_object($source) && $this->emptyString) {
				$options .= $this->createTag('option', array('value' => ''), $this->emptyString);
			}
			
			foreach($source as $value => $title) {
				
				// Blank value of field and source (e.g. "" => "(Any)")
				if($value === '' && ($this->value === '' || $this->value === null)) {
					$selected = 'selected';
				} else {
					// Normal value from the source
					$selected = null;					
					if(count($this->value) > 0){
						$selected = (in_array($value,$this->value)) ? 'selected' : null;
						$this->isSelected = ($selected) ? true : false;
					}
				}
				
				$options .= $this->createTag(
					'option',
					array(
						'selected' => $selected,
						'value' => $value
					),
					$title
				);
			}
		}
		
		$attributes = array(
			'class' => ($this->extraClass() ? $this->extraClass() : ''),
			'id' => $this->id(),
			'name' => $this->name."[]",
			'tabindex' => $this->getTabIndex(),
			'multiple' => 'multiple',
			'size' => $this->rollbacksize
		);
		
		if($this->disabled) $attributes['disabled'] = 'disabled';

		return $this->createTag('select', $attributes, $options);
	}
	
	function saveInto(DataObject $record) {
		$fieldName = $this->name;
		$saveDest = $record->$fieldName();
		if(! $saveDest)
			user_error("AsmselectField::saveInto() Field '$fieldName' not found on $record->class.$record->ID", E_USER_ERROR);
		$saveDest->setByIDList($this->value);
	}
	
	function setListSize($size){
		$this->rollbacksize = $size;
	}
	
	/**
	 * If you don't want to use the Asmselect functionality
	 */
	function disableJavascript(){
		$this->usejavascript = false;
	}
	
}

?>
