<?php

declare(strict_types=1);

function hanzo_supported_languages(): array
{
    return [
        'en' => ['label' => 'English', 'html' => 'en'],
        'sw' => ['label' => 'Kiswahili', 'html' => 'sw'],
        'zh' => ['label' => 'Chinese', 'html' => 'zh-CN'],
    ];
}

function hanzo_default_language(): string
{
    return 'en';
}

function hanzo_language_from_request(): ?string
{
    $raw = $_GET['lang'] ?? null;
    if (!is_string($raw)) {
        return null;
    }
    $lang = strtolower(trim($raw));
    return array_key_exists($lang, hanzo_supported_languages()) ? $lang : null;
}

function hanzo_current_language(): string
{
    $lang = $_SESSION['hanzo_lang'] ?? null;
    if (is_string($lang) && array_key_exists($lang, hanzo_supported_languages())) {
        return $lang;
    }
    return hanzo_default_language();
}

function hanzo_set_language(string $lang): void
{
    if (!array_key_exists($lang, hanzo_supported_languages())) {
        return;
    }
    $_SESSION['hanzo_lang'] = $lang;
    setcookie('hanzo_lang', $lang, [
        'expires' => time() + (86400 * 365),
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function hanzo_i18n_bootstrap(): void
{
    if (!isset($_SESSION['hanzo_lang']) && isset($_COOKIE['hanzo_lang']) && is_string($_COOKIE['hanzo_lang'])) {
        $cookieLang = strtolower(trim($_COOKIE['hanzo_lang']));
        if (array_key_exists($cookieLang, hanzo_supported_languages())) {
            $_SESSION['hanzo_lang'] = $cookieLang;
        }
    }
    if (!isset($_SESSION['hanzo_lang'])) {
        $_SESSION['hanzo_lang'] = hanzo_default_language();
    }

    $requested = hanzo_language_from_request();
    if ($requested === null) {
        return;
    }

    hanzo_set_language($requested);

    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $parts = parse_url($uri);
    $path = (string) ($parts['path'] ?? '/');
    $query = [];
    if (isset($parts['query'])) {
        parse_str((string) $parts['query'], $query);
    }
    unset($query['lang']);
    $next = $path;
    if ($query !== []) {
        $next .= '?' . http_build_query($query);
    }
    header('Location: ' . $next);
    exit;
}

function hanzo_html_lang(): string
{
    $langs = hanzo_supported_languages();
    $lang = hanzo_current_language();
    return (string) ($langs[$lang]['html'] ?? 'en');
}

function hanzo_language_label(string $lang): string
{
    $langs = hanzo_supported_languages();
    return (string) ($langs[$lang]['label'] ?? $lang);
}

function hanzo_lang_switch_url(string $lang): string
{
    if (!array_key_exists($lang, hanzo_supported_languages())) {
        $lang = hanzo_default_language();
    }
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $parts = parse_url($uri);
    $path = (string) ($parts['path'] ?? '/');
    $query = [];
    if (isset($parts['query'])) {
        parse_str((string) $parts['query'], $query);
    }
    $query['lang'] = $lang;
    return $path . '?' . http_build_query($query);
}

function hanzo_i18n_replace_pairs(): array
{
    $sw = [
        'B2B sourcing for Tanzania & East Africa' => 'Ununuzi wa B2B kwa Tanzania na Afrika Mashariki',
        'Categories' => 'Makundi',
        'Why HANZO?' => 'Kwa nini HANZO?',
        'Verified suppliers, structured quotations, and clearing support — without exposing direct factory contacts on the public catalogue.' => 'Wasambazaji waliothibitishwa, nukuu zilizopangwa, na msaada wa uondoshaji mizigo — bila kuonyesha mawasiliano ya moja kwa moja ya kiwanda kwenye katalogi ya umma.',
        'Source smarter from Asia to East Africa' => 'Nunua kwa ufanisi zaidi kutoka Asia hadi Afrika Mashariki',
        'Browse verified product lines, send inquiries, and receive official HANZO quotations with transparent landed-cost breakdowns.' => 'Vinjari bidhaa zilizothibitishwa, tuma maombi, na upokee nukuu rasmi za HANZO zenye mgawanyo wazi wa gharama za kufikisha bidhaa.',
        'Explore categories' => 'Chunguza makundi',
        'Selected trending' => 'Zinazovuma zilizochaguliwa',
        'Hot selling' => 'Zinazouzwa sana',
        'Product directory' => 'Orodha ya bidhaa',
        'No products found.' => 'Hakuna bidhaa zilizopatikana.',
        'Create account' => 'Fungua akaunti',
        'Browse categories' => 'Vinjari makundi',
        'Popular sectors' => 'Sekta maarufu',
        'Compliance' => 'Uzingatiaji',
        'All rights reserved.' => 'Haki zote zimehifadhiwa.',
        'Profile' => 'Wasifu',
        'My profile' => 'Wasifu wangu',
        'Dashboard' => 'Dashibodi',
        'Orders' => 'Maagizo',
        'Quotations' => 'Nukuu',
        'Payments' => 'Malipo',
        'Documents' => 'Nyaraka',
        'Shipping' => 'Usafirishaji',
        'Factory workspace' => 'Eneo la kiwanda',
        'Admin panel' => 'Paneli ya msimamizi',
        'Reports & analytics' => 'Ripoti na uchanganuzi',
        'Pending review' => 'Inasubiri mapitio',
        'With supplier' => 'Kwa msambazaji',
        'Quoted — action needed' => 'Nukuu imetolewa — hatua inahitajika',
        'Accepted' => 'Imekubaliwa',
        'In production' => 'Uzalishaji unaendelea',
        'Quality control' => 'Udhibiti wa ubora',
        'Shipped' => 'Imesafirishwa',
        'In customs' => 'Iko forodhani',
        'Delivered' => 'Imewasilishwa',
        'Cancelled' => 'Imeghairiwa',
    ];
    $zh = [
        'B2B sourcing for Tanzania & East Africa' => '面向坦桑尼亚与东非的 B2B 采购',
        'Categories' => '分类',
        'Why HANZO?' => '为什么选择 HANZO？',
        'Verified suppliers, structured quotations, and clearing support — without exposing direct factory contacts on the public catalogue.' => '认证供应商、结构化报价和清关支持——在公开目录中不暴露工厂直接联系方式。',
        'Source smarter from Asia to East Africa' => '从亚洲到东非，更聪明地采购',
        'Browse verified product lines, send inquiries, and receive official HANZO quotations with transparent landed-cost breakdowns.' => '浏览认证产品线，发送询价，并获得 HANZO 官方报价及透明的到岸成本明细。',
        'Explore categories' => '探索分类',
        'Selected trending' => '精选热销',
        'Hot selling' => '热卖商品',
        'Product directory' => '产品目录',
        'No products found.' => '未找到产品。',
        'Create account' => '创建账户',
        'Browse categories' => '浏览分类',
        'Popular sectors' => '热门行业',
        'Compliance' => '合规',
        'All rights reserved.' => '保留所有权利。',
        'Profile' => '个人资料',
        'My profile' => '我的资料',
        'Dashboard' => '仪表板',
        'Orders' => '订单',
        'Quotations' => '报价',
        'Payments' => '付款',
        'Documents' => '文件',
        'Shipping' => '物流',
        'Factory workspace' => '工厂工作区',
        'Admin panel' => '管理面板',
        'Reports & analytics' => '报表与分析',
        'Pending review' => '待审核',
        'With supplier' => '供应商处理中',
        'Quoted — action needed' => '已报价——需要操作',
        'Accepted' => '已接受',
        'In production' => '生产中',
        'Quality control' => '质量检测',
        'Shipped' => '已发货',
        'In customs' => '清关中',
        'Delivered' => '已送达',
        'Cancelled' => '已取消',
    ];
    return [
        'sw' => $sw,
        'zh' => $zh,
    ];
}

function hanzo_i18n_buffer_callback(string $html): string
{
    $lang = hanzo_current_language();
    if ($lang === 'en') {
        return $html;
    }
    $all = hanzo_i18n_replace_pairs();
    $pairs = $all[$lang] ?? [];
    if ($pairs === []) {
        return $html;
    }
    return strtr($html, $pairs);
}

function hanzo_start_i18n_output_buffer(): void
{
    static $started = false;
    if ($started) {
        return;
    }
    $started = true;
    if (PHP_SAPI === 'cli' || hanzo_current_language() === 'en') {
        return;
    }
    ob_start('hanzo_i18n_buffer_callback');
}

function t(string $key): string
{
    $lang = hanzo_current_language();
    $en = [
        'nav.buyer_support' => 'Buyer support',
        'nav.tagline' => 'East Africa sourcing desk · USD quotes · Verified suppliers via HANZO',
        'nav.login' => 'Log in',
        'nav.register_buyer' => 'Register as buyer',
        'nav.categories' => 'Categories',
        'nav.browse' => 'Browse',
        'nav.join_free' => 'Join free',
        'nav.home' => 'Home',
        'nav.product_directory' => 'Product directory',
        'nav.sectors' => 'Sectors',
        'nav.view_all_sectors' => 'View all sectors',
        'nav.search_products' => 'Search products',
        'nav.my_inquiries' => 'My inquiries',
        'nav.factory_workspace' => 'Factory workspace',
        'nav.admin_panel' => 'Admin panel',
        'nav.view_site' => 'View site',
        'nav.logout' => 'Logout',
        'nav.language' => 'Language',
        'search.placeholder' => 'What are you sourcing today?',
        'search.all_categories' => 'All categories',
        'search.search' => 'Search',
        'nav.brand_subtitle' => 'B2B sourcing for Tanzania & East Africa',
        'footer.buyers' => 'Buyers',
        'footer.create_account' => 'Create account',
        'footer.browse_categories' => 'Browse categories',
        'footer.dashboard' => 'Dashboard',
        'footer.popular_sectors' => 'Popular sectors',
        'footer.compliance' => 'Compliance',
        'footer.compliance_text' => 'Supplier identities and pricing are managed internally. Public listings do not display direct factory contact details.',
        'account.signed_in' => 'Signed in',
        'account.order_updates' => 'Order updates',
        'account.profile_settings' => 'Profile & settings',
        'account.log_out' => 'Log out',
        'order.pending' => 'Pending review',
        'order.assigned' => 'With supplier',
        'order.quoted' => 'Quoted — action needed',
        'order.accepted' => 'Accepted',
        'order.in_production' => 'In production',
        'order.quality_control' => 'Quality control',
        'order.shipped' => 'Shipped',
        'order.in_customs' => 'In customs',
        'order.delivered' => 'Delivered',
        'order.cancelled' => 'Cancelled',
        'quotation.draft' => 'Draft',
        'quotation.sent' => 'Awaiting your response',
        'quotation.accepted' => 'Accepted',
        'quotation.rejected' => 'Rejected',
        'quotation.expired' => 'Expired',
        'payment.pending' => 'Awaiting verification',
        'payment.verified' => 'Verified',
        'payment.rejected' => 'Rejected',
    ];
    $sw = [
        'nav.buyer_support' => 'Msaada wa wanunuzi',
        'nav.tagline' => 'Kitengo cha ununuzi Afrika Mashariki · Nukuu za USD · Wasambazaji waliothibitishwa na HANZO',
        'nav.login' => 'Ingia',
        'nav.register_buyer' => 'Jisajili kama mnunuzi',
        'nav.categories' => 'Makundi',
        'nav.browse' => 'Vinjari',
        'nav.join_free' => 'Jiunge bure',
        'nav.home' => 'Nyumbani',
        'nav.product_directory' => 'Orodha ya bidhaa',
        'nav.sectors' => 'Sekta',
        'nav.view_all_sectors' => 'Angalia sekta zote',
        'nav.search_products' => 'Tafuta bidhaa',
        'nav.my_inquiries' => 'Maombi yangu',
        'nav.factory_workspace' => 'Eneo la kiwanda',
        'nav.admin_panel' => 'Paneli ya msimamizi',
        'nav.view_site' => 'Tazama tovuti',
        'nav.logout' => 'Toka',
        'nav.language' => 'Lugha',
        'search.placeholder' => 'Unatafuta bidhaa gani leo?',
        'search.all_categories' => 'Makundi yote',
        'search.search' => 'Tafuta',
        'nav.brand_subtitle' => 'Ununuzi wa B2B kwa Tanzania na Afrika Mashariki',
        'footer.buyers' => 'Wanunuzi',
        'footer.create_account' => 'Fungua akaunti',
        'footer.browse_categories' => 'Vinjari makundi',
        'footer.dashboard' => 'Dashibodi',
        'footer.popular_sectors' => 'Sekta maarufu',
        'footer.compliance' => 'Uzingatiaji',
        'footer.compliance_text' => 'Utambulisho wa wasambazaji na bei husimamiwa ndani. Orodha za umma hazioneshi mawasiliano ya moja kwa moja ya kiwanda.',
        'account.signed_in' => 'Umeingia',
        'account.order_updates' => 'Masasisho ya oda',
        'account.profile_settings' => 'Wasifu na mipangilio',
        'account.log_out' => 'Toka',
        'order.pending' => 'Inasubiri mapitio',
        'order.assigned' => 'Kwa msambazaji',
        'order.quoted' => 'Nukuu imetolewa — hatua inahitajika',
        'order.accepted' => 'Imekubaliwa',
        'order.in_production' => 'Uzalishaji unaendelea',
        'order.quality_control' => 'Udhibiti wa ubora',
        'order.shipped' => 'Imesafirishwa',
        'order.in_customs' => 'Iko forodhani',
        'order.delivered' => 'Imewasilishwa',
        'order.cancelled' => 'Imeghairiwa',
        'quotation.draft' => 'Rasimu',
        'quotation.sent' => 'Inasubiri majibu yako',
        'quotation.accepted' => 'Imekubaliwa',
        'quotation.rejected' => 'Imekataliwa',
        'quotation.expired' => 'Muda umeisha',
        'payment.pending' => 'Inasubiri uhakiki',
        'payment.verified' => 'Imethibitishwa',
        'payment.rejected' => 'Imekataliwa',
    ];
    $zh = [
        'nav.buyer_support' => '买家支持',
        'nav.tagline' => '东非采购中心 · 美元报价 · HANZO 认证供应商',
        'nav.login' => '登录',
        'nav.register_buyer' => '注册买家账户',
        'nav.categories' => '分类',
        'nav.browse' => '浏览',
        'nav.join_free' => '免费加入',
        'nav.home' => '首页',
        'nav.product_directory' => '产品目录',
        'nav.sectors' => '行业',
        'nav.view_all_sectors' => '查看全部行业',
        'nav.search_products' => '搜索产品',
        'nav.my_inquiries' => '我的询价',
        'nav.factory_workspace' => '工厂工作区',
        'nav.admin_panel' => '管理面板',
        'nav.view_site' => '查看网站',
        'nav.logout' => '退出',
        'nav.language' => '语言',
        'search.placeholder' => '今天想采购什么？',
        'search.all_categories' => '全部分类',
        'search.search' => '搜索',
        'nav.brand_subtitle' => '面向坦桑尼亚与东非的 B2B 采购',
        'footer.buyers' => '买家',
        'footer.create_account' => '创建账户',
        'footer.browse_categories' => '浏览分类',
        'footer.dashboard' => '仪表板',
        'footer.popular_sectors' => '热门行业',
        'footer.compliance' => '合规',
        'footer.compliance_text' => '供应商身份和价格由平台内部管理。公开列表不显示工厂直接联系方式。',
        'account.signed_in' => '已登录',
        'account.order_updates' => '订单更新',
        'account.profile_settings' => '资料与设置',
        'account.log_out' => '退出登录',
        'order.pending' => '待审核',
        'order.assigned' => '供应商处理中',
        'order.quoted' => '已报价——需要操作',
        'order.accepted' => '已接受',
        'order.in_production' => '生产中',
        'order.quality_control' => '质量检测',
        'order.shipped' => '已发货',
        'order.in_customs' => '清关中',
        'order.delivered' => '已送达',
        'order.cancelled' => '已取消',
        'quotation.draft' => '草稿',
        'quotation.sent' => '等待您的回应',
        'quotation.accepted' => '已接受',
        'quotation.rejected' => '已拒绝',
        'quotation.expired' => '已过期',
        'payment.pending' => '待核验',
        'payment.verified' => '已核验',
        'payment.rejected' => '已拒绝',
    ];

    $dict = match ($lang) {
        'sw' => $sw,
        'zh' => $zh,
        default => $en,
    };
    return $dict[$key] ?? $en[$key] ?? $key;
}

