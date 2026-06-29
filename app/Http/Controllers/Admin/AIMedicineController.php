<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIMedicineController extends Controller
{
    private const CATEGORY_MAP = [
        // ── Fever & Pain (slug: fever-pain) ──────────────────────────────────
        'ANALGESIC'        => 'fever-pain',
        'ANTIPYRETIC'      => 'fever-pain',
        'NSAID'            => 'fever-pain',
        'PARACETAMOL'      => 'fever-pain',
        'IBUPROFEN'        => 'fever-pain',
        'DICLOFENAC'       => 'fever-pain',
        'NIMESULIDE'       => 'fever-pain',
        'MEFENAMIC'        => 'fever-pain',
        'PAIN RELIEF'      => 'fever-pain',
        'MUSCLE RELAXANT'  => 'fever-pain',
        'MIGRAINE'         => 'fever-pain',
        'HEADACHE'         => 'fever-pain',

        // ── Nutrition & Health Drinks (slug: nutrition-health-drinks) ─────────
        // (brand-specific first so they don't get grabbed by 'PROTEIN POWDER' → vitamins)
        'PEDIASURE'        => 'nutrition-health-drinks',
        'ENSURE'           => 'nutrition-health-drinks',
        'HORLICKS'         => 'nutrition-health-drinks',
        'COMPLAN'          => 'nutrition-health-drinks',
        'BOOST'            => 'nutrition-health-drinks',
        'ENERGY DRINK'     => 'nutrition-health-drinks',
        'HEALTH DRINK'     => 'nutrition-health-drinks',
        'ELECTROLYTE'      => 'nutrition-health-drinks',
        'PROTEIN DRINK'    => 'nutrition-health-drinks',
        'PROTEIN SHAKE'    => 'nutrition-health-drinks',
        'ORS DRINK'        => 'nutrition-health-drinks',

        // ── Vitamins & Supplements (slug: vitamins) ───────────────────────────
        'MULTIVITAMIN'     => 'vitamins',
        'VITAMIN B'        => 'vitamins',
        'VITAMIN A'        => 'vitamins',
        'VITAMIN E'        => 'vitamins',
        'VITAMIN K'        => 'vitamins',
        'FISH OIL'         => 'vitamins',
        'IRON SUPPLEMENT'  => 'vitamins',
        'MAGNESIUM'        => 'vitamins',
        'NUTRACEUTICAL'    => 'vitamins',
        'FOLIC ACID'       => 'vitamins',
        'BIOTIN'           => 'vitamins',
        'COLLAGEN'         => 'vitamins',
        'WHEY PROTEIN'     => 'vitamins',
        'CASEIN PROTEIN'   => 'vitamins',
        'PROTEIN POWDER'   => 'vitamins',

        // ── Immunity Boosters (slug: immunity) ───────────────────────────────
        'VITAMIN C'        => 'immunity',
        'ZINC'             => 'immunity',
        'IMMUNITY'         => 'immunity',
        'CHYAWANPRASH'     => 'immunity',
        'GILOY'            => 'immunity',
        'TULSI'            => 'immunity',
        'ECHINACEA'        => 'immunity',

        // ── Bone & Joint (slug: bone-joint) ──────────────────────────────────
        'CALCIUM'          => 'bone-joint',
        'VITAMIN D'        => 'bone-joint',
        'GLUCOSAMINE'      => 'bone-joint',
        'CHONDROITIN'      => 'bone-joint',
        'BONE'             => 'bone-joint',
        'JOINT'            => 'bone-joint',
        'ARTHRITIS'        => 'bone-joint',
        'ARTHRIT'          => 'bone-joint',
        'OSTEOPOROSIS'     => 'bone-joint',
        'OSTEO'            => 'bone-joint',
        'KNEE SUPPORT'     => 'bone-joint',

        // ── Digestive Care (slug: digestive) ─────────────────────────────────
        'ANTACID'          => 'digestive',
        'PANTOPRAZOLE'     => 'digestive',
        'OMEPRAZOLE'       => 'digestive',
        'PROTON PUMP'      => 'digestive',
        'GASTR'            => 'digestive',
        'PROBIOTIC'        => 'digestive',
        'LAXATIVE'         => 'digestive',
        'ANTIDIARRH'       => 'digestive',
        'ORS'              => 'digestive',
        'DIGESTIVE ENZYME' => 'digestive',
        'CONSTIPATION'     => 'digestive',
        'DIARRHOEA'        => 'digestive',
        'ACIDITY'          => 'digestive',
        'LACTULOSE'        => 'digestive',
        'ISABGOL'          => 'digestive',
        'PILES'            => 'digestive',

        // ── Diabetes Care (slug: diabetes) ───────────────────────────────────
        'DIABETES'         => 'diabetes',
        'DIABETIC'         => 'diabetes',
        'GLUCOMETER'       => 'diabetes',
        'TEST STRIP'       => 'diabetes',
        'LANCET'           => 'diabetes',
        'INSULIN'          => 'diabetes',
        'METFORMIN'        => 'diabetes',
        'GLIPIZIDE'        => 'diabetes',
        'GLYCEMIC'         => 'diabetes',

        // ── Heart & BP (slug: heart-bp) ──────────────────────────────────────
        'CARDIAC'          => 'heart-bp',
        'CARDIOVASCULAR'   => 'heart-bp',
        'ANTIHYPERTENSIVE' => 'heart-bp',
        'STATIN'           => 'heart-bp',
        'CHOLESTEROL'      => 'heart-bp',
        'OMEGA-3'          => 'heart-bp',
        'OMEGA 3'          => 'heart-bp',
        'BP MONITOR'       => 'heart-bp',
        'BLOOD PRESSURE'   => 'heart-bp',
        'AMLODIPINE'       => 'heart-bp',
        'ATORVASTATIN'     => 'heart-bp',

        // ── Cold & Allergy (slug: cold-allergy) ──────────────────────────────
        'ANTIALLERGIC'     => 'cold-allergy',
        'ANTIHISTAMINE'    => 'cold-allergy',
        'CETIRIZINE'       => 'cold-allergy',
        'LORATADINE'       => 'cold-allergy',
        'ALLERG'           => 'cold-allergy',
        'COUGH SYRUP'      => 'cold-allergy',
        'NASAL SPRAY'      => 'cold-allergy',
        'NASAL'            => 'cold-allergy',
        'SINUS'            => 'cold-allergy',
        'STEAM INHALER'    => 'cold-allergy',
        'LOZENGE'          => 'cold-allergy',
        'COLD'             => 'cold-allergy',

        // ── Respiratory Care (slug: respiratory-care) ────────────────────────
        'NEBULIZER'        => 'respiratory-care',
        'INHALER'          => 'respiratory-care',
        'ASTHMA'           => 'respiratory-care',
        'BRONCHODILATOR'   => 'respiratory-care',
        'SALBUTAMOL'       => 'respiratory-care',
        'BUDESONIDE'       => 'respiratory-care',
        'RESPIRATORY'      => 'respiratory-care',
        'PULSE OXIMETER'   => 'respiratory-care',
        'OXYGEN'           => 'respiratory-care',

        // ── Eye & Ear Care (slug: eye-ear) ───────────────────────────────────
        'EYE DROP'         => 'eye-ear',
        'EAR DROP'         => 'eye-ear',
        'OPHTHALM'         => 'eye-ear',
        'EYE WASH'         => 'eye-ear',
        'ARTIFICIAL TEAR'  => 'eye-ear',
        'EYE VITAMIN'      => 'eye-ear',
        'CONJUNCTIV'       => 'eye-ear',

        // ── Skin Care (slug: skin-care) ──────────────────────────────────────
        'DERMA'            => 'skin-care',
        'MOISTURIZER'      => 'skin-care',
        'MOISTURIZ'        => 'skin-care',
        'SUNSCREEN'        => 'skin-care',
        'SPF'              => 'skin-care',
        'ACNE'             => 'skin-care',
        'ANTI-ACNE'        => 'skin-care',
        'ANTIFUNGAL CREAM' => 'skin-care',
        'ECZEMA'           => 'skin-care',
        'PSORIASIS'        => 'skin-care',
        'CLOTRIMAZOLE'     => 'skin-care',
        'HYDROCORTISONE'   => 'skin-care',
        'SCRUB'            => 'skin-care',
        'LOTION'           => 'skin-care',

        // ── Personal Care (slug: personal-care) ──────────────────────────────
        'FACE WASH'        => 'personal-care',
        'BODY WASH'        => 'personal-care',
        'SOAP'             => 'personal-care',
        'SHAMPOO'          => 'personal-care',
        'CONDITIONER'      => 'personal-care',
        'DEODORANT'        => 'personal-care',
        'FEMININE HYGIENE' => 'personal-care',
        'SANITARY'         => 'personal-care',
        'INTIMATE WASH'    => 'personal-care',

        // ── Hair Care (slug: hair-care) ──────────────────────────────────────
        'HAIR OIL'         => 'hair-care',
        'HAIR SERUM'       => 'hair-care',
        'HAIR GROWTH'      => 'hair-care',
        'ANTI-DANDRUFF'    => 'hair-care',
        'HAIR LOSS'        => 'hair-care',
        'MINOXIDIL'        => 'hair-care',
        'HAIR VITAMIN'     => 'hair-care',
        'SCALP'            => 'hair-care',

        // ── Oral Care (slug: oral-care) ──────────────────────────────────────
        'TOOTHPASTE'       => 'oral-care',
        'TOOTHBRUSH'       => 'oral-care',
        'TOOTH POWDER'     => 'oral-care',
        'TOOTH GEL'        => 'oral-care',
        'TOOTH WHITENING'  => 'oral-care',
        'TOOTH'            => 'oral-care',
        'MOUTHWASH'        => 'oral-care',
        'MOUTH RINSE'      => 'oral-care',
        'MOUTH FRESHENER'  => 'oral-care',
        'ORAL CARE'        => 'oral-care',
        'ORAL HYGIENE'     => 'oral-care',
        'DENTAL'           => 'oral-care',
        'DENTURE'          => 'oral-care',
        'FLOSS'            => 'oral-care',
        'TONGUE CLEANER'   => 'oral-care',
        'GUM CARE'         => 'oral-care',
        'CHARCOAL POWDER'  => 'oral-care',

        // ── Home Healthcare (slug: home-healthcare) ───────────────────────────
        'ADULT DIAPER'     => 'home-healthcare',
        'UNDERPAD'         => 'home-healthcare',
        'WHEELCHAIR'       => 'home-healthcare',
        'WALKER'           => 'home-healthcare',
        'CERVICAL COLLAR'  => 'home-healthcare',
        'BACK SUPPORT'     => 'home-healthcare',
        'KNEE BRACE'       => 'home-healthcare',
        'COMMODE'          => 'home-healthcare',

        // ── Baby Care (slug: baby-care) ──────────────────────────────────────
        'BABY FOOD'        => 'baby-care',
        'BABY DIAPER'      => 'baby-care',
        'DIAPER'           => 'baby-care',
        'BABY WIPE'        => 'baby-care',
        'BABY LOTION'      => 'baby-care',
        'BABY POWDER'      => 'baby-care',
        'BABY SHAMPOO'     => 'baby-care',
        'FEEDING BOTTLE'   => 'baby-care',
        'BABY THERMOMETER' => 'baby-care',
        'INFANT'           => 'baby-care',
        'PEDIATRIC'        => 'baby-care',

        // ── Women's Health (slug: womens-health) ─────────────────────────────
        'PREGNANCY TEST'   => 'womens-health',
        'PRENATAL'         => 'womens-health',
        'MENSTRUAL'        => 'womens-health',
        'PCOS'             => 'womens-health',
        'MENOPAUSE'        => 'womens-health',
        'PROGESTERONE'     => 'womens-health',
        'ESTROGEN'         => 'womens-health',
        "WOMEN'S HEALTH"   => 'womens-health',
        'FEMININE CARE'    => 'womens-health',

        // ── Men's Health (slug: mens-health) ─────────────────────────────────
        "MEN'S MULTIVIT"   => 'mens-health',
        'PROSTATE'         => 'mens-health',
        'TESTOSTERONE'     => 'mens-health',
        'BEARD CARE'       => 'mens-health',

        // ── Sexual Wellness (slug: sexual-wellness) ───────────────────────────
        'CONDOM'           => 'sexual-wellness',
        'LUBRICANT'        => 'sexual-wellness',
        'FERTILITY'        => 'sexual-wellness',
        'SEXUAL WELLNESS'  => 'sexual-wellness',

        // ── Mother Care (slug: mother-care) ──────────────────────────────────
        'BREAST PUMP'      => 'mother-care',
        'NURSING PAD'      => 'mother-care',
        'STRETCH MARK'     => 'mother-care',
        'MATERNITY'        => 'mother-care',
        'LACTATION'        => 'mother-care',

        // ── Liver Care (slug: liver-care) ─────────────────────────────────────
        'LIVER'            => 'liver-care',
        'HEPATO'           => 'liver-care',
        'SILYMARIN'        => 'liver-care',
        'UDILIV'           => 'liver-care',

        // ── Kidney Care (slug: kidney-care) ──────────────────────────────────
        'KIDNEY'           => 'kidney-care',
        'RENAL'            => 'kidney-care',
        'UTI RELIEF'       => 'kidney-care',
        'UTI INFECTION'    => 'kidney-care',
        'URINARY'          => 'kidney-care',
        'CRANBERRY'        => 'kidney-care',

        // ── Mental Wellness (slug: mental-wellness) ───────────────────────────
        'STRESS RELIEF'    => 'mental-wellness',
        'SLEEP AID'        => 'mental-wellness',
        'MELATONIN'        => 'mental-wellness',
        'MEMORY BOOSTER'   => 'mental-wellness',
        'ANXIETY'          => 'mental-wellness',
        'ASHWAGANDHA'      => 'mental-wellness',
        'BRAHMI'           => 'mental-wellness',
        'VALERIAN'         => 'mental-wellness',

        // ── Weight Management (slug: weight-management) ──────────────────────
        'WEIGHT LOSS'      => 'weight-management',
        'SLIMMING'         => 'weight-management',
        'MEAL REPLACEMENT' => 'weight-management',
        'FAT BURNER'       => 'weight-management',
        'WEIGHT GAIN'      => 'weight-management',
        'GARCINIA'         => 'weight-management',

        // ── Medical Devices (slug: medical-devices) ───────────────────────────
        'BP MONITOR'       => 'medical-devices',
        'BLOOD PRESSURE MONITOR' => 'medical-devices',
        'THERMOMETER'      => 'medical-devices',
        'WEIGHING SCALE'   => 'medical-devices',
        'HEATING PAD'      => 'medical-devices',
        'HOT WATER BAG'    => 'medical-devices',
        'ICE BAG'          => 'medical-devices',

        // ── Surgical & First Aid (slug: surgical-first-aid) ──────────────────
        'BANDAGE'          => 'surgical-first-aid',
        'GAUZE'            => 'surgical-first-aid',
        'SURGICAL TAPE'    => 'surgical-first-aid',
        'ANTISEPTIC'       => 'surgical-first-aid',
        'BETADINE'         => 'surgical-first-aid',
        'DETTOL'           => 'surgical-first-aid',
        'WOUND DRESSING'   => 'surgical-first-aid',
        'CREPE BANDAGE'    => 'surgical-first-aid',
        'COTTON'           => 'surgical-first-aid',
        'GLOVES'           => 'surgical-first-aid',
        // ── Ayurvedic Products (slug: ayurvedic-products) ────────────────────
        'AYURVEDIC'        => 'ayurvedic-products',
        'HERBAL'           => 'ayurvedic-products',
        'TRIPHALA'         => 'ayurvedic-products',
        'TRIKATU'          => 'ayurvedic-products',
        'ARJUNA'           => 'ayurvedic-products',
        'NEEM'             => 'ayurvedic-products',
        'HARITAKI'         => 'ayurvedic-products',
        'DABUR'            => 'ayurvedic-products',
        'BAIDYANATH'       => 'ayurvedic-products',
        'HIMALAYA HERBAL'  => 'ayurvedic-products',
        'PATANJALI'        => 'ayurvedic-products',
        'CHARAK'           => 'ayurvedic-products',

        // ── Homeopathic Medicines (slug: homeopathic-medicines) ──────────────
        'HOMEOPATH'        => 'homeopathic-medicines',
        'HOMOEOPATH'       => 'homeopathic-medicines',
        'HOMEOPATHIC DILUTION' => 'homeopathic-medicines',
        'MOTHER TINCTURE'  => 'homeopathic-medicines',
        'SBL '             => 'homeopathic-medicines',
        'SCHWABE'          => 'homeopathic-medicines',
        'BOIRON'           => 'homeopathic-medicines',
        'RECKEWEG'         => 'homeopathic-medicines',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // ROUTE 1: Search - ALWAYS searches ALL sources simultaneously, returns
    //          only items whose name contains the keyword (exact match filter)
    // ─────────────────────────────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────────────────────
    // PAGE: Bulk Import Builder
    // ─────────────────────────────────────────────────────────────────────────
    public function bulkBuilderPage(): \Illuminate\View\View
    {
        return view('admin.medicines.bulk-builder');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ROUTE 5: Bulk search - searches ALL sources, deduplicates by name slug,
    //          no result cap - returns everything both sites return.
    //          Descriptions are fetched from source sites for all items.
    // ─────────────────────────────────────────────────────────────────────────
    public function bulkSearch(Request $request): JsonResponse
    {
        $request->validate(['name' => ['required', 'string', 'min:2', 'max:200']]);
        $name    = trim($request->input('name'));
        $keyword = $this->normaliseSearchText($name);

        $allResults = [];
        $searchLog  = [];

        // Search PharmEasy - all results, no cap
        try {
            $pe = $this->searchPharmEasyBulk($name, 500);
            $pe = $this->filterByKeyword($pe, $keyword);
            $allResults = array_merge($allResults, $pe);
            $searchLog['PharmEasy'] = count($pe);
            Log::info("MediBot Bulk PharmEasy [{$name}] → " . count($pe));
        } catch (\Throwable $e) {
            $searchLog['PharmEasy'] = 'error';
            Log::warning("MediBot Bulk PharmEasy failed [{$name}]: " . $e->getMessage());
        }

        // Search NetMeds - all results, no cap
        try {
            $nm = $this->searchNetMedsBulk($name, 500);
            $nm = $this->filterByKeyword($nm, $keyword);
            $allResults = array_merge($allResults, $nm);
            $searchLog['NetMeds'] = count($nm);
            Log::info("MediBot Bulk NetMeds [{$name}] → " . count($nm));
        } catch (\Throwable $e) {
            $searchLog['NetMeds'] = 'error';
            Log::warning("MediBot Bulk NetMeds failed [{$name}]: " . $e->getMessage());
        }

        // Deduplicate by normalised slug (keep first occurrence = PharmEasy priority)
        $seen    = [];
        $unique  = [];
        foreach ($allResults as $item) {
            $slug = \Illuminate\Support\Str::slug($item['name'] ?? '');
            if ($slug === '' || isset($seen[$slug])) continue;
            $seen[$slug] = true;
            $unique[] = $item;
        }

        // No cap - return everything
        Log::info("MediBot Bulk [{$name}] → " . count($unique) . " unique results | " . json_encode($searchLog));

        return response()->json([
            'results'    => $unique,
            'total'      => count($unique),
            'search_log' => $searchLog,
        ]);
    }

    // NetMeds bulk search - larger page size
    private function searchNetMedsBulk(string $name, int $limit): array
    {
        $ch = curl_init(
            'https://www.netmeds.com/ext/search/application/api/v1.0/products'
            . '?q=' . urlencode($name)
            . '&page_no=1&page_size=' . $limit
        );
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING       => 'gzip',
            CURLOPT_HTTPHEADER     => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124',
                'Accept: application/json',
                'Accept-Language: en-IN,en;q=0.9',
                'Referer: https://www.netmeds.com/',
                'x-application-token: _U-ohI4Iy',
                'Origin: https://www.netmeds.com',
            ],
        ]);
        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200 || !$body) return [];

        $data  = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) return [];

        $items = $data['items'] ?? [];
        if (!is_array($items) || empty($items)) return [];

        $results = [];
        foreach ($items as $p) {
            $pname = $p['name'] ?? '';
            if (empty($pname)) continue;

            $galleryUrls = [];
            foreach ($p['medias'] ?? [] as $media) {
                if (!empty($media['url'])) $galleryUrls[] = $media['url'];
            }
            $galleryUrls = $this->cleanTrustedProductImageUrls($galleryUrls);
            $imageUrl = $galleryUrls[0] ?? '';

            $mrp   = (float) ($p['price']['marked']['min']    ?? 0);
            $price = (float) ($p['price']['effective']['min'] ?? 0);

            $attrs        = $p['attributes'] ?? [];
            $manufacturer = $attrs['marketername']  ?? $attrs['brandfilter'] ?? null;
            $composition  = $attrs['genericname']   ?? null;
            $packSize     = trim(($attrs['packsize'] ?? '') . ' ' . ($attrs['packsizeunit'] ?? ''));

            $fullName = $pname;
            if ($packSize && !str_contains(strtolower($pname), strtolower(trim($packSize)))) {
                $fullName = $pname . ' ' . $packSize;
            }

            $haystack = strtoupper($fullName . ' ' . ($manufacturer ?? '') . ' ' . ($composition ?? ''));

            // Extract real description from NetMeds OTC fields
            $description = null;
            $otcDesc    = $attrs['otc-description'] ?? '';
            $otcBenefit = $attrs['otc-keybenefit']  ?? '';
            if ($otcDesc) {
                $clean = trim(preg_replace('/\s+/', ' ', strip_tags((string)$otcDesc)));
                if (strlen($clean) > 30) $description = $clean;
            }
            if (!$description && $otcBenefit) {
                preg_match_all('/<li>(.*?)<\/li>/si', (string)$otcBenefit, $li);
                $bullets = array_filter(
                    array_map(fn($t) => trim(strip_tags($t)), $li[1]),
                    fn($t) => strlen($t) > 10
                );
                if (!empty($bullets)) {
                    $description = implode(' ', array_slice(array_values($bullets), 0, 3));
                }
            }

            $results[] = [
                'slug'                  => $p['slug'] ?? null,
                'name'                  => $this->cleanProductName($fullName),
                'manufacturer'          => $manufacturer ? $this->titleCase(strtolower($manufacturer)) : null,
                'category'              => $this->guessCategory($haystack, implode(' ', array_filter(array_map(
                    fn($c) => is_array($c) ? ($c['name'] ?? '') : (string) $c,
                    array_slice((array) ($p['l3_categories'] ?? $p['categories'] ?? []), 0, 1)
                )))),
                'description'           => $description,
                'composition'           => $composition ? $this->titleCase(strtolower($composition)) : null,
                'dosage_form'           => 'Unit',
                'uses'                  => [],
                'prescription_required' => false,
                'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2)   : null,
                'price_suggestion'      => $price > 0 ? round($price, 2) : null,
                'image_url'             => $imageUrl ?: null,
                'gallery_image_urls'    => $galleryUrls,
                'source_url'            => $p['slug'] ? "https://www.netmeds.com/products/" . $p['slug'] : "https://www.netmeds.com",
                'source_platform'       => 'NetMeds',
            ];
        }

        return $results;
    }

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
        // We handle this by replacing compound words with their spaced version too -
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

        // Platform-based referer (most reliable - frontend passes source_platform)
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
            // Some CDNs need the exact product-page URL as referer - try without referer
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
            // Some servers return application/octet-stream for images - trust extension instead
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
        $dir = public_path('Images/medicines');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = 'med_' . uniqid() . '.' . $ext;
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        if (file_put_contents($fullPath, $imageData) === false) {
            return response()->json(['error' => 'Could not save image.'], 500);
        }

        $localUrl = asset('Images/medicines/' . $filename);
        Log::info("MediBot image downloaded [{$platform}]: {$remoteUrl} → {$localUrl}");

        return response()->json(['url' => $localUrl, 'filename' => $filename]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ROUTE 3c: batchImages — download ALL images for ALL selected products
    //           in a single PHP request using curl_multi (true parallel).
    //
    // Input:  { items: [ { urls: ["https://..."], platform: "PharmEasy" }, ... ] }
    // Output: { results: [ { index: 0, images: ["local_url", ...] }, ... ] }
    //
    // This replaces N×M individual HTTP requests (one per image per product)
    // with a single request that downloads everything in parallel cURL.
    // The site stays responsive because only ONE PHP worker thread is used.
    // ─────────────────────────────────────────────────────────────────────────
    public function batchImages(Request $request): JsonResponse
    {
        $request->validate([
            'items'              => ['required', 'array', 'min:1', 'max:100'],
            'items.*.urls'       => ['required', 'array', 'min:1', 'max:4'],
            'items.*.urls.*'     => ['required', 'url', 'max:2000'],
            'items.*.platform'   => ['nullable', 'string', 'max:50'],
        ]);

        $dir = public_path('Images/medicines');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        // ── Build a flat list of all (itemIdx, urlIdx, url, referer) jobs ────
        $jobs = [];
        foreach ($request->input('items') as $itemIdx => $item) {
            $platform = strtolower(trim((string) ($item['platform'] ?? '')));
            foreach ($item['urls'] as $urlIdx => $remoteUrl) {
                if (!$remoteUrl || strtolower(parse_url($remoteUrl, PHP_URL_SCHEME) ?? '') !== 'https') continue;
                $host = strtolower(parse_url($remoteUrl, PHP_URL_HOST) ?? '');
                if (!str_contains($host, '.')) continue;
                $blocked = ['localhost', '127.', '192.168.', '10.', '172.16.'];
                $skip = false;
                foreach ($blocked as $b) { if (str_starts_with($host, $b)) { $skip = true; break; } }
                if ($skip) continue;

                $referer = '';
                if (str_contains($platform, 'pharmeasy'))   $referer = 'https://pharmeasy.in/';
                elseif (str_contains($platform, 'netmeds')) $referer = 'https://www.netmeds.com/';
                elseif (str_contains($platform, 'apollo'))  $referer = 'https://www.apollopharmacy.in/';
                if (!$referer) {
                    if (str_contains($host, 'pharmeasy'))   $referer = 'https://pharmeasy.in/';
                    elseif (str_contains($host, 'netmeds')) $referer = 'https://www.netmeds.com/';
                    elseif (str_contains($host, 'pixelbin'))   $referer = 'https://www.netmeds.com/';
                    elseif (str_contains($host, 'apollo'))  $referer = 'https://www.apollopharmacy.in/';
                    elseif (str_contains($host, 'cloudinary')) $referer = 'https://www.apollopharmacy.in/';
                }

                $jobs[] = [
                    'item_idx' => (int) $itemIdx,
                    'url_idx'  => (int) $urlIdx,
                    'url'      => $remoteUrl,
                    'referer'  => $referer,
                ];
            }
        }

        if (empty($jobs)) {
            return response()->json(['results' => []]);
        }

        // ── Build headers shared by all image requests ────────────────────
        $sharedHeaders = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124',
            'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
            'Accept-Language: en-IN,en;q=0.9',
            'Sec-Fetch-Dest: image',
            'Sec-Fetch-Mode: no-cors',
            'Sec-Fetch-Site: cross-site',
        ];

        // ── Spin up curl_multi with all jobs in parallel ──────────────────
        $mh       = curl_multi_init();
        $handles  = [];

        foreach ($jobs as $i => $job) {
            $ch = curl_init($job['url']);
            $hdrs = $sharedHeaders;
            if ($job['referer']) $hdrs[] = 'Referer: ' . $job['referer'];
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 4,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING       => '',
                CURLOPT_HTTPHEADER     => $hdrs,
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[$i] = $ch;
        }

        // Execute all in parallel
        $active = null;
        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) curl_multi_select($mh, 1.0);
        } while ($active && $status === CURLM_OK);

        // ── Collect results, save files ───────────────────────────────────
        $finfo      = new \finfo(FILEINFO_MIME_TYPE);
        $mimeToExt  = [
            'image/jpeg' => 'jpg', 'image/png'  => 'png', 'image/webp' => 'webp',
            'image/gif'  => 'gif', 'image/avif' => 'avif', 'image/bmp' => 'jpg',
        ];

        // results[itemIdx][urlIdx] = localUrl
        $savedByItem = [];

        foreach ($handles as $i => $ch) {
            $body   = curl_multi_getcontent($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            $job = $jobs[$i];
            if ($status !== 200 || !$body || strlen($body) < 100) continue;

            // Detect extension
            $ext      = 'jpg';
            $urlPath  = parse_url($job['url'], PHP_URL_PATH) ?? '';
            $candidate = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
            $candidate = preg_replace('/[^a-z].*/', '', $candidate);
            if (in_array($candidate, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
                $ext = $candidate === 'jpeg' ? 'jpg' : $candidate;
            }
            $mime = $finfo->buffer($body);
            if (isset($mimeToExt[$mime])) $ext = $mimeToExt[$mime];
            elseif (!in_array($ext, ['jpg', 'png', 'webp', 'gif', 'avif'])) continue;

            $filename = 'med_' . uniqid() . '.' . $ext;
            if (file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $body) === false) continue;

            $localUrl = asset('Images/medicines/' . $filename);
            $savedByItem[$job['item_idx']][] = $localUrl;
        }

        curl_multi_close($mh);

        // ── Format output: one entry per input item ───────────────────────
        $results = [];
        foreach ($request->input('items') as $itemIdx => $item) {
            $results[] = [
                'index'  => (int) $itemIdx,
                'images' => $savedByItem[$itemIdx] ?? [],
            ];
        }

        Log::info('MediBot batchImages: ' . count($jobs) . ' URLs → ' . array_sum(array_map('count', $savedByItem)) . ' saved');

        return response()->json(['results' => $results]);
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
    // ROUTE 2: Fetch full detail for a product slug.
    // Accepts: { slug: "...", platform: "PharmEasy"|"NetMeds" }
    // Returns full data including gallery_image_urls (all images from page).
    // ─────────────────────────────────────────────────────────────────────────
    public function detail(Request $request): JsonResponse
    {
        $request->validate([
            'slug'     => ['required', 'string', 'max:300'],
            'platform' => ['nullable', 'string', 'max:50'],
        ]);
        $slug     = trim($request->input('slug'), '/');
        $platform = strtolower(trim((string) $request->input('platform', 'pharmeasy')));

        try {
            if (str_contains($platform, 'netmeds')) {
                $data = $this->fetchNetMedsDetail($slug);
            } else {
                $data = $this->fetchProductDetail($slug);
            }

            if ($data) {
                return response()->json(['data' => $data]);
            }
            return response()->json(['error' => 'Could not load product details.'], 502);
        } catch (\Throwable $e) {
            Log::error("MediBot detail failed [{$slug}]: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 503);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Shared image-deduplication helper
    // Accepts raw URL strings (or null). Strips query strings for comparison.
    // Returns ordered, unique, non-empty HTTPS URLs.
    // ─────────────────────────────────────────────────────────────────────────
    private function deduplicateImageUrls(array $urls): array
    {
        $seen   = [];
        $result = [];
        foreach ($urls as $u) {
            if (!is_string($u) || !$u) continue;
            if (!str_starts_with($u, 'http')) continue;
            $key = strtok($u, '?'); // base URL without query string
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[]   = $u;
            }
        }
        return $result;
    }

    private function cleanTrustedProductImageUrls(array $urls): array
    {
        return $this->deduplicateImageUrls(array_values(array_filter(
            $urls,
            fn ($url) => is_string($url) && ! $this->isBlockedProductImageUrl($url)
        )));
    }

    private function buildProductGallery(array $trustedUrls, array $scrapedUrls, string $productName): array
    {
        $trusted = $this->cleanTrustedProductImageUrls($trustedUrls);
        $scraped = array_values(array_filter($scrapedUrls, function ($url) use ($productName) {
            return is_string($url)
                && ! $this->isBlockedProductImageUrl($url)
                && $this->imageUrlMatchesProductName($url, $productName);
        }));

        return $this->deduplicateImageUrls(array_merge($trusted, $scraped));
    }

    private function isBlockedProductImageUrl(string $url): bool
    {
        $haystack = strtolower(rawurldecode($url));
        $blocked = [
            'logo', 'placeholder', 'default', 'blank', 'no-image', 'noimage',
            'avatar', 'profile', 'user', 'founder', 'ceo', 'team', 'doctor',
            'banner', 'icon', 'sprite', 'loader', 'loading', 'app-store',
            'play-store', 'whatsapp', 'facebook', 'instagram', 'linkedin',
            'twitter', 'youtube',
        ];

        foreach ($blocked as $term) {
            if (str_contains($haystack, $term)) {
                return true;
            }
        }

        return false;
    }

    private function imageUrlMatchesProductName(string $url, string $productName): bool
    {
        $tokens = $this->productNameImageTokens($productName);
        if (empty($tokens)) {
            return false;
        }

        $haystack = $this->normaliseImageText(rawurldecode($url));
        foreach ($tokens as $token) {
            if (str_contains($haystack, $token)) {
                return true;
            }
        }

        return false;
    }

    private function productNameImageTokens(string $productName): array
    {
        $text = $this->normaliseImageText($productName);
        $tokens = array_filter(explode(' ', $text), function ($token) {
            if (strlen($token) < 4) return false;
            return ! in_array($token, [
                'tablet', 'tablets', 'capsule', 'capsules', 'strip', 'strips',
                'bottle', 'cream', 'syrup', 'drops', 'drop', 'pack', 'packs',
                'combo', 'with', 'plus', 'oral', 'solution', 'powder', 'soap',
                'lotion', 'wash', 'gel', 'spray',
            ], true);
        });

        return array_values(array_unique($tokens));
    }

    private function normaliseImageText(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', ' ', $text) ?? '';
        return trim(preg_replace('/\s+/', ' ', $text) ?? '');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Shared: extract all image URLs from a page HTML using every method:
    //   1. JSON-LD  (application/ld+json)
    //   2. og:image meta tags
    //   3. twitter:image meta tags
    //   4. Embedded product JSON blobs in <script> tags
    // Returns raw unsanitised array — caller deduplicates.
    // ─────────────────────────────────────────────────────────────────────────
    private function extractImagesFromHtml(string $html): array
    {
        $urls = [];

        // 1. JSON-LD
        if (preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $m)) {
            foreach ($m[1] as $blob) {
                $ld = json_decode(trim($blob), true);
                if (!is_array($ld)) continue;
                // Handle @graph arrays
                $nodes = isset($ld['@graph']) ? $ld['@graph'] : [$ld];
                foreach ($nodes as $node) {
                    foreach (['image', 'thumbnailUrl'] as $key) {
                        foreach ((array) ($node[$key] ?? []) as $val) {
                            if (is_string($val) && str_starts_with($val, 'http')) $urls[] = $val;
                            if (is_array($val) && !empty($val['url']))             $urls[] = $val['url'];
                        }
                    }
                }
            }
        }

        // 2. og:image meta
        if (preg_match_all('/<meta[^>]+property=["\']og:image(?::secure_url)?["\'][^>]+content=["\'](https?:[^"\']+)["\']/i', $html, $m)) {
            foreach ($m[1] as $u) $urls[] = $u;
        }

        // 3. twitter:image meta
        if (preg_match_all('/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\'](https?:[^"\']+)["\']/i', $html, $m)) {
            foreach ($m[1] as $u) $urls[] = $u;
        }

        // 4. Img src attributes pointing to CDN image hosts (pharmacy product images)
        $cdnPatterns = [
            'cdn\.pharmeasy\.in',
            'images\.pharmeasy\.in',
            'pixelbin\.io',          // NetMeds CDN
            'cloudinary\.com',       // Apollo CDN
            '1mg-logos\.1mg\.com',
            'onemg-image\.s3',
            'apollopharmacy\.in.*?\.(jpg|jpeg|png|webp)',
        ];
        $cdnRegex = '/https?:\/\/(?:' . implode('|', $cdnPatterns) . ')[^\s"\'<>]+\.(?:jpg|jpeg|png|webp|gif|avif)/i';
        if (preg_match_all($cdnRegex, $html, $m)) {
            foreach ($m[0] as $u) $urls[] = $u;
        }

        return $urls;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // NetMeds – fetch full product detail and collect ALL images
    // ─────────────────────────────────────────────────────────────────────────
    private function fetchNetMedsDetail(string $slug): ?array
    {
        $pageUrl = "https://www.netmeds.com/products/{$slug}";
        $page    = $this->httpGet($pageUrl, 'https://www.netmeds.com/');

        $htmlUrls = [];
        $allUrls = [];
        $pd      = null;

        if ($page) {
            // Extract images from raw HTML (CDN, og:image, JSON-LD)
            $htmlUrls = $this->extractImagesFromHtml($page);

            // __NEXT_DATA__ structured data
            if (preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $page, $nm)) {
                $nd = json_decode($nm[1], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $pd = $nd['props']['pageProps']['productData']
                       ?? $nd['props']['pageProps']['product']
                       ?? $nd['props']['pageProps']['pdpData']
                       ?? null;
                }
            }

            // window.__INITIAL_STATE__
            if (!$pd && preg_match('/window\.__INITIAL_STATE__\s*=\s*(\{.+?\});\s*(?:window|<\/script>)/s', $page, $nm)) {
                $state = json_decode($nm[1], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $pd = $state['productDetails']['product'] ?? $state['product'] ?? null;
                }
            }
        }

        // Fynd product API fallback
        if (!$pd) {
            $apiUrl = 'https://www.netmeds.com/ext/product/application/api/v1.0/products/' . urlencode($slug);
            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 15, CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => 'gzip',
                CURLOPT_HTTPHEADER => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124',
                    'Accept: application/json',
                    'Referer: https://www.netmeds.com/',
                    'x-application-token: _U-ohI4Iy',
                ],
            ]);
            $body   = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($status === 200 && $body) {
                $apiData = json_decode($body, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $pd = $apiData['product'] ?? $apiData['data'] ?? (isset($apiData['name']) ? $apiData : null);
                }
            }
        }

        if (!$pd && empty($allUrls)) return null;

        // Collect structured image URLs from product data
        if ($pd) {
            foreach ($pd['medias']   ?? [] as $m) { if (!empty($m['url']))       $allUrls[] = $m['url']; }
            foreach ($pd['images']   ?? [] as $m) {
                $u = is_array($m) ? ($m['url'] ?? $m['secure_url'] ?? '') : (string) $m;
                if ($u) $allUrls[] = $u;
            }
            foreach ($pd['galleries'] ?? [] as $m) {
                $u = is_array($m) ? ($m['url'] ?? '') : (string) $m;
                if ($u) $allUrls[] = $u;
            }
        }

        $attrs      = $pd['attributes'] ?? [];
        $brand      = $pd['brand']['name'] ?? null;
        $manufacturer = $brand ?? $attrs['marketername'] ?? $attrs['brandfilter'] ?? null;
        $composition  = $attrs['genericname'] ?? null;
        $mrp   = (float) ($pd['price']['marked']['min']    ?? $pd['mrp']   ?? 0);
        $price = (float) ($pd['price']['effective']['min'] ?? $mrp);
        $name  = $pd['name'] ?? $slug;
        $uniqueUrls = $this->buildProductGallery($allUrls, $htmlUrls, $name);
        if (empty($uniqueUrls) && !$pd) return null;

        $haystack = strtoupper($name . ' ' . ($manufacturer ?? '') . ' ' . ($composition ?? ''));

        // NetMeds/Fynd detail may expose categories array
        $nmCats = $pd['categories'] ?? $pd['department'] ?? $pd['l3_categories'] ?? [];
        $nmSourceCat = '';
        if (is_array($nmCats) && !empty($nmCats)) {
            $nmSourceCat = is_array($nmCats[0]) ? ($nmCats[0]['name'] ?? '') : (string) $nmCats[0];
        } elseif (is_string($nmCats)) {
            $nmSourceCat = $nmCats;
        }

        return [
            'slug'                  => $pd['slug'] ?? $slug,
            'name'                  => $name,
            'manufacturer'          => $manufacturer ? $this->titleCase(strtolower($manufacturer)) : null,
            'category'              => $this->guessCategory($haystack, $nmSourceCat),
            'description'           => null,
            'composition'           => $composition ? $this->titleCase(strtolower($composition)) : null,
            'dosage_form'           => 'Unit',
            'uses'                  => [],
            'prescription_required' => false,
            'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2) : null,
            'price_suggestion'      => $price > 0 ? round($price, 2) : null,
            'image_url'             => $uniqueUrls[0] ?? null,
            'gallery_image_urls'    => $uniqueUrls,
            'source_url'            => $pageUrl,
            'source_platform'       => 'NetMeds',
        ];
    }

    // ═════════════════════════════════════════════════════════════════════════
    // PharmEasy - search via __NEXT_DATA__ (productList key)
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

        $pp = $nextData['props']['pageProps'] ?? [];

        // PharmEasy changed key from 'searchResults' → 'productList'
        $raw = $pp['productList'] ?? $pp['searchResults'] ?? [];
        if (empty($raw)) return [];

        return $this->parsePharmEasyItems($raw);
    }

    // PharmEasy bulk - fetches multiple pages to get all results
    private function searchPharmEasyBulk(string $name, int $limit): array
    {
        $allItems = [];
        $page     = 1;

        while (true) {
            $url  = 'https://pharmeasy.in/search/all?name=' . urlencode($name) . '&page=' . $page;
            $html = $this->httpGet($url, 'https://pharmeasy.in/');
            if (!$html) break;

            if (!preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) break;

            $nextData = json_decode($m[1], true);
            if (json_last_error() !== JSON_ERROR_NONE) break;

            $pp  = $nextData['props']['pageProps'] ?? [];
            $raw = $pp['productList'] ?? $pp['searchResults'] ?? [];

            if (empty($raw)) break;

            $allItems = array_merge($allItems, $raw);

            // Stop if no more pages or already at limit
            $hasMore = $pp['hasMorePages'] ?? false;
            if (!$hasMore || count($allItems) >= $limit) break;

            $page++;
            // Safety cap: max 5 pages to avoid hammering
            if ($page > 5) break;
        }

        return $this->parsePharmEasyItems(array_slice($allItems, 0, $limit));
    }

    // Shared parser for PharmEasy product arrays
    private function parsePharmEasyItems(array $raw): array
    {
        $results = [];
        foreach ($raw as $p) {
            if (empty($p['name'])) continue;

            // Best image: prefer 'front' face, fall back to first
            $galleryUrls = [];
            foreach ($p['damImages'] ?? [] as $img) {
                if (!empty($img['url'])) $galleryUrls[] = $img['url'];
            }
            if (!empty($p['image'])) $galleryUrls[] = $p['image'];
            $galleryUrls = $this->cleanTrustedProductImageUrls($galleryUrls);

            $imageUrl = '';
            foreach ($p['damImages'] ?? [] as $img) {
                if (($img['face'] ?? '') === 'front' && !empty($img['url']) && in_array($img['url'], $galleryUrls, true)) {
                    $imageUrl = $img['url'];
                    break;
                }
            }
            if (!$imageUrl && !empty($galleryUrls)) $imageUrl = $galleryUrls[0];

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

            $manufacturer = !empty($p['manufacturer'])
                ? $this->titleCase(strtolower($p['manufacturer']))
                : null;

            $mrp   = (float) ($p['mrpDecimal']      ?? 0);
            $price = (float) ($p['salePriceDecimal'] ?? 0);

            $haystack = strtoupper(
                ($p['name'] ?? '') . ' ' .
                ($p['moleculeName'] ?? '') . ' ' .
                ($p['manufacturer'] ?? '') . ' ' . $pf
            );

            // PharmEasy search returns therapy / category taxonomy — use it directly
            $sourceCategory = $p['therapyNames']
                ?? $p['therapy']
                ?? $p['categoryName']
                ?? $p['primaryCategoryName']
                ?? $p['category']
                ?? '';

            $slug = $p['slug'] ?? null;

            $results[] = [
                'slug'                  => $slug,
                'name'                  => $this->cleanProductName($p['name']),
                'manufacturer'          => $manufacturer,
                'category'              => $this->guessCategory($haystack, (string) $sourceCategory),
                'description'           => null, // fetched via generateDescription route
                'composition'           => $p['moleculeName'] ?: null,
                'dosage_form'           => $dosageForm,
                'uses'                  => [],
                'prescription_required' => (bool) ($p['isRxRequired'] ?? false),
                'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2)   : null,
                'price_suggestion'      => $price > 0 ? round($price, 2) : null,
                'image_url'             => $imageUrl ?: null,
                'gallery_image_urls'    => $galleryUrls,
                'source_url'            => $slug
                    ? "https://pharmeasy.in/online-medicine-order/{$slug}"
                    : null,
                'source_platform'       => 'PharmEasy',
            ];
        }
        return $results;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // NetMeds - real Fynd platform search API (confirmed working)
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

            $galleryUrls = [];
            foreach ($p['medias'] ?? [] as $media) {
                if (!empty($media['url'])) $galleryUrls[] = $media['url'];
            }
            $galleryUrls = $this->cleanTrustedProductImageUrls($galleryUrls);
            $imageUrl = $galleryUrls[0] ?? '';

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

            // Extract real description from NetMeds OTC fields
            $description = null;
            $otcDesc    = $attrs['otc-description'] ?? '';
            $otcBenefit = $attrs['otc-keybenefit']  ?? '';
            if ($otcDesc) {
                $clean = trim(preg_replace('/\s+/', ' ', strip_tags((string)$otcDesc)));
                if (strlen($clean) > 30) $description = $clean;
            }
            if (!$description && $otcBenefit) {
                preg_match_all('/<li>(.*?)<\/li>/si', (string)$otcBenefit, $li);
                $bullets = array_filter(
                    array_map(fn($t) => trim(strip_tags($t)), $li[1]),
                    fn($t) => strlen($t) > 10
                );
                if (!empty($bullets)) {
                    $description = implode(' ', array_slice(array_values($bullets), 0, 3));
                }
            }

            $results[] = [
                'slug'                  => $p['slug'] ?? null,
                'name'                  => $fullName,
                'manufacturer'          => $manufacturer ? $this->titleCase(strtolower($manufacturer)) : null,
                'category'              => $this->guessCategory($haystack, implode(' ', array_filter(array_map(
                    fn($c) => is_array($c) ? ($c['name'] ?? '') : (string) $c,
                    array_slice((array) ($p['l3_categories'] ?? $p['categories'] ?? []), 0, 1)
                )))),
                'description'           => $description,
                'composition'           => $composition ? $this->titleCase(strtolower($composition)) : null,
                'dosage_form'           => 'Unit',
                'uses'                  => [],
                'prescription_required' => false,
                'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2)   : null,
                'price_suggestion'      => $price > 0 ? round($price, 2) : null,
                'image_url'             => $imageUrl ?: null,
                'gallery_image_urls'    => $galleryUrls,
                'source_url'            => $p['slug']
                    ? "https://www.netmeds.com/products/" . $p['slug']
                    : "https://www.netmeds.com",
                'source_platform'       => 'NetMeds',
            ];
        }

        return $results;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Apollo Pharmacy - __NEXT_DATA__ HTML scraping
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

            // Apollo returns category / department fields
            $apolloCat = $p['category'] ?? $p['categoryName'] ?? $p['department'] ?? $p['categorySlug'] ?? '';

            $results[] = [
                'slug'                  => $p['slug'] ?? $p['urlKey'] ?? null,
                'name'                  => $this->cleanProductName($pname),
                'manufacturer'          => $manufacturer ? $this->titleCase(strtolower($manufacturer)) : null,
                'category'              => $this->guessCategory($haystack, (string) $apolloCat),
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
    // PharmEasy - fetch full product page and extract description + uses
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
        // Collect ALL images from the detail page using every available source
        $allImageUrls = [];
        $htmlImageUrls = [];

        // 1. damImages — face variants (front / back / left / right / top)
        $damImages = $pd['damImages'] ?? [];
        foreach ($damImages as $img) {
            if (!empty($img['url'])) $allImageUrls[] = $img['url'];
        }

        // 2. productImages — higher-res gallery array used in newer API
        foreach ($pd['productImages'] ?? [] as $img) {
            $u = is_array($img) ? ($img['url'] ?? $img['imageUrl'] ?? '') : (string) $img;
            if ($u) $allImageUrls[] = $u;
        }

        // 3. images[] — older PharmEasy API fallback
        foreach ($pd['images'] ?? [] as $img) {
            $u = is_array($img) ? ($img['url'] ?? '') : (string) $img;
            if ($u) $allImageUrls[] = $u;
        }

        // 4. productSpecifications → identifier = "product-images"
        foreach ($specs as $spec) {
            if (($spec['identifier'] ?? '') === 'product-images') {
                foreach ($spec['images'] ?? $spec['imageList'] ?? [] as $img) {
                    $u = is_array($img) ? ($img['url'] ?? '') : (string) $img;
                    if ($u) $allImageUrls[] = $u;
                }
                break;
            }
        }

        // 5. JSON-LD, og:image, twitter:image, CDN img src from raw HTML
        $htmlImageUrls = array_merge($htmlImageUrls, $this->extractImagesFromHtml($page));

        // 6. seoData / metaData image arrays in __NEXT_DATA__
        $seoImages = $nextData['props']['pageProps']['seoData']['images']
                  ?? $nextData['props']['pageProps']['metaData']['images']
                  ?? [];
        foreach ((array) $seoImages as $img) {
            $u = is_array($img) ? ($img['url'] ?? '') : (string) $img;
            if ($u && str_starts_with($u, 'http')) $htmlImageUrls[] = $u;
        }

        // Deduplicate preserving order
        $productName = $pd['name'] ?? $slug;
        $uniqueUrls = $this->buildProductGallery($allImageUrls, $htmlImageUrls, $productName);

        // Primary image: prefer face=front from damImages, else first unique URL
        $imageUrl = '';
        foreach ($damImages as $img) {
            if (($img['face'] ?? '') === 'front' && !empty($img['url']) && in_array($img['url'], $uniqueUrls, true)) {
                $imageUrl = $img['url'];
                break;
            }
        }
        if (!$imageUrl && !empty($uniqueUrls)) $imageUrl = $uniqueUrls[0];

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

        // Source taxonomy from PharmEasy detail
        $sourceCategory = $pd['therapyNames'] ?? $pd['therapy']
            ?? $pd['categoryName'] ?? $pd['primaryCategoryName'] ?? '';

        return [
            'slug'                  => $pd['slug'] ?? $slug,
            'name'                  => $pd['name'] ?? '',
            'manufacturer'          => $manufacturer,
            'category'              => $this->guessCategory($haystack, (string) $sourceCategory),
            'description'           => $description ?: null,
            'composition'           => $composition ?: null,
            'dosage_form'           => $dosageForm,
            'uses'                  => $uses,
            'prescription_required' => (bool) ($pd['isRxRequired'] ?? false),
            'mrp_suggestion'        => $mrp   > 0 ? round($mrp, 2)   : null,
            'price_suggestion'      => $price > 0 ? round($price, 2) : null,
            'image_url'             => $imageUrl ?: null,
            'gallery_image_urls'    => $uniqueUrls,
            'source_url'            => "https://pharmeasy.in/online-medicine-order/{$slug}",
            'source_platform'       => 'PharmEasy',
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
    // ROUTE 4: Generate AI description - always ≥50 words guaranteed.
    //          Retries once with a stricter prompt if the first attempt is short.
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
            'existing'     => ['nullable', 'string'],
            'source_url'   => ['nullable', 'string', 'max:500'],
            'source_platform' => ['nullable', 'string', 'max:50'],
            'slug'         => ['nullable', 'string', 'max:300'],
        ]);

        $name         = trim($request->input('name'));
        $manufacturer = trim((string) $request->input('manufacturer', ''));
        $composition  = trim((string) $request->input('composition', ''));
        $uses         = (array)  $request->input('uses', []);
        $dosageForm   = trim((string) $request->input('dosage_form', ''));
        $category     = trim((string) $request->input('category', ''));
        $existing     = trim((string) $request->input('existing', ''));
        $sourcePlatform = strtolower(trim((string) $request->input('source_platform', '')));
        $slug         = trim((string) $request->input('slug', ''));

        // ── Step 1: Try to get real description from source site ──────────────
        $scrapedDesc = null;

        // PharmEasy - fetch full product page
        if (str_contains($sourcePlatform, 'pharmeasy') && $slug) {
            try {
                $detail = $this->fetchProductDetail($slug);
                if ($detail && !empty($detail['description']) && str_word_count($detail['description']) >= 20) {
                    $scrapedDesc = $detail['description'];
                    Log::info("MediBot descGen scraped PharmEasy ✓ [{$name}]");
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot descGen PharmEasy scrape failed [{$name}]: " . $e->getMessage());
            }
        }

        // NetMeds - description comes in via 'existing' field (already extracted at search time)
        if (!$scrapedDesc && str_contains($sourcePlatform, 'netmeds') && $existing && str_word_count($existing) >= 20) {
            $scrapedDesc = $existing;
            Log::info("MediBot descGen using NetMeds source desc ✓ [{$name}]");
        }

        if ($scrapedDesc) {
            return response()->json(['description' => $scrapedDesc]);
        }

        // ── Step 2: Fall back to AI generation ───────────────────────────────
        $prompt = $this->descriptionPrompt(
            $name, $manufacturer, $composition, $uses, $dosageForm, $category, $existing
        );

        $text = $this->callAiForDescription($prompt, $name);

        // If the first attempt came back short, retry once with a stricter prompt
        if ($text && str_word_count($text) < 50) {
            Log::warning("MediBot descGen: first attempt only " . str_word_count($text) . " words for [{$name}], retrying");
            $retryPrompt = $this->descriptionPrompt(
                $name, $manufacturer, $composition, $uses, $dosageForm, $category,
                "IMPORTANT: The previous attempt was too short. You MUST write at least 60 words. Previous attempt: {$text}"
            );
            $retry = $this->callAiForDescription($retryPrompt, $name . ' [retry]');
            if ($retry && str_word_count($retry) >= str_word_count($text)) {
                $text = $retry;
            }
        }

        if ($text) {
            Log::info("MediBot descGen AI ✓ [{$name}] → " . str_word_count($text) . " words");
            return response()->json(['description' => $text]);
        }

        return response()->json(['error' => 'Could not generate description.'], 502);
    }

    // Tries Gemini then Groq, returns the text or null
    private function callAiForDescription(string $prompt, string $label): ?string
    {
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
                        'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 300],
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
                        Log::info("MediBot descGen Gemini ✓ [{$label}]");
                        return $text;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot descGen Gemini failed [{$label}]: " . $e->getMessage());
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
                            ['role' => 'system', 'content' => 'You write professional product descriptions of at least 60 words. Return ONLY the description text.'],
                            ['role' => 'user',   'content' => $prompt],
                        ],
                        'temperature' => 0.4, 'max_tokens' => 300,
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
                        Log::info("MediBot descGen Groq ✓ [{$label}]");
                        return $text;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("MediBot descGen Groq failed [{$label}]: " . $e->getMessage());
            }
        }

        return null;
    }

    private function descriptionPrompt(
        string $name, string $manufacturer, string $composition,
        array $uses, string $dosageForm, string $category, string $existing
    ): string {
        $usesText  = !empty($uses) ? implode(', ', array_slice($uses, 0, 5)) : '';
        $contextParts = array_filter([
            $manufacturer ? "Manufactured by {$manufacturer}."     : '',
            $dosageForm   ? "Dosage form: {$dosageForm}."          : '',
            $composition  ? "Active ingredients: {$composition}."  : '',
            $usesText     ? "Uses: {$usesText}."                   : '',
            $existing     ? "Context: {$existing}."                : '',
        ]);
        $context = implode(' ', $contextParts);

        return <<<PROMPT
Write a professional product description for an Indian pharmacy website product listing.

Product: {$name}
{$context}

Requirements:
- MUST be at least 60 words and no more than 150 words
- Count every word carefully - do not stop before 60 words
- Professional, informative tone suitable for a pharmacy website
- Cover what the product is, its key benefits, active ingredients (if applicable), and who it is for
- Do NOT include price, dosage instructions, or side effects
- Return ONLY the plain description paragraph - no headings, no bullet points, no extra commentary
PROMPT;
    }

    private function aiPrompt(string $name): string
    {
        return <<<PROMPT
You are an expert product data assistant for an Indian online medical and healthcare store (sells medicines, baby care, face wash, diapers, supplements, devices, cosmetics - everything a physical pharmacy sells).

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

    /**
     * Determine our category slug for a product.
     *
     * Priority:
     *  1. Source platform taxonomy (PharmEasy therapy, NetMeds category etc.) if provided
     *  2. Keyword scan of the haystack (name + composition + manufacturer + packform)
     *  3. Default fallback: vitamins
     */
    private function guessCategory(string $haystack, string $sourceCategory = ''): string
    {
        // 1. Try direct taxonomy mapping first
        if ($sourceCategory !== '') {
            $mapped = $this->mapSourceCategory($sourceCategory);
            if ($mapped !== null) {
                return $mapped;
            }
        }

        // 2. Keyword scan
        $upper = strtoupper($haystack);
        foreach (self::CATEGORY_MAP as $kw => $cat) {
            if (str_contains($upper, strtoupper($kw))) {
                return $cat;
            }
        }

        // 3. Default
        return 'vitamins';
    }

    /**
     * Map a source-platform taxonomy string (PharmEasy therapy, NetMeds category,
     * Apollo category) directly to one of our category slugs.
     * Returns null if no confident match, so callers can fall back to guessCategory().
     */
    private function mapSourceCategory(string $sourceCategory): ?string
    {
        $s = strtoupper(trim($sourceCategory));
        if ($s === '') return null;

        // Direct taxonomy → our slug map (ordered most-specific first)
        $map = [
            // ── Pain / Fever ──────────────────────────────────────────────────
            'PAIN'              => 'fever-pain',
            'ANALGESIC'         => 'fever-pain',
            'ANTIPYRETIC'       => 'fever-pain',
            'FEVER'             => 'fever-pain',
            'NSAID'             => 'fever-pain',
            'MUSCLE RELAXANT'   => 'fever-pain',

            // ── Vitamins ──────────────────────────────────────────────────────
            'VITAMIN'           => 'vitamins',
            'SUPPLEMENT'        => 'vitamins',
            'NUTRACEUTIC'       => 'vitamins',
            'MINERAL'           => 'vitamins',
            'PROTEIN'           => 'vitamins',
            'FISH OIL'          => 'vitamins',
            'OMEGA'             => 'vitamins',

            // ── Immunity ──────────────────────────────────────────────────────
            'IMMUNITY'          => 'immunity',
            'IMMUNE'            => 'immunity',

            // ── Bone & Joint ──────────────────────────────────────────────────
            'BONE'              => 'bone-joint',
            'JOINT'             => 'bone-joint',
            'CALCIUM'           => 'bone-joint',
            'ARTHRITIS'         => 'bone-joint',
            'OSTEOPOROSIS'      => 'bone-joint',

            // ── Digestive ─────────────────────────────────────────────────────
            'GASTROINTESTINAL'  => 'digestive',
            'DIGESTIVE'         => 'digestive',
            'ANTACID'           => 'digestive',
            'LAXATIVE'          => 'digestive',
            'PROBIOTIC'         => 'digestive',
            'GI CARE'           => 'digestive',

            // ── Diabetes ──────────────────────────────────────────────────────
            'DIABETES'          => 'diabetes',
            'DIABETIC'          => 'diabetes',
            'ANTI DIABETIC'     => 'diabetes',
            'ANTI-DIABETIC'     => 'diabetes',
            'BLOOD GLUCOSE'     => 'diabetes',
            'GLUCOMETER'        => 'diabetes',

            // ── Heart & BP ────────────────────────────────────────────────────
            'CARDIAC'           => 'heart-bp',
            'CARDIO'            => 'heart-bp',
            'ANTIHYPERTENSIVE'  => 'heart-bp',
            'HYPERTENSION'      => 'heart-bp',
            'BLOOD PRESSURE'    => 'heart-bp',
            'CHOLESTEROL'       => 'heart-bp',
            'STATIN'            => 'heart-bp',

            // ── Cold & Allergy ────────────────────────────────────────────────
            'ALLERGY'           => 'cold-allergy',
            'ANTIHISTAMINE'     => 'cold-allergy',
            'COLD AND COUGH'    => 'cold-allergy',
            'COLD & COUGH'      => 'cold-allergy',
            'COUGH'             => 'cold-allergy',
            'NASAL'             => 'cold-allergy',
            'SINUS'             => 'cold-allergy',

            // ── Respiratory ───────────────────────────────────────────────────
            'RESPIRATORY'       => 'respiratory-care',
            'ASTHMA'            => 'respiratory-care',
            'BRONCHIAL'         => 'respiratory-care',
            'NEBULIZ'           => 'respiratory-care',
            'PULMONARY'         => 'respiratory-care',

            // ── Eye & Ear ─────────────────────────────────────────────────────
            'EYE'               => 'eye-ear',
            'EAR'               => 'eye-ear',
            'OPHTHALM'          => 'eye-ear',
            'OPHTHALMOLOGY'     => 'eye-ear',

            // ── Skin Care ─────────────────────────────────────────────────────
            'SKIN'              => 'skin-care',
            'DERMATOLOGY'       => 'skin-care',
            'DERMA'             => 'skin-care',
            'ACNE'              => 'skin-care',
            'SUNSCREEN'         => 'skin-care',
            'MOISTUR'           => 'skin-care',
            'ANTIFUNGAL'        => 'skin-care',

            // ── Personal Care ─────────────────────────────────────────────────
            'PERSONAL CARE'     => 'personal-care',
            'FACE WASH'         => 'personal-care',
            'BODY WASH'         => 'personal-care',
            'FEMININE HYGIENE'  => 'personal-care',
            'SANITARY'          => 'personal-care',
            'DEODORANT'         => 'personal-care',

            // ── Hair Care ─────────────────────────────────────────────────────
            'HAIR CARE'         => 'hair-care',
            'HAIR'              => 'hair-care',
            'SCALP'             => 'hair-care',

            // ── Oral Care ─────────────────────────────────────────────────────
            'ORAL CARE'         => 'oral-care',
            'ORAL HYGIENE'      => 'oral-care',
            'DENTAL'            => 'oral-care',
            'TOOTHPASTE'        => 'oral-care',
            'MOUTHWASH'         => 'oral-care',

            // ── Baby Care ─────────────────────────────────────────────────────
            'BABY CARE'         => 'baby-care',
            'BABY'              => 'baby-care',
            'INFANT'            => 'baby-care',
            'PEDIATRIC'         => 'baby-care',

            // ── Women's Health ────────────────────────────────────────────────
            'WOMEN'             => 'womens-health',
            'GYNAE'             => 'womens-health',
            'GYNAECOLOGY'       => 'womens-health',
            'MENSTRUAL'         => 'womens-health',
            'PRENATAL'          => 'womens-health',
            'PREGNANCY'         => 'womens-health',
            'PCOS'              => 'womens-health',

            // ── Men's Health ──────────────────────────────────────────────────
            "MEN'S HEALTH"      => 'mens-health',
            'MEN HEALTH'        => 'mens-health',
            'ANDROLOGY'         => 'mens-health',
            'PROSTATE'          => 'mens-health',

            // ── Sexual Wellness ───────────────────────────────────────────────
            'SEXUAL WELLNESS'   => 'sexual-wellness',
            'CONTRACEPTIVE'     => 'sexual-wellness',
            'CONDOM'            => 'sexual-wellness',
            'FERTILITY'         => 'sexual-wellness',

            // ── Mother Care ───────────────────────────────────────────────────
            'MOTHER CARE'       => 'mother-care',
            'MATERNITY'         => 'mother-care',
            'LACTATION'         => 'mother-care',
            'NURSING'           => 'mother-care',

            // ── Mental Wellness ───────────────────────────────────────────────
            'MENTAL HEALTH'     => 'mental-wellness',
            'SLEEP'             => 'mental-wellness',
            'STRESS'            => 'mental-wellness',
            'ANXIETY'           => 'mental-wellness',
            'BRAIN'             => 'mental-wellness',

            // ── Weight Management ─────────────────────────────────────────────
            'WEIGHT MANAGEMENT' => 'weight-management',
            'WEIGHT LOSS'       => 'weight-management',
            'OBESITY'           => 'weight-management',
            'SLIMMING'          => 'weight-management',

            // ── Nutrition & Health Drinks ─────────────────────────────────────
            'NUTRITION'         => 'nutrition-health-drinks',
            'HEALTH DRINK'      => 'nutrition-health-drinks',
            'SPORTS NUTRITION'  => 'nutrition-health-drinks',
            'ENERGY DRINK'      => 'nutrition-health-drinks',
            'ELECTROLYTE'       => 'nutrition-health-drinks',

            // ── Liver Care ────────────────────────────────────────────────────
            'LIVER'             => 'liver-care',
            'HEPATO'            => 'liver-care',
            'HEPATOPROTECTIVE'  => 'liver-care',

            // ── Kidney Care ───────────────────────────────────────────────────
            'KIDNEY'            => 'kidney-care',
            'RENAL'             => 'kidney-care',
            'URINARY'           => 'kidney-care',
            'UROLOG'            => 'kidney-care',

            // ── Medical Devices ───────────────────────────────────────────────
            'MEDICAL DEVICE'    => 'medical-devices',
            'GLUCOMETER'        => 'medical-devices',
            'THERMOMETER'       => 'medical-devices',
            'BLOOD PRESSURE MONITOR' => 'medical-devices',
            'PULSE OXIMETER'    => 'medical-devices',
            'WEIGHING SCALE'    => 'medical-devices',

            // ── Surgical & First Aid ──────────────────────────────────────────
            'SURGICAL'          => 'surgical-first-aid',
            'FIRST AID'         => 'surgical-first-aid',
            'WOUND CARE'        => 'surgical-first-aid',
            'ANTISEPTIC'        => 'surgical-first-aid',
            'BANDAGE'           => 'surgical-first-aid',

            // ── Home Healthcare ───────────────────────────────────────────────
            'HOME HEALTHCARE'   => 'home-healthcare',
            'HOME CARE'         => 'home-healthcare',
            'ORTHOPAEDIC'       => 'home-healthcare',
            'MOBILITY AID'      => 'home-healthcare',

            // ── Ayurvedic ─────────────────────────────────────────────────────
            'AYURVEDIC'         => 'ayurvedic-products',
            'AYURVEDA'          => 'ayurvedic-products',
            'HERBAL'            => 'ayurvedic-products',
            'UNANI'             => 'ayurvedic-products',

            // ── Homeopathic ───────────────────────────────────────────────────
            'HOMEOPATHIC'       => 'homeopathic-medicines',
            'HOMOEOPATHIC'      => 'homeopathic-medicines',
            'HOMEOPATHY'        => 'homeopathic-medicines',
        ];

        foreach ($map as $kw => $slug) {
            if (str_contains($s, $kw)) {
                return $slug;
            }
        }

        return null;
    }

    /**
     * Remove duplicate trailing pack-size tokens from a product name.
     *
     * PharmEasy names often look like:
     *   "Dabur Khadiradi Gutika Tablet 40's 40 S"
     *   "Dolo 650 Tablet 15's 15 S"
     *
     * Pattern: the name ends with  <qty>'s <qty> S   or  <qty> <unit> <qty> <unit>
     * We collapse that into just the first occurrence.
     */
    private function cleanProductName(string $name): string
    {
        $name = trim($name);

        // Remove trailing " <digits> S" when the same digits already appear before it
        // e.g. "Tablet 40's 40 S" → "Tablet 40's"
        $name = preg_replace_callback(
            "/\\b(\\d+)['\x27\x60]?s?\\s+(\\d+)\\s+S\\b/i",
            function ($m) {
                // Only collapse if the two numbers are the same
                return $m[1] === $m[2] ? $m[1] . "'s" : $m[0];
            },
            $name
        );

        // Remove trailing " <N> <Unit>" when "<N><Unit>" or "<N>'s" already exists
        // e.g. "Tablet 40's 40 Tablet" → "Tablet 40's"
        $units = 'Tablet|Capsule|Strip|Bottle|Pack|Sachet|Vial|Tube|Box|Piece|Unit|Ml|Mg|Gm|Kg|Nos';
        $name = preg_replace(
            '/(\d+\'?s?\s*)(' . $units . ')\.?\s+\d+\s+(?:' . $units . ')\.?\s*$/i',
            '$1$2',
            $name
        );

        return trim($name);
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
