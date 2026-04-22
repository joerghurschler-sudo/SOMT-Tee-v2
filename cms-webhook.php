<?php
/**
 * SOMT CMS Webhook
 * Empfängt Content-Updates und schreibt index.html
 * 
 * Usage: POST mit JSON body an diese Datei
 * Authorization: Bearer token im Header
 */

define('AUTH_TOKEN', 'somt-cms-2026');
define('INDEX_PATH', __DIR__ . '/index.html');
define('BACKUP_PATH', __DIR__ . '/index.html.bak');

// CORS und JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Auth
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!str_starts_with($auth, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Missing authorization']);
    exit;
}
$token = trim(substr($auth, 7));
if ($token !== AUTH_TOKEN) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

// Nur POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST required']);
    exit;
}

// JSON lesen
$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Backup erstellen
if (file_exists(INDEX_PATH)) {
    copy(INDEX_PATH, BACKUP_PATH);
}

// Template mit Platzhaltern
$html = <<<'HTML'
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{title}}</title>
  <meta name="description" content="{{meta_description}}">
  <meta property="og:title" content="{{og_title}}">
  <meta property="og:description" content="{{og_description}}">
  <meta property="og:image" content="{{og_image}}">
  <meta property="og:url" content="{{og_url}}">
  <meta property="og:type" content="website">
  <meta name="robots" content="index, follow">
  <meta name="theme-color" content="#ffffff">
  <link rel="canonical" href="{{canonical_url}}">
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "{{title}}",
    "url": "{{og_url}}",
    "image": "{{og_image}}",
    "description": "{{meta_description}}",
    "email": "info@drinksomt.ch",
    "sameAs": ["https://www.instagram.com/somt_ch/"],
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Zürich",
      "addressCountry": "CH"
    }
  }
  </script>
  <link rel="icon" type="image/png" href="img/Somt-Logo_oSchrift_7.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,700;1,400&family=Fjalla+One&family=Space+Grotesk:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="header header--hero">
    <div class="header-socials">
      <a href="mailto:info@drinksomt.ch">
        <img src="img/mail-icon.svg" alt="Mail" class="header-icon">
      </a>
      <a href="https://www.instagram.com/somt_ch/" target="_blank" rel="noopener">
        <img src="img/instagram-icon.svg" alt="Instagram" class="header-icon">
      </a>
    </div>
    <a href="#home" class="logo">
      <img src="img/Somt-Logo_mitSchrift_hand_trans.png" alt="SOMT Logo" class="logo-img">
    </a>
    <nav class="nav nav--header">
      <a href="#boutique">boutique</a>
      <a href="#pairing">pairing</a>
      <a href="#about">about</a>
    </nav>
  </header>

  <nav class="nav nav--side">
    <a href="#boutique">boutique</a>
    <a href="#pairing">pairing</a>
    <a href="#about">about</a>
  </nav>

  <section id="home" class="hero-section">
    <div class="hero-text">
      <h1>{{hero_title}}</h1>
    </div>
    <div class="hero-content">
      <img src="{{hero_image}}" alt="{{hero_image_alt}}">
    </div>
    <div class="hero-text-2">
      <h1>{{hero_tagline}}</h1>
    </div>
  </section>

  <section id="boutique" class="section boutique">
    <div class="products">
      <article class="product product--image-left">
        <div class="product-image">
          <img src="{{product1_image}}" alt="{{product1_name}}">
        </div>
        <div class="product-content">
          <h3>{{product1_name}}</h3>
          <p class="taste">{{product1_taste}}</p>
          <p class="product-desc">{{product1_description}}</p>
          <p class="product-order"><u>{{product1_order}}</u></p>
          <p class="product-details"><i><b>Inhalt</b>: {{product1_ingredients}}</i></p>
        </div>
      </article>

      <article class="product product--image-right">
        <div class="product-image">
          <img src="{{product2_image}}" alt="{{product2_name}}" class="hojicha-img">
        </div>
        <div class="product-content">
          <h3>{{product2_name}}</h3>
          <p class="taste">{{product2_taste}}</p>
          <p class="product-desc">{{product2_description}}</p>
          <p class="product-order"><u>{{product2_order}}</u></p>
          <p class="product-details"><i><b>Inhalt</b>: {{product2_ingredients}}</i></p>
        </div>
      </article>

      <article class="product product--image-left">
        <div class="product-image">
          <img src="{{product3_image}}" alt="{{product3_name}}" class="wuyi-img">
        </div>
        <div class="product-content">
          <h3>{{product3_name}}</h3>
          <p class="taste">{{product3_taste}}</p>
          <p class="product-desc">{{product3_description}}</p>
          <p class="product-order"><u>{{product3_order}}</u></p>
          <p class="product-details"><i><b>Inhalt</b>: {{product3_ingredients}}</i></p>
        </div>
      </article>
    </div>
  </section>

  <section id="pairing" class="section pairing">
    <div class="pairing-hero">
      <h1><span class="pairing-title-large">{{pairing_title_large}}</span><br><span class="pairing-subtitle">{{pairing_subtitle}}</span></h1>
    </div>
    <div class="pairing-content">
      <img src="{{pairing_image}}" alt="{{pairing_image_alt}}">
      <div class="pairing-text">
        <p>{{pairing_p1}}</p>
        <p>{{pairing_p2}}</p>
        <p>{{pairing_p3}}</p>
      </div>
    </div>
  </section>

  <section class="section about">
    <img src="{{about_image}}" alt="Hojicha Tee" class="about-img">
    <h1 id="about">{{about_title}}</h1>
    <div class="about-text">
      <div class="about-abstand"></div>
      <p>{{about_p1}}</p>
      <p>{{about_p2}}</p>
      <p>{{about_p3}}</p>
    </div>
  </section>

  <div style="height: 5rem;"></div>
  <footer class="footer">
    <div class="footer-content">
      <i>© {{copyright_year}} Somt</i>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
HTML;

// Ersetze alle Platzhalter
foreach ($data as $key => $value) {
    $html = str_replace('{{' . $key . '}}', $value, $html);
}

// Schreibe index.html
$result = file_put_contents(INDEX_PATH, $html);
if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to write file', 'path' => INDEX_PATH]);
    exit;
}

echo json_encode([
    'success' => true,
    'bytes_written' => $result,
    'updated_fields' => count($data),
    'timestamp' => date('c')
]);
