<?php

declare(strict_types=1);

/**
 * Buyer-only order tracking notifications (writes to `notifications` with target_role = buyer).
 * Requires column `notifications.related_order_id` — run database/migrations/003_buyer_order_notifications.sql.
 */

function buyer_notifications_schema_ready(PDO $pdo): bool
{
    static $ready = null;
    if ($ready !== null) {
        return $ready;
    }
    try {
        $st = $pdo->query("SHOW COLUMNS FROM `notifications` LIKE 'related_order_id'");
        $ready = $st !== false && $st->fetch(PDO::FETCH_ASSOC) !== false;
    } catch (Throwable) {
        $ready = false;
    }
    return $ready;
}

function buyer_notification_insert(PDO $pdo, int $buyerId, int $orderId, string $title, string $message): void
{
    if (!buyer_notifications_schema_ready($pdo)) {
        return;
    }
    $title = mb_substr($title, 0, 150);
    $st = $pdo->prepare(
        'INSERT INTO notifications (target_role, target_id, related_order_id, title, message, is_read) VALUES (\'buyer\', ?, ?, ?, ?, 0)'
    );
    $st->execute([$buyerId, $orderId, $title, $message]);
}

/** Notify buyer when their order request is created (pending). */
function buyer_notify_order_submitted(PDO $pdo, int $orderId): void
{
    if (!buyer_notifications_schema_ready($pdo)) {
        return;
    }
    $st = $pdo->prepare('SELECT buyer_id, order_code, status FROM orders WHERE id = ? LIMIT 1');
    $st->execute([$orderId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return;
    }
    $buyerId = (int) $row['buyer_id'];
    $code = (string) $row['order_code'];
    $msg = 'Your sourcing request was received and is pending HANZO review.';
    buyer_notification_insert($pdo, $buyerId, $orderId, 'Order ' . $code . ' submitted', $msg);
}

/** Notify buyer when order workflow status changes (admin, factory, or buyer actions). */
function buyer_notify_order_status_changed(PDO $pdo, int $orderId, string $oldStatus, string $newStatus): void
{
    if ($oldStatus === $newStatus || !buyer_notifications_schema_ready($pdo)) {
        return;
    }
    $st = $pdo->prepare('SELECT buyer_id, order_code FROM orders WHERE id = ? LIMIT 1');
    $st->execute([$orderId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return;
    }
    $buyerId = (int) $row['buyer_id'];
    $code = (string) $row['order_code'];
    $from = order_status_label($oldStatus);
    $to = order_status_label($newStatus);
    $msg = 'Status updated from “' . $from . '” to “' . $to . '”. Open your orders for full details.';
    buyer_notification_insert($pdo, $buyerId, $orderId, 'Order ' . $code . ' updated', $msg);
}

function buyer_order_notifications_unread_count(PDO $pdo, int $buyerId): int
{
    if (!buyer_notifications_schema_ready($pdo)) {
        return 0;
    }
    $st = $pdo->prepare(
        'SELECT COUNT(*) FROM notifications WHERE target_role = \'buyer\' AND target_id = ? AND is_read = 0 AND related_order_id IS NOT NULL'
    );
    $st->execute([$buyerId]);
    return (int) $st->fetchColumn();
}

/** @return list<array{id:int,title:string,message:?string,related_order_id:?int,is_read:int,created_at:string}> */
function buyer_order_notifications_fetch(PDO $pdo, int $buyerId, int $limit = 50): array
{
    if (!buyer_notifications_schema_ready($pdo)) {
        return [];
    }
    $lim = max(1, min(100, $limit));
    $st = $pdo->prepare(
        'SELECT id, title, message, related_order_id, is_read, created_at FROM notifications
         WHERE target_role = \'buyer\' AND target_id = ? AND related_order_id IS NOT NULL
         ORDER BY created_at DESC, id DESC
         LIMIT ' . $lim
    );
    $st->execute([$buyerId]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

function buyer_notification_mark_read(PDO $pdo, int $buyerId, int $notificationId): void
{
    if (!buyer_notifications_schema_ready($pdo) || $notificationId <= 0) {
        return;
    }
    $pdo->prepare(
        'UPDATE notifications SET is_read = 1 WHERE id = ? AND target_role = \'buyer\' AND target_id = ? AND related_order_id IS NOT NULL'
    )->execute([$notificationId, $buyerId]);
}

function buyer_notifications_mark_all_read(PDO $pdo, int $buyerId): void
{
    if (!buyer_notifications_schema_ready($pdo)) {
        return;
    }
    $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE target_role = \'buyer\' AND target_id = ? AND is_read = 0 AND related_order_id IS NOT NULL')
        ->execute([$buyerId]);
}
