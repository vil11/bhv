<?php

/**
 * Update Metadata of all Artist Songs or for concrete Artist Albums only:
 *  - run manually by calling the function directly from "index.php"
 *  - start Artist folder name & Album folder name with an underscore* in order to tag it for updating
 *  - if no Albums are tagged for updating, all Artist Songs will be updated
 *  - you can enable automatic renaming of tagged folders on operation success, by default this option is disabled
 *
 * @param bool $autoRenamingIfSuccess
 * @throws Exception
 */
function updateMetadata(bool $autoRenamingIfSuccess = false)
{
    $bhv = new bhv();
    $result = $bhv->updateMetadata($autoRenamingIfSuccess);

    $result = ($result) ? 'SUCCESS' : 'FAIL!!!';
    echo "\n\n[$result] finished.\n\n";
}
