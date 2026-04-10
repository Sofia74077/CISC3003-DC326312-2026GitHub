<?php
declare(strict_types=1);

function dataPath(string $file): string {
    return dirname(__DIR__) . '/data/' . $file;
}

function makeCoverDataUri(string $isbn): string {
    $safeIsbn = htmlspecialchars($isbn, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="60" height="84" viewBox="0 0 60 84">'
        . '<rect width="60" height="84" rx="4" fill="#e8eaf6"/>'
        . '<rect x="4" y="4" width="52" height="76" rx="3" fill="#5c6bc0"/>'
        . '<rect x="10" y="12" width="40" height="10" fill="#c5cae9"/>'
        . '<text x="30" y="42" font-size="7" text-anchor="middle" fill="#ffffff">ISBN</text>'
        . '<text x="30" y="52" font-size="6" text-anchor="middle" fill="#ffffff">' . $safeIsbn . '</text>'
        . '</svg>';
    return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
}

function resolveLocalCoverUrl(string $isbn): ?string {
    $baseDir = dirname(__DIR__) . '/images/tinysquare/';
    $candidates = [
        $isbn . '.jpg',
        $isbn . '.jpeg',
        $isbn . '.png',
        strtoupper($isbn) . '.jpg',
        strtoupper($isbn) . '.jpeg',
        strtoupper($isbn) . '.png',
    ];

    foreach ($candidates as $file) {
        if (is_file($baseDir . $file)) {
            return 'images/tinysquare/' . $file;
        }
    }

    return null;
}

function getCustomers(): array {
    $rows = @file(dataPath('customers.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$rows) {
        return [];
    }

    $customers = [];
    foreach ($rows as $row) {
        $parts = explode(';', $row);
        if (count($parts) < 12) {
            continue;
        }

        $id = (int)trim($parts[0]);
        $firstName = trim($parts[1]);
        $lastName = trim($parts[2]);
        $university = trim($parts[4]);
        $city = trim($parts[6]);
        $salesSeries = trim($parts[11]);

        $salesParts = array_filter(array_map('trim', explode(',', $salesSeries)), 'strlen');
        $salesValues = array_map('intval', $salesParts);
        $salesSum = array_sum($salesValues);

        $customers[$id] = [
            'id' => $id,
            'customerName' => trim($firstName . ' ' . $lastName),
            'email' => trim($parts[3]),
            'university' => $university,
            'address' => trim($parts[5]),
            'city' => $city,
            'region' => trim($parts[7]),
            'country' => trim($parts[8]),
            'postal' => trim($parts[9]),
            'phone' => trim($parts[10]),
            'salesByMonth' => $salesValues,
            'sales' => $salesSum,
        ];
    }

    return $customers;
}

function getCustomerById(int $id): ?array {
    $customers = getCustomers();
    return $customers[$id] ?? null;
}

function getOrdersByCustomerId(int $customerId): array {
    $rows = @file(dataPath('orders.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$rows) {
        return [];
    }

    $orders = [];
    foreach ($rows as $row) {
        $parts = array_map('trim', explode(',', $row));
        if (count($parts) < 5) {
            continue;
        }

        $orderId = (int)$parts[0];
        $rowCustomerId = (int)$parts[1];
        if ($rowCustomerId !== $customerId) {
            continue;
        }

        $isbn = $parts[2];
        $category = $parts[count($parts) - 1];
        $titleTokens = array_slice($parts, 3, count($parts) - 4);
        $title = implode(', ', $titleTokens);

        $orders[] = [
            'orderID' => $orderId,
            'customerID' => $rowCustomerId,
            'isbn' => $isbn,
            'title' => $title,
            'category' => $category,
            'coverUrl' => resolveLocalCoverUrl($isbn) ?? makeCoverDataUri($isbn),
        ];
    }

    return $orders;
}