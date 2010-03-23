<?php

class tx_euldap_import_addfields implements tx_scheduler_AdditionalFieldProvider {
    public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {
    
        if (empty($taskInfo['importID'])) {
            if($parentObject->CMD == 'edit') {
                $taskInfo['importID'] = $task->importID;
            } else {
                $taskInfo['importID'] = '';
            }
        }
        
        // Write the code for the field
        $fieldID = 'importID';
        $fieldCode = '<input type="text" name="tx_scheduler[importID]" id="' . $fieldID . '" value="' . $taskInfo['importID'] . '" size="4" />';
        $additionalFields = array();
        $additionalFields[$fieldID] = array(
            'code'     => $fieldCode,
            'label'    => 'Import configuration record ID'
        );

        return $additionalFields;
    }

    public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
        $submittedData['importID'] = intval(trim($submittedData['importID']));
        return true;
    }

    public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
        $task->importID = $submittedData['importID'];
    }
}
?>