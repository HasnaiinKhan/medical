<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIMedicineController extends Controller
{
    private const CATEGORY_MAP = [
        'ANALGESIC'=>'fever-pain','ANTIPYRETIC'=>'fever-pain','NSAID'=>'fever-pain',
        'PAIN'=>'fever-pain','PARACETAMOL'=>'fever-pain','IBUPROFEN'=>'fever-pain',
        'VITAMIN'=>'vitamins','SUPPLEMENT'=>'vitamins','MINERAL'=>'vitamins',
        'NUTRACEUTICAL'=>'vitamins','PROTEIN'=>'vitamins','OMEGA'=>'vitamins',
        'ANTACID'=>'digestive','PROTON'=>'digestive','GASTR'=>'digestive',
        'PROBIOTIC'=>'digestive','LAXATIVE'=>'digestive','ANTIDIARRH'=>'digestive',
        'DIABETES'=>'diabetes','ANTIDIABETIC'=>'diabetes','GLUCOSE'=>'diabetes','INSULIN'=>'diabetes',
        'CARDIAC'=>'heart-bp','CARDIOVASCULAR'=>'heart-bp','ANTIHYPERTENSIVE'=>'heart-bp','STATIN'=>'heart-bp',
        'DERMA'=>'skin','SKIN'=>'skin','ANTIFUNGAL'=>'skin','ACNE'=>'skin',
        'FACE WASH'=>'skin','MOISTURIZ'=>'skin','SUNSCREEN'=>'skin','LOTION'=>'skin',
        'ANTI-ACNE'=>'skin','SCRUB'=>'skin',
        'ANTIALLERGIC'=>'cold-allergy','ANTIHISTAMINE'=>'cold-allergy',
        'ALLERG'=>'cold-allergy','COLD'=>'cold-allergy','COUGH'=>'cold-allergy',
        'RESPIRATORY'=>'cold-allergy','SINUS'=>'cold-allergy','NASAL'=>'cold-allergy',
        'OPHTHALM'=>'eye-ear','EYE DROP'=>'eye-ear','EAR DROP'=>'eye-ear',
        'BONE'=>'bone-joint','JOINT'=>'bone-joint','ARTHRIT'=>'bone-joint',
        'OSTEO'=>'bone-joint','CALCIUM'=>'bone-joint',
        'IMMUNE'=>'immunity','IMMUNITY'=>'immunity','AYURVEDIC'=>'immunity',
        'HERBAL'=>'immunity','ASHWAGANDHA'=>'immunity',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // ROUTE 1: Search — returns list of matching products
    // ─────────────────────────────────────────────────────────────────────────
    public function generate(Request $request): JsonResponse
    {
        $request->validate(['name' => ['required', 'string', 'min:2', 'max:200']]);
        $name = trim($request->input('name'));

        // ── PharmEasy search ──────────────────────────────────────────────────
        try {
            $results = $this->searchPharmEasy($name);
            if (! empty($results)) {
                Log::info("MediBot search: PharmEasy ✓ [{$name}] → " . count($results) . " results");
                return response()->json(['results' => $results, 'source' => 'pharmeasy']);
            }
        } catch (\Throwable $e) {
            Log::warning("MediBot: PharmEasy search failed [{$name}]: " . $e->getMessage());
        }

        // ── AI fallback (Gemini → Groq) generates a single result ────────────
        $geminiKey = config('services.gemini.key');
        if ($geminiKey) {
            try {
                $data = $this->fromGemini($name, $geminiKey);
                if ($data) {
                    Log::info("MediBot: Gemini ✓ [{$name}]");
                    return response()->json(['results' => [$data], 'source' => 'gemini']);
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot: Gemini failed [{$name}]: " . $e->getMessage());
            }
        }

        $groqKey = config('services.groq.key');
        if ($groqKey) {
            try {
                $data = $this->fromGroq($name, $groqKey);
                if ($data) {
                    Log::info("MediBot: Groq ✓ [{$name}]");
                    return response()->json(['results' => [$data], 'source' => 'groq']);
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot: Groq failed [{$name}]: " . $e->getMessage());
            }
        }

        return response()->json([
            'error' => "No results found for \"{$name}\". Try a different name like \"Dolo 650\", \"Himalaya face wash\", or \"Pampers diaper\"."
        ], 404);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ROUTE 2: Fetch full detail (with description) for a specific product slug
    // ─────────────────────────────────────────────────────────────────────────
    public function detail(Request $request): JsonResponse
    {
        $request->validate(['slug' => ['required', 'string', 'max:300']]);
        $slug = trim($request->input('slug'), '/');

        try {
            $data = $this->fetchProductDetail($slug);
            if ($data) {
                return response()->json(['data' => $data]);
            }
            return response()->json(['error' => 'Could not load product details.'], 502);
        } catch (\Throwable $e) {
            Log::error("MediBot detail failed [{$slug}]: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 503);
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PharmEasy — search, return ALL results (up to 10)
    // ═════════════════════════════════════════════════════════════════════════
    private function searchPharmEasy(string $name): array
    {
        $html = $this->httpGet(
            'https://pharmeasy.in/search/all?name=' . urlencode($name),
            'https://pharmeasy.in/'
        );
        if (! $html) return [];

        if (! preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) return [];

        $nextData = json_decode($m[1], true);
        if (json_last_error() !== JSON_ERROR_NONE) return [];

        $raw = $nextData['props']['pageProps']['searchResults'] ?? [];
        if (empty($raw)) return [];

        $results = [];
        foreach (array_slice($raw, 0, 10) as $p) {
            if (empty($p['name'])) continue;

            // Best image
            $imageUrl = '';
            foreach ($p['damImages'] ?? [] as $img) {
                if (($img['face'] ?? '') === 'front') { $imageUrl = $img['url'] ?? ''; break; }
            }
            if (! $imageUrl) $imageUrl = $p['damImages'][0]['url'] ?? '';

            // Dosage form
            $pf = strtoupper($p['packform'] ?? '');
            $dosageForm = match(true) {
                str_contains($pf, 'STRIP')  => 'Tablet',
                str_contains($pf, 'BOTTLE') => 'Bottle',
                str_contains($pf, 'TUBE')   => 'Tube',
                str_contains($pf, 'VIAL')   => 'Injection',
                str_contains($pf, 'PACKET') => 'Pack',
                str_contains($pf, 'BOX')    => 'Box',
                str_contains($pf, 'SACHET') => 'Sachet',
                default                      => $this->titleCase($pf) ?: 'Unit',
            };

            $manufacturer = ! empty($p['manufacturer'])
                ? $this->titleCase(strtolower($p['manufacturer']))
                : null;

            $mrp   = (float) ($p['mrpDecimal']      ?? 0);
            $price = (float) ($p['salePriceDecimal'] ?? 0);

            $haystack = strtoupper(
                ($p['name'] ?? '') . ' ' .
                ($p['moleculeName'] ?? '') . ' ' .
                ($p['manufacturer'] ?? '') . ' ' . $pf
            );

            $results[] = [
                'slug'                  => $p['slug'] ?? null,
                'name'                  => $p['name'],
                'manufacturer'          => $manufacturer,
                'category'              => $this->guessCategory($haystack),
                'description'           => null, // fetched on demand via /detail
                'composition'           => $p['moleculeName'] ?: null,
                'dosage_form'           => $dosageForm,
                'uses'                  => [],
                'prescription_required' => (bool) ($p['isRxRequired'] ?? false),
                'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2)   : null,
                'price_suggestion'      => $price > 0 ? round($price, 2) : null,
                'image_url'             => $imageUrl ?: null,
                'source_url'            => $p['slug']
                    ? "https://pharmeasy.in/online-medicine-order/{$p['slug']}"
                    : null,
            ];
        }

        return $results;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PharmEasy — fetch full product page and extract description + uses
    // ═════════════════════════════════════════════════════════════════════════
    private function fetchProductDetail(string $slug): ?array
    {
        $url  = "https://pharmeasy.in/online-medicine-order/{$slug}";
        $page = $this->httpGet($url, 'https://pharmeasy.in/');
        if (! $page) return null;

        if (! preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $page, $m)) return null;

        $nextData = json_decode($m[1], true);
        if (json_last_error() !== JSON_ERROR_NONE) return null;

        $pd = $nextData['props']['pageProps']['productDetails'] ?? null;
        if (! $pd) return null;

        // ── Description from productSpecifications ────────────────────────────
        $description = '';
        $uses        = [];
        $specs = $pd['productSpecifications'] ?? [];
        foreach ($specs as $spec) {
            if (($spec['identifier'] ?? '') === 'description') {
                $html = $spec['value'] ?? $spec['valueWithImageUrl'] ?? '';

                // Extract plain description paragraph (first <p> under Description heading)
                if (preg_match('/<h2[^>]*>.*?Description.*?<\/h2>.*?<p[^>]*>(.*?)<\/p>/si', $html, $dm)) {
                    $description = trim(strip_tags($dm[1]));
                }

                // Extract uses bullets
                if (preg_match('/<h2[^>]*>.*?Uses.*?<\/h2>(.*?)<\/div>/si', $html, $um)) {
                    preg_match_all('/<li>(.*?)<\/li>/si', $um[1], $li);
                    foreach ($li[1] as $item) {
                        $t = trim(strip_tags($item));
                        if ($t) $uses[] = $t;
                        if (count($uses) >= 6) break;
                    }
                }

                // Extract benefits bullets as fallback for uses
                if (empty($uses) && preg_match('/<h2[^>]*>.*?Benefits.*?<\/h2>(.*?)<\/div>/si', $html, $bm)) {
                    preg_match_all('/<li>(.*?)<\/li>/si', $bm[1], $bli);
                    foreach ($bli[1] as $item) {
                        $t = trim(strip_tags($item));
                        if ($t) $uses[] = $t;
                        if (count($uses) >= 6) break;
                    }
                }

                break;
            }
        }

        // ── Full manufacturer name from feature-and-details ───────────────────
        $manufacturer = null;
        foreach ($specs as $spec) {
            if (($spec['identifier'] ?? '') === 'feature-and-details') {
                foreach ($spec['tableData'] ?? [] as $row) {
                    if (str_contains(strtolower($row['key'] ?? ''), 'manufacturer')) {
                        $manufacturer = trim(strip_tags($row['value'] ?? ''));
                        break 2;
                    }
                }
            }
        }
        if (! $manufacturer && ! empty($pd['manufacturer'])) {
            $manufacturer = $this->titleCase(strtolower($pd['manufacturer']));
        }

        // ── Other fields ──────────────────────────────────────────────────────
        $images = $pd['damImages'] ?? [];
        $imageUrl = '';
        foreach ($images as $img) {
            if (($img['face'] ?? '') === 'front') { $imageUrl = $img['url'] ?? ''; break; }
        }
        if (! $imageUrl) $imageUrl = $images[0]['url'] ?? '';

        $composition = implode(', ', array_column($pd['compositions'] ?? [], 'name'))
            ?: ($pd['molecule'] ?? null);

        $pf = strtoupper($pd['packform'] ?? '');
        $dosageForm = ! empty($pd['dosageForm'])
            ? $this->titleCase($pd['dosageForm'])
            : match(true) {
                str_contains($pf, 'STRIP')  => 'Tablet',
                str_contains($pf, 'BOTTLE') => 'Bottle',
                str_contains($pf, 'TUBE')   => 'Tube',
                str_contains($pf, 'VIAL')   => 'Injection',
                default                      => $this->titleCase($pf) ?: 'Unit',
            };

        $mrp   = (float) ($pd['costPrice']  ?? 0);
        $price = (float) ($pd['salePrice']  ?? 0);

        $therapy  = strtoupper($pd['therapy'] ?? $pd['therapyNames'] ?? '');
        $haystack = strtoupper(($pd['name'] ?? '') . ' ' . $therapy . ' ' . ($composition ?? '') . ' ' . $pf);

        return [
            'slug'                  => $pd['slug'] ?? $slug,
            'name'                  => $pd['name'] ?? '',
            'manufacturer'          => $manufacturer,
            'category'              => $this->guessCategory($haystack),
            'description'           => $description ?: null,
            'composition'           => $composition ?: null,
            'dosage_form'           => $dosageForm,
            'uses'                  => $uses,
            'prescription_required' => (bool) ($pd['isRxRequired'] ?? false),
            'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2)   : null,
            'price_suggestion'      => $price > 0 ? round($price, 2) : null,
            'image_url'             => $imageUrl ?: null,
            'source_url'            => "https://pharmeasy.in/online-medicine-order/{$slug}",
        ];
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Gemini 2.5 Flash
    // ═════════════════════════════════════════════════════════════════════════
    private function fromGemini(string $name, string $apiKey): ?array
    {
        $model = config('services.gemini.model', 'models/gemini-2.5-flash');
        $url   = "https://generativelanguage.googleapis.com/v1beta/{$model}:generateContent?key={$apiKey}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'contents'         => [['parts' => [['text' => $this->aiPrompt($name)]]]],
                'generationConfig' => ['temperature' => 0.1, 'maxOutputTokens' => 800],
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 25, CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200) { Log::warning("Gemini HTTP {$status}"); return null; }
        $d = json_decode($body, true);
        return $this->parseAiJson($d['candidates'][0]['content']['parts'][0]['text'] ?? '');
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Groq
    // ═════════════════════════════════════════════════════════════════════════
    private function fromGroq(string $name, string $apiKey): ?array
    {
        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'model'       => config('services.groq.model', 'llama-3.1-8b-instant'),
                'messages'    => [
                    ['role' => 'system', 'content' => 'Return ONLY valid JSON, no markdown.'],
                    ['role' => 'user',   'content' => $this->aiPrompt($name)],
                ],
                'temperature' => 0.1, 'max_tokens' => 800,
            ]),
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $apiKey, 'Content-Type: application/json'],
            CURLOPT_TIMEOUT => 25, CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200) { Log::warning("Groq HTTP {$status}"); return null; }
        $d = json_decode($body, true);
        return $this->parseAiJson($d['choices'][0]['message']['content'] ?? '');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function aiPrompt(string $name): string
    {
        return <<<PROMPT
You are an expert product data assistant for an Indian online medical and healthcare store (sells medicines, baby care, face wash, diapers, supplements, devices, cosmetics — everything a physical pharmacy sells).

Generate structured product data for: "{$name}"

Return ONLY valid JSON (no markdown, no code fences):
{
  "slug": null,
  "name": "Full product name with variant/size",
  "manufacturer": "Brand or manufacturer name",
  "category": "One of: fever-pain|vitamins|digestive|diabetes|heart-bp|skin|cold-allergy|eye-ear|bone-joint|immunity",
  "description": "3-4 sentence professional product description",
  "composition": "Key ingredients or null for non-medicines",
  "dosage_form": "Tablet|Capsule|Syrup|Cream|Gel|Drops|Pack|Bottle|Box|Tube|Sachet|Unit",
  "uses": ["use 1", "use 2", "use 3", "use 4"],
  "prescription_required": false,
  "mrp_suggestion": 99.00,
  "price_suggestion": 89.00,
  "image_url": null,
  "source_url": null
}
PROMPT;
    }

    private function parseAiJson(string $content): ?array
    {
        $content = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
        $content = preg_replace('/\s*```$/', '', $content);
        if (preg_match('/\{.*\}/s', $content, $m)) $content = $m[0];
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data['name'])) return null;
        return $data;
    }

    private function guessCategory(string $haystack): string
    {
        foreach (self::CATEGORY_MAP as $kw => $cat) {
            if (str_contains($haystack, $kw)) return $cat;
        }
        return 'fever-pain';
    }

    private function httpGet(string $url, string $referer = ''): ?string
    {
        $ch = curl_init($url);
        $headers = ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124',
                    'Accept: text/html,*/*', 'Accept-Language: en-IN,en;q=0.9'];
        if ($referer) $headers[] = "Referer: {$referer}";
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20, CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => 'gzip', CURLOPT_HTTPHEADER => $headers,
        ]);
        $body = curl_exec($ch); $s = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        return $s === 200 ? $body : null;
    }

    private function titleCase(string $s): string
    {
        return mb_convert_case(strtolower($s), MB_CASE_TITLE);
    }
}
