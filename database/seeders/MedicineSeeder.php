<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Fever & Pain'           => ['slug' => 'fever-pain',    'icon' => '🌡️'],
            'Vitamins & Supplements' => ['slug' => 'vitamins',       'icon' => '💊'],
            'Digestive Care'         => ['slug' => 'digestive',      'icon' => '🫁'],
            'Diabetes Care'          => ['slug' => 'diabetes',       'icon' => '🩸'],
            'Heart & BP'             => ['slug' => 'heart-bp',       'icon' => '❤️'],
            'Skin Care'              => ['slug' => 'skin',           'icon' => '✨'],
            'Cold & Allergy'         => ['slug' => 'cold-allergy',   'icon' => '🤧'],
            'Eye & Ear Care'         => ['slug' => 'eye-ear',        'icon' => '👁️'],
            'Bone & Joint'           => ['slug' => 'bone-joint',     'icon' => '🦴'],
            'Immunity Boosters'      => ['slug' => 'immunity',       'icon' => '🛡️'],
        ];

        $catIds = [];
        foreach ($categories as $name => $data) {
            $catIds[$data['slug']] = Category::query()->updateOrCreate(
                ['slug' => $data['slug']],
                ['name' => $name]
            )->id;
        }

        // Curated Unsplash photo IDs per category (medicine/pharmacy themed)
        // Format: https://images.unsplash.com/photo-{id}?w=400&h=400&fit=crop&auto=format
        $images = [
            // Fever & Pain
            'dolo-650-tablet'                   => 'https://d1s24u4ln0wd0i.cloudfront.net/med/3606/DOLO%20650MG%20TAB%201X15_1.webp',
            'crocin-advance-tablet'             => 'https://d1s24u4ln0wd0i.cloudfront.net/med_op/2947_1.webp',  
            'combiflam-tablet'                  => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/I00375/combiflam-strip-of-20-tablets-box-front-1-1756885218-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
            'brufen-400mg-tablet'               => 'https://d1s24u4ln0wd0i.cloudfront.net/med/23645/brufen-400mg-tablet-20s_17371089741b28f1de6240468e9406c7a00e0bf52f.webp',
            'voveran-50mg-tablet'               => 'https://d1s24u4ln0wd0i.cloudfront.net/med_op/14733_2.webp',

            // Vitamins & Supplements
            'zincovit-tablet'                   => 'https://cdn01.pharmeasy.in/dam/products_otc/188996/zincovit-strip-of-15-tablets-green-2-1702990444.jpg?dim=700x0&dpr=1&q=100',

            'becosules-capsule'                 => 'https://d1s24u4ln0wd0i.cloudfront.net/med/1315/BECOSULES%20CAP%201X20_1.webp',
            'supradyn-tablet'                   => 'https://d1s24u4ln0wd0i.cloudfront.net/med/12691/SUPRADYN%20DAILY%20TAB%201X15_1.webp',

            'evion-400mg-capsule'               => 'https://cdn01.pharmeasy.in/dam/products_otc/S04683/evion-400mg-strip-of-20-capsules-2-1767003545.jpg?dim=700x0&dpr=1&q=100',
            'shelcal-500-tablet'                => 'https://d1s24u4ln0wd0i.cloudfront.net/med/12152/shelcal-os-tablet-15s_17270293806048a3d5cba34e0bac60841e148b8607.webp',
            'revital-h-capsule'                 => 'https://cdn01.pharmeasy.in/dam/products_otc/281751/revital-men-complete-multivitamin-with-natural-ginseng-60-capsules-6.01-1749723606.jpg?dim=700x0&dpr=1&q=100',
            'depura-60k-vitamin-d3'             => 'https://d1s24u4ln0wd0i.cloudfront.net/med/3291/DEPURA%2060K%20ORAL%20SOLUTION%205%20ML_1.webp',

            // Digestive Care
            'digene-tablet-mint'                => 'https://cdn01.pharmeasy.in/dam/products_otc/270620/digene-acidity-gas-relief-tablets-15s-mint-flavour-2-1671740678.jpg?dim=700x0&dpr=1&q=100',
            'pantocid-40mg-tablet'              => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/I01274/pantocid-40mg-strip-of-15-tablets-box-front-1-1756885419-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
            'omez-20mg-capsule'                 => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/I00600/omez-10mg-strip-of-15-capsules-front-2-1756826457-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
            'gaviscon-liquid'                   => 'https://d1s24u4ln0wd0i.cloudfront.net/med/5411/GAVISCON%20PEP%20SYP%20150ML_1.webp',
            'ors-l-orange-sachet'               => 'https://d1s24u4ln0wd0i.cloudfront.net/med_op/1700820537656076399ec47.webp',
            'enterogermina-suspension'          => 'https://d1s24u4ln0wd0i.cloudfront.net/med/4184/ENTEROGERMINA%20ORAL%20SUSPENSION%205ML_1.webp',
            'himalaya-liv-52-ds-tablet'         => '',

            // Diabetes Care
            'glucon-d-regular-200g'             => 'https://cdn01.pharmeasy.in/dam/products_otc/T27588/glucon-d-regular-200-gm-free-glucon-d-50-gm-2-1742544309.jpg?dim=700x0&dpr=1&q=100',
            'dr-morepen-bg-03-glucometer'       => 'https://cdn01.pharmeasy.in/dam/products_otc/I05582/dr-morepen-gluco-one-blood-glucose-test-strip-bg-03-50-nos-2-1766379753.jpg?dim=700x0&dpr=1&q=100',
            'accu-chek-active-strips-50'        => 'https://cdn01.pharmeasy.in/dam/products_otc/000665/accu-chek-active-glucometer-test-strips-box-of-50-6.1-1734607563.jpg?dim=700x0&dpr=1&q=100',

            // Heart & BP
            'ecosprin-75mg-tablet'              => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/064425/ecosprin-75mg-strip-of-14-tablets-box-front-1-1756894428-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
            'telma-40mg-tablet'                 => 'https://cdn01.pharmeasy.in/dam/products/I02514/telma-40mg-strip-of-30-tablets-2-1641530972.jpg?dim=320x320&dpr=1&q=100',
            'atorva-20mg-tablet'                => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/O78617/atorva-20mg-strip-of-15-tablets-box-front-1-1756954857-non-watermarked.jpg?dim=320x320&dpr=1&q=100',

            // Skin Care
            'cetaphil-sun-spf-50-gel'           => 'https://cdn01.pharmeasy.in/dam/products_otc/L28261/cetaphil-sun-spf-50-light-gel-sensitive-skin-50-ml-6.1-1768556573.jpg?dim=700x0&dpr=1&q=100',
            'la-shield-sunscreen-spf-40'        => 'https://cdn01.pharmeasy.in/dam/products_otc/C28750/la-shield-sunscreen-gel-expert-acne-protect-spf-40-pa-50-gm-6.1-1771240508.jpg?dim=700x0&dpr=1&q=100',
            'abzorb-dusting-powder'             => 'https://cdn01.pharmeasy.in/dam/products_otc/Q84402/abzorb-total-skin-relief-dusting-powder-20-extra-bottle-of-120gm-6.1-1718197859.jpg?dim=700x0&dpr=1&q=100',
            'himalaya-neem-face-wash'           => 'https://cdn01.pharmeasy.in/dam/products_otc/090735/himalaya-purifying-neem-prevents-pimples-face-wash-100-ml-6.1-1775911541.jpg?dim=700x0&dpr=1&q=100',

            // Cold & Allergy
            'sinarest-tablet'                   => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/L55502/sinarest-new-strip-of-15-tablets-box-front-1-1756972717-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
            'allegra-120mg-tablet'              => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/005715/allegra-120mg-strip-of-10-tablets-box-front-1-1756894044-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
            'montek-lc-tablet'                  => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/N44296/montek-lc-strip-of-15-tablets-front-2-1756991764-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
            'nasivion-nasal-drops'              => 'https://cdn01.pharmeasy.in/dam/products_otc/375380/nasivion-classic-adult-nasal-drops-fast-long-lasting-relief-from-blocked-nose-10-ml-6.1-1775911716.jpg?dim=700x0&dpr=1&q=100',

            // Eye & Ear Care
            'systane-ultra-eye-drops'           => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/254899/systane-ultra-bottle-of-10ml-eye-drops-box-front-1-1756099902-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
            'cipla-ciplox-eye-drops'            => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/254580/ciplox-03-e-e-drops-10ml-combo-3-1756826283-non-watermarked.jpg?dim=640x640&q=75',
            'waxsol-ear-drops'                  => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/183950/waxolve-ear-drops-10ml-combo-3-1756459777-non-watermarked.jpg?dim=320x320&dpr=1&q=100',

            // Bone & Joint
            'volini-gel-30g'                    => 'https://cdn01.pharmeasy.in/dam/products_otc/183150/volini-pain-relief-gel-tube-of-30-g-6.1-1712725915.jpg?dim=700x0&dpr=1&q=100',
            'moov-pain-relief-cream'            => 'https://cdn01.pharmeasy.in/dam/products_otc/I35672/moov-instant-pain-relief-cream-50g-suitable-for-back-pain-joint-pain-knee-pain-muscle-pain-6.01-1750416619.jpg?dim=700x0&dpr=1&q=100',
            'calcimax-forte-tablet'             => 'https://cdn01.pharmeasy.in/dam/products_otc/207833/calcimax-forte-plus-strip-of-30-tablets-3-1671741011.jpg?dim=700x0&dpr=1&q=100',

            // Immunity
            'chyawanprash-500g'                 => 'https://cdn01.pharmeasy.in/dam/products_otc/O67098/baidyanath-chyawanprash-special-ayurvedic-immunity-booster-500g-with-75g-extra-2-1756960391.jpg?dim=700x0&dpr=1&q=100',
            'septilin-tablet'                   => 'https://cdn01.pharmeasy.in/dam/products_otc/158085/himalaya-septilin-tablets-60s-6.1-1743139248.jpg?dim=700x0&dpr=1&q=100',
            'zinc-vitamin-c-tablet'             => 'https://cdn01.pharmeasy.in/dam/productsnowatermark/N69076/vitchew-cz-orange-flavour-sugar-free-strip-of-15-chewable-tablets-front-2-1756972671-non-watermarked.jpg?dim=320x320&dpr=1&q=100',
        ];

        $items = [
            // Fever & Pain
            ['n' => 'Dolo 650 Tablet',            'm' => 'Micro Labs',    'c' => 'fever-pain',   'mrp' => 45,   'p' => 38,   'rx' => false,
             'd' => 'Paracetamol 650mg — fast-acting relief for fever, headache, and mild to moderate pain. Gentle on the stomach. Strip of 15 tablets.'],
            ['n' => 'Crocin Advance Tablet',       'm' => 'GSK',          'c' => 'fever-pain',   'mrp' => 52,   'p' => 44,   'rx' => false,
             'd' => 'Paracetamol 500mg with advanced formulation for quick fever and pain relief. Trusted by doctors for over 50 years. Strip of 20 tablets.'],
            ['n' => 'Combiflam Tablet',            'm' => 'Sanofi',       'c' => 'fever-pain',   'mrp' => 48,   'p' => 41,   'rx' => false,
             'd' => 'Ibuprofen + Paracetamol combination for effective relief from pain, inflammation, and fever. Ideal for muscle aches and dental pain. Strip of 20 tablets.'],
            ['n' => 'Brufen 400mg Tablet',         'm' => 'Abbott',       'c' => 'fever-pain',   'mrp' => 65,   'p' => 55,   'rx' => false,
             'd' => 'Ibuprofen 400mg — NSAID for relief of mild to moderate pain, fever, and inflammation. Effective for arthritis, menstrual cramps, and sports injuries.'],
            ['n' => 'Voveran 50mg Tablet',         'm' => 'Novartis',     'c' => 'fever-pain',   'mrp' => 88,   'p' => 75,   'rx' => true,
             'd' => 'Diclofenac Sodium 50mg — powerful anti-inflammatory for joint pain, back pain, and post-operative pain. Prescription required.'],

            // Vitamins & Supplements
            ['n' => 'Zincovit Tablet',             'm' => 'Apex Labs',    'c' => 'vitamins',     'mrp' => 108,  'p' => 98,   'rx' => false,
             'd' => 'Multivitamin with Zinc — comprehensive formula with 13 vitamins, 11 minerals, and grape seed extract. Boosts immunity and energy levels. Pack of 15 tablets.'],
            ['n' => 'Becosules Capsule',            'm' => 'Pfizer',       'c' => 'vitamins',     'mrp' => 95,   'p' => 86,   'rx' => false,
             'd' => 'B-Complex with Vitamin C — essential B vitamins plus Vitamin C for energy metabolism, healthy skin, and nervous system support. Pack of 20 capsules.'],
            ['n' => 'Supradyn Tablet',              'm' => 'Bayer',        'c' => 'vitamins',     'mrp' => 69,   'p' => 62,   'rx' => false,
             'd' => 'Daily multivitamin and multimineral supplement for overall health and vitality. Contains 11 vitamins and 8 minerals. Strip of 15 tablets.'],
            ['n' => 'Evion 400mg Capsule',          'm' => 'Merck',        'c' => 'vitamins',     'mrp' => 90,   'p' => 81,   'rx' => false,
             'd' => 'Vitamin E 400mg — powerful antioxidant for skin health, immune function, and cell protection. Helps reduce oxidative stress. Pack of 10 capsules.'],
            ['n' => 'Shelcal 500 Tablet',           'm' => 'Torrent',      'c' => 'vitamins',     'mrp' => 164,  'p' => 148,  'rx' => false,
             'd' => 'Calcium Carbonate 500mg + Vitamin D3 — essential for strong bones and teeth. Helps prevent osteoporosis and calcium deficiency. Strip of 15 tablets.'],
            ['n' => 'Revital H Capsule',            'm' => 'Sun Pharma',   'c' => 'vitamins',     'mrp' => 360,  'p' => 330,  'rx' => false,
             'd' => 'Ginseng with multivitamins and minerals — daily health supplement for energy, stamina, and mental alertness. Pack of 30 capsules.'],
            ['n' => 'Depura 60k Vitamin D3',        'm' => 'Aristo',       'c' => 'vitamins',     'mrp' => 117,  'p' => 100,  'rx' => false,
             'd' => 'Cholecalciferol 60,000 IU — high-dose Vitamin D3 sachet for treating Vitamin D deficiency. Supports bone health and immune function.'],

            // Digestive Care
            ['n' => 'Digene Tablet (Mint)',         'm' => 'Abbott',       'c' => 'digestive',    'mrp' => 42,   'p' => 36,   'rx' => false,
             'd' => 'Antacid with simethicone — fast relief from acidity, heartburn, and gas. Mint flavour for a refreshing taste. Chewable tablet, strip of 15.'],
            ['n' => 'Pantocid 40mg Tablet',         'm' => 'Sun Pharma',   'c' => 'digestive',    'mrp' => 180,  'p' => 155,  'rx' => true,
             'd' => 'Pantoprazole 40mg — proton pump inhibitor for treatment of GERD, peptic ulcers, and acid-related disorders. Take before meals. Prescription required.'],
            ['n' => 'Omez 20mg Capsule',            'm' => 'Dr Reddy',     'c' => 'digestive',    'mrp' => 120,  'p' => 102,  'rx' => true,
             'd' => 'Omeprazole 20mg — reduces stomach acid production for treatment of ulcers, GERD, and Zollinger-Ellison syndrome. Prescription required.'],
            ['n' => 'Gaviscon Liquid',              'm' => 'Reckitt',      'c' => 'digestive',    'mrp' => 211,  'p' => 197,  'rx' => false,
             'd' => 'Sodium alginate antacid — forms a protective raft on stomach contents to prevent acid reflux. Ideal for heartburn during pregnancy. 200ml bottle.'],
            ['n' => 'ORS-L Orange Sachet',          'm' => 'FDC',          'c' => 'digestive',    'mrp' => 55,   'p' => 48,   'rx' => false,
             'd' => 'Oral Rehydration Salts with electrolytes — replenishes fluids and minerals lost during diarrhoea and vomiting. Orange flavour. Pack of 10 sachets.'],
            ['n' => 'Enterogermina Suspension',     'm' => 'Sanofi',       'c' => 'digestive',    'mrp' => 747,  'p' => 687,  'rx' => false,
             'd' => 'Bacillus clausii probiotic — restores intestinal flora after antibiotic therapy or diarrhoea. Safe for all ages including infants. Pack of 10 vials.'],
            ['n' => 'Himalaya Liv.52 DS Tablet',    'm' => 'Himalaya',     'c' => 'digestive',    'mrp' => 195,  'p' => 175,  'rx' => false,
             'd' => 'Herbal liver tonic — protects liver cells from toxins, improves appetite, and aids digestion. Double-strength formula. Pack of 60 tablets.'],

            // Diabetes Care
            ['n' => 'Glucon-D Regular 200g',        'm' => 'Zydus',        'c' => 'diabetes',     'mrp' => 79,   'p' => 72,   'rx' => false,
             'd' => 'Instant glucose energy drink — provides quick energy boost for fatigue and weakness. Contains Vitamin C and calcium. Regular flavour, 200g tin.'],
            ['n' => 'Dr Morepen BG-03 Glucometer',  'm' => 'Morepen',      'c' => 'diabetes',     'mrp' => 1415, 'p' => 750,  'rx' => false,
             'd' => 'Blood glucose monitoring kit — accurate readings in 5 seconds with minimal blood sample. Includes 10 test strips, lancets, and lancing device.'],
            ['n' => 'Accu-Chek Active Strips (50)', 'm' => 'Roche',        'c' => 'diabetes',     'mrp' => 1115, 'p' => 1026, 'rx' => false,
             'd' => 'Blood glucose test strips for Accu-Chek Active glucometer — 50 strips per pack. No coding required. Results in 5 seconds.'],

            // Heart & BP
            ['n' => 'Ecosprin 75mg Tablet',         'm' => 'USV',          'c' => 'heart-bp',     'mrp' => 45,   'p' => 40,   'rx' => true,
             'd' => 'Aspirin 75mg — antiplatelet agent for prevention of heart attacks and strokes in high-risk patients. Take with food. Prescription required.'],
            ['n' => 'Telma 40mg Tablet',             'm' => 'Glenmark',     'c' => 'heart-bp',     'mrp' => 220,  'p' => 195,  'rx' => true,
             'd' => 'Telmisartan 40mg — ARB for treatment of hypertension and reduction of cardiovascular risk. Once-daily dosing. Prescription required.'],
            ['n' => 'Atorva 20mg Tablet',            'm' => 'Zydus',        'c' => 'heart-bp',     'mrp' => 180,  'p' => 155,  'rx' => true,
             'd' => 'Atorvastatin 20mg — statin for lowering LDL cholesterol and triglycerides. Reduces risk of heart disease and stroke. Prescription required.'],

            // Skin Care
            ['n' => 'Cetaphil Sun SPF 50 Gel',      'm' => 'Galderma',     'c' => 'skin',         'mrp' => 1299, 'p' => 1235, 'rx' => false,
             'd' => 'Broad-spectrum SPF 50 sunscreen gel — lightweight, non-greasy formula for daily sun protection. Suitable for sensitive and oily skin. 75g tube.'],
            ['n' => 'La Shield Sunscreen SPF 40',   'm' => 'Akumentis',    'c' => 'skin',         'mrp' => 850,  'p' => 816,  'rx' => false,
             'd' => 'Mineral sunscreen with SPF 40 — physical UV filter for sensitive skin. Water-resistant, fragrance-free formula. Recommended by dermatologists. 75g.'],
            ['n' => 'Abzorb Dusting Powder',        'm' => 'Sun Pharma',   'c' => 'skin',         'mrp' => 164,  'p' => 156,  'rx' => false,
             'd' => 'Antifungal dusting powder with clotrimazole — prevents and treats fungal infections, prickly heat, and body odour. Keeps skin dry and fresh. 100g.'],
            ['n' => 'Himalaya Neem Face Wash',      'm' => 'Himalaya',     'c' => 'skin',         'mrp' => 175,  'p' => 158,  'rx' => false,
             'd' => 'Purifying neem face wash — removes excess oil, unclogs pores, and prevents acne. Gentle herbal formula suitable for daily use. 150ml tube.'],

            // Cold & Allergy
            ['n' => 'Sinarest Tablet',              'm' => 'Centaur',      'c' => 'cold-allergy', 'mrp' => 110,  'p' => 99,   'rx' => false,
             'd' => 'Paracetamol + Phenylephrine + Chlorpheniramine — comprehensive cold relief for nasal congestion, runny nose, sneezing, and fever. Strip of 10 tablets.'],
            ['n' => 'Allegra 120mg Tablet',         'm' => 'Sanofi',       'c' => 'cold-allergy', 'mrp' => 240,  'p' => 218,  'rx' => true,
             'd' => 'Fexofenadine 120mg — non-drowsy antihistamine for allergic rhinitis, urticaria, and seasonal allergies. Once-daily dosing. Prescription required.'],
            ['n' => 'Montek LC Tablet',             'm' => 'Sun Pharma',   'c' => 'cold-allergy', 'mrp' => 320,  'p' => 285,  'rx' => true,
             'd' => 'Montelukast + Levocetirizine — dual-action for allergic rhinitis and chronic urticaria. Reduces inflammation and blocks histamine. Prescription required.'],
            ['n' => 'Nasivion Nasal Drops',         'm' => 'Merck',        'c' => 'cold-allergy', 'mrp' => 85,   'p' => 76,   'rx' => false,
             'd' => 'Oxymetazoline nasal drops — fast-acting decongestant for blocked nose due to cold, sinusitis, and hay fever. Relief within minutes. 10ml bottle.'],

            // Eye & Ear Care
            ['n' => 'Systane Ultra Eye Drops',      'm' => 'Alcon',        'c' => 'eye-ear',      'mrp' => 450,  'p' => 420,  'rx' => false,
             'd' => 'Lubricating eye drops for dry eye relief — provides long-lasting comfort and protection. Suitable for contact lens wearers. 10ml bottle.'],
            ['n' => 'Cipla Ciplox Eye Drops',       'm' => 'Cipla',        'c' => 'eye-ear',      'mrp' => 65,   'p' => 58,   'rx' => true,
             'd' => 'Ciprofloxacin 0.3% eye drops — antibiotic for bacterial conjunctivitis and corneal ulcers. Prescription required. 5ml bottle.'],
            ['n' => 'Waxsol Ear Drops',             'm' => 'Napp Pharma',  'c' => 'eye-ear',      'mrp' => 120,  'p' => 108,  'rx' => false,
             'd' => 'Docusate sodium ear drops — softens and removes hardened ear wax safely. Use for 2 nights before syringing. 10ml bottle.'],

            // Bone & Joint
            ['n' => 'Volini Gel 30g',               'm' => 'Sun Pharma',   'c' => 'bone-joint',   'mrp' => 185,  'p' => 165,  'rx' => false,
             'd' => 'Diclofenac topical gel — fast pain relief for muscle aches, joint pain, sprains, and sports injuries. Apply 3-4 times daily. 30g tube.'],
            ['n' => 'Moov Pain Relief Cream',       'm' => 'Reckitt',      'c' => 'bone-joint',   'mrp' => 145,  'p' => 130,  'rx' => false,
             'd' => 'Ayurvedic pain relief cream with wintergreen oil, turpentine, and eucalyptus — provides warm relief for backache, neck pain, and joint stiffness. 50g.'],
            ['n' => 'Calcimax Forte Tablet',        'm' => 'Meyer',        'c' => 'bone-joint',   'mrp' => 280,  'p' => 252,  'rx' => false,
             'd' => 'Calcium + Vitamin D3 + Magnesium — comprehensive bone health supplement. Prevents osteoporosis and supports muscle function. Strip of 15 tablets.'],

            // Immunity Boosters
            ['n' => 'Chyawanprash 500g',            'm' => 'Dabur',        'c' => 'immunity',     'mrp' => 285,  'p' => 256,  'rx' => false,
             'd' => 'Ayurvedic immunity booster with 40+ herbs including Amla, Ashwagandha, and Giloy. Strengthens immunity, improves stamina, and promotes overall wellness.'],
            ['n' => 'Septilin Tablet',              'm' => 'Himalaya',     'c' => 'immunity',     'mrp' => 165,  'p' => 148,  'rx' => false,
             'd' => 'Herbal immunomodulator with Guduchi, Shankh Pushpi, and Licorice — enhances immune response and helps fight recurrent infections. Pack of 60 tablets.'],
            ['n' => 'Zinc + Vitamin C Tablet',      'm' => 'Cipla',        'c' => 'immunity',     'mrp' => 195,  'p' => 175,  'rx' => false,
             'd' => 'Effervescent Zinc 10mg + Vitamin C 500mg — daily immunity support. Dissolve in water for a refreshing orange drink. Pack of 20 effervescent tablets.'],
        ];

        foreach ($items as $item) {
            $slug = Str::slug($item['n']);
            Medicine::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id'           => $catIds[$item['c']],
                    'name'                  => $item['n'],
                    'manufacturer'          => $item['m'],
                    'description'           => $item['d'],
                    'mrp_paise'             => (int) ($item['mrp'] * 100),
                    'price_paise'           => (int) ($item['p'] * 100),
                    'prescription_required' => $item['rx'],
                    'stock'                 => rand(50, 500),
                    'image_url'             => $images[$slug] ?? null,
                ]
            );
        }
    }
}
