<?php
require_once __DIR__ . '/../config/config.php';

class DrugModel
{
    public function searchDrug($name)
    {
        $encoded = urlencode(trim($name));

        // Step 1: Find RxCUI
        $url = "https://rxnav.nlm.nih.gov/REST/drugs.json?name=$encoded";
        $data = $this->getJson($url);
        if (!$data || empty($data['drugGroup']['conceptGroup'])) {
            return ["error" => "Drug not found. Try a different name."];
        }

        $drugInfo = null;
        $termType = 'Unknown';
        foreach ($data['drugGroup']['conceptGroup'] as $group) {
            if (!empty($group['conceptProperties'][0])) {
                $drugInfo = $group['conceptProperties'][0];
                $termType = $group['tty'] ?? 'Unknown';
                break;
            }
        }

        if (!$drugInfo) return ["error" => "Drug information not available."];

        $rxcui = $drugInfo['rxcui'];
        $rawName = $drugInfo['name'];

        // Clean name
        $cleanName = preg_replace(['/[\[\{].*[\]\}]/', '/\s+/'], ['', ' '], $rawName);
        $cleanName = trim($cleanName) ?: $rawName;

        // Properties
        $props = $this->getJson("https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/properties.json");
        $doseForm = $props['properties']['dosageForm'] ?? 'Not specified';
        $strength = $props['properties']['strength'] ?? 'Not specified';

        // Ingredients & Pack Components
        $ingredients = [];
        $packComponents = [];
        $allRelated = $this->getJson("https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/allrelated.json");

        if ($allRelated && !empty($allRelated['allRelatedGroup']['conceptGroup'])) {
            foreach ($allRelated['allRelatedGroup']['conceptGroup'] as $group) {
                $tty = $group['tty'] ?? '';

                if ($tty === 'IN') {
                    foreach ($group['conceptProperties'] ?? [] as $ing) {
                        $ingredients[] = $ing['name'];
                    }
                }

                if ($tty === 'SCD') {
                    foreach ($group['conceptProperties'] ?? [] as $comp) {
                        $name = $comp['name'];
                        $compRxcui = $comp['rxcui'];

                        $quantity = '';
                        if (preg_match('/^(\d+)\s+/', $name, $m)) {
                            $quantity = $m[1];
                            $name = preg_replace('/^\d+\s+/', '', $name);
                        }

                        $cleanCompName = trim(preg_replace('/[\[\{].*[\]\}]/', '', $name)) ?: $name;

                        $compIngredients = [];
                        $sub = $this->getJson("https://rxnav.nlm.nih.gov/REST/rxcui/$compRxcui/allrelated.json");
                        if ($sub) {
                            foreach ($sub['allRelatedGroup']['conceptGroup'] ?? [] as $g) {
                                if (($g['tty'] ?? '') === 'IN') {
                                    foreach ($g['conceptProperties'] ?? [] as $i) {
                                        $compIngredients[] = $i['name'];
                                    }
                                }
                            }
                        }

                        $packComponents[] = [
                            'name' => $cleanCompName,
                            'quantity' => $quantity,
                            'ingredients' => array_unique($compIngredients)
                        ];
                    }
                }
            }
        }

        // Brand names
        $brands = [];
        $brandData = $this->getJson("https://rxnav.nlm.nih.gov/REST/rxcui/$rxcui/related.json?tty=BN");
        if ($brandData && !empty($brandData['relatedGroup']['conceptGroup'])) {
            foreach ($brandData['relatedGroup']['conceptGroup'] as $g) {
                foreach ($g['conceptProperties'] ?? [] as $b) {
                    $brands[] = $b['name'];
                }
            }
        }

        $brands = array_values(array_unique($brands));
        $ingredients = array_values(array_unique($ingredients));

        return [
            "name" => $cleanName,
            "rxcui" => $rxcui,
            "doseForm" => $doseForm,
            "strength" => $strength,
            "ingredients" => $ingredients,
            "brands" => $brands,
            "packComponents" => $packComponents,
            "isPack" => in_array($termType, ['BPCK', 'GPCK']),
            "termType" => $termType
        ];
    }

    private function getJson($url)
    {
        $data = @file_get_contents($url);
        return $data ? json_decode($data, true) : null;
    }
}