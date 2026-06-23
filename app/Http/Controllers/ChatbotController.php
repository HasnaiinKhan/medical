<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    // ── Emergency keywords - always handled first ──────────────────────────────
    private const EMERGENCY_KEYWORDS = [
        'chest pain','heart attack','can\'t breathe','cannot breathe','difficulty breathing',
        'severe allergic','anaphylaxis','overdose','seizure','unconscious','stroke',
        'not breathing','stopped breathing','emergency','ambulance',
    ];

    // ── Symptom → DB search keywords ──────────────────────────────────────────
    private const SYMPTOM_MAP = [
        'headache'        => ['paracetamol','dolo','crocin','combiflam','ibuprofen','saridon','analgesic','pain'],
        'fever'           => ['paracetamol','dolo','crocin','ibuprofen','fever','antipyretic','meftal'],
        'body pain'       => ['ibuprofen','combiflam','diclofenac','pain','analgesic','muscle','brufen'],
        'migraine'        => ['sumatriptan','saridon','migraine','paracetamol','ibuprofen'],
        'toothache'       => ['ibuprofen','diclofenac','pain','dental'],
        'period pain'     => ['meftal','ibuprofen','combiflam','period','cramp'],
        'back pain'       => ['diclofenac','ibuprofen','combiflam','muscle','pain'],
        'joint pain'      => ['diclofenac','ibuprofen','joint','arthritis','pain','glucosamine'],
        'cold'            => ['cold','cetirizine','antihistamine','nasal','decongestant','sinarest','allegra'],
        'cough'           => ['cough','dextromethorphan','benadryl','honitus','expectorant'],
        'sore throat'     => ['throat','strepsils','betadine','lozenges','cough'],
        'runny nose'      => ['cetirizine','antihistamine','nasal','decongestant','cold','allegra'],
        'blocked nose'    => ['nasal','decongestant','otrivin','sinarest'],
        'sneezing'        => ['cetirizine','antihistamine','allegra','montair','cold'],
        'flu'             => ['paracetamol','ibuprofen','cetirizine','cold','flu','fever','cough'],
        'asthma'          => ['salbutamol','inhaler','bronchodilator','asthma','levolin','asthalin'],
        'stomach ache'    => ['antacid','omeprazole','pantoprazole','digestion','stomach','spasm'],
        'acidity'         => ['antacid','omeprazole','pantoprazole','ranitidine','gelusil','digene'],
        'gas'             => ['gas','antacid','digestion','simethicone','eno','gelusil'],
        'bloating'        => ['gas','digestion','antacid','simethicone'],
        'diarrhea'        => ['ors','electrolyte','loperamide','norflox','diarrhea','probiotic'],
        'loose motion'    => ['ors','electrolyte','loperamide','norflox','diarrhea'],
        'constipation'    => ['laxative','lactulose','constipation','isabgol','dulcolax'],
        'vomiting'        => ['ondansetron','domperidone','vomiting','nausea','emeset'],
        'nausea'          => ['ondansetron','domperidone','nausea','vomiting','emeset'],
        'indigestion'     => ['antacid','digestion','omeprazole','pantoprazole','digene'],
        'heartburn'       => ['antacid','omeprazole','pantoprazole','acidity'],
        'ulcer'           => ['omeprazole','pantoprazole','ranitidine','ulcer','antacid'],
        'rash'            => ['antihistamine','cetirizine','skin','rash','calamine','hydrocortisone'],
        'itching'         => ['antihistamine','cetirizine','calamine','skin','itch'],
        'allergy'         => ['cetirizine','antihistamine','allegra','montair','allergy'],
        'acne'            => ['acne','benzoyl','clindamycin','skin','pimple'],
        'fungal'          => ['antifungal','clotrimazole','fluconazole','fungal','candid'],
        'wound'           => ['antiseptic','betadine','bandage','wound','dressing'],
        'burn'            => ['burn','silver sulfadiazine','antiseptic','wound'],
        'eye infection'   => ['eye','drops','ciprofloxacin','conjunctivitis','ophthalmic'],
        'red eyes'        => ['eye','drops','antihistamine','conjunctivitis'],
        'ear pain'        => ['ear','drops','wax','pain','otitis'],
        'ear infection'   => ['ear','drops','antibiotic','otitis'],
        'diabetes'        => ['metformin','glipizide','insulin','diabetes','sugar'],
        'blood sugar'     => ['metformin','glipizide','insulin','diabetes','sugar'],
        'blood pressure'  => ['amlodipine','atenolol','losartan','hypertension','bp'],
        'hypertension'    => ['amlodipine','atenolol','losartan','hypertension'],
        'cholesterol'     => ['atorvastatin','rosuvastatin','cholesterol','statin'],
        'weakness'        => ['vitamin','iron','b12','supplement','energy','multivitamin','tonic'],
        'fatigue'         => ['vitamin','iron','b12','supplement','energy','multivitamin'],
        'vitamin d'       => ['vitamin d','cholecalciferol','calcirol','bone','calcium'],
        'calcium'         => ['calcium','vitamin d','bone','supplement','shelcal'],
        'iron deficiency' => ['iron','ferrous','haemoglobin','anaemia','supplement'],
        'anaemia'         => ['iron','ferrous','haemoglobin','anaemia','supplement'],
        'immunity'        => ['vitamin c','zinc','immunity','supplement','multivitamin'],
        'insomnia'        => ['sleep','melatonin','insomnia','sedative'],
        'anxiety'         => ['anxiety','stress','ashwagandha','supplement'],
        'stress'          => ['stress','ashwagandha','supplement'],
        'infection'       => ['antibiotic','amoxicillin','azithromycin','infection','ciprofloxacin'],
        'urine infection' => ['ciprofloxacin','norflox','uti','urinary','antibiotic'],
        'uti'             => ['ciprofloxacin','norflox','uti','urinary','antibiotic'],
        'throat infection'=> ['amoxicillin','azithromycin','antibiotic','throat','strepsils'],
    ];

    // ── High-risk / prescription-only keywords ─────────────────────────────────
    private const PRESCRIPTION_KEYWORDS = [
        'antibiotic','amoxicillin','azithromycin','ciprofloxacin','metformin',
        'insulin','atenolol','amlodipine','losartan','atorvastatin','rosuvastatin',
        'salbutamol','inhaler','controlled','morphine','codeine','tramadol',
        'alprazolam','diazepam','clonazepam','sleeping pill',
    ];

    // ── Order-related intents that trigger order selection flow ───────────────
    private const ORDER_RELATED_INTENTS = ['delivery', 'payment', 'return', 'order_status', 'order'];

    // ── FAQ intents ────────────────────────────────────────────────────────────
    private const FAQ_MAP = [
        'delivery' => [
            'keywords' => ['delivery','deliver','shipping','ship','how long','when will','arrive','dispatch'],
            'reply'    => "🚚 <strong>Delivery Info:</strong><br>• Free delivery on orders above ₹500<br>• We deliver across 32+ pincodes in Ahmedabad<br>• Typical delivery time: same day to next day<br>• Enter your pincode at the top of the page to check availability.",
        ],
        'payment' => [
            'keywords' => ['payment','pay','cod','cash on delivery','online payment','razorpay','upi','card'],
            'reply'    => "💳 <strong>Payment Options:</strong><br>• Cash on Delivery (COD)<br>• Online payment via Razorpay (UPI, cards, net banking)<br>Both options are available at checkout.",
        ],
        'return' => [
            'keywords' => ['return','refund','cancel','cancellation','money back','exchange'],
            'reply'    => "↩️ <strong>Returns & Refunds:</strong><br>You can request a refund within 30 days of your order. Go to <em>My Orders</em> and select the order to raise a refund request. Our team will review it promptly.",
        ],
        'prescription' => [
            'keywords' => ['prescription','rx','doctor','need prescription','require prescription'],
            'reply'    => "📋 <strong>Prescription Medicines:</strong><br>Some medicines require a valid prescription from a licensed doctor. These are marked with an <strong>Rx</strong> badge on the product page. Please have your prescription ready when ordering such medicines.",
        ],
        'contact' => [
            'keywords' => ['contact','phone','call','whatsapp','support','help','reach','address'],
            'reply'    => "📞 <strong>Contact Us:</strong><br>• WhatsApp: Click the green WhatsApp button on this page<br>• Address: Shop 54/04, opp. Unigold Hospital, Jivraj Park, Ahmedabad – 380051<br>• We typically reply within minutes on WhatsApp.",
        ],
        'store' => [
            'keywords' => ['store','shop','location','where','open','timing','hours','near me'],
            'reply'    => "📍 <strong>Store Info:</strong><br>Rx Plus 365<br>Shop 54/04, opp. Unigold Hospital Main Gate, near Jivraj, Jivraj Park, Ahmedabad – 380051<br><br>We also deliver online across Ahmedabad. Enter your pincode to check availability.",
        ],
        'navigate' => [
            'keywords' => ['how to order','how do i','place order','add to cart','checkout','register','login','sign in','sign up','account'],
            'reply'    => "🛒 <strong>How to Order:</strong><br>1. Browse or search for medicines<br>2. Click <em>Add to Cart</em><br>3. Go to Cart and click <em>Checkout</em><br>4. Enter your delivery details and choose payment<br>5. Confirm your order - done! 🎉<br><br>Need an account? Click <em>Register</em> in the top menu.",
        ],
    ];

    // ── Greetings / small talk ─────────────────────────────────────────────────
    private const GREETINGS = ['hi','hello','hey','hii','helo','namaste','good morning','good evening','good afternoon','sup'];

    private const HOW_ARE_YOU = [
        'how are you','how r you','how are u','how r u',
        'kese ho','kaise ho','kese hain','kaise hain','kaisa hai','kaisi ho',
        'how do you do','you doing','hows it going','how\'s it going',
        'all good','you good','aap kaise','aap kaisa',
    ];

    private const WHATS_UP = [
        'what\'s up','whats up','wassup','sup','what up',
        'kya chal raha','kya haal','kya hal','sab theek','sab thik',
    ];
    private const THANKS     = ['thank','thanks','thank you','thankyou','thx','ty','great help','helpful'];
    private const BYE        = ['bye','goodbye','see you','take care','ok bye','cya','later'];
    private const OK         = ['ok','okay','alright','got it','sure','noted','understood','fine','cool','great','perfect','sounds good','no problem','np'];

    // ── Symptom safety disclaimer ──────────────────────────────────────────────
    private const SYMPTOM_DISCLAIMER = "⚠️ <em>I'm not a doctor and cannot diagnose or prescribe. If symptoms are severe or persistent, please consult a licensed healthcare professional.</em>";

    // ── High-risk medicine disclaimer ─────────────────────────────────────────
    private const RX_DISCLAIMER = "📋 <em>Some of these medicines may require a valid prescription. Please consult your doctor before purchasing prescription medicines.</em>";

    // ─────────────────────────────────────────────────────────────────────────
    public function chat(Request $request): JsonResponse
    {
        $raw     = trim((string) $request->input('message', ''));
        $message = strtolower($raw);

        if ($message === '') {
            return $this->reply("Please type your symptom, medicine name, or question - I'm here to help! 😊");
        }

        // ── Fuzzy-correct the message before any matching ──────────────────────
        $corrected        = $this->fuzzyCorrect($message);
        $wasCorrected     = $corrected !== $message;
        $message          = $corrected;

        // 1. Emergency check - always first
        foreach (self::EMERGENCY_KEYWORDS as $kw) {
            if (str_contains($message, $kw)) {
                return $this->reply(
                    "🚨 <strong>This sounds like a medical emergency.</strong><br><br>" .
                    "Please call <strong>112</strong> (emergency) or go to the nearest hospital immediately.<br><br>" .
                    "Do not wait - your safety comes first. 🏥"
                );
            }
        }

        // 2. "How are you?" / "Kese ho?" - conversational check-in
        foreach (self::HOW_ARE_YOU as $h) {
            if (str_contains($message, $h)) {
                return $this->reply(
                    "I'm doing great, thanks for asking! 😊<br><br>" .
                    "I'm <strong>MedCare AI</strong>, always here and ready to help you with medicines, orders, and health queries.<br><br>" .
                    "How can I help you today? 💊"
                );
            }
        }

        // 2b. "What's up?" / "Kya chal raha?" - casual check-in
        foreach (self::WHATS_UP as $w) {
            if (str_contains($message, $w)) {
                return $this->reply(
                    "Not much, just here to help! 😄<br><br>" .
                    "Ask me anything - medicines, order tracking, delivery info, or health tips. I've got you covered! 💊"
                );
            }
        }

        // 3. Standard greetings
        foreach (self::GREETINGS as $g) {
            if (str_contains($message, $g)) {
                $timeGreeting = $this->getTimeGreeting();
                return $this->reply(
                    "{$timeGreeting} 👋 I'm <strong>MedCare AI</strong>, your smart pharmacy assistant at Rx Plus 365.<br><br>" .
                    "I can help you:<br>" .
                    "• 💊 Find medicines & health products<br>" .
                    "• 📦 Track your orders<br>" .
                    "• 🚚 Answer delivery & payment questions<br>" .
                    "• 🏪 Help you navigate the store<br><br>" .
                    "What can I help you with today?"
                );
            }
        }

        // 4. Thanks
        foreach (self::THANKS as $t) {
            if (str_contains($message, $t)) {
                return $this->reply("You're welcome! 😊 Stay healthy and take care. Feel free to ask anytime you need help.");
            }
        }

        // 5. Bye
        foreach (self::BYE as $b) {
            if (str_contains($message, $b)) {
                return $this->reply("Take care! 💊 Come back anytime you need medicines or have questions. Stay healthy! 🌟");
            }
        }

        // 6. Ok / acknowledgement
        foreach (self::OK as $o) {
            if ($message === $o) {   // exact match only - avoids "ok bye", "okay let me check" etc.
                return $this->reply(
                    "👍 Sure! Let me know if there's anything else I can help you with.<br><br>" .
                    "You can ask me about medicines, symptoms, orders, or delivery anytime. 😊"
                );
            }
        }

        // 5. Order tracking
        if ($this->matchesAny($message, ['track','order','my order','order status','where is my','order id','order number'])) {
            return $this->handleOrderTracking($message, $raw);
        }

        // 6. FAQ intents - for order-related topics, ask which order if logged in
        foreach (self::FAQ_MAP as $intent => $data) {
            if ($this->matchesAny($message, $data['keywords'])) {
                if (in_array($intent, self::ORDER_RELATED_INTENTS) && Auth::check()) {
                    return $this->askWhichOrder($data['reply']);
                }
                return $this->reply($data['reply']);
            }
        }

        // 7. Direct medicine name search (e.g. "do you have dolo 650")
        if ($this->matchesAny($message, ['do you have','is there','available','stock','find','search','looking for','need','want','buy','get me'])) {
            $searchTerm = $this->extractSearchTerm($raw);
            if ($searchTerm) {
                return $this->searchAndRespond($searchTerm, $message, true);
            }
        }

        // 8. Symptom-based search
        $keywords = $this->extractSymptomKeywords($message);
        if (!empty($keywords)) {
            return $this->searchAndRespond(implode(' ', array_slice($keywords, 0, 3)), $message, false, $keywords);
        }

        // 9. Generic fallback - try searching the raw message
        $products = $this->searchMedicines(array_filter(explode(' ', $message), fn($w) => strlen($w) >= 3));
        if ($products->isNotEmpty()) {
            return response()->json([
                'reply'      => "Here's what I found for <strong>\"" . e($raw) . "\"</strong> in our store:",
                'products'   => $products->values(),
                'search_url' => route('medicines.index', ['q' => $raw]),
            ]);
        }

        // 10. No match
        return response()->json([
            'reply'      => "I couldn't find anything specific for <strong>\"" . e($raw) . "\"</strong> right now.<br><br>You can:<br>• Try different keywords<br>• <a href=\"" . route('medicines.index') . "\" style=\"color:#2563eb;font-weight:700;\">Browse all medicines →</a><br>• Contact us on WhatsApp for personalised help",
            'products'   => [],
            'search_url' => route('medicines.index', ['q' => $raw]),
        ]);
    }

    // ── Fetch a specific order's full details (called via AJAX when user selects an order) ──
    public function orderDetail(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'reply' => "Please <a href=\"" . route('login') . "\" style=\"color:#2563eb;font-weight:700;\">sign in</a> to view your order details.",
                'order' => null,
            ]);
        }

        $orderId = $request->input('order_id');
        $order   = Order::with('items.medicine')
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json([
                'reply' => "I couldn't find that order. Please check and try again.",
                'order' => null,
            ]);
        }

        return response()->json([
            'reply' => "Here are the details for <strong>Order #{$order->order_number}</strong>:",
            'order' => $this->buildOrderDetailArray($order),
        ]);
    }

    // ── Order tracking handler ─────────────────────────────────────────────────
    private function handleOrderTracking(string $message, string $raw): JsonResponse
    {
        // If user is logged in and hasn't provided a specific order number, ask which order
        if (Auth::check()) {
            // Try to extract a real order number from the message (ORD-XXXX format or 6+ digit number)
            // Note: pattern is NOT case-insensitive to avoid matching plain words like "order"
            preg_match('/\b(ORD[-]\w+|\d{6,})\b/', $raw, $matches);
            $orderNumber = $matches[1] ?? null;

            if ($orderNumber) {
                // User gave a specific order number - look it up
                $order = Order::where('order_number', $orderNumber)
                               ->where('user_id', Auth::id())
                               ->first()
                         ?? Order::find((int) $orderNumber);

                if ($order) {
                    // Return the full detail directly
                    return response()->json([
                        'reply' => "Here are the details for <strong>Order #{$order->order_number}</strong>:",
                        'order' => $this->buildOrderDetailArray($order),
                    ]);
                }

                return $this->reply(
                    "I couldn't find an order with number <strong>\"" . e($orderNumber) . "\"</strong>.<br><br>" .
                    "Please check the order number and try again, or visit <a href=\"" . route('orders.index') . "\" style=\"color:#2563eb;font-weight:700;\">My Orders</a> to see all your orders."
                );
            }

            // No order number - ask which order they want to process
            return $this->askWhichOrder();
        }

        // Not logged in
        return $this->reply(
            "📦 To view your orders, please <a href=\"" . route('login') . "\" style=\"color:#2563eb;font-weight:700;\">sign in</a> first.<br><br>" .
            "Once logged in, I can show you all your orders and their delivery status."
        );
    }

    // ── Ask user which order they want to process ─────────────────────────────
    private function askWhichOrder(string $contextReply = ''): JsonResponse
    {
        $orders = Auth::user()->orders()->orderBy('created_at', 'desc')->get();

        if ($orders->isEmpty()) {
            $msg = $contextReply
                ? $contextReply . "<br><br>📦 You don't have any orders yet. <a href=\"" . route('medicines.index') . "\" style=\"color:#2563eb;font-weight:700;\">Start shopping →</a>"
                : "📦 You don't have any orders yet. <a href=\"" . route('medicines.index') . "\" style=\"color:#2563eb;font-weight:700;\">Start shopping →</a>";
            return $this->reply($msg);
        }

        $prefix = $contextReply ? $contextReply . "<br><br>" : "";
        $reply  = $prefix . "📦 <strong>Which order would you like to process?</strong><br>Here are your recent orders - tap one to see full details:";

        $orderList = $orders->take(5)->map(fn($o) => [
            'id'           => $o->id,
            'order_number' => $o->order_number,
            'status'       => $o->status,
            'total'        => number_format($o->totalRupees(), 2),
            'date'         => $o->created_at->format('d M Y'),
            'items_count'  => $o->items()->count(),
        ])->toArray();

        return response()->json([
            'reply'           => $reply,
            'order_selection' => true,
            'orders'          => $orderList,
        ]);
    }

    // ── Build a full order detail array (shared by orderDetail & handleOrderTracking) ──
    private function buildOrderDetailArray(Order $order): array
    {
        $order->loadMissing('items.medicine');

        $statusEmoji = [
            'placed'                 => '📋',
            'confirmed'              => '✅',
            'shipped'                => '🚚',
            'delivered'              => '🎉',
            'cancelled'              => '❌',
            'payment_failed'         => '⚠️',
            'Refund_requested' => '🔄',
            'refund_initiated'       => '💸',
            'refunded'               => '✅',
        ][$order->status] ?? '📦';

        $items = $order->items->map(fn($item) => [
            'name'     => $item->medicine_name_snapshot ?? ($item->medicine?->name ?? 'Unknown'),
            'qty'      => $item->quantity,
            'price'    => number_format($item->unit_price_paise / 100, 2),
            'subtotal' => number_format($item->line_total_paise / 100, 2),
        ])->toArray();

        return [
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'status'           => $order->status,
            'status_label'     => ucwords(str_replace('_', ' ', $order->status)),
            'status_emoji'     => $statusEmoji,
            'total'            => number_format($order->totalRupees(), 2),
            'subtotal'         => number_format($order->subtotal_paise / 100, 2),
            'delivery_fee'     => number_format($order->delivery_fee_paise / 100, 2),
            'date'             => $order->created_at->format('d M Y, h:i A'),
            'payment_method'   => $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment',
            'payment_status'   => ucfirst($order->payment_status ?? 'N/A'),
            'delivery_address' => trim(implode(', ', array_filter([
                $order->address_line1,
                $order->address_line2,
                $order->delivery_area,
                $order->delivery_pin,
            ]))),
            'customer_name'    => $order->customer_name,
            'customer_phone'   => $order->customer_phone,
            'items'            => $items,
            'items_count'      => count($items),
            'can_refund'       => $order->canRequestRefund(),
            'refund_url'       => route('refunds.create', $order),
            'order_url'        => route('orders.show', $order),
        ];
    }
    private function searchAndRespond(string $searchTerm, string $message, bool $directSearch, array $keywords = []): JsonResponse
    {
        $kws      = $keywords ?: array_filter(explode(' ', strtolower($searchTerm)), fn($w) => strlen($w) >= 3);
        $products = $this->searchMedicines($kws);

        $hasPrescriptionRisk = $this->matchesAny($message, self::PRESCRIPTION_KEYWORDS);
        $isSymptomBased      = !$directSearch && !empty($keywords);

        if ($products->isEmpty()) {
            $reply = "I couldn't find <strong>\"" . e($searchTerm) . "\"</strong> in our store right now.<br><br>" .
                     "You can <a href=\"" . route('medicines.index', ['q' => $searchTerm]) . "\" style=\"color:#2563eb;font-weight:700;\">search our full catalogue →</a> or contact us on WhatsApp for help.";
            return response()->json(['reply' => $reply, 'products' => [], 'search_url' => route('medicines.index', ['q' => $searchTerm])]);
        }

        $reply = $this->buildSymptomReply($message, $products->count(), $isSymptomBased);

        if ($isSymptomBased) {
            $reply .= '<br><br>' . self::SYMPTOM_DISCLAIMER;
        }
        if ($hasPrescriptionRisk) {
            $reply .= '<br><br>' . self::RX_DISCLAIMER;
        }

        return response()->json([
            'reply'      => $reply,
            'products'   => $products->values(),
            'search_url' => route('medicines.index', ['q' => $searchTerm]),
        ]);
    }

    // ── Extract symptom keywords ───────────────────────────────────────────────
    private function extractSymptomKeywords(string $message): array
    {
        $keywords = [];
        foreach (self::SYMPTOM_MAP as $symptom => $words) {
            if (str_contains($message, $symptom)) {
                $keywords = array_merge($keywords, $words);
            }
        }
        // Partial word match
        foreach (explode(' ', $message) as $word) {
            if (strlen($word) < 3) continue;
            foreach (self::SYMPTOM_MAP as $symptom => $kws) {
                if (str_contains($symptom, $word)) {
                    $keywords = array_merge($keywords, $kws);
                }
            }
        }
        return array_unique($keywords);
    }

    // ── Extract search term from "do you have X" style messages ───────────────
    private function extractSearchTerm(string $raw): ?string
    {
        $patterns = [
            '/(?:do you have|is there|find|search for|looking for|need|want|buy|get me|available)\s+(.+)/i',
            '/(?:i need|i want|i\'m looking for|i am looking for)\s+(.+)/i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $raw, $m)) {
                return trim($m[1]);
            }
        }
        return null;
    }

    // ── Search medicines in DB ─────────────────────────────────────────────────
    private function searchMedicines(array $keywords): \Illuminate\Support\Collection
    {
        $keywords = array_values(array_filter($keywords, fn($k) => strlen(trim($k)) >= 2));
        if (empty($keywords)) return collect();

        return Medicine::with('category')
            ->where('stock', '>', 0)
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $kw = trim($kw);
                    $q->orWhere('name', 'like', "%{$kw}%")
                      ->orWhere('manufacturer', 'like', "%{$kw}%")
                      ->orWhere('description', 'like', "%{$kw}%")
                      ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$kw}%"));
                }
            })
            ->orderBy('price_paise')
            ->limit(5)
            ->get()
            ->map(fn(Medicine $m) => [
                'id'                    => $m->id,
                'name'                  => $m->name,
                'manufacturer'          => $m->manufacturer,
                'category'              => $m->category?->name,
                'price'                 => number_format($m->priceRupees(), 2),
                'mrp'                   => number_format($m->mrpRupees(), 2),
                'discount'              => $m->discountPercent(),
                'image'                 => $m->imageUrl(),
                'url'                   => route('medicines.show', $m),
                'prescription_required' => $m->prescription_required,
            ]);
    }

    // ── Build symptom reply ────────────────────────────────────────────────────
    private function buildSymptomReply(string $message, int $count, bool $isSymptom): string
    {
        $symptomReplies = [
            'headache'      => "I'm sorry you have a headache. Here are some commonly used products for headache relief available in our store:",
            'fever'         => "I'm sorry you're not feeling well. Here are products commonly used for fever relief in our store:",
            'cold'          => "Sorry to hear you have a cold. Here are some products that may help with cold & congestion:",
            'cough'         => "Here are some cough relief products available in our store:",
            'acidity'       => "For acidity & heartburn, here are some antacids and digestive aids from our store:",
            'stomach ache'  => "For stomach discomfort, here are some options from our store:",
            'diarrhea'      => "Staying hydrated is important. Here are some products that may help:",
            'loose motion'  => "Staying hydrated is important. Here are some products that may help:",
            'vomiting'      => "For nausea & vomiting, here are some options from our store:",
            'nausea'        => "For nausea, here are some options from our store:",
            'allergy'       => "For allergy relief, here are some antihistamines and related products:",
            'diabetes'      => "For diabetes management, here are some products from our store:",
            'blood pressure'=> "For blood pressure management, here are some options:",
            'weakness'      => "For weakness & fatigue, these supplements may help:",
            'fatigue'       => "For fatigue & low energy, these supplements may help:",
            'infection'     => "For infections, here are some options. Please note that antibiotics require a doctor's prescription:",
            'pain'          => "For pain relief, here are some options from our store:",
            'skin'          => "For skin concerns, here are some products from our store:",
            'eye'           => "For eye-related concerns, here are some products:",
            'ear'           => "For ear-related concerns, here are some products:",
        ];

        foreach ($symptomReplies as $symptom => $reply) {
            if (str_contains($message, $symptom)) {
                return $reply;
            }
        }

        return $isSymptom
            ? "Based on what you described, here are some relevant products from our store:"
            : "Here's what I found in our store:";
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function matchesAny(string $message, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (str_contains($message, $kw)) return true;
        }
        return false;
    }

    private function reply(string $html, array $products = [], ?string $searchUrl = null): JsonResponse
    {
        $data = ['reply' => $html, 'products' => $products];
        if ($searchUrl) $data['search_url'] = $searchUrl;
        return response()->json($data);
    }

    // ── Fuzzy spell-correction ─────────────────────────────────────────────────
    // Corrects each word in the message against a vocabulary of all known keywords.
    // Only replaces a word if levenshtein distance ≤ threshold AND similarity ≥ 70%.
    private function fuzzyCorrect(string $message): string
    {
        $vocab = $this->buildVocabulary();
        $words = explode(' ', $message);

        $corrected = array_map(function (string $word) use ($vocab) {
            // Skip very short words, numbers, and words already in vocabulary
            if (strlen($word) <= 2 || is_numeric($word) || in_array($word, $vocab, true)) {
                return $word;
            }

            $bestMatch  = null;
            $bestDist   = PHP_INT_MAX;
            $threshold  = match (true) {
                strlen($word) <= 4 => 1,   // short words: max 1 typo
                strlen($word) <= 7 => 2,   // medium words: max 2 typos
                default            => 3,   // long words: max 3 typos
            };

            foreach ($vocab as $candidate) {
                // Skip candidates much shorter or longer than the input
                if (abs(strlen($candidate) - strlen($word)) > $threshold + 1) continue;

                $dist = levenshtein($word, $candidate);
                if ($dist < $bestDist) {
                    $bestDist  = $dist;
                    $bestMatch = $candidate;
                }
            }

            if ($bestMatch === null || $bestDist > $threshold) {
                return $word;
            }

            // Extra guard: require ≥ 70% similarity
            similar_text($word, $bestMatch, $pct);
            if ($pct < 70.0) {
                return $word;
            }

            return $bestMatch;
        }, $words);

        return implode(' ', $corrected);
    }

    // ── Build the vocabulary for fuzzy matching ────────────────────────────────
    private function buildVocabulary(): array
    {
        static $vocab = null;
        if ($vocab !== null) return $vocab;

        $words = [];

        // Symptom keys and their search keywords
        foreach (self::SYMPTOM_MAP as $symptom => $kws) {
            foreach (explode(' ', $symptom) as $w) $words[] = $w;
            foreach ($kws as $kw) {
                foreach (explode(' ', $kw) as $w) $words[] = $w;
            }
        }

        // FAQ keywords
        foreach (self::FAQ_MAP as $data) {
            foreach ($data['keywords'] as $kw) {
                foreach (explode(' ', $kw) as $w) $words[] = $w;
            }
        }

        // Greetings / small talk
        foreach (array_merge(self::GREETINGS, self::HOW_ARE_YOU, self::WHATS_UP, self::THANKS, self::BYE) as $phrase) {
            foreach (explode(' ', $phrase) as $w) $words[] = $w;
        }

        // Emergency keywords
        foreach (self::EMERGENCY_KEYWORDS as $phrase) {
            foreach (explode(' ', $phrase) as $w) $words[] = $w;
        }

        // Common medicine/order words
        $extra = [
            'order','orders','track','tracking','delivery','payment','refund','cancel',
            'medicine','medicines','tablet','capsule','syrup','cream','drops','inhaler',
            'prescription','doctor','pharmacy','stock','available','price','buy','cart',
            'checkout','login','register','account','address','pincode','shipping',
        ];
        foreach ($extra as $w) $words[] = $w;

        // Deduplicate, lowercase, filter short words
        $vocab = array_values(array_unique(
            array_filter(
                array_map('strtolower', $words),
                fn($w) => strlen($w) >= 3
            )
        ));

        return $vocab;
    }

    // ── Time-based greeting ────────────────────────────────────────────────────
    private function getTimeGreeting(): string
    {
        $hour = (int) now()->format('G');
        if ($hour >= 5 && $hour < 12)  return 'Good morning!';
        if ($hour >= 12 && $hour < 17) return 'Good afternoon!';
        if ($hour >= 17 && $hour < 21) return 'Good evening!';
        return 'Hello!';
    }

}
