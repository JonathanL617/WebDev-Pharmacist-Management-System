<?php
class DrugModel {

    public function searchDrug($name) {

        $encoded = urlencode($name);

        // 1. Get RxCUI Info
        $rxcuiUrl = "https://rxnav.nlm.nih.gov/REST/drugs.json?name=$encoded";
        $rxcuiData = json_decode(file_get_contents($rxcuiUrl), true);

        if (empty($rxcuiData['drugGroup']['conceptGroup'])) {
            return ["error" => "Drug not found. Try a different name."];
        }

        // get concept group
        $drugInfo = null;
        $termType = "Unknown";
        foreach ($rxcuiData['drugGroup']['conceptGroup'] as $group) {
            if (isset($group['conceptProperties'][0])) {
                $drugInfo = $group['conceptProperties'][0];
                $termType = $group['tty'] ?? "Unknown";
                break;
            }
        }

        if (!$drugInfo) {
            return ["error" => "Drug not found."];
        }

        $rxcui = $drugInfo['rxcui'];
        $drugName = $drugInfo['name'];

        // clean name
        $clean = preg_replace('/\s*\[[^\]]*\]/', '', $drugName);
        $clean = preg_replace('/\s*\{[^\}]*\}/', '', $clean);
        $clean = trim($clean);

        if ($clean === "") $clean = $drugName;

        // properties
        $doseForm = "Not specified";
        $strength = "Not specified";

        $propUrl = "https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/properties.json";
        $propData = json_decode(@file_get_contents($propUrl), true);

        if (!empty($propData['properties'])) {
            $p = $propData['properties'];
            $doseForm = $p['dosageForm'] ?? "Not specified";
            $strength = $p['strength'] ?? "Not specified";
        }

        // ingredients + pack components
        $ingredients = [];
        $packComponents = [];

        $allRelUrl = "https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/allrelated.json";
        $allRelData = json_decode(@file_get_contents($allRelUrl), true);

        if (!empty($allRelData['allRelatedGroup']['conceptGroup'])) {
            foreach ($allRelData['allRelatedGroup']['conceptGroup'] as $group) {
                $tty = $group['tty'] ?? "";

                // Single Drug Ingredients
                if ($tty === "IN") {
                    foreach ($group['conceptProperties'] as $i) {
                        $ingredients[] = $i['name'];
                    }
                }

                // Pack components
                if ($tty === "SCD") {
                    foreach ($group['conceptProperties'] as $c) {

                        $compName = $c['name'];
                        $compRxcui = $c['rxcui'];

                        // extract quantity
                        $quantity = "";
                        if (preg_match('/^(\d+)\s/', $compName, $m)) {
                            $quantity = $m[1];
                            $compName = preg_replace('/^\d+\s+/', '', $compName);
                        }

                        // clean component name
                        $cleanComp = preg_replace('/\s*\[[^\]]+\]/', '', $compName);
                        $cleanComp = preg_replace('/\s*\{[^\}]+\}/', '', $cleanComp);
                        $cleanComp = trim($cleanComp);

                        if ($cleanComp === "") $cleanComp = $compName;

                        // get ingredients for each component
                        $compIng = [];
                        $subUrl = "https://rxnav.nlm.nih.gov/REST/rxcui/$compRxcui/allrelated.json";
                        $subData = json_decode(@file_get_contents($subUrl), true);

                        if (!empty($subData['allRelatedGroup']['conceptGroup'])) {
                            foreach ($subData['allRelatedGroup']['conceptGroup'] as $g2) {
                                if (($g2['tty'] ?? "") === "IN") {
                                    foreach ($g2['conceptProperties'] as $ing) {
                                        $compIng[] = $ing['name'];
                                    }
                                }
                            }
                        }

                        $packComponents[] = [
                            "name" => $cleanComp,
                            "quantity" => $quantity,
                            "ingredients" => $compIng
                        ];
                    }
                }
            }
        }

        // brand names
        $brands = [];
        $brandUrl = "https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/related.json?tty=BN";
        $brandData = json_decode(@file_get_contents($brandUrl), true);

        if (!empty($brandData['relatedGroup']['conceptGroup'])) {
            foreach ($brandData['relatedGroup']['conceptGroup'] as $bGroup) {
                foreach ($bGroup['conceptProperties'] as $b) {
                    $brands[] = $b['name'];
                }
            }
        }

        // remove duplicates
        $ingredients = array_values(array_unique($ingredients));
        $brands = array_values(array_unique($brands));

        return [
            "name" => $clean,
            "rxcui" => $rxcui,
            "termType" => $termType,
            "doseForm" => $doseForm,
            "strength" => $strength,
            "ingredients" => $ingredients,
            "packComponents" => $packComponents,
            "brands" => $brands,
            "isPack" => in_array($termType, ["BPCK", "GPCK"])
        ];
    }
}
?>
