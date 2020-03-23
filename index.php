<?php

require_once 'app/features.php';

$features = new features();


//$features->updateMetadata();
//$features->performQC(['group' => 'acceptance']);
//$features->updateCatalog();
//$features->performQC(['testsuite' => 'full']);

$features->downloadAlbums([
    'http://myzuka.club/Album/275415/Can-Saw-Delight-1977',
//    'http://myzuka.club/Album/276252/Can-Out-Of-Reach-1978',
//    'http://myzuka.club/Album/276501/Can-Can-1979',
//    'http://myzuka.club/Album/113801/Can-Flow-Motion-1976',
]);
