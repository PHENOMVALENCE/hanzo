<?php

declare(strict_types=1);

/** Payment row amount → USD for balance math (catalog & quotes are USD). */
function hanzo_payment_amount_to_usd(float $amount, string $currency): float
{
    $c = strtoupper(trim($currency));
    if ($c === 'USD') {
        return $amount;
    }
    if ($c === 'TZS') {
        $r = hanzo_exchange_rates()['TZS_PER_USD'];

        return $r > 0 ? $amount / $r : $amount;
    }

    return $amount;
}

/**
 * Per-order agreed total (latest accepted quote), verified paid (USD equivalent), and balance due.
 *
 * @param array<int> $orderIds
 * @return array<int, array{agreed_usd: float|null, paid_usd: float, due_usd: float|null}>
 */
function hanzo_order_balance_map(PDO $pdo, array $orderIds): array
{
    $orderIds = array_values(array_unique(array_filter(array_map(static fn ($id): int => (int) $id, $orderIds), static fn (int $id): bool => $id > 0)));
    $out = [];
    foreach ($orderIds as $oid) {
        $out[$oid] = ['agreed_usd' => null, 'paid_usd' => 0.0, 'due_usd' => null];
    }
    if ($orderIds === []) {
        return $out;
    }

    $ph = implode(',', array_fill(0, count($orderIds), '?'));

    $sql = "SELECT q.order_id, q.total_landed_cost FROM quotations q
        INNER JOIN (
            SELECT order_id, MAX(id) AS mid FROM quotations WHERE status = 'accepted' GROUP BY order_id
        ) z ON z.mid = q.id WHERE q.order_id IN ($ph)";
    $st = $pdo->prepare($sql);
    $st->execute($orderIds);
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $oid = (int) $row['order_id'];
        if (isset($out[$oid])) {
            $out[$oid]['agreed_usd'] = (float) $row['total_landed_cost'];
        }
    }

    $st = $pdo->prepare("SELECT order_id, currency, amount FROM payments WHERE status = 'verified' AND order_id IN ($ph)");
    $st->execute($orderIds);
    while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
        $oid = (int) $row['order_id'];
        if (!isset($out[$oid])) {
            continue;
        }
        $out[$oid]['paid_usd'] += hanzo_payment_amount_to_usd((float) $row['amount'], (string) $row['currency']);
    }

    foreach ($out as &$rec) {
        if ($rec['agreed_usd'] !== null) {
            $rec['due_usd'] = max(0.0, $rec['agreed_usd'] - $rec['paid_usd']);
        }
        unset($rec);
    }

    return $out;
}

function hanzo_format_order_money_usd(?float $value): string
{
    if ($value === null) {
        return '—';
    }

    return 'US$' . number_format($value, 2);
}

/**
 * Balance remaining on the order after each verified payment (chronological), keyed by payment id.
 *
 * @return array<int, float|null>
 */
function hanzo_buyer_payment_due_after_map(PDO $pdo, int $buyerId): array
{
    $st = $pdo->prepare('SELECT id FROM orders WHERE buyer_id = ?');
    $st->execute([$buyerId]);
    $orderIds = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    $balances = hanzo_order_balance_map($pdo, $orderIds);

    $st = $pdo->prepare("SELECT id, order_id, currency, amount FROM payments WHERE buyer_id = ? AND status = 'verified' ORDER BY created_at ASC, id ASC");
    $st->execute([$buyerId]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $cumulative = [];
    $out = [];
    foreach ($rows as $row) {
        $oid = (int) $row['order_id'];
        $pid = (int) $row['id'];
        $add = hanzo_payment_amount_to_usd((float) $row['amount'], (string) $row['currency']);
        $cumulative[$oid] = ($cumulative[$oid] ?? 0.0) + $add;
        $agreed = $balances[$oid]['agreed_usd'] ?? null;
        $out[$pid] = $agreed !== null ? max(0.0, $agreed - $cumulative[$oid]) : null;
    }

    return $out;
}
