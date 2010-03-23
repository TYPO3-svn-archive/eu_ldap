<?php

class tx_euldap_import extends tx_scheduler_Task {
	
	public function execute() {
		
	}
	
	public function getAdditionalInformation() {
        return 'Import configuration: '.$this->importID;
    }
	
}

?>