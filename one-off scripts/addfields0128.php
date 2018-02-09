<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";



// Make sure to give your file a descriptive name and place in the root of your installation.  Then access the appropriate URL in a browser.

// Turn on debugging level
$Vtiger_Utils_Log = true;
// Need these files
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
/*
$module = new Vtiger_Module();
$module->name = 'VanlineContacts';
$module->save();

$module->initTables();

$block1 = new Vtiger_Block();
$block1->label = 'LBL_VANLINECONTACTS_INFORMATION';
$module->addBlock($block1);*/
 $module = Vtiger_Module::getInstance('VanlineContacts'); // The module your blocks and fields will be in.
$block3 = new Vtiger_Block();
$block3->label = 'LBL_VANLINECONTACTS_ADDRESS';
$module->addBlock($block3);

 $module = Vtiger_Module::getInstance('VanlineContacts'); // The module your blocks and fields will be in.
//$module = Vtiger_Module::getInstance('VanlineContacts'); // The module your blocks and fields will be in.
$block4 = new Vtiger_Block();
$block4->label = 'LBL_VANLINECONTACTS_DESCRIPTION';
$module->addBlock($block4);

// To use a pre-existing block
 $module = Vtiger_Module::getInstance('VanlineContacts'); // The module your blocks and fields will be in.
 $block1 = Vtiger_Block::getInstance('LBL_VANLINECONTACTS_INFORMATION', $module);  // Must be the actual instance name, not just what appears in the browser.


// START Add new field
$field1 = new Vtiger_Field();
$field1->label = 'LBL_VANLINECONTACTS_LNAME';
$field1->name = 'vcontacts_lname';
$field1->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field1->column = 'vcontacts_lname';   //  This will be the columnname in your database for the new field.
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 2; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field1->typeofdata = 'V~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field1);
$module->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_VANLINECONTACTS_P1';
$field2->name = 'vcontacts_p1';
$field2->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field2->column = 'vcontacts_p1';   //  This will be the columnname in your database for the new field.
$field2->columntype = 'INT(20)';
$field2->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field2->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_VANLINECONTACTS_P2';
$field3->name = 'vcontacts_p2';
$field3->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field3->column = 'vcontacts_p2';   //  This will be the columnname in your database for the new field.
$field3->columntype = 'INT(20)';
$field3->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field3->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'LBL_VANLINECONTACTS_P3';
$field4->name = 'vcontacts_p3';
$field4->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field4->column = 'vcontacts_p3';   //  This will be the columnname in your database for the new field.
$field4->columntype = 'INT(20)';
$field4->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field4->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_VANLINECONTACTS_EMAIL1';
$field5->name = 'vcontacts_email1';
$field5->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field5->column = 'vcontacts_email1';   //  This will be the columnname in your database for the new field.
$field5->columntype = 'VARCHAR(100)';
$field5->uitype = 13; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field5->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_VANLINECONTACTS_FAX';
$field6->name = 'vcontacts_fax';
$field6->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'vcontacts_fax';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'INT(20)';
$field6->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field6);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_VANLINECONTACTS_VANLINES';
$field9->name = 'vcontacts_vanlines';
$field9->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'vcontacts_vanlines';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'VARCHAR(100)';
$field9->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field9);
$field9->setRelatedModules(array('Vanlines'));


$field10 = new Vtiger_Field();
$field10->label = 'LBL_VANLINECONTACTS_SEMAIL';
$field10->name = 'vcontacts_semail';
$field10->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'vcontacts_semail';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(100)';
$field10->uitype = 13; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field10);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_VANLINECONTACTS_TITLE';
$field11->name = 'vcontacts_title';
$field11->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'vcontacts_title';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'VARCHAR(100)';
$field11->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field11);

$field12 = new Vtiger_Field();
$field12->label = 'LBL_VANLINECONTACTS_DEPARTMENT';
$field12->name = 'vcontacts_dept';
$field12->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field12->column = 'vcontacts_dept';   //  This will be the columnname in your database for the new field.
$field12->columntype = 'VARCHAR(100)';
$field12->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field12->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field12);
/*
$field13 = new Vtiger_Field();
$field13->label = 'LBL_VANLINECONTACTS_FNAME';
$field13->name = 'vcontacts_fname';
$field13->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field13->column = 'vcontacts_fname';   //  This will be the columnname in your database for the new field.
$field13->columntype = 'VARCHAR(100)';
$field13->uitype = 2; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field13->typeofdata = 'V~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block1->addField($field13);

$field28 = new Vtiger_Field();
$field28->label = 'Assigned To';
$field28->name = 'assigned_user_id';
$field28->table = 'vtiger_crmentity';
$field28->column = 'smownerid';
$field28->uitype = 53;
$field28->typeofdata = 'V~M';

$block1->addField($field28);

$field29 = new Vtiger_Field();
$field29->label = 'Created Time';
$field29->name = 'CreatedTime';
$field29->table = 'vtiger_crmentity';
$field29->column = 'createdtime';
$field29->uitype = 70;
$field29->typeofdata = 'T~O';
$field29->displaytype = 2;

$block1->addField($field29);

$field30 = new Vtiger_Field();
$field30->label = 'Modified Time';
$field30->name = 'ModifiedTime';
$field30->table = 'vtiger_crmentity';
$field30->column = 'modifiedtime';
$field30->uitype = 70;
$field30->typeofdata = 'T~O';
$field30->displaytype = 2;

$block1->addField($field30);*/

