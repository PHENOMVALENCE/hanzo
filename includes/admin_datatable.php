<?php

declare(strict_types=1);

/**
 * Shared GET params for admin list pages (pagination, search, filters, sort).
 *
 * @return array{page:int,per_page:int,offset:int,q:string,status:string,date_from:string,date_to:string,category_id:int,sort:string,dir:string}
 */
function admin_dt_params(int $defaultPerPage = 15): array
{
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = max(5, min(100, (int) ($_GET['per_page'] ?? $defaultPerPage)));
    $q = trim((string) ($_GET['q'] ?? ''));
    $status = trim((string) ($_GET['status'] ?? ''));
    $dateFrom = trim((string) ($_GET['date_from'] ?? ''));
    $dateTo = trim((string) ($_GET['date_to'] ?? ''));
    $categoryId = max(0, (int) ($_GET['category_id'] ?? 0));
    $sort = strtolower(preg_replace('/[^a-z0-9_]/', '', (string) ($_GET['sort'] ?? '')));
    $dirU = strtoupper((string) ($_GET['dir'] ?? 'DESC'));
    $dir = $dirU === 'ASC' ? 'ASC' : 'DESC';

    return [
        'page' => $page,
        'per_page' => $perPage,
        'offset' => ($page - 1) * $perPage,
        'q' => $q,
        'status' => $status,
        'date_from' => $dateFrom,
        'date_to' => $dateTo,
        'category_id' => $categoryId,
        'sort' => $sort,
        'dir' => $dir,
    ];
}

/** Build query string from current request merged with overrides (empty string removes key). */
function admin_dt_query(array $merge = []): string
{
    $g = $_GET;
    foreach ($merge as $k => $v) {
        if ($v === null || $v === '') {
            unset($g[$k]);
        } else {
            $g[$k] = $v;
        }
    }

    return http_build_query($g);
}

function admin_dt_clamp_page(int $page, int $total, int $perPage): int
{
    $pp = max(1, $perPage);
    $maxPage = max(1, (int) ceil($total / $pp));

    return min(max(1, $page), $maxPage);
}

/**
 * @param array<string,string> $sortMap request sort key => SQL ORDER BY expression (identifier only, no user input)
 */
function admin_dt_order_fragment(string $sort, string $dir, array $sortMap, string $defaultExpr): string
{
    $expr = $sortMap[$sort] ?? $defaultExpr;
    $d = $dir === 'ASC' ? 'ASC' : 'DESC';

    return $expr . ' ' . $d;
}

/** Next dir when clicking a column header. */
function admin_dt_toggle_dir(string $currentSort, string $column, string $currentDir): string
{
    if ($currentSort === $column) {
        return strtoupper($currentDir) === 'DESC' ? 'asc' : 'desc';
    }

    return 'desc';
}

function admin_dt_render_pager(string $path, int $total, int $page, int $perPage): void
{
    $path = ltrim($path, '/');
    $pp = max(1, $perPage);
    $pages = max(1, (int) ceil($total / $pp));
    $page = admin_dt_clamp_page($page, $total, $pp);
    $from = $total > 0 ? (($page - 1) * $pp + 1) : 0;
    $to = min($total, $page * $pp);

    echo '<div class="admin-dt-pager d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3 border-top mt-2">';
    echo '<div class="small text-muted">Showing ' . (int) $from . '–' . (int) $to . ' of ' . (int) $total . '</div>';
    echo '<nav aria-label="Pagination"><ul class="pagination pagination-sm mb-0">';

    $mk = static function (int $p) use ($path): string {
        $qs = admin_dt_query(['page' => $p]);

        return e(app_url($path . ($qs !== '' ? '?' . $qs : '')));
    };

    $prev = max(1, $page - 1);
    $next = min($pages, $page + 1);
    echo '<li class="page-item' . ($page <= 1 ? ' disabled' : '') . '"><a class="page-link" href="' . $mk($prev) . '">Prev</a></li>';

    $show = [];
    foreach ([1, $page - 1, $page, $page + 1, $pages] as $i) {
        if ($i >= 1 && $i <= $pages) {
            $show[$i] = true;
        }
    }
    ksort($show);
    $lastPrinted = 0;
    foreach (array_keys($show) as $i) {
        if ($lastPrinted && $i > $lastPrinted + 1) {
            echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
        $active = $i === $page ? ' active' : '';
        echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $mk($i) . '">' . $i . '</a></li>';
        $lastPrinted = $i;
    }

    echo '<li class="page-item' . ($page >= $pages ? ' disabled' : '') . '"><a class="page-link" href="' . $mk($next) . '">Next</a></li>';
    echo '</ul></nav></div>';
}

/** Th with sort link; $column is GET sort= value. */
function admin_dt_sort_th(string $label, string $column, string $currentSort, string $currentDir, string $path): void
{
    $path = ltrim($path, '/');
    $nextDir = admin_dt_toggle_dir($currentSort, $column, $currentDir);
    $qs = admin_dt_query(['sort' => $column, 'dir' => $nextDir, 'page' => 1]);
    $href = e(app_url($path . ($qs !== '' ? '?' . $qs : '')));
    $arrow = '';
    if ($currentSort === $column) {
        $arrow = strtoupper($currentDir) === 'ASC' ? ' <span class="text-muted">↑</span>' : ' <span class="text-muted">↓</span>';
    }
    echo '<th scope="col"><a class="text-decoration-none text-dark" href="' . $href . '">' . e($label) . $arrow . '</a></th>';
}
