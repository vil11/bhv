<?php

require_once 'app/features.php';

$features = new features();


//$features->updateMetadata();
//$features->performQC(['group' => 'acceptance']);
//$features->updateMetadata(true);
//$features->updateCatalog();
//$features->performQC(['testsuite' => 'full_integrity']);

//$features->downloadAlbums([
//    '',
//    '',
//]);


// Product Import test scenarios schema

//$items = [null, 'sP', 'cP'];
//
//$resources = ['P'];
//$subResources = ['PO', 'PV'];
//$operations = [null, 'create', 'update'];
//
//$resourceCalls = multiplyInOperations($resources, $operations);
//$subResourceCalls = multiplyInOperations($subResources, $operations);
//
//$formula = '{P:%s} + [R:%s] + [iSR:%s] + [dSR:%s]';
//
//$scenarios = [];
//foreach ($items as $precondition) {
//    foreach ($resourceCalls as $resourceCall) {
//        if (!isResourceApplicable($precondition, $resourceCall)) {
//            continue;
//        }
//
//        foreach ($subResourceCalls as $independentSubResourceCall) {
//            if (!isIndependentSubResourceApplicable($precondition, $independentSubResourceCall)) {
//                continue;
//            }
//
//            foreach ($subResourceCalls as $dependentSubResourceCall) {
//                if (!isDependentSubResourceApplicable($precondition, $independentSubResourceCall, $dependentSubResourceCall)) {
//                    continue;
//                }
//
//                $scenario = sprintf(
//                    $formula,
//                    formScenarioPrecondition($precondition),
//                    formScenarioAction($resourceCall),
//                    formScenarioAction($independentSubResourceCall),
//                    formScenarioAction($dependentSubResourceCall)
//                );
//                $scenarios[] = $scenario;
//            }
//        }
//    }
//}
//$scenarios = array_unique($scenarios);
//
//var_dump($scenarios);
//
//
//function multiplyInOperations(array $points, array $operations): array
//{
//    $calls = [];
//    array_map(function ($operation) use ($points, &$calls) {
//        array_map(function ($point) use ($operation, &$calls) {
//            if (is_null($operation)) {
//                $calls[] = $operation;
//            } else {
//                $calls[] = ['operation' => $operation, 'point' => $point];
//            }
//        }, $points);
//    }, $operations
//    );
//
//    if (count($calls) !== count($points) * count($operations)) {
//        throw new Exception('Invalid combinations qty.');
//    }
//
//    return $calls;
//}
//
//function isResourceApplicable(?string $precondition, ?array $resourceCall): bool
//{
//    if (is_null($precondition) && is_null($resourceCall)) {
//        return false;
//    }
//
//    if (is_null($precondition) && $resourceCall['operation'] === 'update') {
//        return false;
//    }
//
//    if (!is_null($precondition) && !is_null($resourceCall) && $resourceCall['operation'] === 'create') {
//        return false;
//    }
//
//    return true;
//}
//
//function isIndependentSubResourceApplicable(?string $precondition, ?array $independentSubResourceCall): bool
//{
//    if (!is_null($independentSubResourceCall) && $independentSubResourceCall['operation'] === 'update') {
//        return false;
//    }
//
//    return true;
//}
//
//function isDependentSubResourceApplicable(?string $precondition, ?array $independentSubResourceCall, ?array $dependentSubResourceCall): bool
//{
//    if (is_null($dependentSubResourceCall)) {
//        return true;
//    }
//
//    if ($dependentSubResourceCall['point'] === 'PO') {
//        return false;
//    }
//
//    if ($dependentSubResourceCall['point'] === 'PV') {
//        $inPreconditions = !is_null($precondition) && $precondition === 'cP';
//        $inIndependentSubResource = !is_null($independentSubResourceCall) && $independentSubResourceCall['operation'] === 'create' && $independentSubResourceCall['point'] === 'PO';
//        if (!$inPreconditions && !$inIndependentSubResource) {
//            return false;
//        }
//    }
//
//    return true;
//}
//
//function formScenarioPrecondition(?string $model): string
//{
//    return !is_null($model) ? $model : '.';
//}
//
//function formScenarioAction(?array $model): string
//{
//    if (is_null($model)) {
//        return '.';
//    }
//
//    $result = array_values($model);
//    $result = implode('_', $result);
//
//    return $result;
//}
//
//
//
