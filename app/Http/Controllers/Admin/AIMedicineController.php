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
    // ROUTE 1: Search — ALWAYS searches ALL sources simultaneously, returns
    //          only items whose name contains the keyword (exact match filter)
    // ─────────────────────────────────────────────────────────────────────────
    public function generate(Request $request): JsonResponse
    {
        $request->validate(['name' => ['required', 'string', 'min:2', 'max:200']]);
        $name    = trim($request->input('name'));
        $keyword = $this->normaliseSearchText($name); // normalised for word-by-word matching

        $allResults = [];
        $searchLog  = [];

        // ── Search all three sources simultaneously ────────────────────────────
        // PharmEasy
        try {
            $pe = $this->searchPharmEasy($name);
            $pe = $this->filterByKeyword($pe, $keyword);
            $allResults  = array_merge($allResults, $pe);
            $searchLog['PharmEasy'] = count($pe);
            Log::info("MediBot PharmEasy [{$name}] → " . count($pe) . " exact matches");
        } catch (\Throwable $e) {
            $searchLog['PharmEasy'] = 'error';
            Log::warning("MediBot PharmEasy failed [{$name}]: " . $e->getMessage());
        }

        // NetMeds (real Fynd API)
        try {
            $nm = $this->searchNetMeds($name);
            $nm = $this->filterByKeyword($nm, $keyword);
            $allResults  = array_merge($allResults, $nm);
            $searchLog['NetMeds'] = count($nm);
            Log::info("MediBot NetMeds [{$name}] → " . count($nm) . " exact matches");
        } catch (\Throwable $e) {
            $searchLog['NetMeds'] = 'error';
            Log::warning("MediBot NetMeds failed [{$name}]: " . $e->getMessage());
        }

        // Apollo (HTML scraping via __NEXT_DATA__)
        try {
            $ap = $this->searchApolloPharmacy($name);
            $ap = $this->filterByKeyword($ap, $keyword);
            $allResults  = array_merge($allResults, $ap);
            $searchLog['Apollo'] = count($ap);
            Log::info("MediBot Apollo [{$name}] → " . count($ap) . " exact matches");
        } catch (\Throwable $e) {
            $searchLog['Apollo'] = 'error';
            Log::warning("MediBot Apollo failed [{$name}]: " . $e->getMessage());
        }

        // ── Return combined exact matches (up to 25) ──────────────────────────
        if (!empty($allResults)) {
            $allResults = array_slice($allResults, 0, 25);
            Log::info("MediBot combined [{$name}] → " . count($allResults) . " | " . json_encode($searchLog));
            return response()->json(['results' => $allResults, 'source' => 'combined', 'search_log' => $searchLog]);
        }

        // ── AI fallback ────────────────────────────────────────────────────────
        $geminiKey = config('services.gemini.key');
        if ($geminiKey) {
            try {
                $data = $this->fromGemini($name, $geminiKey);
                if ($data) {
                    $data['source_platform'] = 'AI';
                    Log::info("MediBot Gemini fallback [{$name}] | " . json_encode($searchLog));
                    return response()->json(['results' => [$data], 'source' => 'gemini', 'search_log' => $searchLog]);
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot Gemini failed [{$name}]: " . $e->getMessage());
            }
        }

        $groqKey = config('services.groq.key');
        if ($groqKey) {
            try {
                $data = $this->fromGroq($name, $groqKey);
                if ($data) {
                    $data['source_platform'] = 'AI';
                    Log::info("MediBot Groq fallback [{$name}] | " . json_encode($searchLog));
                    return response()->json(['results' => [$data], 'source' => 'groq', 'search_log' => $searchLog]);
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot Groq failed [{$name}]: " . $e->getMessage());
            }
        }

        return response()->json([
            'error'      => "No results found for \"{$name}\". Try a different name.",
            'search_log' => $searchLog,
        ], 404);
    }

    // ── Filter results to exact keyword match.
    //    Strategy: normalise both the query and the product text (collapse
    //    common compound words so "facewash" == "face wash", "eyedrop" ==
    //    "eye drop", etc.), then require EVERY word in the query to appear
    //    somewhere in the product text.  This lets "himalaya facewash" match
    //    "Himalaya Purifying Neem Face Wash 150ml".
    private function filterByKeyword(array $items, string $keyword): array
    {
        $normKeyword = $this->normaliseSearchText($keyword);
        $words       = array_filter(preg_split('/\s+/', $normKeyword));

        if (empty($words)) return $items;

        return array_values(array_filter($items, function (array $item) use ($words): bool {
            $haystack = $this->normaliseSearchText(
                ($item['name']         ?? '') . ' ' .
                ($item['manufacturer'] ?? '') . ' ' .
                ($item['composition']  ?? '') . ' ' .
                ($item['dosage_form']  ?? '')
            );

            foreach ($words as $word) {
                if (!str_contains($haystack, $word)) return false;
            }
            return true;
        }));
    }

    // Normalise text for matching: lowercase, collapse compound pharmacy words,
    // strip punctuation, normalise spaces.
    private function normaliseSearchText(string $text): string
    {
        $text = strtolower($text);

        // Merge common split compounds so both spellings match
        // e.g. "face wash" → "facewash", "eye drop" → "eyedrop"
        $merges = [
            'face wash'     => 'facewash',
            'face  wash'    => 'facewash',
            'eye drop'      => 'eyedrop',
            'eye drops'     => 'eyedrops',
            'ear drop'      => 'eardrop',
            'ear drops'     => 'eardrops',
            'hair oil'      => 'hairoil',
            'hair care'     => 'haircare',
            'body wash'     => 'bodywash',
            'body lotion'   => 'bodylotion',
            'sun screen'    => 'sunscreen',
            'sun cream'     => 'suncream',
            'lip balm'      => 'lipbalm',
            'hand wash'     => 'handwash',
            'hand cream'    => 'handcream',
            'foot cream'    => 'footcream',
            'tooth paste'   => 'toothpaste',
            'tooth brush'   => 'toothbrush',
            'mouth wash'    => 'mouthwash',
            'hand sanitizer'=> 'handsanitizer',
            'hand sanitiser'=> 'handsanitizer',
        ];
        foreach ($merges as $spaced => $merged) {
            $text = str_replace($spaced, $merged, $text);
        }

        // Also do the reverse: if user typed the compound word, expand it so
        // it can match either spelling stored in product names
        // We handle this by replacing compound words with their spaced version too —
        // actually the simpler approach: after merging all spaced → compound,
        // both the query and product text are normalised the same way, so they match.

        // Strip punctuation except spaces and alphanumerics
        $text = preg_replace('/[^\w\s]/u', ' ', $text);

        // Collapse multiple spaces
        $text = preg_replace('/\s+/', ' ', trim($text));

        return $text;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ROUTE 3: Download a remote image, save to public/images/medicines/, return local URL
    // ─────────────────────────────────────────────────────────────────────────
    public function downloadImage(Request $request): JsonResponse
    {
        $request->validate([
            'url'      => ['required', 'url', 'max:2000'],
            'platform' => ['nullable', 'string', 'max:50'],
        ]);

        $remoteUrl = $request->input('url');
        $platform  = strtolower(trim((string) $request->input('platform', '')));

        // ── Security: block private/internal addresses ─────────────────────
        $host = strtolower(parse_url($remoteUrl, PHP_URL_HOST) ?? '');
        $scheme = strtolower(parse_url($remoteUrl, PHP_URL_SCHEME) ?? '');

        if ($scheme !== 'https') {
            return response()->json(['error' => 'Only HTTPS image URLs are allowed.'], 422);
        }

        // Block localhost, private IPs, and obviously non-image hosts
        $blocked = ['localhost', '127.', '192.168.', '10.', '172.16.', '0.0.0.0', '::1'];
        foreach ($blocked as $b) {
            if (str_starts_with($host, $b) || $host === $b) {
                return response()->json(['error' => 'Image host not allowed.'], 422);
            }
        }

        // Must have a real TLD (at least one dot in host)
        if (!str_contains($host, '.')) {
            return response()->json(['error' => 'Invalid image host.'], 422);
        }

        // ── Pick the right referer so CDN hotlink-protection passes ────────
        $refererMap = [
            'pharmeasy'       => 'https://pharmeasy.in/',
            'netmeds'         => 'https://www.netmeds.com/',
            'apollo pharmacy' => 'https://www.apollopharmacy.in/',
            'apollo'          => 'https://www.apollopharmacy.in/',
        ];

        // Platform-based referer (most reliable — frontend passes source_platform)
        $referer = '';
        foreach ($refererMap as $key => $ref) {
            if (str_contains($platform, $key)) {
                $referer = $ref;
                break;
            }
        }

        // Auto-detect from hostname as fallback
        if (empty($referer)) {
            if (str_contains($host, 'pharmeasy'))         $referer = 'https://pharmeasy.in/';
            elseif (str_contains($host, 'netmeds'))        $referer = 'https://www.netmeds.com/';
            elseif (str_contains($host, 'apollo'))         $referer = 'https://www.apollopharmacy.in/';
            elseif (str_contains($host, '1mg'))            $referer = 'https://www.1mg.com/';
            elseif (str_contains($host, 'onemg'))          $referer = 'https://www.1mg.com/';
            elseif (str_contains($host, 'pixelbin'))       $referer = 'https://www.netmeds.com/';  // NetMeds CDN
            elseif (str_contains($host, 'cloudinary'))     $referer = 'https://www.apollopharmacy.in/';
            // Default: no referer (many CDNs don't require one)
        }

        // ── Download the image ──────────────────────────────────────────────
        $imageData = $this->httpGetImage($remoteUrl, $referer);
        if (! $imageData) {
            // Some CDNs need the exact product-page URL as referer — try without referer
            $imageData = $this->httpGetImage($remoteUrl, '');
        }
        if (! $imageData) {
            return response()->json(['error' => 'Could not download image.'], 502);
        }

        // ── Detect extension ────────────────────────────────────────────────
        $ext = 'jpg';
        $urlPath  = parse_url($remoteUrl, PHP_URL_PATH) ?? '';
        $pathInfo = pathinfo($urlPath);
        if (!empty($pathInfo['extension'])) {
            $candidate = strtolower($pathInfo['extension']);
            // strip query-string fragments that may be appended to extension
            $candidate = preg_replace('/[^a-z].*/', '', $candidate);
            if (in_array($candidate, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $ext = $candidate === 'jpeg' ? 'jpg' : $candidate;
            }
        }

        // ── Verify it's actually an image via content-type sniff ───────────
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->buffer($imageData);
        $mimeToExt = [
            'image/jpeg' => 'jpg',  'image/png'  => 'png',
            'image/webp' => 'webp', 'image/gif'  => 'gif',
            'image/avif' => 'avif', 'image/bmp'  => 'jpg',
            'image/tiff' => 'jpg',  'image/svg+xml' => 'jpg', // convert svg fallback
        ];
        if (!isset($mimeToExt[$mime])) {
            // Some servers return application/octet-stream for images — trust extension instead
            $extFallback = $ext ?? 'jpg';
            if (!in_array($extFallback, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
                Log::warning("MediBot downloadImage: unexpected MIME '{$mime}' for {$remoteUrl}");
                return response()->json(['error' => 'URL does not point to a valid image.'], 422);
            }
        } else {
            $ext = $mimeToExt[$mime];
        }

        // avif: browsers support it, but convert to jpg for widest compatibility with <img> tags
        // We just save as-is; modern browsers (Chrome 85+, Firefox 93+) handle avif fine

        // ── Save locally ────────────────────────────────────────────────────
        $dir = public_path('images/medicines');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = 'med_' . uniqid() . '.' . $ext;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        if (file_put_contents($fullPath, $imageData) === false) {
            return response()->json(['error' => 'Could not save image.'], 500);
        }

        $localUrl = asset('images/medicines/' . $filename);
        Log::info("MediBot image downloaded [{$platform}]: {$remoteUrl} → {$localUrl}");

        return response()->json(['url' => $localUrl, 'filename' => $filename]);
    }

    // Dedicated image-download helper with image-specific headers
    private function httpGetImage(string $url, string $referer): ?string
    {
        $ch = curl_init($url);
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124',
            'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            'Accept-Language: en-IN,en;q=0.9',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Sec-Fetch-Dest: image',
            'Sec-Fetch-Mode: no-cors',
            'Sec-Fetch-Site: cross-site',
        ];
        if ($referer) {
            $headers[] = 'Referer: ' . $referer;
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING       => '',          // let curl decompress automatically
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200 || !$body || strlen($body) < 100) return null;
        return $body;
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
    // PharmEasy — search, return results (up to 25)
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
        foreach (array_slice($raw, 0, 25) as $p) {
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
                'source_platform'       => 'PharmEasy',
            ];
        }

        return $results;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // NetMeds — real Fynd platform search API (confirmed working)
    // ═════════════════════════════════════════════════════════════════════════
    private function searchNetMeds(string $name): array
    {
        $ch = curl_init(
            'https://www.netmeds.com/ext/search/application/api/v1.0/products'
            . '?q=' . urlencode($name)
            . '&page_no=1&page_size=25'
        );
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING       => 'gzip',
            CURLOPT_HTTPHEADER     => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124',
                'Accept: application/json',
                'Accept-Language: en-IN,en;q=0.9',
                'Referer: https://www.netmeds.com/',
                'x-application-token: _U-ohI4Iy',   // Fynd app token
                'Origin: https://www.netmeds.com',
            ],
        ]);
        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200 || !$body) return [];

        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) return [];

        $items = $data['items'] ?? [];
        if (!is_array($items) || empty($items)) return [];

        $results = [];
        foreach ($items as $p) {
            $pname = $p['name'] ?? '';
            if (empty($pname)) continue;

            // Image — first media item
            $imageUrl = '';
            foreach ($p['medias'] ?? [] as $media) {
                if (!empty($media['url'])) { $imageUrl = $media['url']; break; }
            }

            // Price
            $mrp   = (float) ($p['price']['marked']['min']    ?? 0);
            $price = (float) ($p['price']['effective']['min'] ?? 0);

            // Manufacturer / brand from attributes
            $attrs        = $p['attributes'] ?? [];
            $manufacturer = $attrs['marketername']  ?? $attrs['brandfilter'] ?? null;
            $composition  = $attrs['genericname']   ?? null;
            $packSize     = trim(($attrs['packsize'] ?? '') . ' ' . ($attrs['packsizeunit'] ?? ''));

            // Full name with pack size if useful
            $fullName = $pname;
            if ($packSize && !str_contains(strtolower($pname), strtolower(trim($packSize)))) {
                $fullName = $pname . ($packSize ? ' ' . $packSize : '');
            }

            $haystack = strtoupper($fullName . ' ' . ($manufacturer ?? '') . ' ' . ($composition ?? ''));

            $results[] = [
                'slug'                  => $p['slug'] ?? null,
                'name'                  => $fullName,
                'manufacturer'          => $manufacturer ? $this->titleCase(strtolower($manufacturer)) : null,
                'category'              => $this->guessCategory($haystack),
                'description'           => null,
                'composition'           => $composition ? $this->titleCase(strtolower($composition)) : null,
                'dosage_form'           => 'Unit',
                'uses'                  => [],
                'prescription_required' => false,
                'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2)   : null,
                'price_suggestion'      => $price > 0 ? round($price, 2) : null,
                'image_url'             => $imageUrl ?: null,
                'source_url'            => $p['slug']
                    ? "https://www.netmeds.com/products/" . $p['slug']
                    : "https://www.netmeds.com",
                'source_platform'       => 'NetMeds',
            ];
        }

        return $results;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Apollo Pharmacy — __NEXT_DATA__ HTML scraping
    // ═════════════════════════════════════════════════════════════════════════
    private function searchApolloPharmacy(string $name): array
    {
        $html = $this->httpGet(
            'https://www.apollopharmacy.in/search/' . urlencode($name),
            'https://www.apollopharmacy.in/'
        );
        if (!$html) return [];

        // Try __NEXT_DATA__ first
        if (preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) {
            $nextData = json_decode($m[1], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Walk the Apollo Next.js data shape for products
                $products = $nextData['props']['pageProps']['searchData']['products'] ?? [];
                if (empty($products)) {
                    $products = $nextData['props']['pageProps']['initialData']['products'] ?? [];
                }
                if (empty($products)) {
                    // Try deep path used on some versions
                    $dehydrated = $nextData['props']['pageProps']['dehydratedState']['queries'] ?? [];
                    foreach ($dehydrated as $q) {
                        $hits = $q['state']['data']['products'] ?? $q['state']['data']['items'] ?? [];
                        if (!empty($hits)) { $products = $hits; break; }
                    }
                }
                if (!empty($products)) {
                    return $this->normaliseApolloProducts($products);
                }
            }
        }

        // Fallback: extract JSON from inline script blocks
        if (preg_match_all('/<script[^>]*>\s*window\.__.*?=\s*(\{.*?\})\s*;?\s*<\/script>/s', $html, $matches)) {
            foreach ($matches[1] as $jsonStr) {
                $d = json_decode($jsonStr, true);
                if (json_last_error() !== JSON_ERROR_NONE) continue;
                $products = $d['products'] ?? $d['items'] ?? $d['searchResults'] ?? [];
                if (!empty($products)) {
                    return $this->normaliseApolloProducts($products);
                }
            }
        }

        return [];
    }

    private function normaliseApolloProducts(array $products): array
    {
        $results = [];
        foreach (array_slice($products, 0, 25) as $p) {
            $pname = $p['productName'] ?? $p['name'] ?? $p['title'] ?? '';
            if (empty($pname)) continue;

            $manufacturer = $p['manufacturerName'] ?? $p['manufacturer'] ?? $p['brand'] ?? null;
            $imageUrl     = $p['productImageURL']  ?? $p['imageUrl']    ?? $p['image']  ?? '';
            $mrp          = (float) ($p['mrp']       ?? $p['price']       ?? 0);
            $price        = (float) ($p['offerPrice'] ?? $p['salePrice']  ?? $p['discountedPrice'] ?? $mrp);
            $composition  = $p['activeIngredients'] ?? $p['composition']  ?? $p['salt'] ?? null;

            $haystack = strtoupper($pname . ' ' . ($manufacturer ?? '') . ' ' . ($composition ?? ''));

            $results[] = [
                'slug'                  => $p['slug'] ?? $p['urlKey'] ?? null,
                'name'                  => $pname,
                'manufacturer'          => $manufacturer ? $this->titleCase(strtolower($manufacturer)) : null,
                'category'              => $this->guessCategory($haystack),
                'description'           => null,
                'composition'           => $composition ? $this->titleCase(strtolower($composition)) : null,
                'dosage_form'           => $p['dosageForm'] ?? $p['form'] ?? 'Unit',
                'uses'                  => [],
                'prescription_required' => (bool) ($p['requiresPrescription'] ?? $p['isRx'] ?? false),
                'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2)   : null,
                'price_suggestion'      => $price > 0 ? round($price, 2) : null,
                'image_url'             => $imageUrl ?: null,
                'source_url'            => isset($p['slug'])
                    ? "https://www.apollopharmacy.in/otc/" . $p['slug']
                    : "https://www.apollopharmacy.in",
                'source_platform'       => 'Apollo Pharmacy',
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
    // ROUTE 4: Generate AI description for a product (used when description is
    //          missing or less than 100 words)
    // ─────────────────────────────────────────────────────────────────────────
    public function generateDescription(Request $request): JsonResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:200'],
            'manufacturer' => ['nullable', 'string', 'max:200'],
            'composition'  => ['nullable', 'string', 'max:500'],
            'uses'         => ['nullable', 'array'],
            'dosage_form'  => ['nullable', 'string', 'max:100'],
            'category'     => ['nullable', 'string', 'max:100'],
            'existing'     => ['nullable', 'string'], // existing short description to expand
        ]);

        $name         = trim($request->input('name'));
        $manufacturer = trim((string) $request->input('manufacturer', ''));
        $composition  = trim((string) $request->input('composition', ''));
        $uses         = (array)  $request->input('uses', []);
        $dosageForm   = trim((string) $request->input('dosage_form', ''));
        $category     = trim((string) $request->input('category', ''));
        $existing     = trim((string) $request->input('existing', ''));

        $prompt = $this->descriptionPrompt(
            $name, $manufacturer, $composition, $uses, $dosageForm, $category, $existing
        );

        // Try Gemini first
        $geminiKey = config('services.gemini.key');
        if ($geminiKey) {
            try {
                $model = config('services.gemini.model', 'models/gemini-2.5-flash');
                $url   = "https://generativelanguage.googleapis.com/v1beta/{$model}:generateContent?key={$geminiKey}";

                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS     => json_encode([
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.3, 'maxOutputTokens' => 400],
                    ]),
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_TIMEOUT => 20, CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $body   = curl_exec($ch);
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($status === 200) {
                    $d    = json_decode($body, true);
                    $text = trim($d['candidates'][0]['content']['parts'][0]['text'] ?? '');
                    if ($text) {
                        Log::info("MediBot descGen Gemini ✓ [{$name}]");
                        return response()->json(['description' => $text, 'source' => 'gemini']);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot descGen Gemini failed [{$name}]: " . $e->getMessage());
            }
        }

        // Fallback: Groq
        $groqKey = config('services.groq.key');
        if ($groqKey) {
            try {
                $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS     => json_encode([
                        'model'       => config('services.groq.model', 'llama-3.1-8b-instant'),
                        'messages'    => [
                            ['role' => 'system', 'content' => 'You write professional product descriptions. Return ONLY the description text, no extra commentary.'],
                            ['role' => 'user',   'content' => $prompt],
                        ],
                        'temperature' => 0.3, 'max_tokens' => 400,
                    ]),
                    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $groqKey, 'Content-Type: application/json'],
                    CURLOPT_TIMEOUT => 20, CURLOPT_SSL_VERIFYPEER => false,
                ]);
                $body   = curl_exec($ch);
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($status === 200) {
                    $d    = json_decode($body, true);
                    $text = trim($d['choices'][0]['message']['content'] ?? '');
                    if ($text) {
                        Log::info("MediBot descGen Groq ✓ [{$name}]");
                        return response()->json(['description' => $text, 'source' => 'groq']);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot descGen Groq failed [{$name}]: " . $e->getMessage());
            }
        }

        return response()->json(['error' => 'Could not generate description.'], 502);
    }

    private function descriptionPrompt(
        string $name, string $manufacturer, string $composition,
        array $uses, string $dosageForm, string $category, string $existing
    ): string {
        $usesText  = !empty($uses)  ? implode(', ', array_slice($uses, 0, 5)) : '';
        $contextParts = array_filter([
            $manufacturer  ? "Manufactured by {$manufacturer}."      : '',
            $dosageForm    ? "Dosage form: {$dosageForm}."            : '',
            $composition   ? "Active ingredients: {$composition}."   : '',
            $usesText      ? "Uses: {$usesText}."                     : '',
            $existing      ? "Existing short description: {$existing}." : '',
        ]);
        $context = implode(' ', $contextParts);

        return <<<PROMPT
Write a professional product description for an Indian pharmacy website product listing.

Product: {$name}
{$context}

Requirements:
- Minimum 120 words, maximum 180 words
- Professional, informative tone suitable for a pharmacy website
- Cover what the product is, its key benefits, active ingredients (if applicable), and who it is for
- Do NOT include price, dosage instructions, or side effects
- Return ONLY the description text, no headings or bullet points
PROMPT;
    }

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
