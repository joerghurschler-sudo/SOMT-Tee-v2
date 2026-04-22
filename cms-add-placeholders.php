<?php
/**
 * cms-add-placeholders.php
 * Fetches the current drinksomt.ch index.html, adds {{placeholder}} tags
 * for all editable content, and writes it back.
 * 
 * Access: https://drinksomt.ch/cms-add-placeholders.php?token=somt-cms-2026
 * After running: redirects to drinksomt.ch
 */

$TOKEN = 'somt-cms-2026';
$INDEX_URL = 'https://drinksomt.ch/index.html';
$DOCROOT = '/var/www/vhosts/joerghurschler.com/drinksomt.ch/';

if (!isset($_GET['token']) || $_GET['token'] !== $TOKEN) {
    http_response_code(401);
    die(json_encode(['error' => 'unauthorized']));
}

// Fetch current index.html
$context = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0\r\n",
        'timeout' => 30
    ]
]);

$html = @file_get_contents($INDEX_URL, false, $context);

if ($html === false) {
    die(json_encode(['error' => 'Failed to fetch index.html from drinksomt.ch']));
}

// --- Replace specific text patterns with placeholders ---

$replacements = [
    // Hero
    '<h1>kalter tee<br class="mobile-break"> aus zürich!</h1>' =>
        '<h1>{{hero_title}}</h1>',

    '<p class="hero-subtitle">zuckerfrei • lokal • frisch gebrüht</p>' =>
        '<p class="hero-subtitle">{{hero_tagline}}</p>',

    'src="img/yue-guang-bai_web_4.jpg"' =>
        'src="{{hero_image}}"',

    'alt="Yue Guan Bai – weißer Tee aus Yunnan"' =>
        'alt="{{hero_image_alt}}"',

    // Pairing section
    '<h2 class="section-title">tee-pairing:</h2>' =>
        '<h2 class="section-title">{{pairing_title}}</h2>',

    '<h3 class="section-subtitle">eine neue art<br> der speisebegleitung</h3>' =>
        '<h3 class="section-subtitle">{{pairing_subtitle}}</h3>',

    'src="img/yue-guang-bai_web_1.jpg"' =>
        'src="{{pairing_image}}"',

    'alt="Teetasting – dunkle Gläser mit Tee auf einem Tisch"' =>
        'alt="{{pairing_image_alt}}"',

    // Pairing text paragraph 1
    'tee ist nach wasser das weltweit am häufigsten konsumierte getränk. in weiten teilen asiens begleitet tee ganz selbstverständlich die mahlzeiten, so wie bei uns ein gutes glas wein. tee und wein haben viel gemeinsam: beide bringen terroir ins glas – einen unverwechselbaren charakter von boden, klima und handwerk. tee ist dabei aber noch viel variabler, denn je nach zubereitung zeigt derselbe tee ganz unterschiedliche geschmacksprofile.' =>
        '{{pairing_text1}}',

    // Pairing text paragraph 2
    'somt interessiert sich für eine neue art der speisebegleitung: die grosse vielfalt komplexer blatttees eröffnet neue möglichkeiten für alkoholfreie pairings. ein gericht kann durch die passende wahl eines bestimmten tees ruhiger, klarer oder spannender werden. beim tee-pairing bleiben gäste zudem präsent und erleben die speisen oft präziser als im klassischen wein-pairing.' =>
        '{{pairing_text2}}',

    // Pairing text paragraph 3
    'somt wirkt gern mit bei konzepten, menüs und pairing-planung.' =>
        '{{pairing_text3}}',

    // About section
    'src="img/hojicha_web_1.jpg"' =>
        'src="{{about_image}}"',

    '<h2 class="about-title">teekultur neu gedacht</h2>' =>
        '<h2 class="about-title">{{about_title}}</h2>',

    // About paragraph 1
    'somt ist ein junges tee-label aus zürich. inspiriert durch gelebte zeiten in asien, wo tee jederzeit und überall präsent ist, wurde der eklatante mangel an tee in unserem eigenen alltag sichtbar. diese lücke will somt füllen, will da hinein heissen und kalten tee giessen, aus feinen blatt- und stengel- und kräutertees aus aller welt, hauptsache lecker und aufregend und spannend im abgang. hinter somt stecken unzählige missglückte und einige gelungene tee-experimente, weiterbildungen in sensorik und in tee-wissen (ua. bei länggass-tee in bern), sowie viele arbeitsstunden in der gastronomie und unzählige gespräche über tee mit tollen menschen.' =>
        '{{about_text1}}',

    // About paragraph 2
    'und so bringt somt tee auf den tisch: wir begleiten einerseits die gastronomie bezüglich tee als speisebegleitung, beraten bei auswahl, pairing und zubereitung und suchen gemeinsam nach neuen trinkkonzepten (siehe pairing). gleichzeitig füllen wir sorgfältig ausgewählten und frisch in zürich gebrühten tee ohne zusatzstoffe in glasflaschen für den direkten ausschank an, als eigenständige alternative zu wein oder sekt (siehe boutique). perspektivisch entwickelt somt zudem eine kuratierte auswahl an no- und low-alkoholischen getränken für die begleitung von gutem essen.' =>
        '{{about_text2}}',

    // About paragraph 3
    'für fragen, bestellungen, rückmeldungen und kollaborationsmöglichkeiten kontaktiert uns gern <3' =>
        '{{about_text3}}',

    // Products
    '<h3 class="product-name">Yue Guan Bai</h3>' =>
        '<h3 class="product-name">{{product1_name}}</h3>',

    '<p class="product-flavor">floral-fruchtig</p>' =>
        '<p class="product-flavor">{{product1_flavor}}</p>',

    'src="img/yue-guang-bai_web_5.jpg"' =>
        'src="{{product1_image}}"',

    'alt="Yue Guan Bai"' =>
        'alt="{{product1_image_alt}}"',

    '<p class="product-description">Ein weisser tee aus yunnan...</p>' =>
        '<p class="product-description">{{product1_description}}</p>',

    '<p class="product-order">auf Bestellung in glasflaschen à 3.3 dl oder 7 dl.</p>' =>
        '<p class="product-order">{{product1_order}}</p>',

    '<p class="product-ingredients">Inhalt: zürich-wasser, auszug aus teeblättern. pasteurisiert, 3 monate haltbar.</p>' =>
        '<p class="product-ingredients">{{product1_ingredients}}</p>',

    '<h3 class="product-name">Hojicha</h3>' =>
        '<h3 class="product-name">{{product2_name}}</h3>',

    '<p class="product-flavor">röstig-brotig</p>' =>
        '<p class="product-flavor">{{product2_flavor}}</p>',

    'src="img/hojicha_web_2.jpg"' =>
        'src="{{product2_image}}"',

    '<p class="product-description">Pfannengerösteter bancha-tee...</p>' =>
        '<p class="product-description">{{product2_description}}</p>',

    '<h3 class="product-name">Wuyi Oolong</h3>' =>
        '<h3 class="product-name">{{product3_name}}</h3>',

    '<p class="product-flavor">mineralisch-fruchtig</p>' =>
        '<p class="product-flavor">{{product3_flavor}}</p>',

    'src="img/wuyi-oolong_web_2.jpg"' =>
        'src="{{product3_image}}"',

    '<p class="product-description">Mittelstark gerösteter wuyi oolong...</p>' =>
        '<p class="product-description">{{product3_description}}</p>',

    '<p class="product-order">auf Bestellung in glasflaschen à 3.3 dl oder 7 dl.</p>' =>
        '<p class="product-order">{{product3_order}}</p>',

    '<p class="product-ingredients">Inhalt: zürich-wasser, auszug aus teeblättern. pasteurisiert, 3 monate haltbar.</p>' =>
        '<p class="product-ingredients">{{product3_ingredients}}</p>',

    // Meta
    '<title>somt tee</title>' =>
        '<title>{{meta_title}}</title>',

    'content="Kalte Tee-Erlebnisse aus Zürich – hochoptionaler, zuckerfreier, bio Tee. Bestellung per Web oder Teetasting."' =>
        'content="{{meta_description}}"',

    'content="somt tee"' =>
        'content="{{og_title}}"',

    'content="Kalte Tee-Erlebnisse aus Zürich – hochoptionaler, zuckerfreier, bio Tee."' =>
        'content="{{og_description}}"',

    'content="https://drinksomt.ch/"' =>
        'content="{{og_url}}"',
];

$new_html = str_replace(array_keys($replacements), array_values($replacements), $html);

$bytes = file_put_contents($DOCROOT . 'index.html', $new_html);

if ($bytes === false) {
    die(json_encode(['error' => 'Failed to write index.html']));
}

// Count how many replacements were made
$replacements_made = 0;
foreach ($replacements as $old => $new) {
    if (strpos($html, $old) !== false) {
        $replacements_made++;
    }
}

echo json_encode([
    'success' => true,
    'bytes_written' => $bytes,
    'replacements_made' => $replacements_made,
    'total_placeholders' => count($replacements),
    'redirect' => 'https://drinksomt.ch/'
], JSON_PRETTY_PRINT);

echo "\n<script>setTimeout(() => { window.location.href = 'https://drinksomt.ch/'; }, 3000);</script>";
