<?php
if (!checkIsWindfallActive()) {
    return;
}
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: ".__FILE__."<br />\n\e[0m";

        return;
    }
}

print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$moduleName = 'WFWarehouseCond';
$moduleInstance = Vtiger_Module::getInstance($moduleName);
if(!$moduleInstance){
    return;
}

$picklistFields = ['wfwarehousecond_location', 'wfwarehousecond_cond'];


$locArray = [
    'UniGroup' => [
        '1',
        '1s',
        '2',
        '2s',
        '3',
        '3s',
        '4',
        '4s',
        '5',
        '5s',
        '6',
        '6s',
        '7',
        '7s',
        '8',
        '8s',
        '9',
        '9s',
        '10',
        '10s',
        '11',
        '11s',
        '12',
        '12s',
        '13',
        '13s',
        '14',
        '14s',
        '15',
        '15s',
        '16',
        '16s',
        '17',
        '17s',
        '18',
        '18s',
        '19',
        '19s',
    ],
    'Sirva' => [
        '1',
        '1s',
        '2',
        '2s',
        '3',
        '3s',
        '4',
        '4s',
        '5',
        '5s',
        '6',
        '6s',
        '7',
        '7s',
        '8',
        '8s',
        '9',
        '9s',
        '10',
        '10s',
        '11',
        '11s',
        '12',
        '12s',
        '13',
        '13s',
        '14',
        '14s',
        '15',
        '15s',
        '16',
        '16s',
        '17',
        '17s',
        '18',
        '18s',
        '19',
        '19s',
    ],
    'Base' => [
        '1',
        '1s',
        '2',
        '2s',
        '3',
        '3s',
        '4',
        '4s',
        '5',
        '5s',
        '6',
        '6s',
        '7',
        '7s',
        '8',
        '8s',
        '9',
        '9s',
        '10',
        '10s',
        '11',
        '11s',
        '12',
        '12s',
        '13',
        '13s',
        '14',
        '14s',
        '15',
        '15s',
        '16',
        '16s',
        '17',
        '17s',
        '18',
        '18s',
        '19',
        '19s',
    ],
    'Atlas STG' => [
        'DD0001',
        'DD0002',
        'DD0011',
        'DD0003',
        'DD0004',
        'DD0009',
        'DD0007',
        'DD0005',
        'DD0008',
        'DD0010',
        'DD0006',
        'DL0001',
        'DL0050',
        'DL0054',
        'DL0002',
        'DL0003',
        'DL0004',
        'DL0075',
        'DL0073',
        'DL0036',
        'DL0060',
        'DL0013',
        'DL0064',
        'DL0062',
        'DL0063',
        'DL0015',
        'DL0065',
        'DL0055',
        'DL0006',
        'DL0071',
        'DL0025',
        'DL0041',
        'DL0061',
        'DL0052',
        'DL0069',
        'DL0058',
        'DL0078',
        'DL0074',
        'DL0057',
        'DL0007',
        'DL0059',
        'DL0008',
        'DL0009',
        'DL0070',
        'DL0072',
        'DL0056',
        'DL0076',
        'DL0016',
        'DL0053',
        'DL0029',
        'DL0049',
        'DL0017',
        'DL0068',
    ],
];

$condArray = [
    'UniGroup' => [
        'BE',
        'BR',
        'BU',
        'CH',
        'CR',
        'CU',
        'D',
        'F',
        'G',
        'L',
        'M',
        'MCU',
        'MI',
        'MO',
        'PE',
        'R',
        'RU',
        'SC',
        'SH',
        'SO',
        'T',
        'W',
        'WT',
        'Z',
    ],
    'Sirva' => [
        'BE',
        'BR',
        'BU',
        'CH',
        'CR',
        'D',
        'F',
        'G',
        'L',
        'M',
        'MI',
        'MO',
        'P',
        'PS',
        'R',
        'RU',
        'S',
        'SC',
        'SH',
        'SO',
        'ST',
        'T',
        'W',
        'WM',
        'Z',
    ],
    'Base' => [
        'BE',
        'BR',
        'BU',
        'CH',
        'CR',
        'D',
        'F',
        'G',
        'L',
        'M',
        'MI',
        'MO',
        'P',
        'R',
        'RU',
        'S',
        'SC',
        'SH',
        'SO',
        'ST',
        'T',
        'W',
        'Z',
    ],
    'Atlas STG' => [
        'DN000W',
        'DN00BE',
        'DN00BR',
        'DN00BU',
        'DN00CV',
        'DN00CH',
        'DN000Z',
        'DN00CR',
        'DN00CT',
        'DN0032',
        'DN000D',
        'DN0DST',
        'DN000F',
        'DN0FRA',
        'DN000G',
        'DN0033',
        'DN0031',
        'DN000L',
        'DN000M',
        'DN0034',
        'DN00MI',
        'DN0MSG',
        'DN00MH',
        'DN00MO',
        'DN0035',
        'DN0028',
        'DN00PE',
        'DN000P',
        'DN0030',
        'DN000R',
        'DN00RU',
        'DN00SC',
        'DN00SH',
        'DN00SO',
        'DN00SP',
        'DN00ST',
        'DN0STR',
        'DN000T',
        'DN0UPH',
        'DN00WA',
        'DN00WT',
        'DN00WS',
    ]
];




foreach ($picklistFields as $picklistField) {
    $field = Vtiger_Field::getInstance($picklistField, $moduleInstance);
    if($field){
        $arrayToUse = $picklistField == 'wfwarehousecond_location' ? $locArray : $condArray;
        foreach($arrayToUse as $vanline => $values){
            $field->setVanlineSpecificPicklistValues($values, $vanline);
        }
    }
}


