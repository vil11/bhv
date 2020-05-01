<?php

require_once 'app/features.php';

$features = new features();


//$features->updateMetadata();
//$features->performQC(['group' => 'acceptance']);
//$features->updateCatalog();
//$features->performQC(['testsuite' => 'full']);

$features->downloadAlbums([
    'http://myzuka.club/Album/300086/Bomb-The-Bass-Into-The-Dragon-1988',
    'http://myzuka.club/Album/787081/Bomb-The-Bass-Unknown-Territory-1991',
    'http://myzuka.club/Album/287040/Clear-1995',
//    '',
//    '',
]);
