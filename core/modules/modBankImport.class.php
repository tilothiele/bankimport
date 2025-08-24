<?php
/* BankImport minimal Module for Dolibarr */

include_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';
require_once DOL_DOCUMENT_ROOT . '/custom/bankimport/core/modules/BankImportHelper.php';

class modBankImport extends DolibarrModules
{
    public $version;
    
    /**
     * Constructor
     */
    function __construct($db)
    {
        global $langs, $conf;
        $this->db = $db;
        
        $this->version = BankImportHelper::getEnv('VERSION', 'unknown');
        
        // Unique ID (custom modules > 100000)
        $this->numero = 104001;
        
        $this->rights_class = 'bankimport';
        
        // Where the module shows up in Setup
        $this->family = "financial";
        $this->name = "BankImport";
        $this->description = "Import von Kontoauszügen";
        $this->version = $version;
        $this->const_name = 'MAIN_MODULE_BANKIMPORT';
        $this->special = 0;
        $this->picto = 'fa-money-bill-transfer'; // pictogram
        $this->editor_name = 'tilo.thiele@hamburg.de';
        
        // Default module options
        $this->module_parts = array();
        $this->dirs = array();
        $this->config_page_url = array();
        $this->depends = array();
        $this->requiredby = array();
        $this->phpmin = array(7, 4);
        $this->langfiles = array("bankimport@bankimport");
                
        // --- Menu definition ---
        $r = 0;
        $this->menu[$r++] = array(
            'fk_menu'   => 'fk_mainmenu=bank',
            'type'      => 'left',
            'titre'     => 'Kontoauszüge importieren',
            'mainmenu'  => 'bank',
            'leftmenu'  => 'bankimport',
            'url'       => '/custom/bankimport/import.php',
            'langs'     => 'bankimport@bankimport',
            'position'  => 100,
            'enabled'   => '1',
            'perms'     => '1',
            'target'    => '',
            'user'      => 0
        );
    }
}
