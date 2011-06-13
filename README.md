
Here's a handy use for it:

	//decorate member and add the following to updateCMSFields()
	if($groups = DataObject::get('Group')){
		$mtable = new AsmselectField('Groups','Security Groups',$groups->map('ID','Title'),$this->owner->Groups()->map('ID','ID'));
		$fields->addFieldToTab('Root.Security',$mtable);
	}