$block1->save($module);


$field15 = new Vtiger_Field();
$field15->label = 'LBL_VANLINECONTACTS_ADDRESS1';
$field15->name = 'vcontacts_address1';
$field15->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field15->column = 'vcontacts_address1';   //  This will be the columnname in your database for the new field.
$field15->columntype = 'VARCHAR(100)';
$field15->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field15->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field15);

$field16 = new Vtiger_Field();
$field16->label = 'LBL_VANLINECONTACTS_ADDRESS2';
$field16->name = 'vcontacts_address2';
$field16->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field16->column = 'vcontacts_address2';   //  This will be the columnname in your database for the new field.
$field16->columntype = 'VARCHAR(100)';
$field16->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field16->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field16);
 
$field17 = new Vtiger_Field();
$field17->label = 'LBL_VANLINECONTACTS_CITY';
$field17->name = 'vcontacts_city';
$field17->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field17->column = 'vcontacts_city';   //  This will be the columnname in your database for the new field.
$field17->columntype = 'VARCHAR(100)';
$field17->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field17->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field17);

$field18 = new Vtiger_Field();
$field18->label = 'LBL_VANLINECONTACTS_STATE';
$field18->name = 'vcontacts_state';
$field18->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field18->column = 'vcontacts_state';   //  This will be the columnname in your database for the new field.
$field18->columntype = 'VARCHAR(100)';
$field18->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field18->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field18);

$field19 = new Vtiger_Field();
$field19->label = 'LBL_VANLINECONTACTS_ZIP';
$field19->name = 'vcontacts_zip';
$field19->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field19->column = 'vcontacts_zip';   //  This will be the columnname in your database for the new field.
$field19->columntype = 'VARCHAR(100)';
$field19->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field19->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field19);

$field20 = new Vtiger_Field();
$field20->label = 'LBL_VANLINECONTACTS_COUNTRY';
$field20->name = 'vcontacts_country';
$field20->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field20->column = 'vcontacts_country';   //  This will be the columnname in your database for the new field.
$field20->columntype = 'VARCHAR(100)';
$field20->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field20->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field20);

$field21 = new Vtiger_Field();
$field21->label = 'LBL_VANLINECONTACTS_SADDRESS1';
$field21->name = 'vcontacts_saddress1';
$field21->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field21->column = 'vcontacts_saddress1';   //  This will be the columnname in your database for the new field.
$field21->columntype = 'VARCHAR(100)';
$field21->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field21->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field21);


$field22 = new Vtiger_Field();
$field22->label = 'LBL_VANLINECONTACTS_SADDRESS2';
$field22->name = 'vcontacts_saddress2';
$field22->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field22->column = 'vcontacts_saddress2';   //  This will be the columnname in your database for the new field.
$field22->columntype = 'VARCHAR(100)';
$field22->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field22->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


$block3->addField($field22);
 

$field23 = new Vtiger_Field();
$field23->label = 'LBL_VANLINECONTACTS_SCITY';
$field23->name = 'vcontacts_scity';
$field23->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field23->column = 'vcontacts_scity';   //  This will be the columnname in your database for the new field.
$field23->columntype = 'VARCHAR(100)';
$field23->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field23->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field23);

$field24 = new Vtiger_Field();
$field24->label = 'LBL_VANLINECONTACTS_SSTATE';
$field24->name = 'vcontacts_sstate';
$field24->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field24->column = 'vcontacts_sstate';   //  This will be the columnname in your database for the new field.
$field24->columntype = 'VARCHAR(100)';
$field24->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field24->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field24);

$field25 = new Vtiger_Field();
$field25->label = 'LBL_VANLINECONTACTS_SZIP';
$field25->name = 'vcontacts_szip';
$field25->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field25->column = 'vcontacts_szip';   //  This will be the columnname in your database for the new field.
$field25->columntype = 'VARCHAR(100)';
$field25->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field25->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field25);

$field26 = new Vtiger_Field();
$field26->label = 'LBL_VANLINECONTACTS_SCOUNTRY';
$field26->name = 'vcontacts_scountry';
$field26->table = 'vtiger_vanlinecontacts';  // This is the tablename from your database that the new field will be added to.
$field26->column = 'vcontacts_scountry';   //  This will be the columnname in your database for the new field.
$field26->columntype = 'VARCHAR(100)';
$field26->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field26->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block3->addField($field26);

$block3->save($module);



$field27 = new Vtiger_Field(); // needs to bechanged not saving data
$field27->label = 'LBL_VANLINECONTACTS_DESCRIPTION';
$field27->name = 'description';
$field27->table = 'vtiger_crmentity';  // This is the tablename from your database that the new field will be added to.
$field27->column = 'description';   //  This will be the columnname in your database for the new field.
$field27->columntype = 'VARCHAR(100)';
$field27->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field27->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$block4->addField($field27);

$block4->save($module);
/*
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$module->addFilter($filter1);

$filter1->addField($field13)->addField($field1, 1)->addField($field11, 2)->addField($field5, 3)->addField($field2, 4);

$module->setDefaultSharing();

$module->initWebservice();
*/

//START Add navigation link in module
$module = Vtiger_Module::getInstance('Vanlines');
$module->setRelatedList(Vtiger_Module::getInstance('VanlineContacts'), 'Vanline Contacts', array('ADD', 'SELECT'), 'get_dependents_list');
//END Add navigation link in module
;
