<?php

/**
 * AsmselectField is a SilverStripe implementation of jquery-asmselect
 *
 * http://code.google.com/p/jquery-asmselect/
 *
 * @author Jeremy Shipman http://www.burnbright.co.nz
 *
 * Example usage:
 *
 * $members = DataObject::get('ID','Title');
 * $asmfield = new AsmselectField('SpecialMembers','Special Members',$members);
 *
 */

class AsmselectField extends DropdownField
{

    protected $rollbacksize = 10; //size of multiple select filed if js is broken
    protected $usejavascript = true;

    public $dontEscape = true;
    protected $reserveNL = false;

    public function __construct($name, $title = null, $source = array(), $values = array(), $form = null)
    {
        parent::__construct($name, $title, $source, $values, $form, null);
    }

    public function Field($properties = array())
    {
        if ($this->usejavascript) {
            Requirements::css(ASMSELECTFIELD_DIR.'/css/jquery.asmselect.basic.css'); //can be included seperately if desired
            Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js');
            Requirements::javascript(THIRDPARTY_DIR.'/jquery-livequery/jquery.livequery.js');
            Requirements::javascript(ASMSELECTFIELD_DIR.'/javascript/jquery.asmselect.js');

            $id = $this->id();
            $removelabel = 'remove'; //TODO: allow to be custom

            //TODO: provide more customisation options
            Requirements::javascript(ASMSELECTFIELD_DIR.'/javascript/asmselectfield.js');
        }

        $options = '';

        $source = $this->getSource();
        $values = $this->value;

        // Get values from the join, if available
        if (is_object($this->form)) {
            $record = $this->form->getRecord();
            if (!$values && $record && $record->hasMethod($this->name)) {
                $funcName = $this->name;
                $join = $record->$funcName();
                if ($join) {
                    foreach ($join as $joinItem) {
                        $values[] = $joinItem->ID;
                    }
                }
            }
        }

        if ($source) {
            // For SQLMap sources, the empty string needs to be added specially
            if (is_object($source) && $this->emptyString) {
                $options .= $this->createTag('option', array('value' => ''), $this->emptyString);
            }
            foreach ($source as $value => $title) {
                // Blank value of field and source (e.g. "" => "(Any)")
                if ($value === '' && ($values === '' || $values === null)) {
                    $selected = 'selected';
                } else {
                    // Normal value from the source
                    $selected = null;
                    if (count($values) > 0) {
                        $selected = (in_array($value, $values)) ? 'selected' : null;
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
            'class' => ($this->extraClass() ? $this->extraClass()." asmselectfield" : 'asmselectfield'),
            'id' => $this->id(),
            'name' => $this->name."[]",
            'tabindex' => $this->getAttribute('tabindex'),
            'multiple' => 'multiple',
            'size' => $this->rollbacksize
        );

        if ($this->disabled) {
            $attributes['disabled'] = 'disabled';
        }

        return $this->createTag('select', $attributes, $options);
    }

    public function saveInto(DataObjectInterface $record)
    {
        $fieldName = $this->name;
        $saveDest = $record->$fieldName();
        if (! $saveDest) {
            user_error("AsmselectField::saveInto() Field '$fieldName' not found on $record->class.$record->ID", E_USER_ERROR);
        }
        if (is_array($this->value)) {

            //hack to make the field work in the CMS (likely cms 2.4 js related)
            if (Director::is_ajax() && Controller::curr() instanceof LeftAndMain) {
                $this->value = explode(",", $this->value[0]);
            }

            $saveDest->setByIDList($this->value);
        }
    }

    public function setListSize($size)
    {
        $this->rollbacksize = $size;
    }

    /**
     * If you don't want to use the Asmselect functionality
     */
    public function disableJavascript()
    {
        $this->usejavascript = false;
    }
}
