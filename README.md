# SilverStripe ASM Select Field

Select multiple fields, with enhancement by javascript.

https://code.google.com/p/jquery-asmselect/

## Example usage

```php
//decorate member and add the following to updateCMSFields()
$groups = Group::get();
if($groups->exists()){
	$groupslist = new AsmselectField('Groups','Security Groups', $groups->map()->toArray(),$this->owner->Groups()->map('ID','ID')->toArray());
	$fields->addFieldToTab('Root.Security', $groupslist);
}
```

