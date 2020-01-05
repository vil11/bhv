<?php

require_once 'app/features.php';

$features = new features();

$features->updateMetadata(true);
$features->updateCatalog();
//$features->performQC();
