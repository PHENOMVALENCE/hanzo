<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const HANZO_ALLOWED_LANGS = ['en', 'sw', 'zh'];
const HANZO_DEFAULT_LANG = 'en';

function hanzo_allowed_languages(): array
{
    return HANZO_ALLOWED_LANGS;
}

function hanzo_normalize_lang(mixed $lang): string
{
    $v = is_string($lang) ? strtolower(trim($lang)) : '';
    return in_array($v, HANZO_ALLOWED_LANGS, true) ? $v : HANZO_DEFAULT_LANG;
}

function hanzo_current_language(): string
{
    return hanzo_normalize_lang($_SESSION['lang'] ?? HANZO_DEFAULT_LANG);
}

function hanzo_set_language(string $lang): void
{
    $_SESSION['lang'] = hanzo_normalize_lang($lang);
}

function hanzo_html_lang(): string
{
    return match (hanzo_current_language()) {
        'sw' => 'sw',
        'zh' => 'zh-CN',
        default => 'en',
    };
}

function hanzo_language_label(string $lang): string
{
    return match (hanzo_normalize_lang($lang)) {
        'sw' => 'Kiswahili',
        'zh' => '中文',
        default => 'English',
    };
}

function hanzo_lang_switch_url(string $lang): string
{
    $lang = hanzo_normalize_lang($lang);
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

function hanzo_language_file(string $lang): string
{
    $lang = hanzo_normalize_lang($lang);
    return dirname(__DIR__) . '/lang/' . $lang . '.php';
}

function hanzo_load_translations(string $lang): array
{
    $file = hanzo_language_file($lang);
    if (!is_file($file)) {
        return [];
    }
    $arr = require $file;
    return is_array($arr) ? $arr : [];
}

function hanzo_translation_dict(): array
{
    static $cache = [];
    $lang = hanzo_current_language();
    if (!isset($cache[$lang])) {
        $base = hanzo_load_translations('en');
        $cur = $lang === 'en' ? $base : array_merge($base, hanzo_load_translations($lang));
        $cache[$lang] = $cur;
    }
    return $cache[$lang];
}

function __(string $key): string
{
    $dict = hanzo_translation_dict();
    return isset($dict[$key]) ? (string) $dict[$key] : $key;
}

function t(string $key): string
{
    return __($key);
}

function hanzo_update_user_language_preference(PDO $pdo, int $uid, string $role, string $lang): void
{
    if ($uid <= 0 || !in_array($role, ['buyer', 'factory', 'admin'], true)) {
        return;
    }
    $table = match ($role) {
        'buyer' => 'buyers',
        'factory' => 'factories',
        'admin' => 'admins',
        default => '',
    };
    if ($table === '') {
        return;
    }
    try {
        $sql = 'UPDATE ' . $table . ' SET preferred_language = ? WHERE id = ?';
        $st = $pdo->prepare($sql);
        $st->execute([hanzo_normalize_lang($lang), $uid]);
    } catch (Throwable) {
        // Column may not exist yet; ignore safely.
    }
}

/**
 * Initialize language from GET/session and optionally persist for logged-in user.
 */
function hanzo_language_bootstrap(?PDO $pdo = null): void
{
    if (!isset($_SESSION['lang']) || !is_string($_SESSION['lang'])) {
        $_SESSION['lang'] = HANZO_DEFAULT_LANG;
    }

    $requested = $_GET['lang'] ?? null;
    if (!is_string($requested) || $requested === '') {
        $_SESSION['lang'] = hanzo_normalize_lang($_SESSION['lang']);
        return;
    }

    $lang = hanzo_normalize_lang($requested);
    $_SESSION['lang'] = $lang;

    $user = $_SESSION['user'] ?? null;
    if ($pdo !== null && is_array($user) && isset($user['id'], $user['role'])) {
        hanzo_update_user_language_preference($pdo, (int) $user['id'], (string) $user['role'], $lang);
    }
}

function hanzo_phrase_maps(): array
{
    return [
        'sw' => [
            'Buyer dashboard' => 'Dashibodi ya Mnunuzi',
            'Welcome back,' => 'Karibu tena,',
            'All orders' => 'Maagizo yote',
            'Total orders' => 'Jumla ya maagizo',
            'Awaiting your decision' => 'Inasubiri uamuzi wako',
            'Quotes need a response' => 'Nukuu zinahitaji majibu',
            'In fulfillment' => 'Katika utekelezaji',
            'Production through customs' => 'Uzalishaji hadi forodha',
            'Recent orders' => 'Maagizo ya hivi karibuni',
            'Open full list' => 'Fungua orodha yote',
            'No orders yet.' => 'Bado hakuna maagizo.',
            'Browse products' => 'Vinjari bidhaa',
            'Factory Dashboard' => 'Dashibodi ya Kiwanda',
            'HANZO verified partner' => 'Mshirika aliyethibitishwa na HANZO',
            'Manage catalog, fulfil assigned orders, and post production milestones through HANZO.' => 'Simamia katalogi, timiza maagizo uliyopewa, na chapisha hatua za uzalishaji kupitia HANZO.',
            'Active SKUs' => 'SKU zinazotumika',
            'Live on marketplace' => 'Ziko sokoni',
            'Draft SKUs' => 'SKU rasimu',
            'Finish & publish' => 'Kamilisha na chapisha',
            'Awaiting start' => 'Inasubiri kuanza',
            'Orders in “assigned”' => 'Maagizo yaliyo “assigned”',
            'Production & QC' => 'Uzalishaji na QC',
            'In factory & QC stages' => 'Katika hatua za kiwanda na QC',
            'Recent assigned orders' => 'Maagizo yaliyogawiwa karibuni',
            'Open all' => 'Fungua yote',
            'No orders assigned yet.' => 'Bado hakuna maagizo yaliyogawiwa.',
            'Workspace' => 'Eneo la kazi',
            'Manage products' => 'Simamia bidhaa',
            'Assigned orders' => 'Maagizo yaliyogawiwa',
            'Production updates' => 'Masasisho ya uzalishaji',
            'My profile' => 'Wasifu wangu',
            'Privacy & workflow' => 'Faragha na mtiririko wa kazi',
            'Shipping & Delivery Tracking' => 'Ufuatiliaji wa usafirishaji na uwasilishaji',
            'Location' => 'Mahali',
            'Tracking #' => 'Namba ya ufuatiliaji',
            'Update time' => 'Muda wa sasisho',
            'No shipping updates yet.' => 'Bado hakuna masasisho ya usafirishaji.',
            'Search results' => 'Matokeo ya utafutaji',
            'Product categories' => 'Makundi ya bidhaa',
            'View products' => 'Tazama bidhaa',
            'View details' => 'Tazama maelezo',
            'Product not found.' => 'Bidhaa haikupatikana.',
            'Category not found.' => 'Kundi halikupatikana.',
        ],
        'zh' => [
            'Buyer dashboard' => '买家仪表板',
            'Welcome back,' => '欢迎回来，',
            'All orders' => '全部订单',
            'Total orders' => '订单总数',
            'Awaiting your decision' => '等待您的决定',
            'Quotes need a response' => '报价需要回复',
            'In fulfillment' => '履约中',
            'Production through customs' => '生产到清关阶段',
            'Recent orders' => '最近订单',
            'Open full list' => '查看完整列表',
            'No orders yet.' => '暂无订单。',
            'Browse products' => '浏览产品',
            'Factory Dashboard' => '工厂仪表板',
            'HANZO verified partner' => 'HANZO 认证合作伙伴',
            'Manage catalog, fulfil assigned orders, and post production milestones through HANZO.' => '通过 HANZO 管理目录、处理分配订单并发布生产里程碑。',
            'Active SKUs' => '在售 SKU',
            'Live on marketplace' => '已在市场展示',
            'Draft SKUs' => '草稿 SKU',
            'Finish & publish' => '完成并发布',
            'Awaiting start' => '待开始',
            'Orders in “assigned”' => '状态为“assigned”的订单',
            'Production & QC' => '生产与质检',
            'In factory & QC stages' => '处于工厂与质检阶段',
            'Recent assigned orders' => '最近分配订单',
            'Open all' => '查看全部',
            'No orders assigned yet.' => '暂无已分配订单。',
            'Workspace' => '工作区',
            'Manage products' => '管理产品',
            'Assigned orders' => '已分配订单',
            'Production updates' => '生产更新',
            'My profile' => '我的资料',
            'Privacy & workflow' => '隐私与流程',
            'Shipping & Delivery Tracking' => '物流与交付追踪',
            'Location' => '位置',
            'Tracking #' => '追踪号',
            'Update time' => '更新时间',
            'No shipping updates yet.' => '暂无物流更新。',
            'Search results' => '搜索结果',
            'Product categories' => '产品分类',
            'View products' => '查看产品',
            'View details' => '查看详情',
            'Product not found.' => '未找到产品。',
            'Category not found.' => '未找到分类。',
        ],
    ];
}

function hanzo_i18n_output_callback(string $html): string
{
    $lang = hanzo_current_language();
    if ($lang === 'en') {
        return $html;
    }
    $maps = hanzo_phrase_maps();
    $pairs = $maps[$lang] ?? [];
    return $pairs === [] ? $html : strtr($html, $pairs);
}

function hanzo_start_i18n_output_buffer(): void
{
    static $started = false;
    if ($started) {
        return;
    }
    $started = true;
    if (hanzo_current_language() !== 'en') {
        ob_start('hanzo_i18n_output_callback');
    }
}

